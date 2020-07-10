#!/bin/sh
echo "サービス起動中"
service nginx start
service php7.4-fpm start
service mysql start
echo "サービス起動完了"