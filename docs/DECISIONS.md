# Decisões e questões técnicas

Este arquivo registra apenas escolhas observáveis em configuração/código e distingue explicitamente sua motivação desconhecida. Não foram encontrados registros formais de ADRs; não inferir justificativas como decisões aprovadas.

## Escolhas observadas

| Escolha observada | Evidência | Motivo documentado |
| --- | --- | --- |
| Laravel 12 com PHP `^8.2` | `composer.json`; lock em Laravel 12.3.0 | Não encontrado. |
| Separação em controllers, services, repositories e models | `app/Http/Controllers`, `app/Services`, `app/Repositories`, `app/Models` | O código mostra a estrutura, mas não registra o motivo. |
| Blade com Vite, Tailwind e Alpine no frontend | `resources/views`, `vite.config.js`, `package.json` | Não encontrado. Também há referências CDN a Bootstrap e FullCalendar em views. |
| Pest/PHPUnit para testes | `tests/`, `tests/Pest.php`, `phpunit.xml`, dependências Composer | Não encontrado. |
| SQLite como padrão de conexão se `DB_CONNECTION` não estiver configurado | `config/database.php` | Não encontrado. O banco efetivo do ambiente é desconhecido. |
| Regras de produção protegidas por papéis `admin` e `gerente` | `routes/web.php`, `app/Http/Middleware/CheckRole.php` | Não encontrado além do papel de controle de acesso aparente. |

## Questões em aberto

- Qual banco é o alvo oficial de desenvolvimento e produção? O projeto mantém configurações para vários drivers e o ambiente ativo não foi inspecionado.
- Qual é o papel de `usuarios` em relação a `users`? Qual tabela é a fonte de verdade para identidade e autorização?
- As rotas devem exigir autenticação por padrão? Algumas áreas têm middleware explícito, enquanto outras rotas de negócio estão fora dos grupos autenticados; a intenção não está registrada.
- Referências de imports a `FinanceiroController`, `OutrosController` e `PesquisarController` sem classes correspondentes são rotas antigas, trabalho incompleto ou arquivos ausentes?
- Qual comportamento desejado para rotas duplicadas, como índice de terceirizadas e `pagamento.create`?
- Qual provedor, escopo e prioridade estão aprovados para integrações documentadas (WhatsApp, pagamentos e backup)? Requisitos citam possibilidades, sem decisão de provedor encontrada.
- Quais fluxos e regras de negócio devem ser considerados critérios de aceite? A cobertura automatizada atual não abrange os módulos ERP.
