# RestaurantLocation.php — ソース全文と逐次解説

**原本:** `src/Models/RestaurantLocation.php`

## 1. ファイル概要

- **役割:** 店舗の所在地・営業状態と従業員一覧を保持し、アコーディオンHTMLを出力します。
- **入力と出力:** 入力: 店舗属性、Employeeオブジェクト。出力: RestaurantLocationとHTML/Markdown/文字列。
- **主要メソッド・処理:** __construct()、addEmployee()、RandomGenerator()、toHTML()
- **前提知識:** has-a、配列、foreach、details/summary
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

class RestaurantLocation implements FileConvertible
{
    /** @var list<Employee> */
    private array $employees = [];

    public function __construct(
        private string $name,
        private string $address,
        private string $city,
        private string $state,
        private string $postalCode,
        private bool $isOpen,
    ) {
        if (trim($name) === '' || trim($address) === '' || trim($city) === '' || trim($state) === '' || trim($postalCode) === '') {
            throw new \InvalidArgumentException('Restaurant location fields may not be empty.');
        }
    }

    public function getName(): string { return $this->name; }
    public function getAddress(): string { return $this->address; }
    public function getCity(): string { return $this->city; }
    public function getState(): string { return $this->state; }
    public function getPostalCode(): string { return $this->postalCode; }
    public function isOpen(): bool { return $this->isOpen; }
    /** @return list<Employee> */
    public function getEmployees(): array { return $this->employees; }

    public function addEmployee(Employee $employee): void
    {
        foreach ($this->employees as $existing) {
            if ($existing->getId() === $employee->getId()) {
                throw new \InvalidArgumentException('Duplicate employee ID at this location.');
            }
        }
        $this->employees[] = $employee;
    }

    public static function RandomGenerator(): self
    {
        [$city, $state] = RandomData::location();
        $location = new self(
            RandomData::restaurantName(), RandomData::streetAddress(), $city, $state,
            RandomData::postalCode(), random_int(0, 9) !== 0,
        );
        for ($i = 0, $count = random_int(2, 5); $i < $count; $i++) {
            $employee = Employee::RandomGenerator();
            // Extremely rare random ID collision: retry instead of failing mock generation.
            $ids = array_map(static fn(Employee $existing): int => $existing->getId(), $location->employees);
            while (in_array($employee->getId(), $ids, true)) {
                $employee = Employee::RandomGenerator();
            }
            $location->addEmployee($employee);
        }
        return $location;
    }

    public function toString(): string
    {
        $employeeLines = array_map(static fn(Employee $employee): string => '  ' . $employee->toString(), $this->employees);
        return sprintf(
            "Location: %s; Address: %s, %s, %s %s; Open: %s\nEmployees:\n%s",
            $this->name, $this->address, $this->city, $this->state, $this->postalCode,
            $this->isOpen ? 'Yes' : 'No', implode("\n", $employeeLines),
        );
    }

    public function toHTML(): string
    {
        $name = Html::escape($this->name);
        $address = Html::escape($this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postalCode);
        $status = $this->isOpen ? 'Open' : 'Closed';
        $html = '<details class="location"><summary>' . $name . '</summary><div class="location-body">';
        $html .= '<p><strong>Address:</strong> ' . $address . '</p>';
        $html .= '<p><strong>Status:</strong> ' . $status . '</p>';
        $html .= '<h4>Employees (' . count($this->employees) . ')</h4><ul class="employee-list">';
        foreach ($this->employees as $employee) {
            $html .= $employee->toHTML();
        }
        return $html . '</ul></div></details>';
    }

