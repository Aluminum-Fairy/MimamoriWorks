#!/bin/sh
echo "サービス起動中"
service nginx start
service php7.4-fpm start
service mysql start
eval $(ssh-agent -s)
ssh-add /home/mirai/.ssh/GitKey_rsa
echo "サービス起動完了"