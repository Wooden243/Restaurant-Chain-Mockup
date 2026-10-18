# 00 START HERE — Restaurant Chain Mockup

## 3分で把握する

Webブラウザにアクセスすると、Node.jsのHTTPサーバーが毎回3つの架空チェーンを生成してHTMLで返す。チェーンは複数の店舗を持ち、店舗は複数の従業員を持つ。詳細はHTML標準の`<details>`タグで開閉できる。

## 主な技術と必要度

| 技術 | SWE | AI/ML | FE | 説明 |
|---|---|---|---|---|
| クラス・`extends`・`super` | 必須 | 重要 | 重要 | データと振る舞いの再利用 |
| コンポジション（親が子オブジェクトを保持） | 必須 | 重要 | 重要 | Company→locations→employees の構造 |
| `toHTML`とエスケープ | 重要 | 参考 | 必須 | 文字列を安全に表示 |
| `http.createServer` | 必須 | 重要 | 重要 | HTTPリクエストの処理 |
| ランダムデータ生成 | 重要 | 重要 | 重要 | UI・ロジックを実物データなしに検証 |
| 自動テスト | 必須 | 必須 | 必須 | 期待動作を再確認 |

## 最重要コード

1. `src/models.js` の `RestaurantChain extends Company`、`Employee extends User`
2. `RestaurantChain.randomGenerator()` が店舗を複数生成し、`RestaurantLocation.randomGenerator()` が従業員を複数生成する部分
3. `RestaurantChain.toHTML()` が店舗の `toHTML()` を呼び出す構造
4. `src/server.js` のルーティングとHTTP応答

## 推奨順序

1. `README.md` で起動する。
2. `learning/01_ARCHITECTURE.md` で全体構造を見る。
3. `learning/03_EXECUTION_FLOW.md` でブラウザ→サーバー→HTMLの流れを追う。
4. `learning/code-walkthrough/models.md` を読み、疑問点は `02_KEY_CONCEPTS.md` を参照する。
5. テストと `05_REVIEW.md` に進む。

## 15分・30分・60分

**15分：** アプリを起動し、全体構造・継承・オブジェクトの包含関係を見る。

**30分：** ランダム生成→HTML生成→HTTP送信の3段階をソースで追い、テストを実行する。

**60分：** 各クラスのコンストラクタと3つの変換メソッドを確認し、レビュー演習を解く。

## 今は暗記しなくてよいもの

HTML/CSSの細かな装飾値、ランダムな名前候補の配列、正規表現の代替となるHTMLエスケープ表など。重要なのは**オブジェクトのつながりと変換責務の分担**である。
