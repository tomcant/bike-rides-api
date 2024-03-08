# Note: we can't use the Bref v2 image here because it doesn't support handling
#       requests in parallel (which we need to do in order to exercise the full
#       flow in dev). See https://github.com/brefphp/bref/issues/1471

FROM docker.io/bref/php-82-fpm-dev:1.7.35

COPY php-fpm.conf /opt/bref/etc/php-fpm.conf

COPY --from=docker.io/jakzal/phpqa:1.90.3-php8.2-alpine \
  /tools/deptrac \
  /tools/php-cs-fixer \
  /tools/local-php-security-checker \
  /usr/bin/

COPY --from=composer:2.7.1 /usr/bin/composer /usr/bin/
