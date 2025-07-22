<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProducaoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\OutrosController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TerceirizadaController;
use App\Http\Controllers\ProfissionalController;
use App\Models\Terceirizada; // Certifique-se de importar o Model
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\PesquisarController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\DespesaController;


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
Route::put('/pedido/{pedido}', [PedidoController::class, 'update'])->name('pedido.update');

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



Route::get('/formulario', [FormularioController::class, 'index'])->name('formulario.index');
Route::post('/formulario', [FormularioController::class, 'store'])->name('formulario.store');



Route::get('/pedido/{id}/visualizar', [FormularioController::class, 'visualizar'])->name('pedido.visualizar');
Route::get('/pedidos/{id}/imprimirviatap', [PedidoController::class, 'imprimirviatap'])->name('pedidos.imprimirviatap');
Route::get('/pedidos/{id}/imprimirviaretirada', [PedidoController::class, 'imprimirviaretirada'])->name('pedidos.imprimirviaretirada');
Route::get('/pedidos/{id}/imprimirviacompleta', [PedidoController::class, 'imprimirviacompleta'])->name('pedidos.imprimirviacompleta');

Route::post('/itens/store', [ItemController::class, 'store'])->name('items.store');




Route::resource('agendamentos', AgendamentoController::class);
Route::get('/calendario', [App\Http\Controllers\AgendamentoController::class, 'calendario'])->name('agendamentos.calendario');
Route::post('/pagamento/{id}/registrar', [\App\Http\Controllers\PagamentoController::class, 'registrar'])->name('pagamento.registrar');
Route::get('/pagamento/create/{cliente_id?}', [PagamentoController::class, 'create'])->name('pagamento.create');
Route::get('/pedido/{id}/visualizar', [FormularioController::class, 'visualizar'])->name('pedido.visualizar');

Route::put('/item/{item}', [ItemController::class, 'update'])->name('item.update');
Route::delete('/terceirizada/{terceirizada}', [TerceirizadaController::class, 'destroy'])->name('terceirizada.destroy');
Route::post('/terceirizada', [TerceirizadaController::class, 'store'])->name('terceirizada.store');



Route::post('/pedido/{pedido}/imagens', [FormularioController::class, 'adicionarImagem'])->name('pedido.imagem.store');
Route::delete('/pedido/imagens/{imagem}', [FormularioController::class, 'removerImagem'])->name('pedido.imagem.destroy');

// Registrar middleware no grupo web (ou globalmente se quiser)
Route::middleware(['auth', CheckRole::class . ':admin,gerente'])->group(function () {
    Route::get('/admin-area', function () {
        return 'Área admin';
    });
});

Route::middleware(['auth', CheckRole::class . ':admin,gerente'])->group(function () {
    // Suas rotas aqui

Route::get('/producao', [ProducaoController::class, 'index'])->name('producao.index');

    // Rotas de Pagamentos

Route::resource('pagamento', PagamentoController::class);
});
Route::put('/producao/{id}', [ProducaoController::class, 'update'])->name('producao.update');
Route::get('/agendamentos/create', [AgendamentoController::class, 'create'])->name('agendamentos.create');



Route::middleware(['auth'])->group(function () {
    Route::resource('despesas', DespesaController::class)->except(['show']);
});


Route::get('/clientes/{id}/itens', [AgendamentoController::class, 'getItensCliente']);
require __DIR__.'/auth.php';
