# src/models.js — 完全コードウォークスルー

## ① 役割、入力、出力、依存関係

架空の業務データを表現するクラスと、表示用の変換メソッドを提供する。入力はフィールドのオブジェクト。出力はモデルのインスタンスと文字列。

## ② ソースコード全文（実装と一致）

```js
'use strict';
const escapeHTML = value => String(value).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
const line = (k,v) => `${k}: ${v}`;
class FileConvertible {
  toString() { throw new Error('toString must be implemented'); }
  toHTML() { throw new Error('toHTML must be implemented'); }
  toMarkdown() { throw new Error('toMarkdown must be implemented'); }
}
class User extends FileConvertible {
  constructor({id,name,email}) { super(); this.id=id; this.name=name; this.email=email; }
  toString() { return `${this.name} (#${this.id}, ${this.email})`; }
  toHTML() { return `<span>${escapeHTML(this.toString())}</span>`; }
  toMarkdown() { return this.toString(); }
  static randomGenerator() { return new User({id:randomInt(1000,9999),name:randomName(),email:`guest${randomInt(100,999)}@example.test`}); }
}
class Employee extends User {
  constructor({id,name,email,jobTitle,salary,startDate,awards=[]}) { super({id,name,email}); Object.assign(this,{jobTitle,salary,startDate,awards}); }
  toString() { return `${super.toString()}, ${this.jobTitle}, $${this.salary.toLocaleString()}, since ${this.startDate}, awards: ${this.awards.join(', ') || 'none'}`; }
  toHTML() { return `<li>${escapeHTML(this.toString())}</li>`; }
  toMarkdown() { return `- ${this.toString()}`; }
  static randomGenerator() { return new Employee({id:randomInt(1000,99999),name:randomName(),email:`employee${randomInt(10,999)}@example.test`,jobTitle:pick(['Cashier','Chef','Server','Manager']),salary:randomInt(25000,90000),startDate:`${randomInt(2015,2025)}-${pad(randomInt(1,12))}-${pad(randomInt(1,28))}`,awards:Math.random() < .35 ? ['Employee of the Month'] : []}); }
}
class Company extends FileConvertible {
  constructor({name,yearFounded,description,website,phone,industry,ceo,isPublic}) {super();Object.assign(this,{name,yearFounded,description,website,phone,industry,ceo,isPublic});}
  toString() {return `${this.name} | Founded: ${this.yearFounded} | CEO: ${this.ceo} | Public: ${this.isPublic ? 'Yes':'No'} | ${this.industry} | ${this.website} | ${this.phone} | ${this.description}`;}
  toHTML() {return `<section><h3>${escapeHTML(this.name)}</h3><p>${escapeHTML(this.toString())}</p></section>`;}
  toMarkdown() {return `## ${this.name}\n\n${this.toString()}`;}
  static randomGenerator() {return new Company({name:randomCompany(),yearFounded:randomInt(1960,2024),description:'Restaurant and hospitality operations',website:'https://example.com',phone:`+1-555-${randomInt(100,999)}-${randomInt(1000,9999)}`,industry:'Food service',ceo:randomName(),isPublic:Math.random() < .5});}
}
class RestaurantLocation extends FileConvertible {
  constructor({name,address,city,state,postalCode,employees=[]}) {super();Object.assign(this,{name,address,city,state,postalCode,employees});}
  toString() {return `${this.name} — ${this.address}, ${this.city}, ${this.state} ${this.postalCode}\nEmployees:\n${this.employees.map(e=>e.toString()).join('\n')}`;}
  toHTML() {return `<details class="location"><summary>${escapeHTML(this.name)}</summary><div class="location-info"><p><strong>Address:</strong> ${escapeHTML(this.address)}, ${escapeHTML(this.city)}, ${escapeHTML(this.state)} ${escapeHTML(this.postalCode)}</p><h4>Employees</h4><ul>${this.employees.map(e=>e.toHTML()).join('')}</ul></div></details>`;}
  toMarkdown() {return `### ${this.name}\n\n${this.address}, ${this.city}, ${this.state} ${this.postalCode}\n\n**Employees**\n${this.employees.map(e=>e.toMarkdown()).join('\n')}`;}
  static randomGenerator() {return new RestaurantLocation({name:pick(['River Side','Cedar Grill','Green Garden','Sunset Kitchen','Corner Table']),address:`${randomInt(10,999)} ${pick(['Oak','Main','King','Lake'])} Street`,city:pick(['Austin','Denver','Portland','Seattle']),state:pick(['TX','CO','OR','WA']),postalCode:String(randomInt(10000,99999)),employees:Array.from({length:randomInt(2,5)},()=>Employee.randomGenerator())});}
}
class RestaurantChain extends Company {
  constructor({chainId,restaurantLocations,cuisineType,numberOfLocations,hasDriveThru,yearFounded,parentCompany,...company}) {super({...company,yearFounded});Object.assign(this,{chainId,restaurantLocations,cuisineType,numberOfLocations,hasDriveThru,parentCompany});if(numberOfLocations!==restaurantLocations.length)throw new Error('Location count mismatch');}
  toString() {return `Restaurant Chain ${this.name}\n${super.toString()}\nChain ID: ${this.chainId} | Cuisine: ${this.cuisineType} | Locations: ${this.numberOfLocations} | Drive-through: ${this.hasDriveThru ? 'Yes':'No'} | Parent: ${this.parentCompany || 'Independent'}\n${this.restaurantLocations.map(l=>l.toString()).join('\n\n')}`;}
  toHTML() {return `<article class="chain"><h2>Restaurant Chain ${escapeHTML(this.name)}</h2><div class="chain-body"><details class="company"><summary>Restaurant Chain Information</summary><div class="company-info"><p><strong>Company:</strong> ${escapeHTML(this.name)} | <strong>CEO:</strong> ${escapeHTML(this.ceo)} | <strong>Founded:</strong> ${this.yearFounded}</p><p>${escapeHTML(this.description)} | ${escapeHTML(this.cuisineType)} | ${this.numberOfLocations} locations | Drive-through: ${this.hasDriveThru ? 'Yes':'No'} | Parent: ${escapeHTML(this.parentCompany || 'Independent')}</p><p>Website: <a href="${escapeHTML(this.website)}" rel="noopener noreferrer">${escapeHTML(this.website)}</a> | ${escapeHTML(this.phone)} | Public: ${this.isPublic ? 'Yes':'No'}</p></div></details>${this.restaurantLocations.map(l=>l.toHTML()).join('')}</div></article>`;}
  toMarkdown() {return `# Restaurant Chain ${this.name}\n\n${super.toMarkdown()}\n\n**Chain ID:** ${this.chainId}  \n**Cuisine:** ${this.cuisineType}  \n**Drive-through:** ${this.hasDriveThru}  \n**Parent:** ${this.parentCompany || 'Independent'}\n\n${this.restaurantLocations.map(l=>l.toMarkdown()).join('\n\n')}`;}
  static randomGenerator() {const c=Company.randomGenerator();const n=randomInt(1,4);return new RestaurantChain({...c, name:randomCompany(),yearFounded:c.yearFounded,chainId:randomInt(100,99999),restaurantLocations:Array.from({length:n},()=>RestaurantLocation.randomGenerator()),cuisineType:pick(['Italian','Japanese','Mexican','American','Mediterranean']),numberOfLocations:n,hasDriveThru:Math.random()<.5,parentCompany:Math.random()<.5 ? 'Independent' : randomCompany()});}
}
function randomInt(min,max) {return Math.floor(Math.random()*(max-min+1))+min;}
function pick(xs) {return xs[randomInt(0,xs.length-1)];}
function pad(n) {return String(n).padStart(2,'0');}
function randomName() {return `${pick(['Alex','Jordan','Taylor','Morgan','Casey','Riley','Drew','Jamie'])} ${pick(['Smith','Brown','Lee','Garcia','Patel','Jones','Kim','Miller'])}`;}
function randomCompany() {return `${pick(['Rogahn','Ortiz','Mohr','Stark','Bashirian','Conn','Wolff','Kshlerin'])}, ${pick(['Dining','Foods','Hospitality','Group'])}`;}
module.exports={FileConvertible,User,Employee,Company,RestaurantLocation,RestaurantChain,escapeHTML};
```

## ③ 行・ブロック単位の解説

**L1** `'use strict';`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L2** `const escapeHTML = value => String(value).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;`  
変数を宣言し、値または関数を束縛する。constは再代入を禁止する。

**L3** `const line = (k,v) => ′${k}: ${v}′;`  
変数を宣言し、値または関数を束縛する。constは再代入を禁止する。

**L4** `class FileConvertible {`  
クラス定義。名前と継承元を決め、オブジェクトの構造・振る舞いをまとめる。

**L5** `toString() { throw new Error('toString must be implemented'); }`  
テキスト表現を返す。親クラスの同名メソッドはsuperで再利用できる。

**L6** `toHTML() { throw new Error('toHTML must be implemented'); }`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L7** `toMarkdown() { throw new Error('toMarkdown must be implemented'); }`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L8** `}`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L9** `class User extends FileConvertible {`  
クラス定義。名前と継承元を決め、オブジェクトの構造・振る舞いをまとめる。

**L10** `constructor({id,name,email}) { super(); this.id=id; this.name=name; this.email=email; }`  
コンストラクタ。newで作った際に引数を受け取り、プロパティを初期化する。

**L11** `toString() { return ′${this.name} (#${this.id}, ${this.email})′; }`  
テキスト表現を返す。親クラスの同名メソッドはsuperで再利用できる。

**L12** `toHTML() { return ′<span>${escapeHTML(this.toString())}</span>′; }`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L13** `toMarkdown() { return this.toString(); }`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L14** `static randomGenerator() { return new User({id:randomInt(1000,9999),name:randomName(),email:′guest${randomInt(100,9`  
静的ファクトリメソッド。架空データを生成し、新しいオブジェクトを返す。

**L15** `}`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L16** `class Employee extends User {`  
クラス定義。名前と継承元を決め、オブジェクトの構造・振る舞いをまとめる。

**L17** `constructor({id,name,email,jobTitle,salary,startDate,awards=[]}) { super({id,name,email}); Object.assign(this,{jobT`  
コンストラクタ。newで作った際に引数を受け取り、プロパティを初期化する。

**L18** `toString() { return ′${super.toString()}, ${this.jobTitle}, $${this.salary.toLocaleString()}, since ${this.startDat`  
テキスト表現を返す。親クラスの同名メソッドはsuperで再利用できる。

**L19** `toHTML() { return ′<li>${escapeHTML(this.toString())}</li>′; }`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L20** `toMarkdown() { return ′- ${this.toString()}′; }`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L21** `static randomGenerator() { return new Employee({id:randomInt(1000,99999),name:randomName(),email:′employee${randomI`  
静的ファクトリメソッド。架空データを生成し、新しいオブジェクトを返す。

**L22** `}`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L23** `class Company extends FileConvertible {`  
クラス定義。名前と継承元を決め、オブジェクトの構造・振る舞いをまとめる。

**L24** `constructor({name,yearFounded,description,website,phone,industry,ceo,isPublic}) {super();Object.assign(this,{name,y`  
コンストラクタ。newで作った際に引数を受け取り、プロパティを初期化する。

**L25** `toString() {return ′${this.name} | Founded: ${this.yearFounded} | CEO: ${this.ceo} | Public: ${this.isPublic ? 'Yes`  
テキスト表現を返す。親クラスの同名メソッドはsuperで再利用できる。

**L26** `toHTML() {return ′<section><h3>${escapeHTML(this.name)}</h3><p>${escapeHTML(this.toString())}</p></section>′;}`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L27** `toMarkdown() {return ′## ${this.name}\n\n${this.toString()}′;}`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L28** `static randomGenerator() {return new Company({name:randomCompany(),yearFounded:randomInt(1960,2024),description:'Re`  
静的ファクトリメソッド。架空データを生成し、新しいオブジェクトを返す。

**L29** `}`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L30** `class RestaurantLocation extends FileConvertible {`  
クラス定義。名前と継承元を決め、オブジェクトの構造・振る舞いをまとめる。

**L31** `constructor({name,address,city,state,postalCode,employees=[]}) {super();Object.assign(this,{name,address,city,state`  
コンストラクタ。newで作った際に引数を受け取り、プロパティを初期化する。

**L32** `toString() {return ′${this.name} — ${this.address}, ${this.city}, ${this.state} ${this.postalCode}\nEmployees:\n${t`  
テキスト表現を返す。親クラスの同名メソッドはsuperで再利用できる。

**L33** `toHTML() {return ′<details class="location"><summary>${escapeHTML(this.name)}</summary><div class="location-info"><`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L34** `toMarkdown() {return ′### ${this.name}\n\n${this.address}, ${this.city}, ${this.state} ${this.postalCode}\n\n**Empl`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L35** `static randomGenerator() {return new RestaurantLocation({name:pick(['River Side','Cedar Grill','Green Garden','Suns`  
静的ファクトリメソッド。架空データを生成し、新しいオブジェクトを返す。

**L36** `}`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L37** `class RestaurantChain extends Company {`  
クラス定義。名前と継承元を決め、オブジェクトの構造・振る舞いをまとめる。

**L38** `constructor({chainId,restaurantLocations,cuisineType,numberOfLocations,hasDriveThru,yearFounded,parentCompany,...co`  
コンストラクタ。newで作った際に引数を受け取り、プロパティを初期化する。

**L39** `toString() {return ′Restaurant Chain ${this.name}\n${super.toString()}\nChain ID: ${this.chainId} | Cuisine: ${this`  
テキスト表現を返す。親クラスの同名メソッドはsuperで再利用できる。

**L40** `toHTML() {return ′<article class="chain"><h2>Restaurant Chain ${escapeHTML(this.name)}</h2><div class="chain-body">`  
HTML表現を返す。返すのはDOMではなく文字列。動的データは必要に応じHTMLエスケープする。

**L41** `toMarkdown() {return ′# Restaurant Chain ${this.name}\n\n${super.toMarkdown()}\n\n**Chain ID:** ${this.chainId}  \n`  
Markdown表現を文字列として返す。保存や他システムへの転記に使える。

**L42** `static randomGenerator() {const c=Company.randomGenerator();const n=randomInt(1,4);return new RestaurantChain({...c`  
静的ファクトリメソッド。架空データを生成し、新しいオブジェクトを返す。

**L43** `}`  
前後の定義に従ってデータの生成・検証・表示を行う処理。引数と戻り値を確認する。

**L44** `function randomInt(min,max) {return Math.floor(Math.random()*(max-min+1))+min;}`  
呼び出し元へ計算結果を返す。ここでその関数の実行が終了する。

**L45** `function pick(xs) {return xs[randomInt(0,xs.length-1)];}`  
呼び出し元へ計算結果を返す。ここでその関数の実行が終了する。

**L46** `function pad(n) {return String(n).padStart(2,'0');}`  
呼び出し元へ計算結果を返す。ここでその関数の実行が終了する。

**L47** `function randomName() {return ′${pick(['Alex','Jordan','Taylor','Morgan','Casey','Riley','Drew','Jamie'])} ${pick([`  
呼び出し元へ計算結果を返す。ここでその関数の実行が終了する。

**L48** `function randomCompany() {return ′${pick(['Rogahn','Ortiz','Mohr','Stark','Bashirian','Conn','Wolff','Kshlerin'])},`  
呼び出し元へ計算結果を返す。ここでその関数の実行が終了する。

**L49** `module.exports={FileConvertible,User,Employee,Company,RestaurantLocation,RestaurantChain,escapeHTML};`  
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
