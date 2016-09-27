#!/bin/bash

yyyymm=`date -d '1 month ago' +'%Y%m'`
echo $yyyymm

echo `date`' START MONTHLY BATCH' $yyyymm

/usr/bin/php /var/www/html/SPCS/app/index.php 3 $yyyymm $yyyymm

echo `date`' END MONTHLY BATCH' $yyyymm


