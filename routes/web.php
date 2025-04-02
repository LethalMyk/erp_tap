<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProducaoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\OutrosController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TerceirizadaController;
use App\Http\Controllers\ProfissionalController;
use App\Models\Terceirizada; // Certifique-se de importar o Model
use App\Http\Controllers\ServicoController;

Route::get('/', function () {
    return view('welcome');
});
// Página inicial
Route::get('/home', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas de Clientes
Route::resource('clientes', ClienteController::class);

// Rotas de Pedidos
Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
Route::resource('pedidos', PedidoController::class);
Route::delete('/pedidos/{pedido}/imagens/{imagem}', [PedidoController::class, 'destroyImagem'])->name('pedidos.imagens.destroy');
Route::get('/get-items/{pedido_id}', [TerceirizadaController::class, 'getItems']);
Route::get('/get-items/{pedido}', function(Pedido $pedido) {
    return response()->json($pedido->items);
});

// Rotas de Items

Route::resource('items', ItemController::class);

// Rotas de Terceirizadas

Route::resource('terceirizadas', TerceirizadaController::class);
Route::get('/terceirizadas', function () {
    $terceirizadas = Terceirizada::with(['item', 'pedido.cliente'])->get();
    return view('terceirizadas.index', compact('terceirizadas'));
})->name('terceirizadas.index');

// Rotas de Profissionais

Route::resource('profissional', ProfissionalController::class);


// Rotas de Servicos

Route::resource('servico', ServicoController::class);

// Outras rotas de produção, agenda, financeiro e outros
Route::get('/producao/controle', [ProducaoController::class, 'index'])->name('producao.controle');
Route::get('/producao/relatorios', [ProducaoController::class, 'relatorios'])->name('producao.relatorios');

Route::get('/agenda/logistica', [AgendaController::class, 'logistica'])->name('agenda.logistica');
Route::get('/agenda/orcamentos', [AgendaController::class, 'orcamentos'])->name('agenda.orcamentos');

Route::get('/financeiro/vencimentos', [FinanceiroController::class, 'vencimentos'])->name('financeiro.vencimentos');
Route::get('/financeiro/consulta', [FinanceiroController::class, 'consulta'])->name('financeiro.consulta');
Route::get('/financeiro/recebimento', [FinanceiroController::class, 'recebimento'])->name('financeiro.recebimento');

Route::get('/outros/imper', [OutrosController::class, 'imper'])->name('outros.imper');
Route::get('/outros/pintura', [OutrosController::class, 'pintura'])->name('outros.pintura');
Route::get('/outros/fabric', [OutrosController::class, 'fabric'])->name('outros.fabric');

Route::post('/itens/store', [ItemController::class, 'store'])->name('items.store');

require __DIR__.'/auth.php';
