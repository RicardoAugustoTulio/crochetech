# CrocheTech MVP: Design

**Spec:** `.specs/features/mvp/spec.md`

## Arquitetura

MVC do Laravel. View (Blade) → Controller (valida via FormRequest ou `$request->validate`, autoriza via Policy) → Model Eloquent (regras de domínio) → MySQL. Sem camada Service (AD-002).

```
Navegador ─HTTP─> routes/web.php ─> middleware auth ─> Controller ─> Model ─> MySQL
                                           │                └─ Policy (dono)
                                           └─> view Blade (layouts/app + componentes Breeze)
```

## Ambiente

- Projeto em `~/utp/tcc/crochetech` (git). Sail: serviços `laravel.test` + `mysql` apenas.
- `.env`: `APP_NAME=CrocheTech`, `APP_LOCALE=pt_BR`, `APP_FALLBACK_LOCALE=pt_BR`, `APP_FAKER_LOCALE=pt_BR`, `APP_URL=http://localhost:8090`, `APP_PORT=8090`, `FORWARD_DB_PORT=33070`, `VITE_PORT=5174`, `DB_CONNECTION=mysql`, `DB_HOST=mysql`, `DB_DATABASE=crochetech`, `SESSION_DRIVER=database`. `config/app.php` timezone `America/Sao_Paulo`.
- Comandos: `./vendor/bin/sail up -d`, `./vendor/bin/sail artisan ...`, `npm run build` (host).
- Tradução: `lang/pt_BR/{auth,pagination,passwords,validation}.php` escritos à mão (sem pacote extra) + `lang/pt_BR.json` para strings do Breeze. Atributos em `validation.php` → `attributes` (titulo→título, email→e-mail, password→senha, fator→fator de adaptação, quantidade, unidade, numero→número, texto, descricao→descrição, instrucao→instrução, receita_id→receita, name→nome).
- Senha: `Password::defaults(fn () => Password::min(8)->letters()->numbers())` em `AppServiceProvider::boot`.
- `app/Http/Controllers/Controller.php` usa `AuthorizesRequests` (Laravel 12 não traz por padrão).

## Esquema físico (migrations)

Todas as FKs com `constrained()`; `timestamps()` em todas.

| Tabela | Colunas |
|---|---|
| users | padrão Laravel (id, name, email UQ, email_verified_at, password, remember_token) |
| receitas | id; user_id FK users cascadeOnDelete; receita_original_id FK receitas nullable nullOnDelete; titulo varchar(150); descricao text null; tipo enum('original','adaptada') default 'original'; fator_adaptacao decimal(5,2) null |
| carreiras | id; receita_id FK cascade; numero unsignedInteger; descricao text null; UNIQUE(receita_id, numero) |
| pontos | id; carreira_id FK cascade; tipo varchar(50); quantidade unsignedInteger; instrucao text null |
| repeticoes | id; carreira_id FK cascade; quantidade unsignedInteger; instrucao text null |
| materiais | id; receita_id FK cascade; nome varchar(100); quantidade decimal(10,2); unidade varchar(20) |
| projetos | id; user_id FK cascade; receita_id FK cascade; titulo varchar(150); status enum('nao_iniciado','em_andamento','concluido') default 'nao_iniciado'; data_conclusao timestamp null |
| progressos | id; projeto_id FK cascade; carreira_id FK cascade; pontos_feitos unsignedInteger default 0; concluida boolean default false; concluida_em timestamp null; UNIQUE(projeto_id, carreira_id) |
| observacoes | id; projeto_id FK cascade; texto text |

## Models (app/Models) e métodos de domínio

Nomes de tabela explícitos (`protected $table`) onde a pluralização inglesa falharia: `Material`→`materiais`, `Repeticao`→`repeticoes`, `Observacao`→`observacoes`, `Progresso`→`progressos`. Usar `$fillable`, `casts()`.

