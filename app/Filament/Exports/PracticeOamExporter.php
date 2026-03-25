<?php

namespace App\Filament\Exports;

use App\Models\PracticeOam;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Illuminate\Database\Eloquent\Builder;

class PracticeOamExporter extends Exporter
{
    protected static ?string $model = PracticeOam::class;

    public static function getColumns(): array
    {
        return [
            // ID columns
            ExportColumn::make('oam_name')
                ->label('OAM Name'),
            ExportColumn::make('tipo_prodotto')
                ->label('Prodotto'),
            ExportColumn::make('principal_name')
                ->label('Mandante'),
            ExportColumn::make('name')
                ->label('Cliente'),
            ExportColumn::make('CRM_code')
                ->label('CRM Code'),
            ExportColumn::make('practice_name')
                ->label('Practice Name'),
            // Boolean columns (converted to 1/0)
            ExportColumn::make('is_cancel')
                ->label('Stornata')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_perfected')
                ->label('Perfezionata')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_conventioned')
                ->label('Convenzionata')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_notconventioned')
                ->label('Non Convenzionata')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_working')
                ->label('Working')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_notconvenctioned')
                ->label('Non Convenctioned')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_previous')
                ->label('Previous')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_invoice')
                ->label('Fatturata')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_before')
                ->label('Before')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_after')
                ->label('After')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            // Decimal/Amount columns
            ExportColumn::make('liquidato')
                ->label('Liquidato'),
            ExportColumn::make('liquidato_lavorazione')
                ->label('Liquidato Lavorazione'),
            ExportColumn::make('compenso')
                ->label('Compenso'),
            ExportColumn::make('compenso_lavorazione')
                ->label('Compenso Lavorazione'),
            ExportColumn::make('erogato')
                ->label('Erogato'),
            ExportColumn::make('erogato_lavorazione')
                ->label('Erogato Lavorazione'),
            ExportColumn::make('compenso_premio')
                ->label('Compenso Premio'),
            ExportColumn::make('compenso_rimborso')
                ->label('Compenso Rimborso'),
            ExportColumn::make('compenso_assicurazione')
                ->label('Compenso Assicurazione'),
            ExportColumn::make('compenso_cliente')
                ->label('Compenso Cliente'),
            ExportColumn::make('storno')
                ->label('Storno'),
            ExportColumn::make('provvigione')
                ->label('Provvigione'),
            ExportColumn::make('provvigione_lavorazione')
                ->label('Provvigione Lavorazione'),
            ExportColumn::make('provvigione_premio')
                ->label('Provvigione Premio'),
            ExportColumn::make('provvigione_rimborso')
                ->label('Provvigione Rimborso'),
            ExportColumn::make('provvigione_assicurazione')
                ->label('Provvigione Assicurazione'),
            ExportColumn::make('provvigione_storno')
                ->label('Provvigione Storno'),
            // Date columns
            ExportColumn::make('inserted_at')
                ->label('Inserita'),
            ExportColumn::make('accepted_at')
                ->label('Data Accettazione'),
            ExportColumn::make('erogated_at')
                ->label('Erogata'),
            ExportColumn::make('perfected_at')
                ->label('Data Perfezionamento'),
            ExportColumn::make('invoice_at')
                ->label('Fatturata'),
            ExportColumn::make('canceled_at')
                ->label('Data Storno'),
            // Integer columns
            ExportColumn::make('mese')
                ->label('Mese'),
            // Related data columns
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = "L'esportazione di Practice OAM è stata completata e " . number_format($export->successful_rows) . ' righe sono state elaborate.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' righe non sono state esportate a causa di errori.';
        }

        return $body;
    }
}
