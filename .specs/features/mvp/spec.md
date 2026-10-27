# CrocheTech MVP: Especificação

## Problema

Artesãos gerenciam receitas de crochê manualmente e fazem cálculos de adaptação (multiplicar/reduzir pontos, repetições e materiais) sem apoio computacional, o que gera erros e perda de tempo. Também não há registro do progresso de cada peça.

## Fora do escopo

| Item | Motivo |
|---|---|
| Compartilhar receitas entre usuários | Cada usuário vê apenas seus dados (RNF07) |
| Upload de imagens / PDF | Não está nos requisitos |
| API REST / app nativo | Web responsivo cobre RNF01 |
| Perfis de administrador | Ator único: Usuário |

---

## Histórias

### P1: Autenticação (RF01) ⭐
Como usuário, quero me cadastrar e entrar no sistema para manter meus dados privados.

1. WHEN o usuário envia nome, e-mail válido e senha (mín. 8, letras e números) + confirmação THEN o sistema SHALL criar a conta com senha em hash (bcrypt) e autenticar/redirecionar ao dashboard. [AUTH-01]
2. WHEN o e-mail já existe THEN SHALL exibir "O campo e-mail já está sendo utilizado." e não criar a conta. [AUTH-02]
3. WHEN o e-mail não tem formato válido (ex.: sem @) THEN SHALL rejeitar com mensagem em português. [AUTH-03]
4. WHEN a senha não atende ao mínimo THEN SHALL exibir mensagem pedindo senha mais forte. [AUTH-04]
5. WHEN login com credenciais inválidas THEN SHALL exibir erro e permanecer no login. [AUTH-05]
6. WHEN usuário não autenticado acessa qualquer rota interna THEN SHALL redirecionar ao login. [AUTH-06]
7. WHEN o usuário edita perfil (nome/e-mail/senha) ou exclui a conta THEN SHALL aplicar (perfil do Breeze traduzido). [AUTH-07]

### P1: Manter receitas originais (RF03) ⭐
1. WHEN acessa Receitas THEN SHALL listar só as receitas do usuário, com filtro por tipo (original/adaptada) e busca por título, paginado (10). [REC-01]
2. WHEN cria receita com título (obrigatório, máx. 150) e descrição (opcional) THEN SHALL salvar com `tipo=original`. [REC-02]
3. WHEN visualiza receita THEN SHALL exibir carreiras em ordem com pontos, repetições, pontos esperados por carreira, materiais e, se adaptada, link para a original e o fator. [REC-03]
4. WHEN edita receita THEN SHALL atualizar título/descrição. [REC-04]
5. WHEN exclui receita com projetos vinculados THEN SHALL pedir confirmação informando a quantidade de projetos e, confirmado, remover receita e dependentes (cascata). Receitas adaptadas derivadas ficam com `receita_original_id` nulo. [REC-05]
6. WHEN tenta acessar receita de outro usuário THEN SHALL responder 403. [REC-06]

### P1: Carreiras, pontos e repetições (RF06, RF07, RF08) ⭐
1. WHEN adiciona carreira a uma receita original THEN SHALL sugerir o próximo número sequencial; número inteiro ≥ 1, único por receita; descrição opcional. [CAR-01]
2. WHEN edita a carreira THEN SHALL permitir adicionar/editar/remover pontos (tipo obrigatório da lista: corrente, ponto baixo, ponto alto, meio ponto alto, ponto baixíssimo, aumento, diminuição, anel mágico, outro; quantidade inteira ≥ 1; instrução opcional). [CAR-02]
3. WHEN edita a carreira THEN SHALL permitir adicionar/editar/remover repetições (quantidade inteira ≥ 1; instrução opcional). [CAR-03]
4. WHEN exclui carreira THEN SHALL remover pontos, repetições e progressos ligados. [CAR-04]
5. WHEN a receita é adaptada THEN SHALL bloquear (403) criação/edição/remoção de carreiras, pontos, repetições e materiais. [CAR-05]

### P1: Materiais (RF05) ⭐
1. WHEN adiciona material a receita original com nome (obrigatório), quantidade (decimal > 0) e unidade (lista: g, m, novelo, unidade, cm) THEN SHALL salvar vinculado à receita. [MAT-01]
2. WHEN edita/exclui material THEN SHALL aplicar. [MAT-02]

### P1: Adaptar receita (RF04, RF09..RF13, RNF03, RNF09) ⭐
1. WHEN abre "Adaptar" de uma receita original THEN SHALL mostrar formulário com fator (decimal, 0,1 a 10) e título sugerido "<título> (×<fator>)". [ADP-01]
2. WHEN fator ≤ 0 ou fora do intervalo ou não numérico THEN SHALL exibir erro e não gerar receita. [ADP-02]
3. WHEN fator válido THEN SHALL criar, em uma transação, nova receita `tipo=adaptada`, `fator_adaptacao=fator`, `receita_original_id=original`, copiando carreiras (mesmo número/descrição); repetições `quantidade = max(1, round(q × fator))`; pontos idem, exceto quando a carreira tem repetição (aí a sequência de pontos é mantida e só a repetição escala) e o anel mágico (nunca escala) e materiais `quantidade = round(q × fator, 2)`; redirecionar à receita adaptada. [ADP-03]
4. WHEN a adaptação termina THEN a receita original SHALL permanecer inalterada. [ADP-04]
5. WHEN a receita é adaptada THEN o botão "Adaptar" SHALL não aparecer e a rota SHALL negar (403). [ADP-05]
6. WHEN visualiza receita adaptada THEN SHALL exibir comparação original × adaptada (pontos esperados por carreira e materiais). [ADP-06]
7. Adaptação de receita com 50 carreiras × 5 pontos SHALL concluir em < 3 s. [ADP-07]

