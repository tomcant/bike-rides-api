FROM docker.io/bref/php-83-fpm-dev:2.2.24

COPY --from=docker.io/jakzal/phpqa:1.97.2-php8.3-alpine \
  /tools/deptrac \
  /tools/php-cs-fixer \
  /usr/bin/

COPY --from=docker.io/composer:2.7.6 /usr/bin/composer /usr/bin/

RUN curl -sL https://github.com/serverless/serverless/releases/download/v3.38.0/serverless-linux-x64 -o /usr/bin/serverless \
 && chmod +x /usr/bin/serverless
