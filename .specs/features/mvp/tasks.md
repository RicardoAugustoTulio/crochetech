# CrocheTech MVP: Tarefas

**Design:** `.specs/features/mvp/design.md` · **Spec:** `.specs/features/mvp/spec.md`
**Status:** Approved

## Plano de execução

```
Fase 1 (sequencial):  T1 ──> T2
Fase 2 (paralela):    T2 ──┬─> T3 [P] Receitas
                           ├─> T4 [P] Carreiras/Pontos/Repetições/Materiais
                           ├─> T5 [P] Adaptação
                           ├─> T6 [P] Projetos/Observações
                           ├─> T7 [P] Execução
                           └─> T8 [P] Dashboard/Relatórios
Fase 3 (sequencial):  T3..T8 ──> T9 Seeder ──> T10 Verificação integrada (orquestrador)
```

## Regras para subagentes (todas as tarefas)

- Trabalhe só nos arquivos listados em **Onde**. Não edite `routes/web.php`, models, migrations, policies ou layout (exceto T1/T2). Se precisar, registre `SPEC_DEVIATION` no relatório.
- Não faça `git commit` (o orquestrador commita por fase). Não rode `migrate:fresh`/`db:wipe` na Fase 2.
- Não rode `npm run build` na Fase 2 (orquestrador recompila no fim). Não crie testes automatizados (AD-005).
- `pint` só nos seus arquivos: `./vendor/bin/sail pint <arquivos>`.
- Smoke test: crie seu próprio usuário via tinker com e-mail `tN@teste.local` (N = número da tarefa) e senha `senha1234`.
- Interface 100% pt-BR, acessível (labels, foco, botões ≥ 48 px), reutilize componentes Breeze/T2.

---

### T1: Scaffold Laravel 12 + Sail + Breeze + pt-BR
**O quê:** Criar o projeto em `~/utp/tcc/crochetech` (o diretório já tem `.specs/`; criar em `/tmp/crochetech-new` e mover o conteúdo para dentro preservando `.specs`), via `docker run --rm -u "$(id -u):$(id -g)" -v /tmp:/opt -w /opt laravelsail/php84-composer:latest composer create-project laravel/laravel:^12.0 crochetech-new`. Instalar Sail com MySQL (`php artisan sail:install --with=mysql`), Breeze stack blade (`composer require laravel/breeze --dev` + `php artisan breeze:install blade`). Configurar `.env` conforme design (portas 8090/33070/5174, locale pt_BR, timezone). Subir `sail up -d`, `migrate`. Escrever `lang/pt_BR/*.php` e `lang/pt_BR.json` traduzindo todas as strings das views do Breeze (login, registro, esqueci senha, perfil, navegação). `Password::defaults` em `AppServiceProvider`. `AuthorizesRequests` no Controller base. CSS base (18 px, paleta rose). `npm install && npm run build`. `git init` + `.gitignore` padrão.
**Onde:** projeto inteiro (scaffold).
**Requisitos:** AUTH-01..07, NF-02, NF-03.
**Pronto quando:** `curl -s -o /dev/null -w '%{http_code}' http://localhost:8090/login` = 200; página de login em português; registro com e-mail inválido mostra erro em pt-BR; `route:list` ok.
**Gate:** rotas, front, smoke.

### T2: Esquema, Models, Policies, Rotas, Layout compartilhado
**O quê:** Migrations e Models exatamente como no design (com todos os métodos de domínio), `ReceitaPolicy`, `ProjetoPolicy`, `routes/web.php` completo do design, controllers criados com todos os métodos da rota (corpo `abort(501)` para as tarefas da Fase 2), views placeholder mínimas para cada view do design (só `<x-app-layout>` + título), partials placeholder `receitas/partials/materiais.blade.php` e `receitas/partials/comparacao.blade.php`, navegação com links (Dashboard, Receitas, Projetos, Relatórios), flash messages no layout, `layouts/impressao.blade.php`, componentes `x-select`, `x-barra-progresso`, `x-status-badge`, `x-card-indicador`. `migrate:fresh`.
**Onde:** `database/migrations/*`, `app/Models/*`, `app/Policies/*`, `routes/web.php`, `app/Http/Controllers/*` (stubs), `resources/views/layouts/*`, `resources/views/components/*`, views placeholder.
**Depende de:** T1. **Requisitos:** NF-01, NF-02, base de todos.
**Pronto quando:** `migrate:fresh` ok; `route:list` lista todas as rotas do design; tinker: criar receita com 2 carreiras/pontos/repetições/materiais, `adaptar(2)` e `adaptar(0.5)` retornam valores da regra ADP-03 e a original não muda; `Carreira::pontosEsperados` e `Projeto::calcularProgresso/atualizarStatus` conferidos; `npm run build` ok.
**Gate:** sintaxe, estilo, rotas, banco, front, domínio.

### T3: CRUD de Receitas [P]
**O quê:** `ReceitaController` (index com filtro tipo + busca + paginação 10 com `withQueryString`; create/store; show com carreiras, pontos, repetições, pontos esperados, `@include` dos partials de materiais e comparação, botões Adaptar (só original), Relatório de materiais, Novo projeto com esta receita; edit/update; destroy com confirmação contando projetos). Validação via FormRequest `ReceitaRequest`. Views `receitas/{index,create,edit,show}`.
**Onde:** `app/Http/Controllers/ReceitaController.php`, `app/Http/Requests/ReceitaRequest.php`, `resources/views/receitas/{index,create,edit,show}.blade.php`.
**Requisitos:** REC-01..06.
**Pronto quando:** smoke 200 nas páginas; criar/editar/excluir via curl funciona; receita de outro usuário → 403.

