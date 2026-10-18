# 03 EXECUTION FLOW — 実行を時系列で追う

1. `npm start` は `node src/server.js` を起動し、3000番ポートで待機する。
2. ブラウザが `GET /` を送信する。
3. サーバーは`new URL(req.url, ...).pathname`が`/`であると確認。
4. `Array.from({length:3}, () => RestaurantChain.randomGenerator())` でチェーンを3つ作る。
5. 1チェーンごとに `Company.randomGenerator()` で会社情報を作成。
6. `n=randomInt(1,4)` で店舗数を決め、`RestaurantLocation.randomGenerator()` を`n`回呼ぶ。
7. 1店舗ごとに2〜5名の `Employee.randomGenerator()` を呼ぶ。
8. `htmlPage` が各 `RestaurantChain.toHTML()` を呼び、さらに各店舗の `toHTML`、従業員の `toHTML` を呼ぶ。
9. `res.end(html)` でブラウザへHTML本文を送信。
10. ブラウザが `GET /style.css` を追加送信し、CSSが適用される。
11. 店舗名をクリックするとブラウザが`details`を開閉する。開閉だけなら新しい通信はない。

## 値の具体例（説明用・ランダム結果ではない）

- `n = 2` → 2件の `RestaurantLocation` が生成される。
- 1店目：`employees.length = 3`、2店目：`employees.length = 4` → 合計7人。
- `numberOfLocations = 2` と `restaurantLocations.length = 2` が一致するので検証を通過。
- `chain.toHTML()`が1つの`<article>`文字列を返す。`htmlPage`が3チェーンのHTMLを連結して1ページにする。

## 状態変化と例外

- HTTPリクエストごとに新しいオブジェクトを生成するため、再読み込みで値が変わる。
- コンストラクタに不正な店舗件数を渡すと`Error('Location count mismatch')`を投げる。
- 存在しないURLなら404、GET以外なら405を返す。
- 外部データベースに書き込まないため、状態はリクエスト間で維持されない。
