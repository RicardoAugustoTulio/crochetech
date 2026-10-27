# CrocheTech

**Visão:** Aplicação web para cadastrar, adaptar (multiplicar/reduzir) e acompanhar a execução de receitas de crochê, preservando sempre a receita original.
**Para:** Artesãos de crochê (inclui iniciantes e terceira idade), uso em celular/tablet durante o trabalho manual.
**Resolve:** Receitas hoje ficam em cadernos/PDFs, adaptações são calculadas à mão (erros, retrabalho) e o progresso não é registrado.

## Objetivos

- Usuário cadastra uma receita completa (carreiras, pontos, repetições, materiais) em < 5 min.
- Adaptação com fator gera receita adaptada correta (arredondamento documentado) em < 3 s (RNF09), sem alterar a original (RNF03).
- Execução guiada com contador de pontos e % de conclusão persistidos.
- Dashboard com indicadores filtráveis e relatórios imprimíveis.

## Stack

- Framework: Laravel 12 (PHP 8.4 via Laravel Sail / Docker Compose)
- Banco: MySQL 8 (container Sail)
- Front: Blade + Tailwind CSS (Laravel Breeze, stack blade), Alpine.js (já incluso no Breeze)
- Build front: Vite (`npm run build` no host, Node 20)

## Escopo v1

- RF01..RF19 e RNF01..RNF09 da Entrega Parcial (ver `features/mvp/spec.md`).
- Relatórios imprimíveis (projetos filtrados e lista de materiais de uma receita).

## Fora do escopo

- API pública / app nativo; compartilhamento de receitas entre usuários; upload de imagens; importação de PDF.
- Relatório de testes e manuais técnicos (entregas futuras do TCC).

## Restrições

- Aderência total entre projeto lógico (DER/classes do TCC) e projeto físico: nomes de tabelas/colunas/métodos devem bater com `features/mvp/design.md`.
- Interface 100% em português do Brasil.
- Porta HTTP 8090, MySQL encaminhado em 33070, Vite 5174 (80 e 5173 já ocupadas na máquina).
