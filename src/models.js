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
