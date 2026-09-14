FROM webdevops/php-nginx-dev:8.5-alpine

ENV WEB_DOCUMENT_ROOT /app/www/

RUN mkdir -p /app/temp/ /app/logs/ /app/var
RUN chmod -R 0777 /app/temp/ /app/logs/ /app/var
RUN chown -R application:application /app/temp/ /app/logs/ /app/var

ARG MAKEFILE_UID=1000
ARG MAKEFILE_GID=1000

RUN usermod -u ${MAKEFILE_UID} application
RUN groupmod -g ${MAKEFILE_GID} application || true

USER application