FROM postgres:13.0
ARG DB_LOCALE
#RUN apt-get update && apt-get install -y locales && rm -rf /var/lib/apt/lists/*
RUN localedef -i ${DB_LOCALE} -c -f UTF-8 -A /usr/share/locale/locale.alias ${DB_LOCALE}.UTF-8
ENV LANG ${DB_LOCALE}.UTF-8
#USER postgres

#COPY docker/postgres/postgresql.conf /etc/postgresql/postgresql.conf
#COPY docker/postgres/pg_hba.conf /etc/postgresql/pg_hba.conf

#CMD ["postgres", "-c", "config_file=/etc/postgresql/postgresql.conf"]
#CMD ["postgres", "-c", "hba_file=/etc/postgresql/pg_hba.conf"]
#Command: postgres -c config_file=/etc/postgresql/postgresql.conf
#Command: postgres -c hba_file=/etc/postgresql/pg_hba.conf
