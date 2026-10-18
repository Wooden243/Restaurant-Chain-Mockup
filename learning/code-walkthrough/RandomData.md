# RandomData.php — ソース全文と逐次解説

**原本:** `src/Support/RandomData.php`

## 1. ファイル概要

- **役割:** 架空の名詞辞書とrandom_intを使い、ランダムな人物・店舗・会社の基本値を生成します。
- **入力と出力:** 入力: 配列または引数なし。出力: 文字列、整数、日付オブジェクト、都市配列。
- **主要メソッド・処理:** pick()、fullName()、location()、startDate()、safeDomain()
- **前提知識:** staticメソッド、private const、random_int、配列
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

namespace RestaurantMockup\Support;

use DateTimeImmutable;

/** Small self-contained fake-data vocabulary; no external Faker dependency. */
final class RandomData
{
    private const FIRST = ['Avery','Jordan','Morgan','Taylor','Riley','Casey','Alex','Jamie','Robin','Drew','Parker','Quinn','Noah','Harper','Emery'];
    private const LAST = ['Reed','Martin','Patel','Kim','Santos','Brooks','Murphy','Chen','Taylor','Rivera','Cooper','Bennett','Lewis','Carter','Hayes'];
    private const CITY_STATES = [
        ['Seattle','WA'], ['Austin','TX'], ['Denver','CO'], ['Portland','OR'],
        ['Chicago','IL'], ['Boston','MA'], ['Phoenix','AZ'], ['Raleigh','NC'],
        ['San Diego','CA'], ['Nashville','TN'], ['Atlanta','GA'], ['Madison','WI'],
    ];
    private const STREET = ['Maple','Oak','Pine','Cedar','Union','Sunset','Broadway','Market','Willow','Lake'];
    private const CUISINES = ['Italian','Japanese','Mexican','American','Mediterranean','Thai','Indian'];
    private const COMPANY_PARTS = ['Harbor','Silver','Maple','Bluebird','Summit','River','Evergreen','Golden','Clover','Northstar'];
    private const JOBS = ['Cashier','Chef','Server','Shift Manager','Host','Line Cook','General Manager'];
    private const INDUSTRIES = ['Food & Beverage','Hospitality','Restaurant Operations'];

    public static function pick(array $values): mixed
    {
        if ($values === []) {
            throw new \InvalidArgumentException('Cannot pick from an empty array.');
        }
        return $values[random_int(0, count($values) - 1)];
    }

    public static function fullName(): string
    {
        return self::pick(self::FIRST) . ' ' . self::pick(self::LAST);
    }

    public static function companyName(): string
    {
        return self::pick(self::COMPANY_PARTS) . ' ' . self::pick(['Hospitality Group', 'Foods Inc.', 'Dining Co.', 'Restaurants LLC']);
    }

    public static function restaurantName(): string
    {
        return self::pick(self::COMPANY_PARTS) . ' ' . self::pick(['Kitchen','Bistro','Grill','Table','Cafe','Corner']);
    }

    public static function location(): array
    {
        return self::pick(self::CITY_STATES);
    }

    public static function streetAddress(): string
    {
        return random_int(10, 9800) . ' ' . self::pick(self::STREET) . ' ' . self::pick(['St','Ave','Blvd','Road']);
    }

    public static function postalCode(): string
    {
        return str_pad((string) random_int(1000, 99999), 5, '0', STR_PAD_LEFT);
    }

    public static function phone(): string
    {
        return sprintf('(%03d) %03d-%04d', random_int(201, 989), random_int(200, 999), random_int(0, 9999));
    }

    public static function cuisine(): string
    {
        return self::pick(self::CUISINES);
    }

    public static function jobTitle(): string
    {
        return self::pick(self::JOBS);
    }

    public static function industry(): string
    {
        return self::pick(self::INDUSTRIES);
    }

    public static function startDate(): DateTimeImmutable
    {
        $yearsAgo = random_int(0, 10);
        $daysAgo = random_int(0, 364);
        return (new DateTimeImmutable('today'))->modify("-{$yearsAgo} years")->modify("-{$daysAgo} days");
    }

    public static function yearFounded(): int
    {
        return random_int(1980, (int) date('Y'));
    }

    public static function safeDomain(string $name): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name)) ?? 'restaurant');
        return 'https://' . trim($slug, '-') . '.example.com';
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L23：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。

- **L11** `private const FIRST = ['Avery','Jordan','Morgan','Taylor','Riley','Casey','Alex','Jamie','Robin','Drew','Parker','Quinn','Noah','Harper','Emery']` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L12** `private const LAST = ['Reed','Martin','Patel','Kim','Santos','Brooks','Murphy','Chen','Taylor','Rivera','Cooper','Bennett','Lewis','Carter','Haye` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L13** `private const CITY_STATES = [` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L18** `private const STREET = ['Maple','Oak','Pine','Cedar','Union','Sunset','Broadway','Market','Willow','Lake'];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L19** `private const CUISINES = ['Italian','Japanese','Mexican','American','Mediterranean','Thai','Indian'];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L20** `private const COMPANY_PARTS = ['Harbor','Silver','Maple','Bluebird','Summit','River','Evergreen','Golden','Clover','Northstar'];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L21** `private const JOBS = ['Cashier','Chef','Server','Shift Manager','Host','Line Cook','General Manager'];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。
- **L22** `private const INDUSTRIES = ['Food & Beverage','Hospitality','Restaurant Operations'];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。

### L24–L31：`pick`

与えられた配列の要素を乱数の添字で1つ取得します。配列が空だと有効な添字がないため例外にします。ほかのランダム生成関数はこの共通処理を再利用します。

### L32–L36：`fullName`

`fullName()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L37–L41：`companyName`

`companyName()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L42–L46：`restaurantName`

`restaurantName()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L47–L51：`location`

`location()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L52–L56：`streetAddress`

`streetAddress()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L57–L61：`postalCode`

`postalCode()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L62–L66：`phone`

`phone()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L67–L71：`cuisine`

`cuisine()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L72–L76：`jobTitle`

`jobTitle()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L77–L81：`industry`

`industry()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L82–L88：`startDate`

現在から年単位・日単位で過去へずらした `DateTimeImmutable` を返します。`modify` は元のオブジェクトを変更せず新しい日付オブジェクトを返します。

- **L84** `$yearsAgo = random_int(0, 10);` — 生成範囲内で整数乱数を作成しています。
- **L85** `$daysAgo = random_int(0, 364);` — 生成範囲内で整数乱数を作成しています。
- **L86** `return (new DateTimeImmutable('today'))->modify("-{$yearsAgo} years")->modify("-{$daysAgo} days");` — メソッドの処理結果を呼び出し元へ返します。

### L89–L93：`yearFounded`

`yearFounded()` はランダムデータ生成の小さな部品です。`pick`、`random_int`、`sprintf` 等を組み合わせ、ドメインモデルが必要とする属性を返します。実データではなくテストと画面デモのための値です。

### L94–L99：`safeDomain`

会社名から英数字とハイフンのslugを作り、予約された `example.com` のサブドメインを作成します。生成したURLは実際の販売サイトとは無関係です。

- **L97** `return 'https://' . trim($slug, '-') . '.example.com';` — メソッドの処理結果を呼び出し元へ返します。

## 4. 実行トレース（値と呼び出し順）

`RandomData::fullName()` は辞書から名と姓を独立に1つずつ抽選して結合します。`RandomData::location()` は都市と州のペアを返し、`RestaurantLocation::RandomGenerator()` が二変数に分解します。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: 1cee1bde7353802c18cd6556d927ddd4b2916cd79b829b64890034155dc014f0 -->
