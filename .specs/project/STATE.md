# State

**Last Updated:** 2026-10-27
**Current Work:** mvp - planejamento concluído, execução por subagentes

## Decisões

### AD-001: Tabela `users` do Laravel em vez de `usuarios`
**Decisão:** Manter `users(name, email, password)` padrão do framework.
**Motivo:** Breeze/autenticação/reset de senha dependem desses nomes; renomear gera muito atrito.
**Impacto:** DER corrigido do TCC passa a mostrar `users`. Tabelas de domínio continuam em português.

### AD-002: Lógica de domínio nos Models (Active Record)
**Decisão:** `Receita::adaptar()`, `Ponto::adaptar()`, `Repeticao::adaptar()`, `Material::calcularProporcional()`, `Projeto::calcularProgresso()` etc. ficam nos Models, sem camada Service.
**Motivo:** Aderência direta ao diagrama de classes (critério da banca) e menos código.

### AD-003: Receita adaptada é somente leitura na estrutura
**Decisão:** Em receitas `tipo=adaptada` só título/descrição são editáveis; carreiras, pontos, repetições e materiais vêm do cálculo. Só receitas originais podem ser adaptadas.
**Motivo:** Spec da Entrega Parcial (materiais de adaptada são calculados, RF13) e consistência.

### AD-004: Progresso por carreira + contador de pontos
**Decisão:** `progressos(projeto_id, carreira_id, pontos_feitos, concluida, concluida_em)`; % = carreiras concluídas / total.
**Motivo:** Corrige inconsistência da parcial (`Carreira.concluida` no diagrama de classes vs. `progressos` no DER).

### AD-005: Sem suíte de testes automatizados nesta entrega
**Decisão:** Verificação via `php -l`, `pint --test`, `route:list`, `migrate:fresh --seed`, `npm run build`, tinker e smoke HTTP com curl. Testes automatizados só se o usuário pedir.
**Motivo:** Regra do usuário (não rodar/criar testes sem pedido explícito). Relatório de testes é entrega futura.

### AD-006: Portas
APP_PORT=8090, FORWARD_DB_PORT=33070, VITE_PORT=5174.

### AD-007: Pontos esperados contam pontos resultantes (2026-10-27, verificação T10)
**Decisão:** `Ponto::pontosGerados()` (aumento = 2, anel mágico = 0, demais = 1). Na adaptação, carreira com repetição escala só a repetição, e o anel mágico não escala.
**Motivo:** Antes os valores contavam instruções: a carreira "(1 pb, 1 aum) × 6" mostrava 12 em vez de 18, e o fator era aplicado duas vezes (pontos × repetição).
**Impacto:** Diagrama de classes do TCC deve incluir `Ponto.pontosGerados()`.

## Bloqueios
(nenhum)

## Lições
(nenhuma ainda)
