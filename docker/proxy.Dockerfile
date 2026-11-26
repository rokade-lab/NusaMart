FROM jc21/nginx-proxy-manager:2.9.14@sha256:e6d13908c87d150efc1566a9ed1570661f1c3e09362b26bfe7d7608a831e4591
ARG SOURCE_ID=working-tree
LABEL org.opencontainers.image.revision=$SOURCE_ID
COPY docker/nginx.conf /etc/nginx/conf.d/nusamart.conf
