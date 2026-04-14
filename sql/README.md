# ESP32 API Database SQL Files

このディレクトリには、ESP32 APIアプリケーションのデータベース構造とサンプルデータのSQLファイルが含まれています。

## ファイル一覧

### 01_schema.sql
データベーススキーマ定義ファイル

**内容:**
- `users` テーブルの作成
- インデックスの定義
- テーブル構造の詳細なコメント

**テーブル構造:**
- **users**: ユーザー認証と管理
  - `id`: 主キー（自動採番）
  - `userid`: ユーザー名（ログインID、ユニーク）
  - `password`: ハッシュ化されたパスワード
  - `email`: メールアドレス（NULL可）
  - `is_active`: アクティブ状態（0:無効, 1:有効）
  - `created_at`: 作成日時
  - `updated_at`: 更新日時

### 02_seed_sample.sql
サンプルデータファイル

**内容:**
- テスト用のサンプルユーザーデータ
- デフォルトパスワード: `password123`

**サンプルユーザー:**
- admin / admin@example.com
- testuser / test@example.com
- demo / demo@example.com

## 使用方法

### 1. Dockerコンテナ内で直接実行

```bash
# コンテナに入る
docker-compose exec mysql bash

# MySQLに接続
mysql -u root -p
# パスワード: root

# データベースを選択
USE esp32api_db;

# スキーマを作成
source /var/lib/mysql/sql/01_schema.sql;

# サンプルデータを挿入（オプション）
source /var/lib/mysql/sql/02_seed_sample.sql;
```

### 2. ホストマシンから実行

```bash
# スキーマのみ
docker-compose exec -T mysql mysql -uroot -proot esp32api_db < sql/01_schema.sql

# スキーマ + サンプルデータ
docker-compose exec -T mysql mysql -uroot -proot esp32api_db < sql/01_schema.sql
docker-compose exec -T mysql mysql -uroot -proot esp32api_db < sql/02_seed_sample.sql
```

### 3. phpMyAdmin経由で実行

1. ブラウザで http://localhost:8080/phpmyadmin にアクセス
2. ユーザー名: `root`、パスワード: `root` でログイン
3. `esp32api_db` データベースを選択
4. 「SQL」タブを開く
5. SQLファイルの内容をコピー＆ペースト、または「ファイルを選択」でアップロード
6. 「実行」をクリック

### 4. 初期セットアップ（推奨）

```bash
# プロジェクトルートで実行
cd /Users/dudugou/Documents/code/phpApp/esp32api

# データベースとテーブルを作成
docker-compose exec -T mysql mysql -uroot -proot esp32api_db < sql/01_schema.sql

# 開発環境ではサンプルデータも投入
docker-compose exec -T mysql mysql -uroot -proot esp32api_db < sql/02_seed_sample.sql
```

## バックアップとリストア

### データベース全体のバックアップ

```bash
# スキーマとデータの両方
docker-compose exec mysql mysqldump -uroot -proot esp32api_db > sql/backup_$(date +%Y%m%d_%H%M%S).sql

# スキーマのみ
docker-compose exec mysql mysqldump -uroot -proot --no-data esp32api_db > sql/schema_backup.sql

# データのみ
docker-compose exec mysql mysqldump -uroot -proot --no-create-info esp32api_db > sql/data_backup.sql
```

### リストア

```bash
# バックアップファイルからリストア
docker-compose exec -T mysql mysql -uroot -proot esp32api_db < sql/backup_YYYYMMDD_HHMMSS.sql
```

## データベース構造の確認

```bash
# テーブル一覧を表示
docker-compose exec mysql mysql -uroot -proot -e "SHOW TABLES;" esp32api_db

# usersテーブルの構造を表示
docker-compose exec mysql mysql -uroot -proot -e "DESC users;" esp32api_db

# テーブル作成コマンドを表示
docker-compose exec mysql mysql -uroot -proot -e "SHOW CREATE TABLE users\G" esp32api_db
```

## 注意事項

1. **本番環境での注意**
   - `02_seed_sample.sql` は開発/テスト環境専用です
   - 本番環境では使用しないでください
   - 本番環境では強力なパスワードを設定してください

2. **パスワードハッシュ**
   - サンプルデータのパスワードは `password123` です
   - 実際の運用では必ず適切なパスワードポリシーを適用してください

3. **データベース接続情報**
   - ホスト: localhost（Dockerコンテナ内からは `mysql`）
   - ポート: 3306
   - ユーザー: root
   - パスワード: root
   - データベース名: esp32api_db

4. **CodeIgniterのマイグレーション**
   - これらのSQLファイルは手動実行用です
   - CodeIgniterのマイグレーション機能も利用可能です:
     ```bash
     docker-compose exec php php spark migrate
     ```

## トラブルシューティング

### テーブルが既に存在する場合

SQLファイルには `DROP TABLE IF EXISTS` が含まれているため、既存のテーブルは削除されます。
データを保持したい場合は、事前にバックアップを取ってください。

### 文字化けが発生する場合

データベースとテーブルは `utf8mb4` で作成されます。
接続時に以下を確認してください:

```sql
SET NAMES utf8mb4;
```

### 権限エラーが発生する場合

rootユーザー以外で実行する場合は、適切な権限を付与してください:

```sql
GRANT ALL PRIVILEGES ON esp32api_db.* TO 'your_user'@'%';
FLUSH PRIVILEGES;
```

## 更新履歴

- 2026-04-15: 初版作成
  - usersテーブルの定義
  - サンプルデータの追加
