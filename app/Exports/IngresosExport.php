<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IngresosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $ingresos) {}

    public function collection(): Collection
    {
        return $this->ingresos;
    }

    public function headings(): array
    {
        return ['Fecha', 'Pagos', 'Total'];
    }

    public function map($ingreso): array
    {
        return [
            $ingreso['fecha'],
            $ingreso['cantidad'],
            $ingreso['total'],
        ];
    }
}
