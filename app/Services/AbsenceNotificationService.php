<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\SmsLog;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AbsenceNotificationService
{
    /**
     * SMS Gateway configuration
     * TODO: Configure actual SMS gateway (SSL Wireless, Elitbuzz, etc.)
     */
    protected bool $smsEnabled = false;
    protected ?string $smsGateway = null;

    public function __construct()
    {
        $this->smsEnabled = config('services.sms.enabled', false);
        $this->smsGateway = config('services.sms.gateway', null);
    }

    /**
     * Send absence notifications for today's absent students
     */
    public function notifyTodayAbsentStudents(?int $classId = null): array
    {
        $query = Attendance::with(['student.guardian'])
            ->today()
            ->absent();

        if ($classId) {
            $query->forClass($classId);
        }

        $absentRecords = $query->get();

        return $this->sendNotifications($absentRecords);
    }

    /**
     * Send notifications for given attendance records
     */
    public function sendNotifications(Collection $attendanceRecords): array
    {
        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($attendanceRecords as $attendance) {
            $student = $attendance->student;

            if (!$student) {
                $skipped++;
                continue;
            }

            // Get guardian phone number
            $phone = $this->getGuardianPhone($student);

            if (!$phone) {
                $skipped++;
                $errors[] = "{$student->name}: অভিভাবকের ফোন নম্বর নেই";
                continue;
            }

            // Prepare message
            $message = $this->prepareAbsenceMessage($student, $attendance);

            // Send SMS
            $result = $this->sendSms($phone, $message, $student->id, 'absence_alert');

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
                $errors[] = "{$student->name}: {$result['error']}";
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
            'total' => $attendanceRecords->count(),
            'errors' => $errors,
        ];
    }

    /**
     * Get guardian phone number for a student
     */
    protected function getGuardianPhone(Student $student): ?string
    {
        // Priority: Father phone > Mother phone > Guardian phone > Emergency contact
        $phone = $student->father_phone
            ?? $student->mother_phone
            ?? $student->guardian?->phone
            ?? $student->emergency_contact;

        if (!$phone) {
            return null;
        }

        // Format phone number (Bangladesh)
        return $this->formatPhoneNumber($phone);
    }

    /**
     * Format phone number for SMS
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);

        // Add Bangladesh country code if not present
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '88' . $phone;
        } elseif (strlen($phone) === 10) {
            $phone = '880' . $phone;
        }

        return $phone;
    }

    /**
     * Prepare absence message
     */
    protected function prepareAbsenceMessage(Student $student, Attendance $attendance): string
    {
        $institutionName = institution_name() ?? 'প্রতিষ্ঠান';
        $date = $attendance->date->format('d/m/Y');
        $className = $student->class?->name ?? '';

        return "সম্মানিত অভিভাবক, আপনার সন্তান {$student->name} ({$className}) আজ {$date} তারিখে {$institutionName} এ অনুপস্থিত। - {$institutionName}";
    }

    /**
     * Prepare late arrival message
     */
    protected function prepareLateMessage(Student $student, Attendance $attendance): string
    {
        $institutionName = institution_name() ?? 'প্রতিষ্ঠান';
        $inTime = $attendance->in_time ? \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') : '';
        $lateDuration = $attendance->late_duration_formatted ?? '';

        return "সম্মানিত অভিভাবক, আপনার সন্তান {$student->name} আজ {$inTime} এ ({$lateDuration}) এসেছে। - {$institutionName}";
    }

    /**
     * Send SMS via gateway
     */
    protected function sendSms(string $phone, string $message, int $studentId, string $type): array
    {
        // Log the SMS attempt
        $smsLog = SmsLog::create([
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'student_id' => $studentId,
            'status' => 'pending',
        ]);

        // Check if SMS is enabled
        if (!$this->smsEnabled) {
            $smsLog->update([
                'status' => 'disabled',
                'response' => 'SMS gateway not configured',
            ]);

            return [
                'success' => false,
                'error' => 'SMS gateway not configured',
            ];
        }

        try {
            // TODO: Implement actual SMS gateway integration
            // Example for SSL Wireless:
            // $response = Http::post('https://smsplus.sslwireless.com/api/v3/send-sms', [
            //     'api_token' => config('services.sms.api_key'),
            //     'sid' => config('services.sms.sender_id'),
            //     'msisdn' => $phone,
            //     'sms' => $message,
            //     'csms_id' => $smsLog->id,
            // ]);

            // For now, simulate success
            $smsLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return [
                'success' => true,
                'sms_log_id' => $smsLog->id,
            ];

        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());

            $smsLog->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send bulk absence SMS for a class
     */
    public function sendBulkAbsenceSms(int $classId, string $date): array
    {
        $absentRecords = Attendance::with(['student.guardian'])
            ->forClass($classId)
            ->forDate($date)
            ->absent()
            ->get();

        return $this->sendNotifications($absentRecords);
    }

    /**
     * Check if SMS is enabled
     */
    public function isEnabled(): bool
    {
        return $this->smsEnabled;
    }

    /**
     * Get SMS gateway status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->smsEnabled,
            'gateway' => $this->smsGateway,
            'configured' => !empty($this->smsGateway),
        ];
    }
}
