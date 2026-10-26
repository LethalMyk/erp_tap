# Arquitetura observada

Este documento descreve a estrutura presente no repositório. Foi produzido por inspeção estática de arquivos; não representa validação operacional ou inspeção do banco em execução.

## Visão geral

Aplicação monolítica web em Laravel 12.3.0/PHP 8.2. As rotas HTTP estão em `routes/`; controllers recebem requisições e retornam views/respostas; código de domínio passa por services e repositories; Eloquent models representam entidades persistidas; views são Blade. Vite compila os recursos de `resources/`.

## Pastas principais

| Pasta | Responsabilidade observada |
| --- | --- |
| `app/Http/Controllers` | Endpoints, validação/coordenção da requisição, respostas e views. Inclui controllers de autenticação. |
| `app/Http/Middleware` | Middleware de proxy e `CheckRole` para restrição por papel. |
| `app/Models` | Models Eloquent para entidades do negócio e usuário. |
| `app/Services` | Operações de domínio e coordenação de persistência, por exemplo pedidos, estoque, despesas e orçamentos. |
| `app/Repositories` | Consultas e persistência usadas por serviços. |
| `app/Enums` | Enum `StatusPagamento`. |
| `routes` | Rotas web, autenticação e console. `routes/web.php` registra os módulos web. |
| `resources/views` | Templates Blade para autenticação, layout e áreas de negócio. |
| `resources/css`, `resources/js` | Entradas frontend para Vite. |
| `database/migrations` | Histórico de criação e evolução das tabelas. |
| `database/factories`, `database/seeders` | Factory de usuário e seeders de usuário/profissional/valores base. |
| `tests` | Testes Pest/PHPUnit unitários e de feature. |
| `config`, `bootstrap` | Configuração Laravel e inicialização. |
| `ERP Doc` | Requisitos, processos e documentos de planejamento do ERP. |

## Módulos e pontos de entrada

`routes/web.php` contém rotas para autenticação/perfil, clientes, pedidos e kanban, itens, terceirizadas, profissionais, serviços, formulário de pedido/imagens, agendamentos/calendário, pagamentos, produção, despesas/parcelas, estoque/movimentos/lista de compras e orçamentos/PDF/conversão para pedido. As views correspondentes estão em `resources/views`.

Entre os serviços observados estão `PedidoService`, `ClienteService`, `AgendamentoService`, `OrcamentoService`, `PagamentoService`, `DespesaService`, `EstoqueService`, `MovimentoEstoqueService` e `ListaCompraService`. A lista completa está em `app/Services` e `app/Repositories`.

## Persistência e relações

O esquema descrito pelas migrations inclui tabelas Laravel (`users`, `cache`, `jobs`) e tabelas de domínio: `usuarios`, `clientes`, `pedidos`, `items`, `pedido_imagens`, `terceirizadas`, `arptech`, `profissional`, `servicos`, `pagamentos`, `agendamentos`, `despesas`, `produtos`, `produtos_comprados`, `estoque`, `lista_compras`, `orcamentos`, `itens_orcamento`, `valores_base` e `movimento_estoque`.

Relações Eloquent identificadas incluem:

- Pedido pertence a cliente e profissional; possui itens, imagens, pagamentos, serviços, terceirizadas e agendamentos.
- Item pertence a pedido e possui terceirizadas.
- Agendamento pertence a pedido; pagamento pertence a pedido.
- Serviço pertence a profissional e pedido.
- Orçamento possui itens (`ItemOrcamento`), que pertencem ao orçamento.
- Estoque pertence a produto e possui movimentos; movimento pertence a estoque e usuário.
- Produto possui compras (`ProdutoComprado`) e estoque; compra pertence a produto e despesa.
- Despesa possui parcelas, compras de produtos e imagens; parcela pertence à despesa.
- Lista de compras pode pertencer a pedido e estoque.

Há foreign keys explícitas em parte das migrations, mas nem todo relacionamento Eloquent implica constraint. A migração `usuarios` e a tabela de autenticação `users` coexistem; sua relação funcional ainda precisa ser esclarecida. O arquivo `database/.gitignore` existe, mas o estado real de migrations aplicadas e os dados atuais do banco não foram consultados. Não inferir esquema implantado somente pelo histórico de arquivos.

`config/database.php` suporta SQLite, MySQL, MariaDB, PostgreSQL e SQL Server. SQLite é o padrão do código quando a variável de ambiente não está definida. A conexão usada localmente ou em produção é desconhecida.

## Integrações

**Observadas no código:** armazenamento local público é usado pelo `PedidoService` ao remover imagem. Templates referenciam recursos CDN (Bootstrap, FullCalendar, Alpine.js e fontes); isso é dependência de carregamento do frontend, não integração de negócio. Configuração Laravel contém opções genéricas para serviços como AWS/S3, sem comprovação de uso efetivo pela aplicação.

**Planejadas em documentos, sem implementação de negócio localizada:** avisos WhatsApp, pagamentos via PIX/boletos e API de pagamentos (os requisitos citam provedores como exemplo), backup externo/automatizado e comunicação com APIs externas. Consultar `ERP Doc/Requisitos Funcionais v.1.1.txt`, `ERP Doc/requesitos funcionais` e `ERP Doc/Infraestrutura para uso.txt`. A presença em requisitos não significa integração implementada.
