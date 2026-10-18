# User.php — ソース全文と逐次解説

**原本:** `src/Models/User.php`

## 1. ファイル概要

- **役割:** 従業員の共通基底クラス。ID、氏名、電子メールを保持します。
- **入力と出力:** 入力: 正のID、非空名、メールアドレス。出力: Userオブジェクトと3形式の文字列。
- **主要メソッド・処理:** __construct()、RandomGenerator()、toString()、toHTML()、toMarkdown()
- **前提知識:** クラス、private、getter、型、例外
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

class User implements FileConvertible
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
    ) {
        if ($id <= 0 || trim($name) === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('A user needs a positive ID, name and valid email.');
        }
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    public static function RandomGenerator(): self
    {
        $id = random_int(1000, 999999);
        return new self($id, RandomData::fullName(), "user{$id}@example.com");
    }

    public function toString(): string
    {
        return "User ID: {$this->id}; Name: {$this->name}; Email: {$this->email}";
    }

    public function toHTML(): string
    {
        return '<p class="user-line">' . Html::escape($this->toString()) . '</p>';
    }

    public function toMarkdown(): string
    {
        return '- User #' . $this->id . ': ' . Html::markdown($this->name)
            . ' (' . Html::markdown($this->email) . ')';
    }
}
```

## 3. 行・コードブロック単位の逐次解説

### L1–L11：`宣言、名前空間、依存関係、属性`

冒頭の `<?php` はPHPスクリプト開始、`declare(strict_types=1)` は厳格な型チェック、`namespace` はクラス名の衝突回避、`use` は他の名前空間にあるクラスを短い名前で使う指定です。`private` プロパティや定数は外部からの無制限な変更を防ぎます。


### L12–L21：`__construct`

オブジェクト生成時の初期化です。コンストラクタ引数がプロパティと対応し、入力が不正なときは例外を投げ、壊れた状態のインスタンスが生成されないようにします。型宣言と業務バリデーションは役割が異なります。

- **L17** `if ($id <= 0 || trim($name) === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {` — 条件を調べ、処理の分岐や入力検証に用います。
- **L18** `throw new \InvalidArgumentException('A user needs a positive ID, name and valid email.');` — 不正な入力や状態を例外で拒否します。

### L22–L22：`getId`

`getId()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L23–L23：`getName`

`getName()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L24–L25：`getEmail`

`getEmail()` はこのオブジェクトが持つ対応する属性を返すアクセサ（getter）です。直接 `private` フィールドへ触れない外部コードに、安全な読み取りの窓口を提供します。返り値はメソッド宣言末尾の型で保証されます。

### L26–L31：`RandomGenerator`

一意性を絶対保証するID割当ではなく、乱数IDと架空の氏名/メールを作ります。この課題では店舗内の従業員ID重複をLocation側で防ぎます。

- **L28** `$id = random_int(1000, 999999);` — 生成範囲内で整数乱数を作成しています。
- **L29** `return new self($id, RandomData::fullName(), "user{$id}@example.com");` — このクラス自身のオブジェクトを新規に生成します。

### L32–L36：`toString`

プレーンテキスト形式を返します。モデルのデータを読める形式に整形し、子オブジェクトを持つ場合はそれぞれの `toString()` を順番に呼び出して含めます。返り値はオブジェクトではなくPHPの `string` です。

- **L34** `return "User ID: {$this->id}; Name: {$this->name}; Email: {$this->email}";` — メソッドの処理結果を呼び出し元へ返します。

### L37–L41：`toHTML`

ブラウザー用のHTML断片を文字列として返します。静的なタグはコード内で組み立て、可変値は `Html::escape()` で無害化してから入れます。包含するオブジェクトがある場合はその `toHTML()` を呼び、階層化したHTMLを作ります。

- **L39** `return '<p class="user-line">' . Html::escape($this->toString()) . '</p>';` — 動的文字列をHTMLへ安全に埋め込むためエスケープします。

### L42–L47：`toMarkdown`

Markdown用の文字列を返します。見出しやリストなどの記法を使い、可変の値は `Html::markdown()` で区切り文字をエスケープします。HTMLとは異なりタグではなくMarkdown構文を出力します。

- **L44** `return '- User #' . $this->id . ': ' . Html::markdown($this->name)` — メソッドの処理結果を呼び出し元へ返します。
- **L45** `. ' (' . Html::markdown($this->email) . ')';` — 前後のデータの受け渡しと返り値の型を確認してください。

## 4. 実行トレース（値と呼び出し順）

`new User(1,"Avery Reed","a@example.com")` → 正のID・非空名・正しいメールアドレスとして受理 → `$user->getName()` が `Avery Reed` を返す → `toHTML()` はテキストを安全に囲んだ `<p>` を返します。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: e0fd80e10a779fb2407ac2557274d0ca4a61bcd33747a7e847c98366f05bf1ea -->
