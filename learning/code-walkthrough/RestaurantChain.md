# RestaurantChain.php — ソース全文と逐次解説

**原本:** `src/Models/RestaurantChain.php`

## 1. ファイル概要

- **役割:** Companyを継承した中心クラス。店舗を追加し、チェーン全体と会社情報を階層化して表示します。
- **入力と出力:** 入力: Companyの8属性、チェーンの属性、RestaurantLocationオブジェクト。出力: RestaurantChainと各変換形式。
- **主要メソッド・処理:** __construct()、addLocation()、RandomGenerator()、toHTML()
- **前提知識:** 継承、parent::、配列とcount、HTML合成
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

class RestaurantChain extends Company implements FileConvertible
{
    /** @var list<RestaurantLocation> */
    private array $restaurantLocations = [];

    public function __construct(
        string $name,
        int $yearFounded,
        string $description,
        string $website,
        string $phone,
        string $industry,
        string $ceo,
        bool $publiclyTraded,
        private int $chainId,
        private string $cuisineType,
        private bool $hasDriveThru,
        private string $parentCompany,
    ) {
        parent::__construct($name, $yearFounded, $description, $website, $phone, $industry, $ceo, $publiclyTraded);
        if ($chainId <= 0 || trim($cuisineType) === '') {
            throw new \InvalidArgumentException('Chain ID and cuisine type are required.');
        }
    }

    public function getChainId(): int { return $this->chainId; }
    public function getCuisineType(): string { return $this->cuisineType; }
    public function hasDriveThru(): bool { return $this->hasDriveThru; }
    public function getParentCompany(): string { return $this->parentCompany; }
    public function getNumberOfLocations(): int { return count($this->restaurantLocations); }
    /** @return list<RestaurantLocation> */
    public function getRestaurantLocations(): array { return $this->restaurantLocations; }

    public function addLocation(RestaurantLocation $location): void
    {
        $this->restaurantLocations[] = $location;
    }

    public static function RandomGenerator(): self
    {
        $company = Company::RandomGenerator();
        $chain = new self(
            $company->getName(), $company->getYearFounded(), $company->getDescription(),
            $company->getWebsite(), $company->getPhone(), $company->getIndustry(),
            $company->getCeo(), $company->isPubliclyTraded(),
            random_int(100, 99999), RandomData::cuisine(), random_int(0, 1) === 1,
            random_int(0, 3) === 0 ? RandomData::companyName() : 'Independent',
        );
        for ($i = 0, $count = random_int(2, 4); $i < $count; $i++) {
            $chain->addLocation(RestaurantLocation::RandomGenerator());
        }
        return $chain;
    }

    public function toString(): string
    {
        $locations = array_map(static fn(RestaurantLocation $location): string => $location->toString(), $this->restaurantLocations);
        return sprintf(
            "Restaurant Chain #%d: %s; Cuisine: %s; Locations: %d; Drive-thru: %s; Parent Company: %s\n%s\n%s",
            $this->chainId, $this->getName(), $this->cuisineType, $this->getNumberOfLocations(),
            $this->hasDriveThru ? 'Yes' : 'No', $this->parentCompany, parent::toString(), implode("\n", $locations),
        );
    }

    public function toHTML(): string
    {
        $html = '<section class="chain"><h2>Restaurant Chain ' . Html::escape($this->getName()) . '</h2>';
        $html .= '<div class="chain-card"><div class="panel-heading">Restaurant Chain Information</div><div class="chain-content">';
        $html .= '<div class="chain-metadata">Chain ID: ' . $this->chainId
            . ' · Cuisine: ' . Html::escape($this->cuisineType)
            . ' · Locations: ' . $this->getNumberOfLocations()
            . ' · Drive-thru: ' . ($this->hasDriveThru ? 'Yes' : 'No')
            . ' · Parent: ' . Html::escape($this->parentCompany) . '</div>';
        foreach ($this->restaurantLocations as $location) {
            $html .= $location->toHTML();
        }
        $html .= '<details class="company-details"><summary>Company Details</summary><div class="company-body">';
        $html .= parent::toHTML();
        return $html . '</div></details></div></div></section>';
    }

