<?php

namespace App\Enums;

enum StatusPagamento: string
{
    case PENDENTE = 'EM ABERTO';
    case PAGO = 'PAGAMENTO REGISTRADO';
}
        
