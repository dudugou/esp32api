# ESP32 API - Docker環境

PHP + CodeIgniter4 + MySQL + phpMyAdminの開発環境

## 環境構成

- **PHP**: 8.2-fpm
- **CodeIgniter4**: 最新版
- **MySQL**: 8.0
- **phpMyAdmin**: 最新版
- **Nginx**: Alpine版

## セットアップ手順

### 1. Docker環境の起動

```bash
docker-compose up -d
```

初回起動時は自動的にCodeIgniter4がインストールされます（数分かかります）。

### 2. CodeIgniter4のインストール確認

srcフォルダが作成され、CodeIgniter4がインストールされていることを確認：

```bash
ls -la src/
```

### 3. データベース接続設定

`src/app/Config/Database.php`を編集：

```php
public array $default = [
    'DSN'      => '',
    'hostname' => 'mysql',
    'username' => 'esp32user',
    'password' => 'esp32pass',
    'database' => 'esp32api_db',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8mb4',
    'DBCollat' => 'utf8mb4_general_ci',
    'swapPre'  => '',
    'encrypt'  => false,
    'compress' => false,
    'strictOn' => false,
    'failover' => [],
    'port'     => 3306,
];
```

## アクセスURL

- **アプリケーション**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - サーバー: `mysql`
  - ユーザー名: `root`
  - パスワード: `root`

## データベース情報

- **ホスト**: mysql (Docker内) / localhost:3306 (ホストから)
- **データベース名**: esp32api_db
- **ユーザー名**: esp32user
- **パスワード**: esp32pass
- **ROOTパスワード**: root

## よく使うコマンド

### コンテナの起動
```bash
docker-compose up -d
```

### コンテナの停止
```bash
docker-compose down
```

### コンテナの再構築
```bash
docker-compose up -d --build
```

### ログの確認
```bash
docker-compose logs -f
```

### PHPコンテナに入る
```bash
docker exec -it esp32api_php sh
```

### Composerコマンドの実行
```bash
docker exec -it esp32api_php composer install
docker exec -it esp32api_php composer update
```

### CodeIgniter4のコマンド実行
```bash
docker exec -it esp32api_php php spark list
docker exec -it esp32api_php php spark migrate
```

## トラブルシューティング

### 権限エラーが発生した場合

```bash
docker exec -it esp32api_php chown -R www-data:www-data /var/www/html
docker exec -it esp32api_php chmod -R 755 /var/www/html
```

### CodeIgniter4が表示されない場合

srcフォルダが空の場合、手動でインストール：

```bash
docker exec -it esp32api_php composer create-project codeigniter4/appstarter /var/www/html
```

## フォルダ構造

```
esp32api/
├── docker-compose.yml     # Dockerサービス定義
├── Dockerfile             # PHPコンテナの定義
├── .dockerignore         # Docker除外ファイル
├── nginx/
│   └── nginx.conf        # Nginx設定
├── src/                  # CodeIgniter4プロジェクト（自動生成）
│   ├── app/
│   ├── public/
│   ├── writable/
│   └── ...
└── README.md             # このファイル
```
