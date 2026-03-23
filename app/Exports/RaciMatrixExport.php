<?php

namespace App\Exports;

use App\Models\Process;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RaciMatrixExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $processId;

    public function __construct($processId)
    {
        $this->processId = $processId;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Grassetto per la prima riga (Headings)
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function collection()
    {
        $process = Process::with(['processTasks.businessFunctions'])->find($this->processId);

        // Se il processo non esiste o non ha task, restituisci una collezione vuota
        // per evitare l'errore "all() on null"
        if (!$process || !$process->processTasks) {
            return collect();
        }

        return $process->processTasks;
    }

    // ... restanti metodi headings e map (rimangono invariati)
    public function headings(): array
    {
        return [
            '#',
            'Attività / Task',
            'R (Responsible)',
            'A (Accountable)',
            'C (Consulted)',
            'I (Informed)',
        ];
    }

    public function map($task): array
    {
        return [
            $task->sort_order,
            $task->name,
            // Filtriamo le funzioni per ruolo nella pivot
            $task->businessFunctions->where('pivot.role', 'R')->pluck('code')->implode(', '),
            $task->businessFunctions->where('pivot.role', 'A')->pluck('code')->implode(', '),
            $task->businessFunctions->where('pivot.role', 'C')->pluck('code')->implode(', '),
            $task->businessFunctions->where('pivot.role', 'I')->pluck('code')->implode(', '),
        ];
    }
}
