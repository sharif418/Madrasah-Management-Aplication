<?php

namespace App\Filament\Resources\SalaryPaymentResource\Pages;

use App\Filament\Resources\SalaryPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalaryPayment extends EditRecord
{
    protected static string $resource = SalaryPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate net salary
        $data['net_salary'] = ($data['basic_salary'] ?? 0)
            + ($data['allowances'] ?? 0)
            + ($data['bonus'] ?? 0)
            - ($data['deductions'] ?? 0)
            - ($data['advance_deduction'] ?? 0);

        return $data;
    }
}
