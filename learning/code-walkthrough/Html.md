# Html.php — ソース全文と逐次解説

**原本:** `src/Support/Html.php`

## 1. ファイル概要

- **役割:** 出力時に文字列を安全にHTMLまたはMarkdownへ埋め込むための小さなユーティリティです。
- **入力と出力:** 入力: 任意の文字列。出力: エスケープ済み文字列。
- **主要メソッド・処理:** escape()、markdown()
- **前提知識:** XSS、特殊文字、htmlspecialchars
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

namespace RestaurantMockup\Support;

final class Html
{
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Escape characters that have structural meaning in Markdown tables. */
    public static function markdown(string $value): string
    {
        return str_replace(["\\", '|', "\r", "\n"], ['\\\\', '\\|', '', ' '], $value);
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L7：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。


### L8–L13：`escape`

`htmlspecialchars` を `ENT_QUOTES | ENT_SUBSTITUTE` とUTF-8で実行します。タグ・引用符・アンパサンド等を表示用文字列へ変換し、XSSやHTML構造破壊を抑えます。

### L14–L18：`markdown`

バックスラッシュ、縦棒、改行などをMarkdownで扱える形に置き換えます。HTML用のエスケープとMarkdown用エスケープは用途が異なります。

## 4. 実行トレース（値と呼び出し順）

`Html::escape("<b>Tokyo & Osaka</b>")` は `&lt;b&gt;Tokyo &amp; Osaka&lt;/b&gt;` を返します。`Html::markdown("A|B")` は `A\|B` を返し、Markdownテーブルの列区切り扱いを防ぎます。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: 08beff54759ae581c8911f5e97a5d79734cf77b2d7867f1276d18a16903151a9 -->
