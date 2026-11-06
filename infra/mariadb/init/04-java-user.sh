#!/bin/bash
# Creates the user for the Java processes (Outros_Comandos.txt: java1 with role Java).
set -euo pipefail
mysql -uroot -p"${MARIADB_ROOT_PASSWORD}" <<SQL
CREATE USER IF NOT EXISTS '${MYSQL_JAVA_USER}'@'%' IDENTIFIED BY '${MYSQL_JAVA_PASSWORD}';
GRANT Java TO '${MYSQL_JAVA_USER}'@'%';
SET DEFAULT ROLE Java FOR '${MYSQL_JAVA_USER}'@'%';
SQL
