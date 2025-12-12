<?php

namespace App\Filament\Resources\SalaryPaymentResource\Pages;

use App\Filament\Resources\SalaryPaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalaryPayment extends CreateRecord
{
    protected static string $resource = SalaryPaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate net salary before save
        $data['net_salary'] = ($data['basic_salary'] ?? 0)
            + ($data['allowances'] ?? 0)
            + ($data['bonus'] ?? 0)
            - ($data['deductions'] ?? 0)
            - ($data['advance_deduction'] ?? 0);

        return $data;
    }
}
