# 仮想Linax（Lima）環境の構築

## バージョン
```
MacBook Air M3 2024
macOS Sonoma 14.3
Visual Studio Code バージョン 1.89.1

limactl version 0.22.0
Docker version 26.1.2, build 211e74b240
Docker Compose version 2.27.0
mysql  Ver 8.0.37 for macos14.4 on arm64 (Homebrew)
```

## インストール
### 1.limaとdocker等のインストール
```
% brew install lima
% limactl --version

% brew install docker
% docker --version

% brew install boot2docker-cli docker-compose
% docker-compose version

% brew install mysql@8.0
% vi ~/.zshrc
% export PATH=/opt/homebrew/opt/mysql@8.0/bin:$PATH
% source ~/.zshrc
% mysql --version
```

### 2.仮想Linaxを作成する。
```
% limactl start --name=docker-vm template://docker
> Proceed with the current configuration
```

### 3.Docker Contextの設定
#### 3-1.Docker Contextを作成する。
```
% docker context create lima-docker-vm --docker "host=unix:///Users/ryota24/.lima/docker-vm/sock/docker.sock"
```

#### 3-2.現在のコンテキストを選択する。
```
% docker context use lima-docker-vm
```

#### 3-3.コンテキストを削除する(メンンテナンス用)。
```
% docker context rm lima-docker-vm
```

### 4.仮想Linax環境を操作する。
#### 4-1.一覧を表示する。
```
% limactl ls
```

#### 4-2.停止と起動をする。
```
% limactl stop docker-vm
% limactl start docker-vm
```

#### 4-3.仮想Linax環境に入る。
```
% limactl shell docker-vm
% exit
```

#### 4-4.仮想Linax環境を削除する。
```
% limactl delete docker-vm
```

### 5.Macのフォルダの書き込みを許可する。
```
% vi ~/.lima/docker-vm/lima.yaml
mounts:
- location: "~"
  writable: true
- location: "/tmp/lima"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/web"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/mysql"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/mysql/ddl"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/mysql/db"
  writable: true
```

### VSCode

#### 1.Dev Containersプラグインをインストールする。
#### 2.リモート(仮想LinuxのDocker内)に入る時は、左下の緑のボタン、Reopen in Container(コンテナで再度開く)を選択する。
#### 3.ローカルに戻る時は、左下の緑のボタン、Reopen Folder Locallyを選択する。

### Docker操作

#### 1.仮想Linax環境の作り直し
```
% limactl stop docker-vm
% limactl delete docker-vm
% limactl start --name=docker-vm template://docker
% limactl stop docker-vm

% vi ~/.lima/docker-vm/lima.yaml
mounts:
- location: "~"
  writable: true
- location: "/tmp/lima"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/web"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/mysql"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/mysql/ddl"
  writable: true
- location: "/Users/ryota24/pc_data/project/dev_container/docker/mysql/db"
  writable: true

% limactl start docker-vm
```

#### 2.MySQLの永続化されたDBを削除する
```
% rm -r /Users/ryota24/pc_data/project/dev_container/docker/mysql/db/*
```

#### 3.DockerのMySQLに接続
```
% docker ps -a
% mysql -u root --host 127.0.0.1 --port 3306 docker_db -p
% cd /Users/ryota24/pc_data/project/dev_container/docker/mysql/ddl
% mysql --user=root --password=root --host 127.0.0.1 --port 3306 --local-infile docker_db -e "LOAD DATA LOCAL INFILE './3_test1_table_import.csv' INTO TABLE TEST1_TABLE FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'"
```

#### 4.WebサーバーのDocker(コンテナ)に入る
```
% docker ps -a
% docker exec -it --user=root docker-web-1 bash
% apachectl restart
```

#### 5.MySQLサーバーのDocker(コンテナ)に入る
```
% docker ps -a
% docker exec -it --user=root docker-db-1 bash
% cd /var/lib/docker-entrypoint-initdb.d
% cat 2_test1_table_import.sh
```

#### 6.MySQLが起動しない場合などのログ
```
% cd /Users/ryota24/pc_data/project/dev_container/docker
% docker ps -a
% docker-compose logs
```

#### 7.Dockerをビルドし直す(MySQL単体で動作を見たい時など)
```
% cd /Users/ryota24/pc_data/project/dev_container/docker
% docker-compose down
% docker-compose build
% docker-compose up -d
% docker ps -a
% docker-compose logs
```

#### 8.Docker Pullの使用制限の解除
```
% docker login
```

#### 9.Docker再構築スクリプト
```
% cd /Users/ryota24/pc_data/project/dev_container/tools/limactl_setup
% sh limactl_setup.sh
LIMA_NAME=docker-vm
limactl stop -f $LIMA_NAME
limactl delete $LIMA_NAME
limactl start --name=$LIMA_NAME template://docker
limactl stop $LIMA_NAME
cp -f ./lima.yaml ~/.lima/$LIMA_NAME/lima.yaml
limactl start $LIMA_NAME
docker context create lima-$LIMA_NAME --docker "host=unix://~/.lima/$LIMA_NAME/sock/docker.sock"
docker context use lima-$LIMA_NAME
```

#### 10.コマンドでPHPにアクセスする
```
% curl --include "http://localhost:8080/reversi/php/api1_get_unique_id.php"
```

#### 11.MySQLのアクセス方法
http://localhost:3000
データベースの種類: MySQL
サーバー: db
ユーザー名: user
パスワード: user
データベース: docker_db

#### 12.PHPデバッグ方法
#### 1.リモート(仮想LinuxのDocker内)に入る(左下の緑のボタン、Reopen in Containerを選択する)。
#### 2.PHP Extension Packプラグインをインストールする。
#### 3.RUN AND DEBUGでListen for Xdebugを実行する。
#### 3.api1_get_unique_id.phpにブレークを付ける。
#### 4.コマンドでPHPにアクセスする。
