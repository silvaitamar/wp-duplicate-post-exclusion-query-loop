# Unique Query Loop Extension

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Estende o bloco nativo **Query Loop** (`core/query`) para evitar posts duplicados entre múltiplos loops na mesma página.

**Autor:** [Itamar Silva](https://github.com/silvaitamar) · [Perfil WordPress](https://profiles.wordpress.org/itamarsilvacc/)

## O que faz

Adiciona o atributo `uniqueOnPage` e o controle **Tornar posts únicos na página** no editor de blocos. No front-end, cada Query Loop com a opção ativa exclui posts já exibidos por loops anteriores (via `post__not_in`).

## Requisitos

- WordPress 6.7+
- PHP 7.4+

## Instalação

### A partir do repositório

```bash
git clone https://github.com/silvaitamar/wp-unique-query-loop-extension.git
cd wp-unique-query-loop-extension
composer install --no-dev
npm install
npm run build
```

Copie a pasta para `wp-content/plugins/unique-query-loop-extension/` (ou use o clone diretamente nesse caminho) e ative em **Plugins**.

### A partir de uma release

Baixe o ZIP em [Releases](https://github.com/silvaitamar/wp-unique-query-loop-extension/releases), extraia em `wp-content/plugins/` e ative o plugin.

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
unique-query-loop-extension.php   Bootstrap
readme.txt     Metadados para o WordPress.org (inglês)
```

## Compatibilidade

Funciona com variações do [Advanced Query Loop](https://wordpress.org/plugins/advanced-query-loop/) e outras extensões de `core/query`. Detalhes técnicos em [docs/COMPATIBILITY.md](docs/COMPATIBILITY.md).

## Licença

GPL-2.0-or-later — veja [LICENSE](LICENSE).

## Changelog

Veja [CHANGELOG.md](CHANGELOG.md).
