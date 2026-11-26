FROM php:8.5.11-cli-alpine@sha256:93684051146ec037620855feb77f278090bde45ddc030801cd3f2a7685bc4deb
COPY docker/apk-build.lock /tmp/apk-build.lock
RUN apk add --no-cache libpq=18.7-r0 && apk add --no-cache --virtual .build-deps $(cat /tmp/apk-build.lock) && docker-php-ext-install pdo_pgsql pcntl && apk del .build-deps && rm /tmp/apk-build.lock
ARG SOURCE_ID=working-tree
LABEL org.opencontainers.image.revision=$SOURCE_ID
WORKDIR /app
COPY app app
COPY public public
COPY bin bin
COPY database database
RUN addgroup -S -g 101 nusamart && adduser -S -u 100 -G nusamart nusamart
RUN apk add --no-cache su-exec=0.3-r0
COPY --chmod=755 docker/php-entrypoint.sh /usr/local/bin/nusamart-entrypoint
ENV DB_PASSWORD_FILE=/run/nusamart-secrets/db_password CHECKOUT_TOKEN_FILE=/run/nusamart-secrets/checkout_token BUYER_PASSWORD_FILE=/run/nusamart-secrets/buyer_password
ENTRYPOINT ["nusamart-entrypoint"]
CMD ["php", "-S", "0.0.0.0:8000", "public/index.php"]
