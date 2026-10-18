# 02 KEY CONCEPTS — 用語とコードの解説

## 【必須】class・constructor・new

`class`は同種のオブジェクトの設計図。`new RestaurantChain(data)`はその設計図をもとに1つの実体を生成する。`constructor`は生成時に呼ばれる初期化処理。今回の `RestaurantChain` は企業共通属性を `super({...})` で親に渡し、店舗配列など独自属性を追加する。`super()`を省略した派生クラスのコンストラクタでは`this`を使用できない。実務ではドメインデータの表現に使用する。

## 【必須】継承とポリモーフィズム

`Employee extends User`は従業員がUser属性を再利用するという意味。`Employee#toString`は `super.toString()` で基本情報を使い、その後に役職等を追加。`x.toHTML()`という共通の呼び方で、`x`の実際のクラスに応じたHTMLを返せるのがポリモーフィズム。すべてのクラスを巨大な条件分岐で判別しなくてよい。

## 【必須】共通インターフェース（JavaScriptでの表現）

JavaScriptにはTypeScriptの`interface`キーワードがない。この実装は `class FileConvertible` を共通基底クラスとし、`toString`、`toHTML`、`toMarkdown` の既定実装が `throw` することによって実装忘れを検出する。**コンパイル時の厳密な保証はない**。必要ならTypeScriptの静的型検査や抽象化ライブラリを選ぶ。

## 【重要】配列とmap/join

`locations.map(l => l.toHTML()).join('')` は各店舗のHTML文字列を取得し、区切りなしで連結する。`map`は元配列の要素を変換した新しい配列を返し、`join`は文字列に結合する。`map`で作られる配列自体が元の `locations` を変更するわけではない。

## 【重要】HTTPとサーバーサイドレンダリング

`http.createServer`に渡した関数はHTTPリクエストごとに呼ばれる。`res.writeHead(200, ...)`でステータスとContent-Typeを指定し、`res.end(html)`でレスポンスを終了する。`/`はHTML、`/style.css`はCSS、`/export.md`はMarkdownを返す。404はリソース未発見、405は許可されないメソッド、500はサーバー内部エラーを意味する。

## 【重要】エスケープとHTMLインジェクション

`escapeHTML('<script>')` は `&lt;script&gt;` を返し、文字列がHTMLのタグとして解釈されないようにする。ユーザー入力をHTMLに埋め込むときは必須。今回のモックは自動生成値だが、実務でも同様の考慮が必要。URLを外部入力から受け取る場合はHTMLエスケープ**に加えて**URLスキームの検証が必要。今回はURLを固定値にしている。

## 【重要】ランダムデータとモック

`randomInt(min,max)`は両端を含む疑似乱数整数を返す。`pick()`は候補配列からランダムに1つ選ぶ。`Math.random()`は検証用の架空データに使っており、暗号・認証トークンには使用しない。再現可能なテストが必要なら固定シードの疑似乱数生成器や乱数関数の注入を導入する。

## 【参考】HTMLのdetailsとsummary

`<details><summary>店舗名</summary>説明</details>` はクリックで開閉できる。今回は追加のフロントエンドJSを使わない。UI試作やFAQ表示によく用いられる。

## よくある間違い

- `super()`より前に `this` を使用する。
- クラスを `extends` しただけで、子クラス固有メソッドまで自動実装されると思う。
- `map()`と`forEach()`を同じと考える（前者は新しい配列を返す）。
- `toHTML()`がHTMLを自動表示すると誤解する（実際は**文字列を返すだけ**）。
- `toString`を `Object.prototype.toString` と混同する（今回は意図的に上書き）。
- HTMLエスケープ済みだからあらゆるURLを安全とみなす。
