<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProducaoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\OutrosController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PesquisarController;


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


Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');


Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
Route::get('/pedidos/pesquisar', [PedidoController::class, 'pesquisar'])->name('pedidos.pesquisar');


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



Route::get('/pedidos/{pedido_id}/pagamento', [PagamentoController::class, 'showForm'])->name('pagamento.form');
Route::post('/pedidos/{pedido_id}/pagamento', [PagamentoController::class, 'store'])->name('pagamento.store');
// Rota para exibir detalhes do pedido

Route::get('pedidos/{pedido_id}/detalhes', [PedidoController::class, 'detalhes'])->name('pedidos.detalhes');


require __DIR__.'/auth.php';