    public function toMarkdown(): string
    {
        $out = '## Restaurant Chain ' . Html::markdown($this->getName()) . "\n\n";
        $out .= '- Chain ID: ' . $this->chainId . "\n";
        $out .= '- Cuisine: ' . Html::markdown($this->cuisineType) . "\n";
        $out .= '- Locations: ' . $this->getNumberOfLocations() . "\n";
        $out .= '- Drive-thru: ' . ($this->hasDriveThru ? 'Yes' : 'No') . "\n";
        $out .= '- Parent company: ' . Html::markdown($this->parentCompany) . "\n\n";
        $out .= parent::toMarkdown() . "\n";
        foreach ($this->restaurantLocations as $location) {
            $out .= $location->toMarkdown();
        }
        return $out;
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L14：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。

- **L13** `private array $restaurantLocations = [];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。

### L15–L34：`__construct`

`parent::__construct(...)` がCompany側の8つの共通属性を初期化し、チェーン独自のID、業態、ドライブスルー、親会社を定義します。設立年は親クラスの値を再利用し、同名フィールドを二重に持たない点に注目してください。

- **L29** `parent::__construct($name, $yearFounded, $description, $website, $phone, $industry, $ceo, $publiclyTraded);` — 親クラスのコンストラクタも呼び、共通属性を初期化します。
- **L30** `if ($chainId <= 0 || trim($cuisineType) === '') {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L31** `throw new \InvalidArgumentException('Chain ID and cuisine type are required.');` — 不正な入力や状態を例外で拒否します。

### L35–L35：`getChainId`

`getChainId()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L36–L36：`getCuisineType`

`getCuisineType()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L37–L37：`hasDriveThru`

`hasDriveThru()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L38–L38：`getParentCompany`

`getParentCompany()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L39–L40：`getNumberOfLocations`

`count($this->restaurantLocations)` から実際の店舗件数を導出します。店舗追加後も古いカウントが残る状態を避けられます。

### L41–L42：`getRestaurantLocations`

`getRestaurantLocations()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L43–L47：`addLocation`

`RestaurantLocation` 型の店舗をチェーンの配列に追加します。配列への追加後、`getNumberOfLocations()` は自動的に1増えます。別の件数プロパティを同期する必要がありません。

### L48–L63：`RandomGenerator`

まず Company::RandomGenerator() で会社の値を揃え、getterで取得した属性を `new self(...)` へ渡します。その後2〜4回Locationを生成し `addLocation` します。処理をまとめたファクトリーであり、ページ表示自体はしません。

- **L51** `$chain = new self(` — このクラス自身のオブジェクトを新規に生成します。
- **L55** `random_int(100, 99999), RandomData::cuisine(), random_int(0, 1) === 1,` — 生成範囲内で整数乱数を作成しています。
- **L56** `random_int(0, 3) === 0 ? RandomData::companyName() : 'Independent',` — 生成範囲内で整数乱数を作成しています。
- **L58** `for ($i = 0, $count = random_int(2, 4); $i < $count; $i++) {` — 生成範囲内で整数乱数を作成しています。
- **L61** `return $chain;` — メソッドの処理結果を呼び出し元へ返します。

### L64–L73：`toString`

プレーンテキスト形式を返します。モデルのデータを読める形式に整形し、子オブジェクトを持つ場合はそれぞれの `toString()` を順番に呼び出して含めます。返り値はオブジェクトではなくPHPの `string` です。

- **L67** `return sprintf(` — メソッドの処理結果を呼び出し元へ返します。
- **L70** `$this->hasDriveThru ? 'Yes' : 'No', $this->parentCompany, parent::toString(), implode("\n", $locations),` — 前後のデータの受け渡しと返り値の型を確認してください。

### L74–L90：`toHTML`

Chainの`<section>` と情報カードを作り、`foreach ($this->restaurantLocations ...)` で店舗HTMLを連結します。最後に `parent::toHTML()` を呼び、Companyに定義した8属性を再実装せず出力することが設計上の要点です。

- **L76** `$html = '<section class="chain"><h2>Restaurant Chain ' . Html::escape($this->getName()) . '</h2>';` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L79** `. ' · Cuisine: ' . Html::escape($this->cuisineType)` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L82** `. ' · Parent: ' . Html::escape($this->parentCompany) . '</div>';` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L83** `foreach ($this->restaurantLocations as $location) {` — 配列要素を順番に処理します。
- **L86** `$html .= '<details class="company-details"><summary>Company Details</summary><div class="company-body">';` — ブラウザーの標準アコーディオンUIを構築します。
- **L87** `$html .= parent::toHTML();` — 親Companyが持つ会社情報の描画ロジックを再利用します。
- **L88** `return $html . '</div></details></div></div></section>';` — メソッドの処理結果を呼び出し元へ返します。

### L91–L105：`toMarkdown`

Markdown用の文字列を返します。見出しやリストなどの記法を使い、可変の値は `Html::markdown()` で区切り文字をエスケープします。HTMLとは異なりタグではなくMarkdown構文を出力します。

- **L93** `$out = '## Restaurant Chain ' . Html::markdown($this->getName()) . "\n\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L95** `$out .= '- Cuisine: ' . Html::markdown($this->cuisineType) . "\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L98** `$out .= '- Parent company: ' . Html::markdown($this->parentCompany) . "\n\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L99** `$out .= parent::toMarkdown() . "\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L100** `foreach ($this->restaurantLocations as $location) {` — 配列要素を順番に処理します。
- **L103** `return $out;` — メソッドの処理結果を呼び出し元へ返します。

## 4. 実行トレース（値と呼び出し順）

Chain生成直後は店舗数0 → `addLocation($l)` により1 → `getNumberOfLocations()` は1 → `toHTML()` がChain概要、Locationの表示、`parent::toHTML()` から会社詳細を連結します。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。
- `parent::__construct` と `parent::toHTML` の役割の違いを説明できること。
- `getNumberOfLocations()` が保存フィールドではなく `count()` である理由を理解すること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: 7238e208fa5858276389e8f1b57459ffc838933662a0d7e9bab69a4c93e37211 -->
