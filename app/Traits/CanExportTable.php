<?php
namespace App\Traits;

use App\Exports\FilamentTableExport;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

trait CanExportTable
{
    public function getExportBulkAction(): BulkAction
    {
        return BulkAction::make('export_excel')
            ->label('Esporta in Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function (Collection $records) {
                // 1. Recuperiamo le colonne visibili della tabella
                $columns = $this->getTable()->getColumns();

                // 2. Prepariamo gli header (Label della colonna)
                $headings = collect($columns)->map(fn(Column $column) => $column->getLabel())->toArray();

                // 3. Prepariamo i dati riga per riga
                $data = $records->map(function ($record) use ($columns) {
                    $row = [];
                    foreach ($columns as $column) {
                        // Estraiamo il valore formattato (gestisce state, formatters, etc.)
                        $row[] = $column->record($record)->getState();
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
