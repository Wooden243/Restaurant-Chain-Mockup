# FileConvertible.php — ソース全文と逐次解説

**原本:** `src/Contracts/FileConvertible.php`

## 1. ファイル概要

- **役割:** すべてのドメインモデルが提供すべきテキスト、HTML、Markdownの変換メソッドを宣言します。
- **入力と出力:** 入力: 実装するオブジェクト自身。出力: 各メソッドはstring。
- **主要メソッド・処理:** toString()、toHTML()、toMarkdown()
- **前提知識:** interface、implements、戻り値型
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

namespace RestaurantMockup\Contracts;

/** A common contract for plain text, HTML and Markdown output. */
interface FileConvertible
{
    public function toString(): string;
    public function toHTML(): string;
    public function toMarkdown(): string;
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L8：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。


### L9–L9：`toString`

プレーンテキスト形式を返します。モデルのデータを読める形式に整形し、子オブジェクトを持つ場合はそれぞれの `toString()` を順番に呼び出して含めます。返り値はオブジェクトではなくPHPの `string` です。

### L10–L10：`toHTML`

ブラウザー用のHTML断片を文字列として返します。静的なタグはコード内で組み立て、可変値は `Html::escape()` で無害化してから入れます。包含するオブジェクトがある場合はその `toHTML()` を呼び、階層化したHTMLを作ります。

### L11–L12：`toMarkdown`

Markdown用の文字列を返します。見出しやリストなどの記法を使い、可変の値は `Html::markdown()` で区切り文字をエスケープします。HTMLとは異なりタグではなくMarkdown構文を出力します。

## 4. 実行トレース（値と呼び出し順）

`$company` は `Company` であり `FileConvertible` を実装 → `$company->toHTML()` は文字列を返す → `$employee` は `Employee` だが同じ `toHTML()` 呼び出しが可能。内部のHTML内容はクラスごとに異なります。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: c0cf689d7f29a36bd29ba299b3fa5db9ed508888371fa75586ca05de9aafdd01 -->
