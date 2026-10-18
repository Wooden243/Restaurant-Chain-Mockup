# 05 REVIEW — 演習・模範解答・解説

## Q1 `extends`の意味は？

**答え：** 他のクラスを継承してそのメソッドやプロパティを再利用する。`RestaurantChain extends Company` なら、チェーンは会社の共通情報も持つ。`super(...)`で親のコンストラクタを呼ぶ。

## Q2 `RestaurantChain`と`RestaurantLocation`の関係は継承か？

**答え：** 継承ではなく包含（has-a）。チェーンは店舗の配列を持つ。店舗は会社そのものではないので継承にしない。

## Q3 `map(...).join('')` の役割を説明せよ。

**答え：** 各要素をHTML文字列に変換する `map` と、文字列を区切りなしで連結する `join` の組み合わせ。店舗数に関係なく動的に描画できる。

## Q4 `randomInt(1,4)`の戻り値として0はあり得るか？

**答え：** いいえ。`Math.floor(Math.random()*4)+1` 相当なので整数1〜4が返る。

## Q5 `toHTML`の戻り値は何型？

**答え：** JavaScriptの文字列（string）。DOM要素を直接返すのではなく、サーバーが文字列をHTMLレスポンスに含める。

## Q6 次のコードは何を確認する？

```js
const c = RestaurantChain.randomGenerator();
console.log(c.numberOfLocations === c.restaurantLocations.length);
```

**答え：** チェーンが記録する店舗数と実際の店舗配列の要素数が一致しているかを検証。通常は `true` 。

## Q7 デバッグ：`new RestaurantChain({ ...data, numberOfLocations:3, restaurantLocations:[] })` が失敗する理由

**答え：** コンストラクタが店舗件数不一致を検出して例外を投げるため。配列に3店舗用意するか、件数を0に直す。ただし後者は業務要件で許容されるか検討する。

## Q8 小改修：表示するチェーンを3から5へ変更

**答え：** `src/server.js` の `Array.from({length:3}, ...)` を `length:5` に変更する。`npm test`で回帰テストを行う。単にHTMLへ2件複製すると独立したランダムデータにはならない。

## Q9 `escapeHTML`が必要な理由

**答え：** 特殊文字をHTMLタグ・属性の区切りとして処理させないため。`<` を `&lt;` に変換することで、埋め込む名前の表示文脈を安全にする。

## Q10 `FileConvertible`の実装漏れはどうなる？

**答え：** 今回のJavaScriptではコンパイル時に検出されず、未実装メソッドを呼び出すと例外となる。TypeScriptならインターフェースによる静的検査が可能。
