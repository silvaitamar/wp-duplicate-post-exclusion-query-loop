# Compatibilidade com extensões do Query Loop

Este documento descreve por que o plugin funciona com variações como [Advanced Query Loop](https://wordpress.org/plugins/advanced-query-loop/) (AQL) e como mantemos compatibilidade ao evoluir.

## Princípio de design

O **Silvaitamar Duplicate Post Exclusion for Query Loop** não substitui nem fork o bloco `core/query`. Ele:

1. Adiciona um atributo ao bloco **nativo** (`uniqueOnPage`).
2. Atua nos **hooks oficiais** do ciclo de renderização do Query Loop.
3. Manipula apenas `post__not_in` — argumento padrão da `WP_Query`.

Variações registradas com `registerBlockVariation( 'core/query', … )` — como as do AQL — continuam sendo `core/query` no HTML e no PHP. O atributo `namespace` (ex.: `advanced-query-loop`) não impede nossa lógica.

## Por que funciona com Advanced Query Loop

| Camada | AQL | Este plugin | Resultado |
|--------|-----|-------------|-----------|
| Bloco | Variação de `core/query` + `namespace` | Estende `core/query` via filtros JS | Mesmo bloco base |
| Front-end query | Filtro `query_loop_block_query_vars` (prioridade 10) | Mesmo filtro (prioridade **20**) | AQL aplica regras customizadas; nós mesclamos `post__not_in` **depois** |
| Render | `core/post-template` + contexto `postId` | Captura `postId` em `render_block_context` | IDs registrados independentemente da query AQL |
| Rastreamento | — | `pre_render_block` / `render_block` em `core/query` | Funciona com ou sem `namespace` |

Ordem típica no front-end:

```
pre_render_block (AQL registra filtro de query vars)
  → pre_render_block (SIDPEQL inicia tracking se uniqueOnPage)
    → post-template render
      → query_loop_block_query_vars (AQL: meta, tax, etc.)
      → query_loop_block_query_vars (SIDPEQL: post__not_in)
      → WP_Query
      → render_block_context (SIDPEQL registra postId)
  → render_block (SIDPEQL encerra tracking)
```

## Limitação conhecida: "Herdar consulta do modelo" (`inherit: true`)

Quando um Query Loop usa **"Herdar consulta do modelo"** (`inherit: true`), o `core/post-template` renderiza a partir do `$wp_query` global e **não** chama `build_query_vars_from_query_block()`. Como o filtro `query_loop_block_query_vars` só dispara nesse caminho, a exclusão `post__not_in` **não é aplicada** a um loop herdado.

O rastreamento de IDs, porém, ocorre via `render_block_context` e funciona em qualquer loop. Na prática:

| Situação | Registra posts exibidos | Exclui posts anteriores |
|----------|:----------------------:|:-----------------------:|
| Loop personalizado (não-herdado) | ✅ | ✅ |
| Loop herdado (`inherit: true`) | ✅ | ❌ |

Consequências (confirmadas em template de arquivo):

- **Funciona:** loop herdado primeiro (lista principal) + loop personalizado depois ("veja também"). O loop personalizado remove os duplicados do herdado.
- **Não funciona:** loop personalizado primeiro + loop herdado depois. O loop herdado ainda repete posts já exibidos, pois a exclusão não atua sobre ele.

Suporte completo à exclusão em loops herdados está no roadmap (exigiria atuar em `pre_get_posts` de forma isolada, sem afetar a query principal/paginação).

## Limitação conhecida: preview do editor não deduplica

O preview do editor de blocos e o front-end usam pipelines de renderização diferentes:

| Contexto | Renderização | Hooks do plugin atuam |
|----------|--------------|:---------------------:|
| Front-end | PHP (`WP_Query` + `query_loop_block_query_vars` + `render_block`) | ✅ |
| Editor (preview) | REST API, cada bloco busca posts de forma independente | ❌ |

A exclusão e o registro de IDs vivem em hooks de front-end. No editor, cada Query Loop faz sua própria chamada REST, sem o registro compartilhado por requisição que existe no front-end. Por isso o preview mostra posts duplicados mesmo com o toggle ativo. É o [comportamento documentado](https://developer.wordpress.org/reference/hooks/query_loop_block_query_vars/) do filtro `query_loop_block_query_vars` ("only influence the query that will be rendered on the front-end... the editor preview uses the REST API").

**Status:** limitação aceita (validada). O front-end é a fonte da verdade; o atributo `uniqueOnPage` é salvo e persistido normalmente, sem erros no editor. Replicar a deduplicação no preview exigiria coordenar ordem e estado entre chamadas REST independentes, o que é frágil e fora do escopo.

## Hooks públicos para terceiros

### `sidpeql_query_loop_post__not_in`

Permite que outros plugins ajustem a lista de IDs excluídos antes da merge final.

```php
add_filter( 'sidpeql_query_loop_post__not_in', function ( array $ids, array $query, WP_Block $block ) {
    // Remover ou acrescentar IDs conforme necessário.
    return $ids;
}, 10, 3 );
```

### `sidpeql_should_track_query_block`

Permite opt-out ou condições extras para rastreamento (ex.: integração com page builders).

```php
add_filter( 'sidpeql_should_track_query_block', function ( bool $track, array $parsed_block ) {
    return $track;
}, 10, 2 );
```

## Cenários a validar nos testes

| Cenário | Prioridade | Notas |
|---------|------------|-------|
| Múltiplos loops AQL + `uniqueOnPage` | ✅ Validado | Cenário com variações AQL na mesma página |
| AQL com cache (`enable_caching`) | ✅ Validado | Sem duplicatas após purge |
| Cache de página (full-page cache) | ✅ Validado | HTML já deduplicado é gerado no MISS e servido nos HITs; recalcula após purge |
| AQL `post__not_in` / exclude posts | ✅ Validado | Merge preserva ambas as exclusões |
| `perPage` / taxonomia / meta query | ✅ Validado | Exclusão respeita os filtros do loop |
| `inherit: true` (Herdar consulta) | ⚠️ Limitação | Registra, mas não exclui no loop herdado (ver seção acima) |
| Query Loop nativo (sem variação) | Testar | Deve funcionar igual |
| Paginação / enhanced pagination | V2+ | Fora do MVP |
| REST / editor preview | ✅ Limitação aceita | Exclusão só no front-end (ver seção abaixo) |

> **Cache de página:** a deduplicação roda no PHP em tempo de render, então o HTML que vai para o cache já está deduplicado. Os HITs servem esse mesmo HTML; após `purge` (ou publicação de conteúdo), o próximo MISS recalcula. **Cache de fragmento / ESI** (cada loop renderizado em sub-requisição isolada) compartilharia o registry entre fragmentos e **não é suportado nem testado**.

## Diretrizes para evolução

1. **Nunca** registrar um bloco `core/query` alternativo.
2. **Preferir** filtros oficiais (`query_loop_block_query_vars`, `render_block`, `render_block_context`).
3. **Prioridade 20+** em `query_loop_block_query_vars` para rodar após extensões comuns (10).
4. **Mesclar**, nunca sobrescrever, arrays como `post__not_in`, `tax_query`, `meta_query`.
5. **Documentar** integrações testadas neste arquivo e no changelog.
6. **Evitar** output buffering, `pre_get_posts` global ou hijack de `$wp_query`, salvo solução isolada e documentada (ex.: inherit).

## Plugins relacionados (roadmap de testes)

- [x] Advanced Query Loop
- [ ] Query Loop nativo (sem variação)
- [ ] Outras variações `core/query`

Relatos de compatibilidade podem ser abertos em [Issues](https://github.com/silvaitamar/wp-duplicate-post-exclusion-query-loop/issues).
