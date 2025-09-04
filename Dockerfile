# syntax=docker/dockerfile:1.6
# check=error=true

ARG PHP_VERSION=8.1

FROM mirror.gcr.io/php:${PHP_VERSION}-cli

# Set up php
RUN \
    --mount=type=bind,from=ghcr.io/mlocati/php-extension-installer:latest,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
    --mount=type=cache,sharing=locked,target=/var/lib/apt \
    --mount=type=cache,target=/var/cache/apt \
    <<EOL
export DEBIAN_FRONTEND=noninteractive
rm -f /etc/apt/apt.conf.d/docker-clean
echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache
apt-get update
apt-get install -y --no-install-recommends default-mysql-client sudo
install-php-extensions \
    @composer-2 \
    mysqli \
    pdo_mysql \
    xdebug \
    xsl \
    zip

groupadd --gid 1000 phpuser
useradd --gid phpuser --shell /bin/bash --create-home --uid 1000 phpuser
echo "phpuser ALL=(root) NOPASSWD:ALL" > "/etc/sudoers.d/phpuser"
chmod 0440 "/etc/sudoers.d/phpuser"
EOL

USER phpuser
VOLUME [ "/usr/src/app" ]
WORKDIR /usr/src/app
