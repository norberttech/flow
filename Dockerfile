ARG FLOW_PHP_VERSION=8.2.11
ARG FLOW_BASE_IMAGE_TAG_SUFFIX=cli-alpine3.18
ARG FLOW_BASE_IMAGE_TAG=${FLOW_PHP_VERSION}-${FLOW_BASE_IMAGE_TAG_SUFFIX}
ARG FLOW_BASE_IMAGE=php:${FLOW_BASE_IMAGE_TAG}

FROM ${FLOW_BASE_IMAGE} as flow

COPY build/flow.phar /flow-php/flow.phar

RUN apk update && apk add --no-cache \
    $PHPIZE_DEPS \
    gmp-dev \
    git \
 && docker-php-ext-install bcmath gmp \
 && git clone --recursive --depth=1 https://github.com/kjdev/php-ext-snappy.git /tmp/php-ext-snappy \
 && cd /tmp/php-ext-snappy \
 && phpize \
 && ./configure \
 && make \
 && make install \
 && docker-php-ext-enable snappy \
 && rm -rf /tmp/php-ext-snappy \
 && chmod +x /flow-php/flow.phar

ENTRYPOINT ["php", "/flow-php/flow.phar"]

VOLUME ["/flow-php"]
WORKDIR /flow-php
