<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeStructure;
use App\Models\FeeType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyFees extends Command
{
    /**
     * The name and signature of the console command.
     * প্রতি মাসে ছাত্রদের ফি অটো এসাইন করার কমান্ড
     */
    protected $signature = 'fee:generate-monthly 
                            {--month= : মাস (1-12), ডিফল্ট বর্তমান মাস}
                            {--year= : বছর, ডিফল্ট বর্তমান বছর}
                            {--dry-run : শুধু দেখাবে, কোন ডাটা সেভ হবে না}';

    /**
     * The console command description.
     */
    protected $description = 'মাসিক ফি অটোমেটিক সকল ছাত্রদের জন্য এসাইন করুন';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;
        $dryRun = $this->option('dry-run');

        $this->info("📅 মাস: {$month}, বছর: {$year}");

        if ($dryRun) {
            $this->warn("⚠️ DRY RUN মোড - কোন ডাটা সেভ হবে না");
        }

        // মাসিক বেতন ফি টাইপ খুঁজুন
        $monthlyFeeType = FeeType::where('code', 'TUI')
            ->orWhere('is_recurring', true)
            ->first();

        if (!$monthlyFeeType) {
            $this->error("❌ মাসিক বেতন ফি টাইপ পাওয়া যায়নি! প্রথমে 'ফি এর ধরণ' এ মাসিক বেতন তৈরি করুন।");
            return Command::FAILURE;
        }

        $this->info("📋 ফি টাইপ: {$monthlyFeeType->name}");

        // সকল active ছাত্র
        $students = Student::where('status', 'active')
            ->with(['class', 'defaultDiscount'])
            ->get();

        $this->info("👨‍🎓 মোট সক্রিয় ছাত্র: {$students->count()}");

        $created = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students as $student) {
            try {
                // ছাত্রের class এর জন্য fee structure খুঁজুন
                $feeStructure = FeeStructure::where('class_id', $student->class_id)
                    ->where('fee_type_id', $monthlyFeeType->id)
                    ->where('is_active', true)
                    ->where(function ($query) use ($student) {
                        // আবাসিক/অনাবাসিক ফিল্টার
                        $query->whereNull('is_for_boarder')
                            ->orWhere('is_for_boarder', $student->is_boarder);
                    })
                    ->first();

                if (!$feeStructure) {
                    $this->newLine();
                    $this->warn("⚠️ {$student->name} এর শ্রেণির জন্য ফি কাঠামো নেই");
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // এই মাসে ইতিমধ্যে এসাইন আছে কিনা
                $exists = StudentFee::where('student_id', $student->id)
                    ->where('fee_structure_id', $feeStructure->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // ছাড় হিসাব
                $discount = $student->defaultDiscount;
                $originalAmount = $feeStructure->amount;
                $discountAmount = 0;

                if ($discount) {
                    $discountAmount = $discount->calculateDiscount($originalAmount);
                }

                $finalAmount = max(0, $originalAmount - $discountAmount);

                if (!$dryRun) {
                    StudentFee::create([
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructure->id,
                        'fee_discount_id' => $discount?->id,
                        'month' => $month,
                        'year' => $year,
                        'original_amount' => $originalAmount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'paid_amount' => 0,
                        'due_amount' => $finalAmount,
                        'status' => 'pending',
                        'due_date' => now()->setMonth($month)->setYear($year)->startOfMonth()->addDays($feeStructure->due_day ?? 10),
                    ]);
                }

                $created++;
            } catch (\Exception $e) {
                $errors++;
                Log::error("Fee generation failed for student {$student->id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ সফল: {$created} জন ছাত্রের ফি এসাইন হয়েছে");
        $this->info("⏭️ স্কিপ: {$skipped} জন (ইতিমধ্যে এসাইন/কোন fee structure নেই)");

        if ($errors > 0) {
            $this->error("❌ ত্রুটি: {$errors} জন");
        }

        return Command::SUCCESS;
    }
}