### P1: Projetos e observações (RF02, RF17) ⭐
1. WHEN cria projeto com título (obrigatório) e receita (obrigatória, do próprio usuário, original ou adaptada) THEN SHALL salvar com `status=nao_iniciado`. [PRJ-01]
2. WHEN lista projetos THEN SHALL mostrar título, receita, status, % e barra de progresso; filtro por status. [PRJ-02]
3. WHEN edita projeto THEN SHALL permitir alterar título; a receita só pode mudar se ainda não houver progresso. [PRJ-03]
4. WHEN exclui projeto THEN SHALL pedir confirmação e remover progressos e observações. [PRJ-04]
5. WHEN registra observação (texto obrigatório, máx. 1000) na página do projeto THEN SHALL salvar com data/hora e listar da mais recente para a mais antiga; permitir excluir. [OBS-01]

### P1: Executar receita e acompanhar progresso (RF14, RF15, RF16, RF18) ⭐
1. WHEN abre "Executar" THEN SHALL mostrar a primeira carreira não concluída (ou a indicada em `?carreira=`), com número "Carreira X de N", instruções, pontos, repetições e pontos esperados. [EXE-01]
2. WHEN toca "+1 ponto" / "−1 ponto" THEN SHALL persistir `pontos_feitos` (0..esperado) e exibir "feitos / esperados". [EXE-02]
3. WHEN toca "Concluir carreira" THEN SHALL marcar `concluida=true`, `concluida_em=now()`, recalcular status/percentual e avançar para a próxima carreira; tocar de novo em carreira concluída desfaz. [EXE-03]
4. WHEN toca "Anterior"/"Próxima" THEN SHALL navegar entre carreiras (desabilitado nos limites). [EXE-04]
5. Percentual = carreiras concluídas / total × 100 (1 casa decimal); status: 0 % → nao_iniciado (se nenhum ponto feito) / >0 e <100 → em_andamento / 100 → concluido (preenche `data_conclusao`). [EXE-05]
6. Botões com área de toque ≥ 48 px e texto ≥ 18 px (RNF02/RNF08). [EXE-06]
7. WHEN receita sem carreiras THEN SHALL mostrar aviso com link para cadastrar carreiras. [EXE-07]

### P1: Dashboard (RF19) ⭐
1. WHEN acessa dashboard THEN SHALL mostrar cartões: total de projetos, não iniciados, em andamento, concluídos, média de conclusão (%), total de receitas (originais/adaptadas). [DSH-01]
2. WHEN aplica filtros (status, receita, período de criação de/até) THEN cartões e tabela de projetos SHALL refletir o filtro; filtros mantêm valores na URL (GET). [DSH-02]
3. WHEN não há projetos THEN SHALL mostrar estado vazio com atalho para criar receita/projeto. [DSH-03]

### P2: Relatórios
1. WHEN acessa Relatórios › Projetos com os mesmos filtros do dashboard THEN SHALL gerar tabela (projeto, receita, status, %, criado em, concluído em) + totais, com botão Imprimir (CSS de impressão sem menu). [REL-01]
2. WHEN acessa Relatório de materiais de uma receita THEN SHALL listar materiais e quantidades (lista de compras) imprimível. [REL-02]

### Não funcionais
- RNF01/RNF02/RNF08: layout responsivo (mobile-first Tailwind), fonte base 18 px, contraste AA, labels em todos os campos, foco visível. [NF-01]
- RNF07: senhas em hash; toda consulta escopada por `user_id`; policies para Receita e Projeto; CSRF em todos os formulários. [NF-02]
- Interface e mensagens de validação em pt-BR. [NF-03]

---

## Casos de borda
- Fator 1 gera cópia idêntica (permitido).
- Fator pequeno (0,1) nunca gera quantidade 0 de ponto/repetição (mínimo 1).
- Excluir carreira em receita já em execução recalcula % do projeto na próxima leitura.
- Paginação preserva filtros.

## Rastreabilidade

| ID | História | Tarefa | Status |
|---|---|---|---|
| AUTH-01..07 | Autenticação | T1, T2 | Pending |
| REC-01..06 | Receitas | T3 | Pending |
| CAR-01..05 | Carreiras/pontos/repetições | T4 | Pending |
| MAT-01..02 | Materiais | T4 | Pending |
| ADP-01..07 | Adaptação | T5 | Pending |
| PRJ-01..04, OBS-01 | Projetos | T6 | Pending |
| EXE-01..07 | Execução | T7 | Pending |
| DSH-01..03 | Dashboard | T8 | Pending |
| REL-01..02 | Relatórios | T8 | Pending |
| NF-01..03 | Não funcionais | T1, T2, todos | Pending |

## Critérios de sucesso
- `migrate:fresh --seed` sem erros e usuário demo navega por todas as telas.
- Todas as rotas internas retornam 200 autenticado e 302 → /login sem sessão.
- Adaptação ×2 e ×0,5 da receita demo produz os valores esperados.
