# Company.php — ソース全文と逐次解説

**原本:** `src/Models/Company.php`

## 1. ファイル概要

- **役割:** 企業情報を管理し、チェーンが共通利用する会社の表示ロジックを実装します。
- **入力と出力:** 入力: 名前、設立年、説明、URL、電話、業界、CEO、上場状態。出力: Companyと3形式の文字列。
- **主要メソッド・処理:** __construct()、RandomGenerator()、toString()、toHTML()、toMarkdown()
- **前提知識:** プロパティ、URL検証、HTMLエスケープ
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

namespace RestaurantMockup\Models;

use RestaurantMockup\Contracts\FileConvertible;
use RestaurantMockup\Support\Html;
use RestaurantMockup\Support\RandomData;

class Company implements FileConvertible
{
    public function __construct(
        private string $name,
        private int $yearFounded,
        private string $description,
        private string $website,
        private string $phone,
        private string $industry,
        private string $ceo,
        private bool $publiclyTraded,
    ) {
        if (trim($name) === '' || $yearFounded < 1800 || $yearFounded > (int) date('Y')) {
            throw new \InvalidArgumentException('Invalid company name or foundation year.');
        }
        if (!filter_var($website, FILTER_VALIDATE_URL)
            || !in_array(strtolower((string) parse_url($website, PHP_URL_SCHEME)), ['https', 'http'], true)) {
            throw new \InvalidArgumentException('Company website must be an HTTP(S) URL.');
        }
    }

    public function getName(): string { return $this->name; }
    public function getYearFounded(): int { return $this->yearFounded; }
    public function getDescription(): string { return $this->description; }
    public function getWebsite(): string { return $this->website; }
    public function getPhone(): string { return $this->phone; }
    public function getIndustry(): string { return $this->industry; }
    public function getCeo(): string { return $this->ceo; }
    public function isPubliclyTraded(): bool { return $this->publiclyTraded; }

    public static function RandomGenerator(): self
    {
        $name = RandomData::companyName();
        return new self(
            $name, RandomData::yearFounded(),
            "{$name} operates hospitality and dining businesses.", RandomData::safeDomain($name),
            RandomData::phone(), RandomData::industry(), RandomData::fullName(), random_int(0, 1) === 1,
        );
    }

    public function toString(): string
    {
        return sprintf(
            'Company: %s; Founded: %d; Description: %s; Website: %s; Phone: %s; Industry: %s; CEO: %s; Publicly traded: %s',
            $this->name, $this->yearFounded, $this->description, $this->website, $this->phone,
            $this->industry, $this->ceo, $this->publiclyTraded ? 'Yes' : 'No',
        );
    }

    public function toHTML(): string
    {
        $pairs = [
            'Company Name' => $this->name,
            'Year Founded' => (string) $this->yearFounded,
            'Description' => $this->description,
            'Phone' => $this->phone,
            'Industry' => $this->industry,
            'CEO' => $this->ceo,
            'Publicly Traded' => $this->publiclyTraded ? 'Yes' : 'No',
        ];
        $html = '<dl class="company-facts">';
        foreach ($pairs as $label => $value) {
            $html .= '<div><dt>' . Html::escape($label) . '</dt><dd>' . Html::escape($value) . '</dd></div>';
        }
        $url = Html::escape($this->website);
        return $html . '<div><dt>Website</dt><dd><a href="' . $url . '" rel="noopener noreferrer" target="_blank">'
            . $url . '</a></dd></div></dl>';
    }

