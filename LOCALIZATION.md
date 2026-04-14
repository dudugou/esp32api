# 多言語化（i18n）ガイド

## 概要

ESP32 APIアプリケーションは4つの言語をサポートしています：
- 🇯🇵 日本語（デフォルト）
- 🇺🇸 英語
- 🇨🇳 中国語（簡体字）
- 🇰🇷 韓国語

## 使い方

### 言語の切り替え

#### Web画面から
各ページの右上または ナビゲーションバーに言語切り替えドロップダウンが表示されます：

1. 言語アイコンをクリック
2. 希望の言語を選択
3. ページが自動的にリロードされ、選択した言語で表示されます

#### API経由
```bash
# 言語を切り替える
curl -X POST http://localhost:8080/api/language/switch \
  -H "Content-Type: application/json" \
  -d '{"locale": "en"}'

# 現在の言語を取得
curl http://localhost:8080/api/language/current
```

**サポートされている言語コード:**
- `ja` - 日本語
- `en` - 英語
- `zh-CN` - 中国語
- `ko` - 韓国語

## 開発者向け

### ディレクトリ構造

```
src/app/Language/
├── ja/           # 日本語
│   └── App.php
├── en/           # 英語
│   └── App.php
├── zh-CN/        # 中国語（簡体字）
│   └── App.php
└── ko/           # 韓国語
    └── App.php
```

### 言語ファイルの使用

#### ビューファイルで
```php
<!-- 単純なテキスト -->
<h1><?= lang('App.login.title') ?></h1>

<!-- 変数を含むテキスト -->
<p><?= lang('App.dashboard.welcome', ['name' => 'John']) ?></p>
```

#### コントローラーで
```php
// 言語テキストを取得
$message = lang('App.msg.loginSuccess');

// 変数付き
$greeting = lang('App.dashboard.welcome', ['name' => $user->userid]);
```

### 新しい翻訳の追加

1. 各言語ファイル（`src/app/Language/*/App.php`）を開く
2. 新しいキーと翻訳を追加：

```php
// src/app/Language/ja/App.php
return [
    // 既存の翻訳...
    
    'myNewKey' => '新しい翻訳テキスト',
    'greeting' => 'こんにちは、{name}さん',
];
```

3. すべての言語ファイルに同じキーを追加

### 新しい言語の追加

1. 新しい言語ディレクトリを作成：
```bash
mkdir src/app/Language/fr  # フランス語の例
```

2. `App.php`ファイルを作成し、すべての翻訳を追加

3. `src/app/Config/App.php`を更新：
```php
public array $supportedLocales = ['ja', 'en', 'zh-CN', 'ko', 'fr'];
```

4. `src/app/Helpers/language_helper.php`を更新：
```php
function get_supported_locales(): array
{
    return [
        'ja' => '日本語',
        'en' => 'English',
        'zh-CN' => '中文',
        'ko' => '한국어',
        'fr' => 'Français',  // 追加
    ];
}
```

5. `src/app/Controllers/Language.php`を更新：
```php
$supportedLocales = ['ja', 'en', 'zh-CN', 'ko', 'fr'];
```

### 言語コンポーネント

言語切り替えドロップダウンは再利用可能なコンポーネントとして実装されています：

```php
<!-- 任意のビューに追加 -->
<?= view('components/language_switcher') ?>
```

### APIエンドポイント

| メソッド | エンドポイント | 説明 |
|---------|--------------|------|
| POST | `/api/language/switch` | 言語を切り替える |
| GET | `/api/language/current` | 現在の言語を取得 |
| GET | `/language/switch/{locale}` | Web画面で言語を切り替える |

### 設定

#### デフォルト言語の変更

`src/app/Config/App.php`:
```php
public string $defaultLocale = 'ja';  // 'en', 'zh-CN', 'ko' に変更可能
```

#### サポートされている言語

`src/app/Config/App.php`:
```php
public array $supportedLocales = ['ja', 'en', 'zh-CN', 'ko'];
```

## 翻訳キー一覧

### 共通
- `welcome`, `login`, `logout`, `register`, `email`, `password`, `userId`
- `submit`, `cancel`, `save`, `update`, `delete`, `edit`
- `back`, `home`, `dashboard`, `profile`, `settings`, `language`

### ナビゲーション
- `nav.dashboard` - ダッシュボード
- `nav.profile` - プロフィール編集
- `nav.logout` - ログアウト

### ログイン画面
- `login.title` - ログイン
- `login.subtitle` - ESP32 API
- `login.userId` - ユーザーID
- `login.password` - パスワード
- `login.submit` - ログインボタン
- `login.noAccount` - アカウントがない場合のメッセージ
- `login.registerLink` - 登録リンク

### 登録画面
- `register.title` - ユーザー登録
- `register.subtitle` - 新しいアカウントを作成
- `register.submit` - 登録ボタン
- `register.hasAccount` - 既存アカウントがある場合

### ダッシュボード
- `dashboard.title` - ダッシュボード
- `dashboard.welcome` - ようこそメッセージ
- `dashboard.userInfo` - ユーザー情報
- `dashboard.apiTest` - APIテスト

### プロフィール
- `profile.title` - プロフィール編集
- `profile.subtitle` - ユーザー情報を更新
- `profile.currentInfo` - 現在の情報
- `profile.emailSection` - メール変更
- `profile.passwordSection` - パスワード変更

### メッセージ
- `msg.loginSuccess` - ログイン成功
- `msg.registerSuccess` - 登録完了
- `msg.updateSuccess` - 更新成功
- `msg.authError` - 認証エラー
- `msg.networkError` - 通信エラー

### バリデーション
- `validation.required` - 必須フィールド
- `validation.minLength` - 最小文字数
- `validation.email` - メール形式
- `validation.passwordMismatch` - パスワード不一致

## セッション管理

選択された言語はPHPセッションに保存され、ページ間で保持されます。ブラウザを閉じるまで言語設定が維持されます。

## トラブルシューティング

### 言語が切り替わらない
- ブラウザのキャッシュをクリア
- セッションをクリア（ログアウト→ログイン）

### 翻訳が表示されない
- 言語ファイルにキーが存在するか確認
- `src/app/Language/{locale}/App.php`のシンタックスエラーをチェック

### デフォルト言語に戻ってしまう
- セッションが有効か確認
- ブラウザのCookieが有効か確認

## ベストプラクティス

1. **一貫性**: すべての言語ファイルで同じキーを使用
2. **変数**: 動的コンテンツには `{variable}` プレースホルダーを使用
3. **コンテキスト**: わかりやすいキー名を使用（例: `login.submit` より `login.submitButton`）
4. **テスト**: 各言語で実際に表示を確認
5. **ドキュメント**: 新しいキーを追加したら、このドキュメントも更新

## サンプルコード

### 完全な多言語対応ページの例

```php
<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('App.myPage.title') ?></title>
</head>
<body>
    <!-- 言語切り替え -->
    <?= view('components/language_switcher') ?>
    
    <!-- コンテンツ -->
    <h1><?= lang('App.myPage.heading') ?></h1>
    <p><?= lang('App.myPage.welcome', ['name' => $userName]) ?></p>
    
    <button><?= lang('App.submit') ?></button>
</body>
</html>
```

## 参考リンク

- [CodeIgniter 4 Localization](https://codeigniter.com/user_guide/outgoing/localization.html)
- [PHP Internationalization](https://www.php.net/manual/en/book.intl.php)
