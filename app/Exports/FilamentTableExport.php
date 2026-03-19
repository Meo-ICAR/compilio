<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FilamentTableExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        protected Collection $records,
        protected array $headings
    ) {}

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