- **User**: `receitas()` hasMany, `projetos()` hasMany.
- **Receita**: `usuario()` belongsTo User (FK user_id); `original()` belongsTo Receita (receita_original_id); `adaptacoes()` hasMany Receita; `carreiras()` hasMany orderBy numero; `materiais()` hasMany; `projetos()` hasMany. Constantes `TIPO_ORIGINAL`, `TIPO_ADAPTADA`. `isAdaptada(): bool`. `proximoNumeroCarreira(): int`. **`adaptar(float $fator, ?string $titulo = null): Receita`** (DB::transaction; cria cópia adaptada usando `Ponto::adaptar`, `Repeticao::adaptar`, `Material::calcularProporcional`; eager-load para evitar N+1). **`calcularMateriais(): Collection`** (materiais da receita; usado no relatório). `totalPontosEsperados(): int` (soma das carreiras). Casts: fator_adaptacao `decimal:2`.
- **Carreira**: `receita()`, `pontos()`, `repeticoes()`, `progressos()`. **`pontosEsperados(): int`** = soma(ponto.pontosGerados()) × (soma(repeticoes.quantidade) se houver repetições, senão 1).
- **Ponto**: `carreira()`. Const `TIPOS` (lista da spec CAR-02). **`adaptar(float $fator): int`** = `max(1, (int) round($this->quantidade * $fator))`. Anel mágico não escala. **`pontosGerados(): int`** = quantidade × peso (aumento 2, anel mágico 0, demais 1).
- **Repeticao**: `carreira()`. **`adaptar(float $fator): int`** idem.
- **Material**: `receita()`. Const `UNIDADES = ['g','m','novelo','unidade','cm']`. **`calcularProporcional(float $fator): float`** = `round($this->quantidade * $fator, 2)`.
- **Projeto**: `usuario()` belongsTo User; `receita()`; `progressos()`; `observacoes()` latest. Consts `STATUS` => rótulos ('nao_iniciado'=>'Não iniciado','em_andamento'=>'Em andamento','concluido'=>'Concluído'). **`calcularProgresso(): float`** (0..100, 1 casa). **`atualizarStatus(): void`** (regra EXE-05). **`registrarObservacao(string $texto): Observacao`**. `progressoDa(Carreira $c): Progresso` (firstOrCreate). Casts data_conclusao datetime.
- **Progresso**: `projeto()`, `carreira()`. **`marcarConcluida(): void`** (toggle concluida/concluida_em e chama `projeto->atualizarStatus()`). **`registrarPontos(int $delta): void`** (limita 0..pontosEsperados; chama atualizarStatus). Cast concluida boolean, concluida_em datetime.
- **Observacao**: `projeto()`.

## Policies (app/Policies)

- `ReceitaPolicy`: view/update/delete ⇔ `$receita->user_id === $user->id`; `adaptar` ⇔ dono && original; `editarEstrutura` ⇔ dono && original.
- `ProjetoPolicy`: view/update/delete ⇔ dono.
- Controllers aninhados autorizam pelo pai (`$this->authorize('editarEstrutura', $carreira->receita)`).
- Validação de `receita_id` em projeto: `Rule::exists('receitas','id')->where('user_id', auth()->id())`.

## Rotas (routes/web.php, todas em `middleware(['auth'])` exceto auth do Breeze)

```php
Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('receitas', ReceitaController::class);
Route::get('receitas/{receita}/adaptar', [AdaptacaoController::class, 'create'])->name('receitas.adaptar.create');
Route::post('receitas/{receita}/adaptar', [AdaptacaoController::class, 'store'])->name('receitas.adaptar.store');
Route::resource('receitas.carreiras', CarreiraController::class)->except(['index','show'])->shallow();
Route::resource('carreiras.pontos', PontoController::class)->only(['store','update','destroy'])->shallow();
Route::resource('carreiras.repeticoes', RepeticaoController::class)->only(['store','update','destroy'])->shallow()->parameters(['repeticoes' => 'repeticao']);
Route::resource('receitas.materiais', MaterialController::class)->only(['store','edit','update','destroy'])->shallow()->parameters(['materiais' => 'material']);

Route::resource('projetos', ProjetoController::class);
Route::resource('projetos.observacoes', ObservacaoController::class)->only(['store','destroy'])->shallow()->parameters(['observacoes' => 'observacao']);
Route::get('projetos/{projeto}/executar', [ExecucaoController::class, 'show'])->name('projetos.executar');
Route::post('projetos/{projeto}/carreiras/{carreira}/pontos', [ExecucaoController::class, 'pontos'])->name('projetos.executar.pontos');   // body: delta=1|-1
Route::post('projetos/{projeto}/carreiras/{carreira}/concluir', [ExecucaoController::class, 'concluir'])->name('projetos.executar.concluir');

Route::get('relatorios/projetos', [RelatorioController::class, 'projetos'])->name('relatorios.projetos');
Route::get('relatorios/receitas/{receita}/materiais', [RelatorioController::class, 'materiais'])->name('relatorios.materiais');
// + rotas de profile do Breeze
```

