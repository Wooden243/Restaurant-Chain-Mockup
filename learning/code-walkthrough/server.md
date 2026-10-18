# src/server.js — 完全コードウォークスルー

## ① 役割、入力、出力、依存関係

モデルを生成してHTMLページとHTTP応答を作る。入力はHTTPリクエスト。出力はHTML/CSS/Markdown/テキストのHTTPレスポンス。

## ② ソースコード全文（実装と一致）

```js
'use strict';
const http=require('node:http');
const {RestaurantChain,escapeHTML}=require('./models');
function htmlPage(chains) {return `<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Restaurant Chain Mockup</title><link rel="stylesheet" href="/style.css"></head><body><main><header><h1>Restaurant Chain Mockup</h1><p>Generated restaurant companies, branches, and staff — refresh to regenerate.</p><a class="action" href="/">Generate new data</a></header>${chains.map(c=>c.toHTML()).join('')}<footer>Mock data only. No real businesses or employees are represented.</footer></main></body></html>`;}
const css=`*{box-sizing:border-box}body{font-family:system-ui,-apple-system,sans-serif;color:#263243;background:#f8fafc;margin:0}main{max-width:920px;margin:32px auto;padding:0 18px}header{text-align:center;padding:16px 0 24px}h1{font-size:2rem;margin:0 0 8px}h2{font-size:1.5rem;text-align:center;margin:0 0 18px}h4{margin:12px 0 6px}.action{display:inline-block;background:#2563eb;color:white;padding:10px 15px;border-radius:6px;margin-top:12px;text-decoration:none}.chain{margin:22px 0 35px}.chain-body{border:1px solid #d9e0e8;border-radius:5px;padding:10px;background:white}.company{background:#f2f5f8}.company>summary{background:#eef0f4}.company-info,.location-info{padding:12px;line-height:1.6;overflow-wrap:anywhere}details{border:1px solid #dde3eb;margin:4px 0}summary{cursor:pointer;padding:10px 14px;background:#e9f2ff;color:#245fba;font-weight:600}details[open]>summary{border-bottom:1px solid #dde3eb}ul{list-style:none;padding:0}li{border:1px solid #e5e7eb;padding:8px;font-size:.9rem}footer{color:#64748b;text-align:center;padding:30px}a{color:#245fba}`;
function createServer(){return http.createServer((req,res)=>{try{const pathname=new URL(req.url,'http://localhost').pathname;if(req.method!=='GET'){res.writeHead(405,{'Content-Type':'text/plain','Allow':'GET'});return res.end('Method Not Allowed');}if(pathname==='/style.css'){res.writeHead(200,{'Content-Type':'text/css; charset=utf-8'});return res.end(css);}if(pathname!=='/'&&pathname!=='/export.md'&&pathname!=='/export.txt'){res.writeHead(404,{'Content-Type':'text/plain'});return res.end('Not Found');}const chains=Array.from({length:3},()=>RestaurantChain.randomGenerator());if(pathname==='/export.md'||pathname==='/export.txt'){const md=pathname.endsWith('md');res.writeHead(200,{'Content-Type':md?'text/markdown; charset=utf-8':'text/plain; charset=utf-8'});return res.end(chains.map(c=>md?c.toMarkdown():c.toString()).join('\n\n'));}res.writeHead(200,{'Content-Type':'text/html; charset=utf-8','Cache-Control':'no-store'});res.end(htmlPage(chains));}catch(e){res.writeHead(500,{'Content-Type':'text/plain'});res.end('Internal server error');}});}
if(require.main===module){const port=Number(process.env.PORT||3000);createServer().listen(port,()=>console.log(`Open http://localhost:${port}`));}
module.exports={createServer,htmlPage};
```

## ③ 行・ブロック単位の解説

**L1** `'use strict';`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L2** `const http=require('node:http');`  
CommonJSのモジュールを読み込み、別ファイルまたはNode.js標準モジュールの機能を使う。

**L3** `const {RestaurantChain,escapeHTML}=require('./models');`  
CommonJSのモジュールを読み込み、別ファイルまたはNode.js標準モジュールの機能を使う。

**L4** `function htmlPage(chains) {return ′<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport"`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L5** `const css=′*{box-sizing:border-box}body{font-family:system-ui,-apple-system,sans-serif;color:#263243;background:#f8`  
変数を宣言し、値または関数を束縛する。constは再代入を禁止する。

**L6** `function createServer(){return http.createServer((req,res)=>{try{const pathname=new URL(req.url,'http://localhost')`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L7** `if(require.main===module){const port=Number(process.env.PORT||3000);createServer().listen(port,()=>console.log(′Ope`  
HTTPサーバーを生成し、リクエストを受け取る関数を登録する。

**L8** `module.exports={createServer,htmlPage};`  
他のファイルがrequireで利用するAPIを公開する。

## ④ 実行トレース

- `models.js`: `RestaurantChain.randomGenerator()` → `Company.randomGenerator()` → `RestaurantLocation.randomGenerator()` → `Employee.randomGenerator()` → `RestaurantChain`生成 → `toHTML()` で構造全体を描画。
- `server.js`: GET / → 3チェーン生成 → `htmlPage` → `res.writeHead` → `res.end`。GET /style.cssならCSSだけ送信。
- `models.test.js`: `node --test` が各ケースを起動 → オブジェクト生成 → `assert`で不変条件を検証 → ステータス出力。

## ⑤ 最重要ポイント

1. 入力値の型とコンストラクタ引数を確認する。
2. クラスの継承と配列による包含を区別する。
3. `randomGenerator` は新しいインスタンスを返す。
4. `toHTML` はHTMLを**文字列**として返す。
5. `map` / `join` は配列の各要素の変換・結合を担う。
6. HTMLへ挿入する変数のエスケープを確認する。
7. `return`と`res.end`は用途が違う（関数戻り値とHTTP応答の完了）。
8. 例外・テストが設計の不変条件を支える。
