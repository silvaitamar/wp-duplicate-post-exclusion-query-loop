#!/usr/bin/env bash
# Gera ZIP pronto para WordPress.org ou instalação manual.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SLUG="unique-query-loop-extension"
OUT="${ROOT}/${SLUG}.zip"
STAGE="$(mktemp -d)"

cleanup() {
	rm -rf "$STAGE"
}
trap cleanup EXIT

mkdir -p "${STAGE}/${SLUG}"

rsync -a \
	--exclude-from="${ROOT}/.distignore" \
	"${ROOT}/" "${STAGE}/${SLUG}/"

# Garante artefatos de produção mesmo se .distignore evoluir.
test -f "${STAGE}/${SLUG}/build/index.js" || {
	echo "error: build/index.js ausente. Execute npm run build." >&2
	exit 1
}

rm -f "$OUT"
(cd "$STAGE" && zip -rq "$OUT" "$SLUG")

echo "Created: $OUT"
