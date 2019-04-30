FROM ctftraining/base_image_nginx_mysql_php_56

MAINTAINER Virink@CTFTraining <virink@outlook.com>

COPY src /var/www/html

RUN sh -c 'mysqld_safe &' \
    && sleep 5s \
    && mysql -e "source /var/www/html/db.sql;" -uroot -proot \
    && rm -f /var/www/html/db.sql