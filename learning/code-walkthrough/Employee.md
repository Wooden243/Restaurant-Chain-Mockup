# Employee.php — ソース全文と逐次解説

**原本:** `src/Models/Employee.php`

## 1. ファイル概要

- **役割:** Userを継承し、職種、給与、勤務開始日、役割配列を追加します。
- **入力と出力:** 入力: Userの属性と職名、給与、DateTimeImmutable、役割リスト。出力: Employeeと3形式の文字列。
- **主要メソッド・処理:** __construct()、RandomGenerator()、toString()、toHTML()、toMarkdown()
- **前提知識:** 継承、parent、DateTimeImmutable、number_format
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```php
<?php
declare(strict_types=1);

namespace RestaurantMockup\Models;

use DateTimeImmutable;
use RestaurantMockup\Contracts\FileConvertible;
use RestaurantMockup\Support\Html;
use RestaurantMockup\Support\RandomData;

class Employee extends User implements FileConvertible
{
    /** @param list<string> $roles */
    public function __construct(
        int $id,
        string $name,
        string $email,
        private string $jobTitle,
        private float $salary,
        private DateTimeImmutable $startDate,
        private array $roles,
    ) {
        parent::__construct($id, $name, $email);
        if (trim($jobTitle) === '' || !is_finite($salary) || $salary < 0) {
            throw new \InvalidArgumentException('Job title and nonnegative finite salary are required.');
        }
        foreach ($roles as $role) {
            if (!is_string($role) || trim($role) === '') {
                throw new \InvalidArgumentException('Each employee role must be a nonempty string.');
            }
        }
    }

    public function getJobTitle(): string { return $this->jobTitle; }
    public function getSalary(): float { return $this->salary; }
    public function getStartDate(): DateTimeImmutable { return $this->startDate; }
    /** @return list<string> */
    public function getRoles(): array { return $this->roles; }

    public static function RandomGenerator(): self
    {
        $id = random_int(1000, 999999);
        $title = RandomData::jobTitle();
        return new self(
            $id, RandomData::fullName(), "employee{$id}@example.com",
            $title, (float) random_int(32000, 95000), RandomData::startDate(),
            [$title, 'Guest service'],
        );
    }

    public function toString(): string
    {
        return sprintf(
            'ID: %d; Job Title: %s; Name: %s; Salary: $%s; Start Date: %s; Roles: %s',
            $this->getId(), $this->jobTitle, $this->getName(),
            number_format($this->salary, 2), $this->startDate->format('Y-m-d'), implode(', ', $this->roles),
        );
    }

    public function toHTML(): string
    {
        return '<li class="employee-row">' . Html::escape($this->toString()) . '</li>';
    }

    public function toMarkdown(): string
    {
        return '- ' . Html::markdown($this->toString());
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L13：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。


### L14–L33：`__construct`

Userへの `parent::__construct($id,$name,$email)` を先に呼び、本人情報とメール検証を再利用します。次に職種の空文字、負の給与、無限大の給与、空の役割を拒否します。`DateTimeImmutable` を型指定しているため生の日時文字列を直接渡すとTypeErrorになります。

- **L23** `parent::__construct($id, $name, $email);` — 親クラスのコンストラクタも呼び、共通属性を初期化します。
- **L24** `if (trim($jobTitle) === '' || !is_finite($salary) || $salary < 0) {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L25** `throw new \InvalidArgumentException('Job title and nonnegative finite salary are required.');` — 不正な入力や状態を例外で拒否します。
- **L27** `foreach ($roles as $role) {` — 配列要素を順番に処理します。
- **L28** `if (!is_string($role) || trim($role) === '') {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L29** `throw new \InvalidArgumentException('Each employee role must be a nonempty string.');` — 不正な入力や状態を例外で拒否します。

### L34–L34：`getJobTitle`

`getJobTitle()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L35–L35：`getSalary`

`getSalary()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L36–L37：`getStartDate`

`getStartDate()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L38–L39：`getRoles`

`getRoles()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L40–L50：`RandomGenerator`

`static` なファクトリーメソッドです。まだインスタンスがない状態で `クラス名::RandomGenerator()` を呼び出せます。`RandomData` の値を使用し、必要に応じて子オブジェクトを作った上で `self` のインスタンスを返します。表示処理はこのメソッドに含めません。

- **L42** `$id = random_int(1000, 999999);` — 生成範囲内で整数乱数を作成しています。
- **L44** `return new self(` — このクラス自身のオブジェクトを新規に生成します。
- **L46** `$title, (float) random_int(32000, 95000), RandomData::startDate(),` — 生成範囲内で整数乱数を作成しています。

### L51–L59：`toString`

`sprintf` で文字列を組み立て、`number_format($this->salary,2)` で金額を2桁に整え、`$this->startDate->format(...)` で日付を整え、`implode` で複数の役割を区切り文字で連結します。

- **L53** `return sprintf(` — メソッドの処理結果を呼び出し元へ返します。

### L60–L64：`toHTML`

ブラウザー用のHTML断片を文字列として返します。静的なタグはコード内で組み立て、可変値は `Html::escape()` で無害化してから入れます。包含するオブジェクトがある場合はその `toHTML()` を呼び、階層化したHTMLを作ります。

- **L62** `return '<li class="employee-row">' . Html::escape($this->toString()) . '</li>';` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。

### L65–L69：`toMarkdown`

Markdown用の文字列を返します。見出しやリストなどの記法を使い、可変の値は `Html::markdown()` で区切り文字をエスケープします。HTMLとは異なりタグではなくMarkdown構文を出力します。

- **L67** `return '- ' . Html::markdown($this->toString());` — メソッドの処理結果を呼び出し元へ返します。

## 4. 実行トレース（値と呼び出し順）

`new Employee(2,"Alex Worker","alex@example.com","Chef",52000.5,new DateTimeImmutable("2023-05-21"),["Chef","Trainer"])` → User初期化 → 給与と役割を検証 → `toString()` が `Salary: $52,000.50` および `Start Date: 2023-05-21` を含む文字列を作る → `toHTML()` が `<li>` を作る。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。
- Userの共通属性は継承し、Employeeだけに必要な値を追加する理由を理解すること。
- 給与の `float` と表示文字列（通貨書式）は異なることを認識すること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: 21420f3c378a0ee8517e5adf1579ddb4460833834c147bb00eefa3a2a5b7df64 -->
