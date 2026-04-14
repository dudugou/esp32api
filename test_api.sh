#!/bin/bash

# ESP32 API テストスクリプト
# 使い方: chmod +x test_api.sh && ./test_api.sh

BASE_URL="http://localhost:8080"
USERID="testuser"
PASSWORD="password123"
EMAIL="test@example.com"

echo "==================================="
echo "ESP32 API テスト"
echo "==================================="
echo ""

# 1. ユーザー登録
echo "1. ユーザー登録..."
curl -s -X POST $BASE_URL/api/auth/register \
  -H "Content-Type: application/json" \
  -d "{\"userid\": \"$USERID\", \"password\": \"$PASSWORD\", \"email\": \"$EMAIL\"}" \
  | python3 -m json.tool
echo ""
echo ""

# 2. ログイン
echo "2. ログイン..."
LOGIN_RESPONSE=$(curl -s -X POST $BASE_URL/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"userid\": \"$USERID\", \"password\": \"$PASSWORD\"}")

echo "$LOGIN_RESPONSE" | python3 -m json.tool
echo ""

# トークンを抽出
TOKEN=$(echo "$LOGIN_RESPONSE" | python3 -c "import sys, json; print(json.load(sys.stdin)['data']['token'])" 2>/dev/null)

if [ -z "$TOKEN" ]; then
    echo "エラー: トークンを取得できませんでした"
    exit 1
fi

echo "トークン取得成功!"
echo ""
echo ""

# 3. 公開API
echo "3. 公開API（認証不要）..."
curl -s -X GET $BASE_URL/api/public | python3 -m json.tool
echo ""
echo ""

# 4. トークンなしで保護されたエンドポイントにアクセス
echo "4. トークンなしでアクセス（401エラーを期待）..."
curl -s -X GET $BASE_URL/api/protected | python3 -m json.tool
echo ""
echo ""

# 5. 認証情報確認
echo "5. 認証情報確認..."
curl -s -X GET $BASE_URL/api/auth/me \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
echo ""
echo ""

# 6. 保護されたエンドポイント
echo "6. 保護されたエンドポイント..."
curl -s -X GET $BASE_URL/api/protected \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
echo ""
echo ""

# 7. デバイスデータ送信
echo "7. デバイスデータ送信..."
curl -s -X POST $BASE_URL/api/device/data \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"sensor_id": "ESP32-001", "temperature": 25.5, "humidity": 60.2}' \
  | python3 -m json.tool
echo ""
echo ""

# 8. デバイスデータ取得
echo "8. デバイスデータ取得..."
curl -s -X GET $BASE_URL/api/device/data \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
echo ""
echo ""

echo "==================================="
echo "テスト完了"
echo "==================================="
echo ""
echo "あなたのJWTトークン:"
echo "$TOKEN"
