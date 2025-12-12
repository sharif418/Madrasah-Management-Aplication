<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Guardian;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\AcademicYear;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    protected int $successCount = 0;
    protected int $skipCount = 0;
    protected array $errors = [];
    protected ?AcademicYear $academicYear;

    public function __construct()
    {
        $this->academicYear = AcademicYear::where('is_current', true)->first();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of 0-index and header row

            try {
                // Skip if mandatory fields are missing
                if (empty($row['name'])) {
                    $this->skipCount++;
                    $this->errors[] = "সারি {$rowNumber}: নাম খালি, স্কিপ করা হয়েছে";
                    continue;
                }

                // Check for duplicate student by student_id if provided
                if (!empty($row['student_id'])) {
                    $existingStudent = Student::where('student_id', $row['student_id'])->first();
                    if ($existingStudent) {
                        $this->skipCount++;
                        $this->errors[] = "সারি {$rowNumber}: ছাত্র আইডি {$row['student_id']} আগে থেকেই আছে, স্কিপ করা হয়েছে";
                        continue;
                    }
                }

                DB::beginTransaction();

                // 1. Find or Create Guardian (Parent) User
                $guardianPhone = $row['phone'] ?? $row['guardian_phone'] ?? null;
                $guardianUser = null;
                $guardian = null;

                if ($guardianPhone) {
                    $guardianUser = User::firstOrCreate(
                        ['phone' => $guardianPhone],
                        [
                            'name' => $row['guardian_name'] ?? $row['father_name'] ?? ($row['name'] . ' এর অভিভাবক'),
                            'email' => $row['guardian_email'] ?? null,
                            'password' => Hash::make('12345678'),
                            'role' => 'guardian',
                        ]
                    );

                    // 2. Find or Create Guardian Profile
                    $guardian = Guardian::firstOrCreate(
                        ['user_id' => $guardianUser->id],
                        [
                            'father_name' => $row['father_name'] ?? null,
                            'mother_name' => $row['mother_name'] ?? null,
                            'phone' => $guardianPhone,
                            'father_occupation' => $row['father_occupation'] ?? null,
                            'mother_occupation' => $row['mother_occupation'] ?? null,
                            'address' => $row['address'] ?? null,
                        ]
                    );
                }

                // 3. Find Class & Section
                $class = null;
                if (!empty($row['class'])) {
                    $class = ClassName::where('name', 'like', '%' . trim($row['class']) . '%')->first();
                    if (!$class) {
                        $this->errors[] = "সারি {$rowNumber}: শ্রেণী '{$row['class']}' পাওয়া যায়নি";
                    }
                }

                $section = null;
                if ($class && !empty($row['section'])) {
                    $section = Section::where('class_id', $class->id)
                        ->where('name', 'like', '%' . trim($row['section']) . '%')
                        ->first();
                    if (!$section) {
                        // Try without class filter
                        $section = Section::where('name', 'like', '%' . trim($row['section']) . '%')->first();
                    }
                }

                // 4. Generate Student ID if not provided
                $studentId = !empty($row['student_id'])
                    ? $row['student_id']
                    : Student::generateStudentId($this->academicYear?->year ?? date('Y'));

                // 5. Create Student User
                $studentUser = User::create([
                    'name' => $row['name'],
                    'email' => null,
                    'phone' => null,
                    'password' => Hash::make('12345678'),
                    'role' => 'student',
                ]);

                // 6. Parse date of birth
                $dateOfBirth = null;
                if (!empty($row['dob']) || !empty($row['date_of_birth'])) {
                    $dobValue = $row['dob'] ?? $row['date_of_birth'];
                    try {
                        if (is_numeric($dobValue)) {
                            // Excel serial date
                            $dateOfBirth = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dobValue)->format('Y-m-d');
                        } else {
                            $dateOfBirth = date('Y-m-d', strtotime($dobValue));
                        }
                    } catch (\Exception $e) {
                        $this->errors[] = "সারি {$rowNumber}: জন্ম তারিখ পার্স করতে ব্যর্থ";
                    }
                }

                // 7. Create Student Profile
                Student::create([
                    'user_id' => $studentUser->id,
                    'guardian_id' => $guardian?->id,
                    'class_id' => $class?->id,
                    'section_id' => $section?->id,
                    'academic_year_id' => $this->academicYear?->id,
                    'student_id' => $studentId,
                    'name_bn' => $row['name'],
                    'name_en' => $row['name_en'] ?? $row['name_english'] ?? null,
                    'gender' => $this->normalizeGender($row['gender'] ?? 'male'),
                    'admission_date' => now(),
                    'status' => 'active',
                    'date_of_birth' => $dateOfBirth,
                    'blood_group' => $row['blood_group'] ?? null,
                    'religion' => $row['religion'] ?? 'ইসলাম',
                    'nationality' => $row['nationality'] ?? 'বাংলাদেশী',
                    'birth_certificate_no' => $row['birth_certificate'] ?? $row['birth_certificate_no'] ?? null,
                    'previous_school' => $row['previous_school'] ?? null,
                ]);

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->skipCount++;
                $this->errors[] = "সারি {$rowNumber}: " . $e->getMessage();
                Log::error("Student Import Error Row {$rowNumber}: " . $e->getMessage());
            }
        }
    }

    /**
     * Normalize gender value
     */
    protected function normalizeGender(?string $gender): string
    {
        if (empty($gender)) {
            return 'male';
        }

        $gender = strtolower(trim($gender));

        if (in_array($gender, ['female', 'f', 'মহিলা', 'নারী', 'female'])) {
            return 'female';
        }

        return 'male';
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable',
            'date_of_birth' => 'nullable',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'নাম আবশ্যক',
        ];
    }

    /**
     * Batch insert size
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Get skip count
     */
    public function getSkipCount(): int
    {
        return $this->skipCount;
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get import summary
     */
    public function getSummary(): array
    {
        return [
            'success' => $this->successCount,
            'skipped' => $this->skipCount,
            'errors' => $this->errors,
            'failures' => $this->failures(),
        ];
    }
}
