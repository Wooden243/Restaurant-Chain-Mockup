# 04 DEBUGGING — 自力で調べる手順

## `node: command not found`

Node.jsが未導入またはPATHが通っていない。WSL Ubuntuで `node --version` を実行し確認。Node.js 18以上を導入する。

## `EADDRINUSE: address already in use :::3000`

3000番ポートが使用中。既存サーバーを`Ctrl+C`で終了するか、`PORT=8080 npm start`で別ポートを使う。

## `Cannot find module`

実行ディレクトリとインポートの相対パスを確認。`src/server.js` の `require('./models')` は同じ`src`ディレクトリを参照する。

## 画面が真っ白、または404

URLが `http://localhost:3000/` であるか確認。サーバーターミナルに `Open http://localhost:3000` と表示されているか確認。WSL環境によってはポート転送設定が必要。

## 構文エラー

`node --check src/models.js` と `node --check src/server.js` を実行。エラー位置の前後のカッコ、引用符、カンマを確認。

## CSSが適用されない

開発者ツールのNetworkで `/style.css` が200、Content-Typeがtext/cssか確認。URLに誤りがないか確認。

## ランダム値のためテストが不安定

テストでは固有名詞などの固定値を仮定しない。`numberOfLocations === restaurantLocations.length` など不変条件を確認する。再現性が必要な場合は乱数源を関数に注入する。

## 調査の順序

1. 再現手順を固定する。
2. `npm test`で失敗箇所を確認。
3. `console.log`で `RestaurantChain.randomGenerator()` の返り値を確認。
4. `toHTML`の返り値とブラウザのDOMを比較。
5. Network/ConsoleのHTTPステータスとエラーを見る。
6. 修正したら再度`npm test`と手動再現確認を行う。
