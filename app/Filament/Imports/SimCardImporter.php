<?php

namespace App\Filament\Imports;

use App\Models\SimCard;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SimCardImporter extends Importer
{
    protected static ?string $model = SimCard::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('serial_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('phone_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('simSupplier')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('imei')
                ->rules(['max:255']),
            ImportColumn::make('is_active')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): SimCard
    {
        return SimCard::firstOrNew([
            'serial_number' => $this->data['serial_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your sim card import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
