<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReservasExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $reservas) {}

    public function collection(): Collection
    {
        return $this->reservas;
    }

    public function headings(): array
    {
        return ['Código', 'Cliente', 'Complejo', 'Cancha', 'Fecha', 'Total', 'Estado'];
    }

    public function map($reserva): array
    {
        return [
            $reserva['codigo'],
            $reserva['cliente'],
            $reserva['complejo'],
            $reserva['cancha'],
            $reserva['fecha'],
            $reserva['total'],
            $reserva['estado'],
        ];
    }
}
