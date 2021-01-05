FROM httpd:2.4.46

ARG FMW_WORKDIR

RUN mkdir -p $FMW_WORKDIR && chmod -R 777 $FMW_WORKDIR

COPY docker/apache/httpd.conf /usr/local/apache2/conf/httpd.conf
COPY docker/apache/httpd-vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf
COPY docker/apache/httpd-ssl.conf /usr/local/apache2/conf/extra/httpd-ssl.conf
COPY docker/apache/httpd-mpm.conf /usr/local/apache2/conf/extra/httpd-mpm.conf

WORKDIR $FMW_WORKDIR
