# Instruções para agentes

## Objetivo e contexto

Este repositório contém o ERP Tap, uma aplicação Laravel para uma tapeçaria. Antes de alterar código, leia `README.md`, `docs/ARCHITECTURE.md`, `docs/PROJECT_STATUS.md` e `docs/DECISIONS.md`, além destas instruções. A arquitetura e o estado foram levantados estaticamente; não presuma que cada fluxo foi validado em execução.

## Stack identificada

- PHP 8.2 e Laravel 12.3.0 (`composer.json`/`composer.lock`).
- Eloquent, migrations e Blade.
- Organização existente em `app/Http/Controllers`, `app/Services`, `app/Repositories` e `app/Models`.
- Vite 6.2.3, Tailwind CSS 3.4.17, Alpine.js 3.14.9 e Laravel Vite Plugin 1.2.0 (`package-lock.json`).
- Pest 3.7.4 / PHPUnit (`tests/`, `phpunit.xml`).
- O banco ativo depende do ambiente. Não leia nem divulgue segredos de `.env`; a configuração de fallback está em `config/database.php`.

## Padrões de trabalho

- Preserve o desenho atual e os nomes/contratos existentes. Controllers coordenam requisições; serviços e repositories encapsulam operações conforme os padrões já presentes. Confira os arquivos vizinhos antes de introduzir uma abstração.
- Faça mudanças pequenas, relacionadas ao pedido. Não reformate arquivos inteiros, não renomeie em massa, não altere comportamento não relacionado e não substitua implementações por scaffolding.
- Antes de editar, verifique `git status` e diffs. Não descarte alterações preexistentes nem inclua arquivos locais, segredos ou dados reais no commit.
- Não invente requisitos, integrações ou razões de decisões. Registre lacunas como desconhecidas e atualize a documentação quando uma mudança aprovada alterar o comportamento real.
- Siga PSR-4/namespace existente, convenções Laravel e o estilo do arquivo próximo. Use `vendor/bin/pint --test` para checagem de estilo PHP; aplique Pint apenas aos arquivos alterados quando necessário.

## Banco de dados e segurança

- Nunca execute migrations, seeders, comandos de limpeza, reset/refresh ou alterações de esquema/dados sem solicitação explícita e confirmação do alvo e do impacto.
- Trate migrations existentes como histórico compartilhado. Não as reescreva para corrigir estado já aplicado; proponha uma nova migration e descreva impacto quando a alteração de esquema fizer parte do pedido.
- Valide entradas no servidor, use autorização/middleware apropriados e evite exposição de dados pessoais. Não adicione credenciais, tokens, chaves, dados reais ou dumps à documentação, logs, fixtures ou respostas.
- Não presuma qual banco o `.env` aponta. Confira configuração local sem exibir valores sensíveis antes de qualquer operação de banco autorizada.

## Testes e revisão

- Para mudanças PHP, execute testes direcionados e, quando viável, `php artisan test`; para assets, `npm run build`. Não afirme sucesso sem executar e observar o resultado.
- Os testes atuais não cobrem todos os módulos do ERP. Acrescente ou atualize testes relevantes ao comportamento modificado, sem ampliar o escopo.
- Revise o diff final e procure efeitos em rotas, autorização, relacionamentos, migrations e views. Informe comandos executados, resultados, limitações e riscos observáveis.
- Nesta tarefa de documentação, não foi executado teste, build, migration ou comando que altere dados.

## Documentação compatível

Consulte documentação oficial correspondente às versões instaladas, não apenas tutoriais atuais: Laravel 12 (`https://laravel.com/docs/12.x`), PHP 8.2 (`https://www.php.net/releases/8.2/en.php`), Vite 6 (`https://vite.dev/guide/`), Tailwind CSS 3.4 (`https://v3.tailwindcss.com/docs`) e Pest 3 (`https://pestphp.com/docs/`). Use `composer.lock` e `package-lock.json` como referência de versões exatas instaladas; o `composer.json` e `package.json` declaram faixas, não necessariamente versões efetivamente instaladas.
