# bootstrap.php — ソース全文と逐次解説

**原本:** `src/bootstrap.php`

## 1. ファイル概要

- **役割:** 名前空間付きクラス名をPHPファイルへ対応させ、必要なときにロードする仕組みです。
- **入力と出力:** 入力: クラス名（文字列）。出力: 対応するPHPファイルの読み込み（戻り値なし）。
- **主要メソッド・処理:** spl_autoload_register()
- **前提知識:** 名前空間、require_once、文字列処理
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

spl_autoload_register(static function (string $className): void {
    $prefix = 'RestaurantMockup\\';
    if (!str_starts_with($className, $prefix)) {
        return;
    }
    $relative = substr($className, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
```

## 3. 行・コードブロック単位の逐次解説

### L1–L14：`メイン処理または宣言`

各行の定義を順に追い、名前空間の一致と実行側から呼ばれる箇所に着目します。

- **L6** `if (!str_starts_with($className, $prefix)) {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L11** `if (is_file($file)) {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L12** `require_once $file;` — 必要なファイルを重複せず読み込みます。

- **L4** `spl_autoload_register(static function (string $className): void {` — PHPが未ロードクラスを求めた時に呼び出す関数を登録します。
- **L5** `$prefix = 'RestaurantMockup\\';` — このアプリだけの名前空間を対象にします。
- **L6** `if (!str_starts_with($className, $prefix)) {` — 名前空間外のクラスを誤って読み込まないようにします。
- **L9** `$relative = substr($className, strlen($prefix));` — 先頭の名前空間プレフィックスを除去します。
- **L10** `$file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';` — PHPの名前空間区切りをファイルパスへ変換します。
- **L11** `if (is_file($file)) {` — ファイルがある場合だけrequireを呼び出します。
- **L12** `require_once $file;` — 同じファイルを二重定義しないよう読み込みます。

## 4. 実行トレース（値と呼び出し順）

PHPが `RestaurantMockup\Models\Company` を要求 → prefixが一致する → 残部 `Models\Company` を `Models/Company.php` に変換 → `src/Models/Company.php` が存在するため `require_once` で読み込む。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: e7b0c1c4634ba86d937e021bca2476ea2c5a8739d9cb44ff855625c70ce648ed -->
