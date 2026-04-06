# Backend – PHP 8.4 CLI (Symfony 8.0 dev image)
###############################################################################
FROM php:8.4-cli-bookworm

ARG HOST_UID=1000
ARG HOST_GID=1000
ARG APP_USER=app

# --- System dependencies & PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-install pdo_mysql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# --- Non-root user
RUN groupadd --gid ${HOST_GID} ${APP_USER} \
    && useradd --uid ${HOST_UID} --gid ${HOST_GID} --create-home --shell /bin/bash ${APP_USER}

# --- Tooling
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --link --from=ghcr.io/symfony-cli/symfony-cli:latest /usr/local/bin/symfony /usr/local/bin/symfony

WORKDIR /app

USER ${APP_USER}