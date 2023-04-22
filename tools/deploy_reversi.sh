REMOTE_HOST=wakizaka24@wakizaka24.sakura.ne.jp
PATH_TO_REPLACE=/home/wakizaka24/www/reversi
ssh $REMOTE_HOST rm -rf $PATH_TO_REPLACE/*
cd ../reversi
for file in `ls -A | grep --line-buffered -v .DS_Store`; do
    rsync -a --exclude .DS_Store ${file} $REMOTE_HOST:$PATH_TO_REPLACE
done
open https://wakizaka24.sakura.ne.jp/reversi/
open https://wakizaka24.sakura.ne.jp/reversi/php/info.php
open https://wakizaka24.sakura.ne.jp/reversi/php/db_connection_test.php