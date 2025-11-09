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
use App\Http\Controllers\MovimentoEstoqueController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Middleware\CheckRole;
use App\Models\Terceirizada;
use App\Models\Pedido;

// ===========================
// PÁGINAS INICIAIS
// ===========================
Route::get('/', function () { return view('welcome'); });
Route::get('/home', function () { return view('home'); });
Route::get('/dashboard', function () { return view('dashboard'); })
    ->middleware(['auth'])->name('dashboard');

// ===========================
// PERFIL
// ===========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===========================
// PEDIDOS - ROTAS ESPECÍFICAS
// ===========================
Route::prefix('pedidos')->middleware('auth')->group(function() {
    // Kanban
    Route::get('/kanban', [PedidoController::class, 'kanban'])->name('pedidos.kanban');
    // Atualização de status via AJAX
    Route::post('/update-status', [PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus');
    // Impressões
    Route::get('/{id}/imprimirviatap', [PedidoController::class, 'imprimirViaTap'])->name('pedidos.imprimirviatap');
    Route::get('/{id}/imprimirviaretirada', [PedidoController::class, 'imprimirViaRetirada'])->name('pedidos.imprimirviaretirada');
    Route::get('/{id}/imprimirviacompleta', [PedidoController::class, 'gerarImpressaoViaCompleta'])->name('pedidos.imprimirviacompleta');
});

// ROTAS RESOURCE (depois das específicas)
Route::resource('pedidos', PedidoController::class);

// Adicionar/remover imagens
Route::post('/pedido/{pedido}/imagens', [FormularioController::class, 'adicionarImagem'])->name('pedido.imagem.store');
Route::delete('/pedido/imagens/{imagem}', [FormularioController::class, 'removerImagem'])->name('pedido.imagem.destroy');

// ===========================
// CLIENTES
// ===========================
Route::resource('clientes', ClienteController::class);
Route::put('/pedido/{pedido}', [PedidoController::class, 'update'])->name('pedido.update');

// ===========================
// ITENS
// ===========================
Route::resource('items', ItemController::class);

// ===========================
// TERCEIRIZADAS
// ===========================
Route::resource('terceirizadas', TerceirizadaController::class);
Route::get('/terceirizadas', function () {
    $terceirizadas = Terceirizada::with(['item', 'pedido.cliente'])->get();
    return view('terceirizadas.index', compact('terceirizadas'));
})->name('terceirizadas.index');
Route::post('/terceirizada', [TerceirizadaController::class, 'store'])->name('terceirizada.store');
Route::delete('/terceirizada/{terceirizada}', [TerceirizadaController::class, 'destroy'])->name('terceirizada.destroy');
Route::get('/get-items/{pedido}', function(Pedido $pedido) {
    return response()->json($pedido->items);
});

// ===========================
// PROFISSIONAIS
// ===========================
Route::resource('profissional', ProfissionalController::class);

// ===========================
// SERVIÇOS
// ===========================
Route::resource('servico', ServicoController::class);

// ===========================
// FORMULÁRIO
// ===========================
Route::get('/formulario', [FormularioController::class, 'index'])->name('formulario.index');
Route::post('/formulario', [FormularioController::class, 'store'])->name('formulario.store');
Route::get('/pedido/{id}/visualizar', [FormularioController::class, 'visualizar'])->name('pedido.visualizar');

// ===========================
// AGENDAMENTOS
// ===========================
Route::resource('agendamentos', AgendamentoController::class);
Route::get('/calendario', [AgendamentoController::class, 'calendario'])->name('agendamentos.calendario');

// ===========================
// PAGAMENTOS
// ===========================
Route::resource('pagamento', PagamentoController::class);
Route::post('/pagamento/{id}/registrar', [PagamentoController::class, 'registrar'])->name('pagamento.registrar');
Route::get('/pagamento/create/{cliente_id?}', [PagamentoController::class, 'create'])->name('pagamento.create');

// ===========================
// MIDDLEWARE ADMIN/GERENTE
// ===========================
Route::middleware(['auth', CheckRole::class . ':admin,gerente'])->group(function () {
    Route::get('/admin-area', function () { return 'Área admin'; });
    Route::get('/producao', [ProducaoController::class, 'index'])->name('producao.index');
    Route::put('/producao/{id}', [ProducaoController::class, 'update'])->name('producao.update');
});

// ===========================
// DESPESAS
// ===========================
Route::middleware(['auth'])->group(function () {
    Route::resource('despesas', DespesaController::class)->except(['show']);
    Route::post('/despesas/{id}/registrar-pagamento', [DespesaController::class, 'registrarPagamento'])->name('despesas.registrar-pagamento');
    Route::put('/parcelas/{parcela}', [DespesaController::class, 'updateParcela'])->name('parcelas.update');
    Route::post('/despesas/{parcela}/registrar-pagamento', [DespesaController::class, 'registrarPagamento'])->name('parcelas.registrarPagamento');
});

// ===========================
// ESTOQUE
// ===========================
Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');
Route::post('/estoque', [EstoqueController::class, 'store'])->name('estoque.store');
Route::post('/estoque/{estoque}/movimento', [MovimentoEstoqueController::class, 'store'])->name('estoque.movimento.store');

// Lista de Compras
Route::middleware(['auth'])->group(function () {
    Route::get('/estoque/listacompra', [ListaCompraController::class, 'index'])->name('estoque.listacompra');
    Route::patch('/estoque/listacompra/{item}', [ListaCompraController::class, 'atualizarSituacao'])->name('estoque.listacompra.atualizarSituacao');
    Route::patch('lista-compras/{id}/arquivar', [ListaCompraController::class, 'arquivar'])->name('estoque.listacompra.arquivar');
    Route::patch('lista-compras/{id}/desarquivar', [ListaCompraController::class, 'desarquivar'])->name('estoque.listacompra.desarquivar');
});

// ===========================
// ORÇAMENTOS
// ===========================
Route::prefix('orcamentos')->name('orcamentos.')->group(function() {
    Route::get('/', [OrcamentoController::class, 'index'])->name('index');
    Route::get('/create', [OrcamentoController::class, 'create'])->name('create');
    Route::post('/store', [OrcamentoController::class, 'store'])->name('store');
    Route::get('/{id}', [OrcamentoController::class, 'show'])->name('show');
    Route::get('/{id}/pdf', [OrcamentoController::class, 'pdf'])->name('pdf');
    Route::post('/{id}/converter', [OrcamentoController::class, 'converter'])->name('converter');
});

// ===========================
// AUTH
// ===========================
require __DIR__.'/auth.php';
