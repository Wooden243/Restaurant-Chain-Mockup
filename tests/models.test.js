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