### T4: Carreiras, Pontos, Repetições, Materiais [P]
**O quê:** `CarreiraController` (create com número sugerido, store, edit com CRUD inline de pontos e repetições, update, destroy), `PontoController` e `RepeticaoController` (store/update/destroy, redirect para `carreiras.edit`), `MaterialController` (store/edit/update/destroy, redirect para `receitas.show#materiais`). Autorização `editarEstrutura` (403 em adaptada). Partial `receitas/partials/materiais.blade.php` (lista + formulário inline de novo material, só para original). Views `carreiras/{create,edit}`, `materiais/edit`.
**Onde:** os 4 controllers, FormRequests correspondentes em `app/Http/Requests/`, as views citadas.
**Requisitos:** CAR-01..05, MAT-01..02.
**Pronto quando:** smoke; número duplicado na mesma receita rejeitado; quantidade 0 rejeitada; ações em receita adaptada → 403.

### T5: Adaptação de receita [P]
**O quê:** `AdaptacaoController@create/store` (valida fator `required|numeric|min:0.1|max:10` aceitando vírgula: normalizar `str_replace(',', '.', ...)` antes de validar; título opcional; autoriza `adaptar`; chama `Receita::adaptar`; flash de sucesso com tempo de processamento em ms). View `receitas/adaptar.blade.php` com prévia explicativa da regra de arredondamento. Partial `receitas/partials/comparacao.blade.php`: tabela original × adaptada por carreira (pontos esperados) e por material.
**Onde:** `app/Http/Controllers/AdaptacaoController.php`, `resources/views/receitas/adaptar.blade.php`, `resources/views/receitas/partials/comparacao.blade.php`.
**Requisitos:** ADP-01..07.
**Pronto quando:** fator 0, -1, 11, "abc" → erro; fator 2 e 0,5 geram receitas corretas; original intacta; adaptar adaptada → 403; 50×5 em < 3 s (medir via tinker).

### T6: Projetos e Observações [P]
**O quê:** `ProjetoController` (index com filtro status e barra de progresso; create com `?receita_id=` pré-selecionado; store; show com resumo, %, botão Executar, lista de carreiras com status, observações; edit (receita bloqueada se houver progresso); destroy). `ObservacaoController@store/destroy` usando `Projeto::registrarObservacao`. FormRequests.
**Onde:** `app/Http/Controllers/{ProjetoController,ObservacaoController}.php`, `app/Http/Requests/{ProjetoRequest,ObservacaoRequest}.php`, `resources/views/projetos/{index,create,edit,show}.blade.php`.
**Requisitos:** PRJ-01..04, OBS-01.
**Pronto quando:** smoke; `receita_id` de outro usuário rejeitado; observação vazia rejeitada.

### T7: Execução da receita [P]
**O quê:** `ExecucaoController@show/pontos/concluir` conforme design e EXE-01..07. View `projetos/executar.blade.php` mobile-first: cabeçalho "Carreira X de N", barra de progresso do projeto, instruções, lista de pontos/repetições, contador grande "feitos / esperados", botões grandes −1 / +1 / Concluir carreira (ou Desfazer) / Anterior / Próxima, link voltar ao projeto.
**Onde:** `app/Http/Controllers/ExecucaoController.php`, `resources/views/projetos/executar.blade.php`.
**Requisitos:** EXE-01..07.
**Pronto quando:** smoke; +1/−1 limitados; concluir todas as carreiras → status concluído e data_conclusao; carreira de outra receita → 404.

### T8: Dashboard e Relatórios [P]
**O quê:** `DashboardController@index` (cartões DSH-01, filtros DSH-02 via GET, tabela de projetos com barra de progresso, estado vazio). `RelatorioController@projetos` (mesmos filtros, totais, layout impressão) e `@materiais` (usa `Receita::calcularMateriais`). Extrair a consulta filtrada para um escopo `Projeto::scopeFiltrar($query, array $filtros)`: **exceção autorizada** para editar `app/Models/Projeto.php` só para adicionar esse escopo. Página índice de relatórios não é necessária: link "Relatórios" do menu aponta para `relatorios.projetos`.
**Onde:** `app/Http/Controllers/{DashboardController,RelatorioController}.php`, `app/Models/Projeto.php` (só o escopo), `resources/views/dashboard.blade.php`, `resources/views/relatorios/{projetos,materiais}.blade.php`.
**Requisitos:** DSH-01..03, REL-01..02.
**Pronto quando:** smoke; filtros alteram contagens; impressão sem menu.

### T9: Seeder de demonstração
**O quê:** `DatabaseSeeder` conforme design (usa métodos de domínio, inclusive `adaptar` e `marcarConcluida`).
**Onde:** `database/seeders/DatabaseSeeder.php`.
**Depende de:** T3..T8.
**Pronto quando:** `migrate:fresh --seed` ok; contagens conferidas via tinker; login demo funciona.

### T10: Verificação integrada (orquestrador)
`npm run build`, `pint --test`, `route:list`, `migrate:fresh --seed`, smoke de todas as rotas autenticado/não autenticado, isolamento entre usuários, capturas de tela (Playwright com Chrome do sistema, fora do projeto) para o documento.

## Validação

| Tarefa | Depende (corpo) | Diagrama | Status |
|---|---|---|---|
| T1 | - | - | ✅ |
| T2 | T1 | T1→T2 | ✅ |
| T3..T8 | T2 | T2→T3..T8 | ✅ |
| T9 | T3..T8 | T3..T8→T9 | ✅ |
| T10 | T9 | T9→T10 | ✅ |

Testes automatizados: nenhum por decisão AD-005 (gates de verificação direta no lugar).
