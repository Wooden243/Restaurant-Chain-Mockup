# index.php — ソース全文と逐次解説

**原本:** `index.php`

## 1. ファイル概要

- **役割:** Web表示の入口。HTTPリクエストごとに3つのチェーンを作成し、レンダリングされたHTMLを出力します。
- **入力と出力:** 入力: HTTP GET（URLパラメータ不要）。出力: 完全なHTML文書。
- **主要メソッド・処理:** RestaurantChain::RandomGenerator()、RestaurantChain::toHTML()
- **前提知識:** PHPの配列、for/foreach、require_once、PHPテンプレート構文
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

use RestaurantMockup\Models\RestaurantChain;

// A fresh randomized mock dataset is generated on every HTTP page load.
$chains = [];
for ($i = 0; $i < 3; $i++) {
    $chains[] = RestaurantChain::RandomGenerator();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A randomly generated restaurant-chain management mockup.">
    <title>Restaurant Chain Mockup</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<main class="container">
    <header class="site-header">
        <h1>Restaurant Chain Mockup</h1>
        <p>Explore randomly generated chains, their locations, employees and company details.</p>
        <p class="hint">Reload the page to generate a new dataset. Expand each location to inspect its employees.</p>
    </header>
    <?php foreach ($chains as $chain): ?>
        <?= $chain->toHTML() ?>
    <?php endforeach; ?>
    <footer>Demo data only — names, addresses and personnel are fictional.</footer>
</main>
</body>
</html>
```

## 3. 行・コードブロック単位の逐次解説

### L1–L36：`メイン処理または宣言`

サーバー側のHTMLテンプレートです。`<?php ... ?>` 内で生成したデータをHTMLへ展開します。HTTPでこのファイルにアクセスすることが実行の起点です。

- **L4** `require_once __DIR__ . '/src/bootstrap.php';` — 必要なファイルを重複せず読み込みます。
- **L10** `for ($i = 0; $i < 3; $i++) {` — 一定の回数だけ生成処理を繰り返します。
- **L15** `<html lang="en">` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L16** `<head>` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L25** `<header class="site-header">` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L26** `<h1>Restaurant Chain Mockup</h1>` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L30** `<?php foreach ($chains as $chain): ?>` — 配列要素を順番に処理します。
- **L31** `<?= $chain->toHTML() ?>` — 前後のデータの受け渡しと返り値の型を確認してください。

- **L2** `declare(strict_types=1);` — 型の暗黙変換を抑えるPHP設定です。
- **L4** `require_once __DIR__ . '/src/bootstrap.php';` — autoload登録を1回だけ読み込みます。
- **L9** `$chains = [];` — まだ何も生成していない配列を初期化します。
- **L10** `for ($i = 0; $i < 3; $i++) {` — 同一リクエスト内で3件だけ生成するループです。
- **L11** `$chains[] = RestaurantChain::RandomGenerator();` — 静的ファクトリーメソッドで、会社・店舗・社員を含むオブジェクトが返ります。
- **L17** `<meta charset="utf-8">` — 文字コード・モバイル表示等のHTMLヘッダを宣言します。
- **L21** `<link rel="stylesheet" href="/assets/styles.css">` — CSSをブラウザーが別HTTPリクエストで取得できるようパスを指定します。
- **L30** `<?php foreach ($chains as $chain): ?>` — 生成した3件を順に表示します。
- **L31** `<?= $chain->toHTML() ?>` — エスケープ済みデータを含むHTML文字列を出力します。
- **L33** `<footer>Demo data only — names, addresses and personnel are fictional.</footer>` — 実在データではないことを明示しています。

## 4. 実行トレース（値と呼び出し順）

ブラウザーから `/` にアクセス → `bootstrap.php` 読み込み → `$chains` が0→1→2→3件に増える → それぞれの `toHTML()` が3つの `<section class="chain">` を返す → `<link>` のCSSを取得して画面表示。再読み込みでは新しいチェーン配列を作ります。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: b9583e10b43db26a66060c71f556a5234755a358e038bba4e6dee84d5ae4fadc -->
