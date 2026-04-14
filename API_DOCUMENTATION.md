# ESP32 API ドキュメント

JWT認証を使用したRESTful APIサービス

## 認証フロー

1. **ユーザー登録** → `/api/auth/register`
2. **ログイン** → `/api/auth/login` (JWTトークン取得)
3. **保護されたAPIへのアクセス** → Authorizationヘッダーにトークンを含める

---

## エンドポイント一覧

### 🔓 公開API（認証不要）

#### 1. ユーザー登録
```
POST /api/auth/register
Content-Type: application/json
```

**リクエストボディ:**
```json
{
  "userid": "testuser",
  "password": "password123",
  "email": "test@example.com"
}
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "ユーザー登録が完了しました",
  "data": {
    "id": 1,
    "userid": "testuser",
    "email": "test@example.com",
    "is_active": 1,
    "created_at": "2026-04-15 12:00:00"
  }
}
```

---

#### 2. ログイン（JWTトークン取得）
```
POST /api/auth/login
Content-Type: application/json
```

**リクエストボディ:**
```json
{
  "userid": "testuser",
  "password": "password123"
}
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "ログインしました",
  "data": {
    "user": {
      "id": 1,
      "userid": "testuser",
      "email": "test@example.com",
      "is_active": 1
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

**注意:** このトークンを保存して、保護されたAPIへのアクセスに使用してください。

---

#### 3. 公開エンドポイント
```
GET /api/public
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "これは公開APIです",
  "data": {
    "timestamp": "2026-04-15 12:00:00",
    "server": "ESP32 API Server"
  }
}
```

---

### 🔒 保護されたAPI（JWT認証が必要）

すべての保護されたエンドポイントには、Authorizationヘッダーが必要です：
```
Authorization: Bearer {your_jwt_token}
```

---

#### 4. 認証情報確認
```
GET /api/auth/me
Authorization: Bearer {token}
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "認証済みユーザー情報",
  "data": {
    "id": 1,
    "userid": "testuser",
    "email": "test@example.com"
  }
}
```

---

#### 5. 保護されたエンドポイント
```
GET /api/protected
Authorization: Bearer {token}
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "これは保護されたAPIです",
  "data": {
    "timestamp": "2026-04-15 12:00:00",
    "user": {
      "id": 1,
      "userid": "testuser",
      "email": "test@example.com"
    },
    "message": "認証されたユーザーのみアクセス可能です"
  }
}
```

---

#### 6. デバイスデータ送信
```
POST /api/device/data
Authorization: Bearer {token}
Content-Type: application/json
```

**リクエストボディ:**
```json
{
  "sensor_id": "ESP32-001",
  "temperature": 25.5,
  "humidity": 60.2
}
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "データを受信しました",
  "data": {
    "received": {
      "sensor_id": "ESP32-001",
      "temperature": "25.5",
      "humidity": "60.2"
    },
    "user_id": 1,
    "timestamp": "2026-04-15 12:00:00"
  }
}
```

---

#### 7. デバイスデータ取得
```
GET /api/device/data
Authorization: Bearer {token}
```

**レスポンス例:**
```json
{
  "status": "success",
  "message": "デバイスデータを取得しました",
  "data": [
    {
      "id": 1,
      "sensor_id": "ESP32-001",
      "temperature": 25.5,
      "humidity": 60.2,
      "timestamp": "2026-04-15 11:55:00"
    },
    {
      "id": 2,
      "sensor_id": "ESP32-001",
      "temperature": 25.8,
      "humidity": 59.8,
      "timestamp": "2026-04-15 12:00:00"
    }
  ],
  "user_id": 1
}
```

---

## curlでのテスト例

### 1. ユーザー登録
```bash
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "userid": "testuser",
    "password": "password123",
    "email": "test@example.com"
  }'
```

### 2. ログイン
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "userid": "testuser",
    "password": "password123"
  }'
```

### 3. 保護されたAPIへのアクセス
```bash
# トークンを環境変数に保存
TOKEN="your_jwt_token_here"

# 認証情報確認
curl -X GET http://localhost:8080/api/auth/me \
  -H "Authorization: Bearer $TOKEN"

# 保護されたエンドポイント
curl -X GET http://localhost:8080/api/protected \
  -H "Authorization: Bearer $TOKEN"

# デバイスデータ送信
curl -X POST http://localhost:8080/api/device/data \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sensor_id": "ESP32-001",
    "temperature": 25.5,
    "humidity": 60.2
  }'
```

---

## エラーレスポンス

### 401 Unauthorized（認証エラー）
```json
{
  "status": "error",
  "message": "トークンが無効または期限切れです"
}
```

### 400 Bad Request（バリデーションエラー）
```json
{
  "status": 400,
  "error": 400,
  "messages": {
    "userid": "ユーザーIDは必須です"
  }
}
```

---

## JWT設定

JWTの秘密鍵は環境変数で設定できます：

```bash
# .envファイルに追加
JWT_SECRET_KEY=your-super-secret-key-here
```

デフォルトのトークン有効期限: **24時間**

---

## セキュリティのベストプラクティス

1. **本番環境では必ずJWT_SECRET_KEYを変更してください**
2. トークンはセキュアな場所に保存（LocalStorage は避ける）
3. HTTPS を使用する（本番環境では必須）
4. トークンの有効期限を適切に設定
5. リフレッシュトークンの実装を検討

---

## ESP32からの使用例（Arduino）

```cpp
#include <HTTPClient.h>
#include <ArduinoJson.h>

String token = ""; // ログイン後に保存

void login() {
  HTTPClient http;
  http.begin("http://your-server:8080/api/auth/login");
  http.addHeader("Content-Type", "application/json");
  
  String payload = "{\"userid\":\"esp32user\",\"password\":\"esp32pass\"}";
  int httpCode = http.POST(payload);
  
  if (httpCode == 200) {
    String response = http.getString();
    // JSONをパースしてトークンを取得
    DynamicJsonDocument doc(1024);
    deserializeJson(doc, response);
    token = doc["data"]["token"].as<String>();
    Serial.println("Login successful!");
  }
  
  http.end();
}

void sendSensorData(float temp, float humidity) {
  HTTPClient http;
  http.begin("http://your-server:8080/api/device/data");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + token);
  
  String payload = "{\"sensor_id\":\"ESP32-001\",\"temperature\":" + 
                   String(temp) + ",\"humidity\":" + String(humidity) + "}";
  
  int httpCode = http.POST(payload);
  
  if (httpCode == 200) {
    Serial.println("Data sent successfully!");
  }
  
  http.end();
}
```

---

## トラブルシューティング

### トークンが無効というエラーが出る
- トークンの有効期限を確認
- Authorizationヘッダーの形式を確認: `Bearer {token}`
- トークンが正しくコピーされているか確認

### 404 エラー
- ルーティングが正しく設定されているか確認
- URLが正しいか確認（/api/ を忘れずに）

### 500 エラー
- データベース接続を確認
- エラーログを確認: `docker logs esp32api_php`
