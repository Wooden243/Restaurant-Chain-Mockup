# styles.css — ソース全文と逐次解説

**原本:** `assets/styles.css`

## 1. ファイル概要

- **役割:** HTMLのレイアウト、アコーディオンのスタイル、レスポンシブ表示を定義します。
- **入力と出力:** 入力: ブラウザーのスタイル計算。出力: 視覚的なデザイン。
- **主要メソッド・処理:** 主要セレクタ: .container、.chain-card、.location、.company-facts、@media
- **前提知識:** CSSセレクタ、ボックスモデル、メディアクエリ
- **他ファイルとの関係:** 詳細は `learning/01_ARCHITECTURE.md` の依存図を参照してください。

## 2. ソースコード全文

> このブロックは実装ファイルとバイト内容（末尾改行を除く）が一致しています。変更した場合は逐次解説も再生成・更新してください。

```css
:root{color-scheme:light;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#212b36;background:#f5f7fb;font-size:15px}
*{box-sizing:border-box}body{margin:0}.container{max-width:960px;margin:0 auto;padding:34px 24px 60px}.site-header{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:25px 30px;margin-bottom:28px}h1{font-size:1.9rem;margin:0 0 10px}h2{font-size:1.45rem;margin:38px 0 13px}h4{margin:20px 0 9px}.site-header p{margin:8px 0;color:#485669}.site-header .hint{font-size:.88rem;color:#64748b}.chain-card{background:#fff;border:1px solid #dce4ed;border-radius:8px;overflow:hidden;box-shadow:0 2px 7px #1831500a}.panel-heading{background:#eef1f6;padding:13px 17px;font-weight:700;border-bottom:1px solid #dce4ed}.chain-content{padding:13px}.chain-metadata{padding:8px 9px 15px;color:#4b5563;line-height:1.6;font-size:.88rem}.location,.company-details{border:1px solid #d8e3f0;border-radius:4px;margin-bottom:9px;overflow:hidden}.location>summary,.company-details>summary{cursor:pointer;color:#1766aa;background:#e8f1fd;padding:11px 14px;font-weight:650;list-style-position:inside}.location[open]>summary,.company-details[open]>summary{border-bottom:1px solid #d5e5f6}.location>summary:hover,.company-details>summary:hover{background:#d9eaff}.location-body,.company-body{padding:12px 17px}.location-body p{margin:6px 0 11px}.employee-list{list-style:none;padding:0;margin:0;border:1px solid #e1e5ec;border-radius:5px}.employee-row{padding:9px 11px;font-size:.9rem;line-height:1.5;border-bottom:1px solid #e1e5ec;overflow-wrap:anywhere}.employee-row:last-child{border-bottom:0}.company-facts{margin:0}.company-facts>div{display:grid;grid-template-columns:165px 1fr;gap:12px;padding:9px 0;border-bottom:1px solid #edf0f3}.company-facts>div:last-child{border-bottom:0}dt{font-weight:650}dd{margin:0;overflow-wrap:anywhere}a{color:#1766aa}footer{text-align:center;color:#64748b;padding:35px 0 10px;font-size:.85rem}summary:focus-visible,a:focus-visible{outline:3px solid #2563eb;outline-offset:2px}@media(max-width:600px){.container{padding:16px 10px}.site-header{padding:18px}h1{font-size:1.5rem}h2{font-size:1.2rem}.company-facts>div{grid-template-columns:1fr;gap:3px}}
```

## 3. 行・コードブロック単位の逐次解説

このCSSは小規模なスタイルを1行に圧縮しています。以下は**セレクタ・機能ごと**の逐次解説です。原本に対する編集は `assets/styles.css` を行ってください。

1. `:root` — ページの共通フォント・配色・背景とベース文字サイズ。
2. `*` と `body` — ボックス寸法の計算方式、ブラウザー標準マージンの初期化。
3. `.container` と `.site-header` — 横幅制限、中央配置、ヘッダの背景と余白。
4. `h1`、`h2`、`h4` — 見出しの階層とサイズを設定。
5. `.chain-card`、`.panel-heading` — 店舗情報をカードとして囲み、見出し色を分ける。
6. `.chain-content`、`.chain-metadata` — 内側の余白とチェーン情報のスタイル。
7. `.location`、`.company-details` — ネイティブの開閉要素の枠を定義。
8. `.location>summary`、`[open]>summary`、`:hover` — 開閉可能な見出しの通常状態・展開状態・ホバー状態。
9. `.employee-list`、`.employee-row` — 従業員ごとの行と区切り線。
10. `.company-facts`、`dt`、`dd` — 会社属性の名前と値をグリッドで配置。
11. `summary:focus-visible` — キーボードでフォーカスした際に位置が見えるよう輪郭を表示。
12. `@media(max-width:600px)` — 画面が狭い場合、会社項目の2列を1列へ変更し余白も縮小。

## 4. 実行トレース（値と呼び出し順）

ブラウザーは `.container` に最大幅を適用 → `.chain-card` を白いカードにする → `.location>summary` が水色の見出しを持つ → 幅600px以下では `@media` により余白や会社情報の列構成を変えます。

## 5. 最重要チェックポイント

- このファイルの責務を別のファイルと区別できること。
- 入力（引数や呼び出し元）を示せること。
- 出力（戻り値やHTML）を示せること。
- 主要なメソッド/処理の呼び出し順を追えること。
- 状態を持つファイルでは、どの配列・プロパティが変更されるか理解すること。
- 表示を行う際は、可変文字列を安全な形式へ変換する理由を理解すること。
- このコードを変更した場合のテスト対象を特定できること。

**確認方法:** ソースを編集したら `php tests/run.php` を再実行し、ブラウザーでも表示を確かめてください。

<!-- source-sha256: 3d630a9df46eb9f0270435861f15df1a6b8480493edaeda3abb1acee4e622e83 -->
