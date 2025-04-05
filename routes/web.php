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

// Rotas de Pagamentos

Route::resource('pagamento', PagamentoController::class);

Route::get('/formulario', [FormularioController::class, 'index'])->name('formulario.index');
Route::post('/formulario', [FormularioController::class, 'store'])->name('formulario.store');



Route::get('/pedido/{id}/visualizar', [FormularioController::class, 'visualizar'])->name('pedido.visualizar');
Route::get('/pedidos/{id}/imprimirviatap', [PedidoController::class, 'imprimir'])->name('pedidos.imprimirviatap');


Route::post('/itens/store', [ItemController::class, 'store'])->name('items.store');

require __DIR__.'/auth.php';