Execução: `{carreira}` deve pertencer à receita do projeto (senão 404). Ações de execução são POST com redirect de volta para `projetos.executar?carreira={numero}` (sem JS obrigatório; Alpine opcional).

## Views (resources/views)

- `layouts/app.blade.php` (Breeze) + `layouts/navigation.blade.php` com links: Dashboard, Receitas, Projetos, Relatórios (desktop e menu mobile). Mensagem flash `session('sucesso')` / `session('erro')` exibida no layout com `role="status"`.
- `components/`: reutilizar componentes do Breeze (`x-primary-button`, `x-secondary-button`, `x-danger-button`, `x-text-input`, `x-input-label`, `x-input-error`). Criar só: `x-select` (select estilizado), `x-barra-progresso :valor`, `x-status-badge :status`, `x-card-indicador :titulo :valor`.
- Pastas: `receitas/{index,create,edit,show}`, `receitas/adaptar.blade.php`, `carreiras/{create,edit}` (edit contém CRUD inline de pontos e repetições), `materiais/edit` (cadastro inline no `receitas/show`), `projetos/{index,create,edit,show}` (show contém observações), `projetos/executar.blade.php`, `dashboard.blade.php`, `relatorios/{projetos,materiais}.blade.php` (layout próprio de impressão `layouts/impressao.blade.php` com botão Imprimir `onclick="window.print()"` e `@media print` ocultando o botão).
- Exclusões: `<form method=POST>@method('DELETE')` com `onsubmit="return confirm('...')"`.

## UI / acessibilidade (RNF01, RNF02, RNF08)

- `resources/css/app.css`: `html { font-size: 112.5%; }` (18 px base). Botões principais `min-h-12 px-5 text-lg`. Paleta: primária `rose-700` (contraste AA sobre branco), texto `gray-900`, fundo `rose-50`/`white`.
- Todo input com `<x-input-label for>` e `x-input-error`. `aria-label` em botões só-ícone. Tabelas viram cards em telas < md (ou `overflow-x-auto`).
- Textos 100% pt-BR; datas `d/m/Y H:i`; números com vírgula (`number_format($v, 2, ',', '.')`).

## Seeder (database/seeders/DatabaseSeeder.php)

Usuário demo `Maria Artesã` / `demo@crochetech.com.br` / `croche2026`. Receitas originais realistas: "Bolinha amigurumi" (8 carreiras: anel mágico 6 pb; 6 aum; (1 pb, 1 aum)×6 ...; materiais: fio algodão 20 g, enchimento 10 g, agulha 2,5 mm 1 unidade), "Tapete redondo de barbante" (6+ carreiras, barbante 1 novelo), "Porta-copos" (4 carreiras). Adaptada: tapete via `Receita::adaptar(1.5)`. Projetos: "Bolinha para a sobrinha" concluído (data_conclusao há 5 dias), "Tapete da sala" em andamento (3 carreiras concluídas, pontos parciais na 4ª, 2 observações), "Porta-copos kit 4" não iniciado; `created_at` espalhados nos últimos 60 dias. Segundo usuário `outro@crochetech.com.br` com 1 receita (para verificar isolamento).

## Gates de verificação (sem suíte de testes, AD-005)

| Gate | Comando |
|---|---|
| sintaxe | `./vendor/bin/sail php -l <arquivo>` para cada PHP alterado |
| estilo | `./vendor/bin/sail pint --test` (ou `pint` para corrigir) |
| rotas | `./vendor/bin/sail artisan route:list --except-vendor` sem erro |
| banco | `./vendor/bin/sail artisan migrate:fresh --seed` sem erro |
| front | `npm run build` sem erro |
| smoke | login via curl (cookie jar + `_token`) e `curl -o /dev/null -w '%{http_code}'` nas páginas da tarefa → 200; sem sessão → 302 |
| domínio | `./vendor/bin/sail artisan tinker --execute='...'` para conferir cálculos |
