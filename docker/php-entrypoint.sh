#!/bin/sh
set -eu
fail() { printf '%s\n' '{"status":"error","code":"SECRET_PROVISIONING_FAILED"}' >&2; exit 1; }
[ "$(id -u)" = 0 ] || fail
mkdir -p /run/nusamart-secrets || fail
chown 0:101 /run/nusamart-secrets || fail
chmod 0750 /run/nusamart-secrets || fail
copy_secret() {
    [ -r "$1" ] || fail
    target=$(mktemp /run/nusamart-secrets/.secret.XXXXXX) || fail
    cat "$1" > "$target" || fail
    [ -s "$target" ] || fail
    chmod 0400 "$target" || fail
    chown 100:101 "$target" || fail
    mv -f "$target" "/run/nusamart-secrets/$2" || fail
}
if [ -n "${DB_PASSWORD_SOURCE_FILE:-}" ]; then copy_secret "$DB_PASSWORD_SOURCE_FILE" db_password; fi
if [ -n "${CHECKOUT_TOKEN_SOURCE_FILE:-}" ]; then copy_secret "$CHECKOUT_TOKEN_SOURCE_FILE" checkout_token; fi
if [ -n "${BUYER_PASSWORD_SOURCE_FILE:-}" ]; then copy_secret "$BUYER_PASSWORD_SOURCE_FILE" buyer_password; fi
exec su-exec nusamart docker-php-entrypoint "$@"
