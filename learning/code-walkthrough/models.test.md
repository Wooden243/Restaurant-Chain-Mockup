# tests/models.test.js — 完全コードウォークスルー

## ① 役割、入力、出力、依存関係

モデルとHTTPサーバーの期待動作を検証する。入力はテストデータとHTTPリクエスト。出力はテストの成功・失敗。

## ② ソースコード全文（実装と一致）

```js
'use strict';
const {test}=require('node:test');const assert=require('node:assert/strict');const http=require('node:http');
const M=require('../src/models');const {createServer}=require('../src/server');
const types=['User','Employee','Company','RestaurantLocation','RestaurantChain'];
for(const t of types){test(`${t} implements conversion contract`,()=>{const x=M[t].randomGenerator();assert.ok(x instanceof M.FileConvertible);for(const method of ['toString','toHTML','toMarkdown'])assert.equal(typeof x[method](),'string');});}
test('Employee extends User',()=>assert.ok(M.Employee.randomGenerator() instanceof M.User));
test('RestaurantChain extends Company',()=>assert.ok(M.RestaurantChain.randomGenerator() instanceof M.Company));
test('chain location counter is consistent',()=>{const x=M.RestaurantChain.randomGenerator();assert.equal(x.numberOfLocations,x.restaurantLocations.length);});
test('location mismatch rejected',()=>{const c=M.Company.randomGenerator();assert.throws(()=>new M.RestaurantChain({...c,chainId:1,restaurantLocations:[],numberOfLocations:2,cuisineType:'Test',hasDriveThru:false,parentCompany:''}));});
test('generated branch employees are Employee objects',()=>{const x=M.RestaurantChain.randomGenerator();assert.ok(x.restaurantLocations.every(l=>l.employees.every(e=>e instanceof M.Employee)));});
test('HTML is escaped',()=>assert.equal(M.escapeHTML('<script>"&'), '&lt;script&gt;&quot;&amp;'));
test('chain HTML contains every location',()=>{const c=M.RestaurantChain.randomGenerator();for(const l of c.restaurantLocations)assert.ok(c.toHTML().includes(l.name));});
test('server serves UI and returns error for unknown route',async()=>{const server=createServer();await new Promise(r=>server.listen(0,'127.0.0.1',r));const port=server.address().port;try{for(const [path,code,check] of [['/',200,'Restaurant Chain'],['/style.css',200,'body{'],['/export.md',200,'# Restaurant Chain'],['/bad',404,'Not Found']]){const result=await new Promise((resolve,reject)=>http.get(`http://127.0.0.1:${port}${path}`,response=>{let data='';response.on('data',s=>data+=s);response.on('end',()=>resolve({code:response.statusCode,data}));}).on('error',reject));assert.equal(result.code,code);assert.ok(result.data.includes(check));}}finally{await new Promise(r=>server.close(r));}});
```

## ③ 行・ブロック単位の解説

**L1** `'use strict';`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L2** `const {test}=require('node:test');const assert=require('node:assert/strict');const http=require('node:http');`  
CommonJSのモジュールを読み込み、別ファイルまたはNode.js標準モジュールの機能を使う。

**L3** `const M=require('../src/models');const {createServer}=require('../src/server');`  
CommonJSのモジュールを読み込み、別ファイルまたはNode.js標準モジュールの機能を使う。

**L4** `const types=['User','Employee','Company','RestaurantLocation','RestaurantChain'];`  
変数を宣言し、値または関数を束縛する。constは再代入を禁止する。

**L5** `for(const t of types){test(′${t} implements conversion contract′,()=>{const x=M[t].randomGenerator();assert.ok(x in`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L6** `test('Employee extends User',()=>assert.ok(M.Employee.randomGenerator() instanceof M.User));`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L7** `test('RestaurantChain extends Company',()=>assert.ok(M.RestaurantChain.randomGenerator() instanceof M.Company));`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L8** `test('chain location counter is consistent',()=>{const x=M.RestaurantChain.randomGenerator();assert.equal(x.numberO`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L9** `test('location mismatch rejected',()=>{const c=M.Company.randomGenerator();assert.throws(()=>new M.RestaurantChain(`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L10** `test('generated branch employees are Employee objects',()=>{const x=M.RestaurantChain.randomGenerator();assert.ok(x`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L11** `test('HTML is escaped',()=>assert.equal(M.escapeHTML('<script>"&'), '&lt;script&gt;&quot;&amp;'));`  
自動テストのケースを定義し、想定した振る舞いを検証する。

**L12** `test('chain HTML contains every location',()=>{const c=M.RestaurantChain.randomGenerator();for(const l of c.restaur`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L13** `test('server serves UI and returns error for unknown route',async()=>{const server=createServer();await new Promise`  
HTTPサーバーを生成し、リクエストを受け取る関数を登録する。

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
