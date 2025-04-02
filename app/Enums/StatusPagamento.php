<?php

namespace App\Enums;

enum StatusPagamento: string
{
    case PENDENTE = 'Pendente';
    case PAGO = 'Pago';
    case PARCIAL = 'Parcial';
}
