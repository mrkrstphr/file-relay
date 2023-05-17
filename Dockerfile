FROM php:8.1-alpine3.14

ARG BUILD_DATE

LABEL org.label-schema.schema-version="1.0"
LABEL org.label-schema.build-date=$BUILD_DATE
LABEL org.label-schema.name="mrkrstphr/file-relay"
LABEL org.label-schema.description="File relay service"
LABEL org.label-schema.url="https://github.com/mrkrstphr/file-relay"
LABEL org.label-schema.vcs-url="https://github.com/mrkrstphr/file-relay"
LABEL org.label-schema.vcs-ref=$VCS_REF
LABEL org.label-schema.version=$BUILD_VERSION

RUN mkdir -p /var/www

COPY ./ /var/www
WORKDIR /var/www

EXPOSE 80
VOLUME /data

ENTRYPOINT [ "php", "-S", "0.0.0.0:80", "-t", "." ]
