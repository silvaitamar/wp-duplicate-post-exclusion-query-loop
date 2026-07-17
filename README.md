# Duplicate Post Exclusion for Query Loop Block

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Live Preview](https://img.shields.io/badge/Live%20Preview-WordPress%20Playground-0073aa.svg)](https://wordpress.org/plugins/silvaitamar-duplicate-post-exclusion-query-loop/?preview=1)

Estende o bloco nativo **Query Loop** (`core/query`) para evitar posts duplicados entre múltiplos loops na mesma página.

**Autor:** [Itamar Silva](https://github.com/silvaitamar) · [Perfil WordPress](https://profiles.wordpress.org/itamarsilvacc/)

## O que faz

Adiciona o atributo `uniqueOnPage` e o controle **Tornar posts únicos na página** no editor de blocos. No front-end, cada Query Loop com a opção ativa exclui posts já exibidos por loops anteriores (via `post__not_in`).

## Demonstração ao vivo (Live Preview)

A deduplicação acontece **no front-end**, não no editor — então a melhor forma de vê-la em ação é o **Live Preview** no WordPress Playground, sem instalar nada:

- Botão **Live Preview** na [página do plugin no WordPress.org](https://wordpress.org/plugins/silvaitamar-duplicate-post-exclusion-query-loop/) ou [abra direto no Playground](https://wordpress.org/plugins/silvaitamar-duplicate-post-exclusion-query-loop/?preview=1).
- O demo mostra uma home com um loop **Latest** seguido de loops por categoria; com a opção ativa, os posts do Latest não se repetem nas seções abaixo.
- O ambiente do preview é definido por [`.wordpress-org/blueprints/blueprint.json`](.wordpress-org/blueprints/blueprint.json).

> Abra em um navegador completo (Safari, Chrome, Firefox) — o Playground não roda em navegadores embutidos de apps.

## Requisitos

- WordPress 6.7+
- PHP 7.4+

## Instalação

### A partir do repositório

```bash
git clone https://github.com/silvaitamar/wp-duplicate-post-exclusion-query-loop.git
cd silvaitamar-duplicate-post-exclusion-query-loop
composer install --no-dev
npm install
npm run build
```

Copie a pasta para `wp-content/plugins/silvaitamar-duplicate-post-exclusion-query-loop/` (ou use o clone diretamente nesse caminho) e ative em **Plugins**.

### A partir de uma release

Baixe o ZIP em [Releases](https://github.com/silvaitamar/wp-duplicate-post-exclusion-query-loop/releases), extraia em `wp-content/plugins/` e ative o plugin.

> O pacote de release já inclui `build/` compilado. Não é necessário Node.js no servidor.

## Desenvolvimento

Com dependências instaladas (ver acima):

```bash
npm run start   # watch do bundle do editor
npm run build   # build de produção
npm run lint:js # lint JavaScript
```

Estrutura principal:

```text
src/           Código PHP (PSR-4) e JavaScript do editor
build/         Assets compilados enfileirados no admin
silvaitamar-duplicate-post-exclusion-query-loop.php   Bootstrap
readme.txt     Metadados para o WordPress.org (inglês)
```

## Compatibilidade

Funciona com variações do [Advanced Query Loop](https://wordpress.org/plugins/advanced-query-loop/) e outras extensões de `core/query`. Detalhes técnicos em [docs/COMPATIBILITY.md](docs/COMPATIBILITY.md).

## Licença

GPL-2.0-or-later — veja [LICENSE](LICENSE).

## Changelog

Veja [CHANGELOG.md](CHANGELOG.md).
