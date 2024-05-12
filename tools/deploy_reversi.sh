REMOTE_HOST=wakizaka24@wakizaka24.sakura.ne.jp
PATH_TO_REPLACE=/home/wakizaka24/www/reversi
SECURITY_FILE_PATH=/Users/ryota24/pc_data/secret_data
ssh $REMOTE_HOST mkdir $PATH_TO_REPLACE
ssh $REMOTE_HOST rm -rf $PATH_TO_REPLACE/*
cd ../reversi
for FILE in `ls -A | grep --line-buffered -v .DS_Store`; do
    rsync -a --exclude .DS_Store ${FILE} $REMOTE_HOST:$PATH_TO_REPLACE
done
scp $SECURITY_FILE_PATH/php/class_security_info.php $REMOTE_HOST:$PATH_TO_REPLACE/php/class_security_info.php
open https://wakizaka24.sakura.ne.jp/reversi/
open https://wakizaka24.sakura.ne.jp/reversi/php/page_php_info.php
open https://wakizaka24.sakura.ne.jp/reversi/php/page_db_connection_test.php