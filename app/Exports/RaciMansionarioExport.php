<?php

namespace App\Exports;

use Actions\CreateAction;
use App\Models\RaciAssignment;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RaciMansionarioExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return RaciAssignment::with(['businessFunction', 'processTask.process'])->get();
    }

    /**
     * Intestazioni delle colonne Excel
     */
    public function headings(): array
    {
        return [
            'ID Funzione',
            'Codice Business Function',
            'Macro Area',
            'Nome Funzione',
            'Macro Processo',
            'Task Operativo',
            'Ruolo RACI (Codice)',
            'Descrizione Ruolo'
        ];
    }

    /**
     * Mappatura dei dati per ogni riga
     */
    public function map($raci): array
    {
        return [
            $raci->businessFunction->id,
            $raci->businessFunction->code,
            $raci->businessFunction->macro_area,
            $raci->businessFunction->name,
            $raci->processTask->process->name,
            $raci->processTask->name,
            $raci->role,
            match ($raci->role) {
                'R' => "Responsible (Chi esegue l'attività)",
                'A' => 'Accountable (Chi approva e risponde)',
                'C' => 'Consulted (Chi viene consultato)',
                'I' => 'Informed (Chi viene informato)',
                default => 'N/D'
            },
        ];
    }

    /**
     * Applica gli stili condizionali alle celle
     */
    public function styles(Worksheet $sheet)
    {
        // Stile per l'intestazione (Grassetto e Sfondo Grigio Scuro)
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet
            ->getStyle('A1:H1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFE0E0E0');

        $rows = $sheet->getHighestRow();

        for ($i = 2; $i <= $rows; $i++) {
            $roleValue = $sheet->getCell("G{$i}")->getValue();  // Colonna G = Ruolo RACI

            $color = match ($roleValue) {
                'A' => 'FFFFCCCC',  // Rosso tenue per Accountable (Critico)
                'R' => 'FFCCFFCC',  // Verde tenue per Responsible (Operativo)
                'C' => 'FFE0FFFF',  // Azzurro per Consulted
                'I' => 'F5F5F5F5',  // Grigio per Informed
                default => 'FFFFFFFF'
            };

            $sheet
                ->getStyle("A{$i}:H{$i}")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB($color);
        }
    }
}
