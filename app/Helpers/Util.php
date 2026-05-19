<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Util
{
    static function formatoFecha($fecha)
    {
        $contenedor = strtotime($fecha);
        $dia = date('d', $contenedor);
        $mes = date('m', $contenedor);
        $anio = date('Y', $contenedor);
        $hora = date('H:i:s', $contenedor);
        $texto = $dia . '-' . $mes. '-' . $anio . ' ' . $hora;
        return $texto;
    }
    static function formatoSoloFecha($fecha)
    {
        $contenedor = strtotime($fecha);
        return date('Y-m-d', $contenedor);
    }

    public static function formatoFechaLineas(string $fecha): string
    {
        return Carbon::parse($fecha)->format('d/m/Y');
    }

    static function nombreMes($mes){
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return $meses[$mes-1];
    }


    public static function getEstado($estado)
    {
        return $estado == 1 ? 'Activo' : 'Inactivo';
    }

    public static function getGenero($genero)
    {
        return $genero == 'F' ? 'Femenino' : 'Masculino';
    }

   
}
