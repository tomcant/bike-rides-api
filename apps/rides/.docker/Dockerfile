FROM bref/php-84-fpm-dev:2.3.19

COPY --from=composer:2.8.6 /usr/bin/composer /usr/bin/

RUN curl -sL https://github.com/serverless/serverless/releases/download/v3.38.0/serverless-linux-x64 -o /usr/bin/serverless \
 && chmod +x /usr/bin/serverless
