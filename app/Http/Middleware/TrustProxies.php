<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    // Confia em todos os proxies, para pegar o protocolo correto
    protected $proxies = '*';

    // Define quais headers o Laravel vai usar para identificar a requisição original
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
