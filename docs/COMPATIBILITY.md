# Compatibilidade com extensões do Query Loop

Este documento descreve por que o plugin funciona com variações como [Advanced Query Loop](https://wordpress.org/plugins/advanced-query-loop/) (AQL) e como mantemos compatibilidade ao evoluir.

## Princípio de design

O **Unique Query Loop Extension** não substitui nem fork o bloco `core/query`. Ele:

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
  → pre_render_block (UQLE inicia tracking se uniqueOnPage)
    → post-template render
      → query_loop_block_query_vars (AQL: meta, tax, etc.)
      → query_loop_block_query_vars (UQLE: post__not_in)
      → WP_Query
      → render_block_context (UQLE registra postId)
  → render_block (UQLE encerra tracking)
```

## Hooks públicos para terceiros

### `uqle_query_loop_post__not_in`

Permite que outros plugins ajustem a lista de IDs excluídos antes da merge final.

```php
add_filter( 'uqle_query_loop_post__not_in', function ( array $ids, array $query, WP_Block $block ) {
    // Remover ou acrescentar IDs conforme necessário.
    return $ids;
}, 10, 3 );
```

### `uqle_should_track_query_block`

Permite opt-out ou condições extras para rastreamento (ex.: integração com page builders).

```php
add_filter( 'uqle_should_track_query_block', function ( bool $track, array $parsed_block ) {
    return $track;
}, 10, 2 );
```

## Cenários a validar nos testes

| Cenário | Prioridade | Notas |
|---------|------------|-------|
| Múltiplos loops AQL + `uniqueOnPage` | ✅ Validado | Cenário com variações AQL na mesma página |
| AQL com `inherit: true` | ⚠️ Testar | AQL pode substituir `$wp_query`; exclusão via `query_loop_block_query_vars` pode não aplicar — candidato a v1.1 |
| AQL com cache (`enable_caching`) | ⚠️ Testar | Transients podem servir resultados sem `post__not_in` atualizado |
| Query Loop nativo (sem namespace) | Testar | Deve funcionar igual |
| AQL `post__not_in` / exclude posts | Testar | Merge deve preservar ambas exclusões |
| Paginação / enhanced pagination | V2+ | Fora do MVP |
| REST / editor preview | Conhecido | Exclusão só no front-end |

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

Relatos de compatibilidade podem ser abertos em [Issues](https://github.com/silvaitamar/wp-unique-query-loop-extension/issues).
