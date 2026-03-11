FROM postgres:17

COPY entrypoint.sql /docker-entrypoint-initdb.d/01-init-databases.sql

RUN apt-get update && apt-get install -y \
    postgresql-17-pgvector \
    && rm -rf /var/lib/apt/lists/* \
