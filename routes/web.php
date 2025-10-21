<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProducaoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\OutrosController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TerceirizadaController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\PesquisarController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ListaCompraController;
use App\Http\Middleware\CheckRole;
use App\Models\Terceirizada;
use App\Http\Controllers\MovimentoEstoqueController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\OrcamentoController;



// Página inicial
Route::get('/', function () { return view('welcome'); });
Route::get('/home', function () { return view('home'); });
Route::get('/dashboard', function () { return view('dashboard'); })
    ->middleware(['auth'])->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Clientes
Route::resource('clientes', ClienteController::class);
Route::put('/pedido/{pedido}', [PedidoController::class, 'update'])->name('pedido.update');

// Pedidos
Route::resource('pedidos', PedidoController::class);
Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
Route::delete('/pedidos/{pedido}/imagens/{imagem}', [PedidoController::class, 'destroyImagem'])->name('pedidos.imagens.destroy');
Route::get('/get-items/{pedido_id}', [TerceirizadaController::class, 'getItems']);
Route::get('/get-items/{pedido}', function(Pedido $pedido) {
    return response()->json($pedido->items);
});

// Items
Route::resource('items', ItemController::class);

// Terceirizadas
Route::resource('terceirizadas', TerceirizadaController::class);
Route::get('/terceirizadas', function () {
    $terceirizadas = Terceirizada::with(['item', 'pedido.cliente'])->get();
    return view('terceirizadas.index', compact('terceirizadas'));
})->name('terceirizadas.index');

// Profissionais
Route::resource('profissional', ProfissionalController::class);

// Serviços
Route::resource('servico', ServicoController::class);

// Formulário
Route::get('/formulario', [FormularioController::class, 'index'])->name('formulario.index');
Route::post('/formulario', [FormularioController::class, 'store'])->name('formulario.store');
Route::get('/pedido/{id}/visualizar', [FormularioController::class, 'visualizar'])->name('pedido.visualizar');

// Impressões de pedidos
Route::get('/pedidos/{id}/imprimirviatap', [PedidoController::class, 'imprimirViaTap'])->name('pedidos.imprimirviatap');
Route::get('/pedidos/{id}/imprimirviaretirada', [PedidoController::class, 'imprimirViaRetirada'])->name('pedidos.imprimirviaretirada');
Route::get('/pedidos/{id}/imprimirviacompleta', [PedidoController::class, 'imprimirViaCompleta'])->name('pedidos.imprimirviacompleta');

// Agendamentos
Route::resource('agendamentos', AgendamentoController::class);
Route::get('/calendario', [AgendamentoController::class, 'calendario'])->name('agendamentos.calendario');
Route::get('/agendamentos/create', [AgendamentoController::class, 'create'])->name('agendamentos.create');

// Pagamentos
Route::resource('pagamento', PagamentoController::class);
Route::post('/pagamento/{id}/registrar', [PagamentoController::class, 'registrar'])->name('pagamento.registrar');
Route::get('/pagamento/create/{cliente_id?}', [PagamentoController::class, 'create'])->name('pagamento.create');

// Atualizações e exclusões
Route::put('/item/{item}', [ItemController::class, 'update'])->name('item.update');
Route::delete('/terceirizada/{terceirizada}', [TerceirizadaController::class, 'destroy'])->name('terceirizada.destroy');
Route::post('/terceirizada', [TerceirizadaController::class, 'store'])->name('terceirizada.store');

// Imagens de pedidos
Route::post('/pedido/{pedido}/imagens', [FormularioController::class, 'adicionarImagem'])->name('pedido.imagem.store');
Route::delete('/pedido/imagens/{imagem}', [FormularioController::class, 'removerImagem'])->name('pedido.imagem.destroy');

// Middleware admin/gerente
Route::middleware(['auth', CheckRole::class . ':admin,gerente'])->group(function () {
    Route::get('/admin-area', function () { return 'Área admin'; });
    Route::get('/producao', [ProducaoController::class, 'index'])->name('producao.index');
    Route::put('/producao/{id}', [ProducaoController::class, 'update'])->name('producao.update');
});

// Despesas
Route::middleware(['auth'])->group(function () {
    Route::resource('despesas', DespesaController::class)->except(['show']);
});
Route::post('/despesas/{id}/registrar-pagamento', [DespesaController::class, 'registrarPagamento'])->name('despesas.registrar-pagamento');

// rota para atualizar parcela (usada pelo modal "Salvar Alterações")
Route::put('/parcelas/{parcela}', [DespesaController::class, 'updateParcela'])->name('parcelas.update');

// rota para registrar pagamento via AJAX (botão "Registrar Pagamento")
Route::post('/despesas/{parcela}/registrar-pagamento', [DespesaController::class, 'registrarPagamento'])->name('parcelas.registrarPagamento');

// Clientes e itens
Route::get('/clientes/{id}/itens', [AgendamentoController::class, 'getItensCliente']);


Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');
Route::post('/estoque', [EstoqueController::class, 'store'])->name('estoque.store');

Route::post('/estoque/{estoque}/movimento', [MovimentoEstoqueController::class, 'store'])->name('estoque.movimento.store');


// Lista de Compras
Route::middleware(['auth'])->group(function () {
    Route::get('/estoque/listacompra', [ListaCompraController::class, 'index'])->name('estoque.listacompra');
    Route::patch('/estoque/listacompra/{item}', [ListaCompraController::class, 'atualizarSituacao'])->name('estoque.listacompra.atualizarSituacao');
});

Route::patch('lista-compras/{id}/arquivar', [ListaCompraController::class, 'arquivar'])
    ->name('estoque.listacompra.arquivar');
Route::patch('lista-compras/{id}/desarquivar', [ListaCompraController::class, 'desarquivar'])
    ->name('estoque.listacompra.desarquivar');

// Orçamentos

Route::prefix('orcamentos')->name('orcamentos.')->group(function() {
    Route::get('/', [OrcamentoController::class, 'index'])->name('index');
    Route::get('/create', [OrcamentoController::class, 'create'])->name('create');
    Route::post('/store', [OrcamentoController::class, 'store'])->name('store');
    Route::get('/{id}', [OrcamentoController::class, 'show'])->name('show');
    Route::get('/{id}/pdf', [OrcamentoController::class, 'pdf'])->name('pdf');
    Route::post('/{id}/converter', [OrcamentoController::class, 'converter'])->name('converter');
});

// Auth
require __DIR__.'/auth.php';