    public function toMarkdown(): string
    {
        $out = '### ' . Html::markdown($this->name) . "\n\n";
        $out .= '- Address: ' . Html::markdown($this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postalCode) . "\n";
        $out .= '- Open: ' . ($this->isOpen ? 'Yes' : 'No') . "\n";
        $out .= '- Employees (' . count($this->employees) . "): \n";
        foreach ($this->employees as $employee) {
            $out .= '  ' . $employee->toMarkdown() . "\n";
        }
        return $out . "\n";
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L14：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。

- **L13** `private array $employees = [];` — インスタンス状態または内部定数を宣言しています。外部からの直接変更を制限し、クラスの責務を明確にします。

### L15–L27：`__construct`

オブジェクト生成時の初期化です。コンストラクタ引数がプロパティと対応し、入力が不正なときは例外を投げ、壊れた状態のインスタンスが生成されないようにします。型宣言と業務バリデーションは役割が異なります。

- **L23** `if (trim($name) === '' || trim($address) === '' || trim($city) === '' || trim($state) === '' || trim($postalCode) === '') {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L24** `throw new \InvalidArgumentException('Restaurant location fields may not be empty.');` — 不正な入力や状態を例外で拒否します。

### L28–L28：`getName`

`getName()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L29–L29：`getAddress`

`getAddress()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L30–L30：`getCity`

`getCity()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L31–L31：`getState`

`getState()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L32–L32：`getPostalCode`

`getPostalCode()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L33–L34：`isOpen`

`isOpen()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L35–L36：`getEmployees`

`getEmployees()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L37–L46：`addEmployee`

引数に `Employee` 型を要求します。配列内の従業員IDを確認し、同じ店舗で重複すると `InvalidArgumentException` を投げ、重複しなければ `$this->employees[]` で末尾に追加します。

- **L39** `foreach ($this->employees as $existing) {` — 配列要素を順番に処理します。
- **L40** `if ($existing->getId() === $employee->getId()) {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L41** `throw new \InvalidArgumentException('Duplicate employee ID at this location.');` — 不正な入力や状態を例外で拒否します。

### L47–L65：`RandomGenerator`

`RandomData::location()` は `[都市, 州]` の配列を返し、分割代入して住所を作成します。2〜5回Employeeを作り、ランダムなIDが偶然重複した場合は再抽選してから追加します。

- **L50** `$location = new self(` — このクラス自身のオブジェクトを新規に生成します。
- **L52** `RandomData::postalCode(), random_int(0, 9) !== 0,` — 生成範囲内で整数乱数を作成しています。
- **L54** `for ($i = 0, $count = random_int(2, 5); $i < $count; $i++) {` — 生成範囲内で整数乱数を作成しています。
- **L63** `return $location;` — メソッドの処理結果を呼び出し元へ返します。

### L66–L75：`toString`

プレーンテキスト形式を返します。モデルのデータを読める形式に整形し、子オブジェクトを持つ場合はそれぞれの `toString()` を順番に呼び出して含めます。返り値はオブジェクトではなくPHPの `string` です。

- **L69** `return sprintf(` — メソッドの処理結果を呼び出し元へ返します。

### L76–L90：`toHTML`

`<details>`/`<summary>` でクリック開閉式の店舗カードを作ります。住所と店舗名は `Html::escape` でエスケープし、従業員のリストに対して `Employee::toHTML()` を呼び出します。

- **L78** `$name = Html::escape($this->name);` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L79** `$address = Html::escape($this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postalCode);` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。
- **L81** `$html = '<details class="location"><summary>' . $name . '</summary><div class="location-body">';` — ブラウザーの標準アコーディオンUIを構築します。
- **L84** `$html .= '<h4>Employees (' . count($this->employees) . ')</h4><ul class="employee-list">';` — 配列の実際の要素数を調べています。
- **L85** `foreach ($this->employees as $employee) {` — 配列要素を順番に処理します。
- **L88** `return $html . '</ul></div></details>';` — メソッドの処理結果を呼び出し元へ返します。

### L91–L102：`toMarkdown`

Markdown用の文字列を返します。見出しやリストなどの記法を使い、可変の値は `Html::markdown()` で区切り文字をエスケープします。HTMLとは異なりタグではなくMarkdown構文を出力します。

- **L93** `$out = '### ' . Html::markdown($this->name) . "\n\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L94** `$out .= '- Address: ' . Html::markdown($this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postalCode) . "\n";` — 前後のデータの受け渡しと返り値の型を確認してください。
- **L96** `$out .= '- Employees (' . count($this->employees) . "): \n";` — 配列の実際の要素数を調べています。
- **L97** `foreach ($this->employees as $employee) {` — 配列要素を順番に処理します。
- **L100** `return $out . "\n";` — メソッドの処理結果を呼び出し元へ返します。

## 4. 実行トレース（値と呼び出し順）

空のLocationを作る → `addEmployee($e)` で配列が0→1件になる → `toHTML()` が店舗名を `<summary>` に、住所と営業状態を本文に、従業員の `toHTML()` を `<ul>` に格納します。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。
- `addEmployee` の重複ID検証とランダム生成の再抽選の関係を把握すること。
- `<details>` はJavaScriptなしで開閉することを確認すること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: effb1bfa753b6cb5c8b4256554b74df7af5b7ebe3002164491faf1b4a15b8ff2 -->
