# ERP Tap

Aplicação web Laravel para apoiar a operação de uma tapeçaria. O código existente cobre cadastros e fluxos de clientes, pedidos, produção, agenda, pagamentos, compras, estoque, despesas, orçamentos, profissionais, serviços e terceirizadas. O estado de implementação e as limitações conhecidas estão em [docs/PROJECT_STATUS.md](docs/PROJECT_STATUS.md).

## Tecnologias identificadas

- PHP 8.2 (ambiente observado: 8.2.12) e Laravel 12.3.0, conforme `composer.json`, `composer.lock` e `php artisan --version`.
- Composer 2.9.5 no ambiente observado.
- Node.js 24.13.0 e npm 11.6.2 no ambiente observado.
- Vite 6.2.3, Tailwind CSS 3.4.17, Alpine.js 3.14.9 e Laravel Vite Plugin 1.2.0, conforme `package-lock.json`.
- Pest 3.7.4 e plugin Laravel 3.1.0, PHPUnit configurado por `phpunit.xml`.
- Banco suportado depende do ambiente. `config/database.php` usa SQLite como padrão quando `DB_CONNECTION` não é definido e também configura MySQL, MariaDB, PostgreSQL e SQL Server. O banco atualmente apontado pelo `.env` não foi consultado.

## Pré-requisitos

- PHP 8.2 ou compatível com `composer.json`, com extensões exigidas pelo Laravel e pelo driver de banco escolhido.
- Composer, Node.js e npm.
- Um banco configurado para o ambiente local. SQLite é o padrão da configuração, mas confirme `DB_CONNECTION`/`DB_DATABASE` localmente antes de executar comandos de banco.

## Instalação e execução

1. Instale as dependências: `composer install` e `npm ci`.
2. Crie sua configuração local a partir de `.env.example` (`Copy-Item .env.example .env` no PowerShell; `cp .env.example .env` em shells POSIX) e preencha-a localmente. Nunca compartilhe `.env` nem copie valores secretos para a documentação.
3. Gere a chave da aplicação com `php artisan key:generate`.
4. Configure e confirme o banco antes de aplicar migrations. Para preparar um banco local novo, o comando Laravel usual é `php artisan migrate`; não o execute contra ambientes compartilhados ou dados existentes sem revisão e backup.
5. Em um terminal, execute `composer run dev` para iniciar os processos definidos pelo projeto (servidor Laravel, fila, logs e Vite), ou use `php artisan serve` e `npm run dev` em terminais separados.

Para compilar os assets para produção local: `npm run build`.

## Comandos úteis

- `php artisan route:list` — inspecionar rotas.
- `php artisan test` — executar testes Pest/PHPUnit.
- `vendor/bin/pint --test` — verificar formatação PHP; `vendor/bin/pint` aplica correções.
- `npm run dev` — servidor de desenvolvimento Vite.
- `npm run build` — build dos assets.

Os testes não foram executados durante a preparação desta documentação. A suíte existente concentra-se nos fluxos padrão de autenticação e perfil; consulte o status antes de interpretar sua cobertura.

## Estado atual

Há implementação Laravel organizada em controllers, services, repositories, models, views Blade, rotas e migrations. Isso confirma a presença de código, não a validação em execução de cada fluxo. A rota `/` ainda entrega a view de boas-vindas padrão do Laravel; os módulos do ERP estão em rotas separadas. Consulte [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md), [docs/PROJECT_STATUS.md](docs/PROJECT_STATUS.md) e [docs/DECISIONS.md](docs/DECISIONS.md). Instruções para agentes estão em [AGENTS.md](AGENTS.md).
