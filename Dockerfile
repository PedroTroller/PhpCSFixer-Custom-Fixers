# Set by compose from PHP_MIN_VERSION in .env
ARG PHP_DEFAULT_VERSION

################################################################################

FROM php:${PHP_DEFAULT_VERSION}-cli AS base
RUN apt-get update \
 && apt-get install -y --no-install-recommends git make unzip \
 && rm -rf /var/lib/apt/lists/* \
 && git config --system --add safe.directory '*'
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV HOME=/tmp \
    COMPOSER_HOME=/tmp/composer \
    COMPOSER_ROOT_VERSION=dev-master
WORKDIR /app

################################################################################

FROM base AS dev
# No code here: compose.dev.yaml bind-mounts the repository.

################################################################################

FROM base AS test
ARG DEPENDENCIES
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json ./
RUN case "$DEPENDENCIES" in \
      highest) flags= ;; \
      lowest) flags='--prefer-lowest --prefer-stable' ;; \
      *) echo "DEPENDENCIES must be highest or lowest, got '$DEPENDENCIES'" >&2; exit 1 ;; \
    esac \
 && composer update --no-interaction --no-progress --prefer-dist --no-autoloader --no-scripts $flags \
 && rm -rf /tmp/composer
COPY . .
RUN composer dump-autoload
CMD ["composer", "tests"]
