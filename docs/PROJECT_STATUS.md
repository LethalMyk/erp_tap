# Estado do projeto

Levantamento estático do código, rotas, migrations, testes e documentos em 26/10/2026. Os testes e o sistema em execução não foram verificados nesta tarefa. “Presente no código” significa que existem rotas/classes/views relacionados, não que o fluxo esteja confirmado em produção.

## Implementação identificada

### Concluídas e verificadas

Nenhuma funcionalidade foi verificada em execução durante este levantamento. Os testes de autenticação e perfil existentes são evidência de cenários automatizados definidos, mas não foram executados nesta tarefa.

### Código presente, conclusão funcional pendente

- **Autenticação e perfil:** rotas de registro, login, logout, verificação de e-mail, recuperação/atualização de senha e perfil estão presentes; existem testes de feature para autenticação e perfil.
- **Clientes e pedidos:** rotas, controllers, models e views de cadastro; pedidos incluem kanban, atualização de status, imagens e vias de impressão.
- **Produção e serviços:** controller/rotas de produção com middleware `CheckRole` para papéis `admin` e `gerente`; existem entidades e telas para profissionais, serviços e terceirizadas.
- **Agendamentos:** rotas CRUD e calendário, model/service e views.
- **Financeiro:** pagamentos, despesas e parcelas têm rotas/controllers/views/services e migrations.
- **Compras e estoque:** produtos, produtos comprados, estoque, movimentos e lista de compras aparecem em migrations, models, serviços e views.
- **Orçamentos:** rotas/controller/service/views para listar, criar, mostrar, PDF e conversão para pedido.

Esses itens foram localizados no código; exceto pelos casos cobertos pelos testes existentes, seu funcionamento não foi validado. Mesmo os testes não foram executados nesta tarefa.

## Parcial ou pendente de validação

- A suíte atual contém testes de exemplo, autenticação e perfil; não localizei cobertura específica dos módulos de ERP descritos acima.
- Não foi verificada a conexão efetiva, existência/estado das tabelas, migrations aplicadas, permissões reais, armazenamento de imagens, fila, e-mail nem build frontend.
- O `README.md` anterior era o texto padrão do Laravel; a rota `/` ainda retorna `welcome` padrão. Não se confirmou uma página inicial dedicada ao ERP.
- `routes/web.php` importa `FinanceiroController`, `OutrosController` e `PesquisarController`, cujos arquivos correspondentes não estão presentes em `app/Http/Controllers`. Também há rotas repetidas para índice de terceirizadas e criação de pagamento. O impacto dessas inconsistências precisa ser confirmado executando a inspeção de rotas/testes em ambiente adequado.
- Migrations e código indicam coexistência de `usuarios` e `users`; não está documentado se há sincronização ou propósito distinto.

## Planejado sem implementação confirmada

Os documentos de requisitos citam notificação de clientes por WhatsApp, integração de pagamentos PIX/boletos e APIs de pagamento. Documentos de infraestrutura também descrevem backup automatizado. Não foram encontradas chamadas de integração de negócio no código para esses itens; tratá-los como não iniciados até validação adicional.

## Problemas conhecidos e verificações pendentes

- Testar a inicialização do sistema e `php artisan route:list`; investigar imports de controllers ausentes e duplicidade de rotas.
- Executar a suíte Pest/PHPUnit após configurar um banco de teste isolado; `tests/Pest.php` usa `RefreshDatabase` em testes de feature e `phpunit.xml` deixa SQLite comentado, portanto confirme o ambiente antes de testar.
- Acrescentar cobertura de testes para regras dos módulos de negócio, especialmente autorização e transições de estado.
- Confirmar banco alvo e compatibilidade das migrations no ambiente de desenvolvimento/implantação, sem executar migração até haver autorização e alvo confirmado.
- Validar fluxo de upload/remoção de imagens, permissões, e-mail/fila, geração de PDF e build dos assets.
- Esclarecer modelo de identidade: tabela `users` usada pelo Laravel e tabela de domínio `usuarios`.

## Próximas tarefas recomendadas

1. Rodar, em ambiente seguro e após confirmar a configuração, `php artisan route:list` e testes existentes; registrar falhas com contexto sem tocar em banco compartilhado.
2. Resolver as referências de controller e rotas duplicadas por decisão do responsável do projeto.
3. Confirmar responsabilidades entre `users` e `usuarios` e documentar o banco realmente adotado.
4. Criar testes de feature/unit para os fluxos ERP prioritários antes de afirmar estabilidade.
5. Atualizar o status conforme funcionalidades forem verificadas em execução, separando código existente de requisitos planejados.
