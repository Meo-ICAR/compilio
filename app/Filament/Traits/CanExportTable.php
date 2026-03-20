<?php
namespace App\Filament\Traits;

use App\Exports\FilamentTableExport;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;  // Importante per il type-hinting
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

trait CanExportTable
{
    public static function getExportBulkAction(): BulkAction
    {
        return BulkAction::make('export_excel')
            ->label('Esporta in Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            // Aggiungiamo $livewire come secondo parametro
            ->action(function (Collection $records, HasTable $livewire) {
                // 1. Recuperiamo le colonne direttamente dall'istanza livewire della tabella
                $columns = $livewire->getTable()->getColumns();

                // 2. Prepariamo gli header
                $headings = collect($columns)
                    ->filter(fn(Column $column) => !$column->isHidden())
                    ->map(fn(Column $column) => $column->getLabel())
                    ->toArray();

                // 3. Prepariamo i dati riga per riga
                $data = $records->map(function ($record) use ($columns) {
                    $row = [];
                    foreach ($columns as $column) {
                        if (!$column->isHidden()) {
                            // Otteniamo il valore processato (quello che vedi a schermo)
                            $row[] = $column->record($record)->getState();
                        }
                    }
                    return $row;
                });

                $fileName = 'export_' . now()->format('Y-m-d_H-i') . '.xlsx';

                return Excel::download(
                    new FilamentTableExport($data, $headings),
                    $fileName
                );
            });
    }
}