    public function toMarkdown(): string
    {
        $data = [
            'Company Name' => $this->name,
            'Year Founded' => (string) $this->yearFounded,
            'Description' => $this->description,
            'Website' => $this->website,
            'Phone' => $this->phone,
            'Industry' => $this->industry,
            'CEO' => $this->ceo,
            'Publicly Traded' => $this->publiclyTraded ? 'Yes' : 'No',
        ];
        $out = '### Company Details' . "\n\n";
        foreach ($data as $field => $value) {
            $out .= '- **' . $field . '**: ' . Html::markdown($value) . "\n";
        }
        return $out;
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L11：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。


### L12–L30：`__construct`

会社名と設立年を検証し、`filter_var(FILTER_VALIDATE_URL)` と `parse_url(...,PHP_URL_SCHEME)` によりWebサイトURLがHTTP(S)であることを確認します。HTML属性に直接入れる前の安全対策です。

- **L22** `if (trim($name) === '' || $yearFounded < 1800 || $yearFounded > (int) date('Y')) {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L23** `throw new \InvalidArgumentException('Invalid company name or foundation year.');` — 不正な入力や状態を例外で拒否します。
- **L25** `if (!filter_var($website, FILTER_VALIDATE_URL)` — 条件を調べ、処理の分岐や入力検証に用います。
- **L27** `throw new \InvalidArgumentException('Company website must be an HTTP(S) URL.');` — 不正な入力や状態を例外で拒否します。

### L31–L31：`getName`

`getName()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L32–L32：`getYearFounded`

`getYearFounded()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L33–L33：`getDescription`

`getDescription()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L34–L34：`getWebsite`

`getWebsite()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L35–L35：`getPhone`

`getPhone()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L36–L36：`getIndustry`

`getIndustry()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L37–L37：`getCeo`

`getCeo()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L38–L39：`isPubliclyTraded`

`isPubliclyTraded()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L40–L49：`RandomGenerator`

`static` なファクトリーメソッドです。まだインスタンスがない状態で `クラス名::RandomGenerator()` を呼び出せます。`RandomData` の値を使用し、必要に応じて子オブジェクトを作った上で `self` のインスタンスを返します。表示処理はこのメソッドに含めません。

- **L43** `return new self(` — このクラス自身のオブジェクトを新規に生成します。
- **L46** `RandomData::phone(), RandomData::industry(), RandomData::fullName(), random_int(0, 1) === 1,` — 生成範囲内で整数乱数を作成しています。

### L50–L58：`toString`

プレーンテキスト形式を返します。モデルのデータを読める形式に整形し、子オブジェクトを持つ場合はそれぞれの `toString()` を順番に呼び出して含めます。返り値はオブジェクトではなくPHPの `string` です。

- **L52** `return sprintf(` — メソッドの処理結果を呼び出し元へ返します。

### L59–L78：`toHTML`

会社情報を `$pairs` の連想配列に集め、`foreach` で項目名・値を `<dl><dt><dd>` に展開します。Webサイトは特別に `href` のリンクにしますが、文字列は `Html::escape()` してから挿入します。

- **L71** `foreach ($pairs as $label => $value) {` — 配列要素を順番に処理します。
- **L72** `$html .= '<div><dt>' . Html::escape($label) . '</dt><dd>' . Html::escape($value) . '</dd></div>';` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L74** `$url = Html::escape($this->website);` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L75** `return $html . '<div><dt>Website</dt><dd><a href="' . $url . '" rel="noopener noreferrer" target="_blank">'` — メソッドの処理結果を呼び出し元へ返します。

### L79–L97：`toMarkdown`

Markdown用の文字列を返します。見出しやリストなどの記法を使い、可変の値は `Html::markdown()` で区切り文字をエスケープします。HTMLとは異なりタグではなくMarkdown構文を出力します。

- **L92** `foreach ($data as $field => $value) {` — 配列要素を順番に処理します。
- **L93** `$out .= '- **' . $field . '**: ' . Html::markdown($value) . "\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L95** `return $out;` — メソッドの処理結果を呼び出し元へ返します。

## 4. 実行トレース（値と呼び出し順）

`new Company("Sunrise Co",1998,"Local dining.","https://example.com","...","Food","Morgan",true)` → 年とURLを検証 → `toHTML()` は配列 `$pairs` をループし `<dt>/<dd>` 形式の会社詳細を作成 → URLは `href` に入れる前にエスケープされます。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: 0a5d45c9c332c128f547f4827975de4449bdf9e8c8cd162c54119344f2d5d676 -->
