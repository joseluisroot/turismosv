import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import { test } from 'node:test';
const source = readFileSync('public/js/analytics-consent.js', 'utf8');
function run(saved, id = 'G-75MT2GD44N', blocked = false) {
 const tags = [], body = [], storage = new Map();
 if(saved) storage.set('turismosv_analytics_consent_v1',JSON.stringify(saved));
 function element(tag) {return {tag, dataset:{}, setAttribute(){}, addEventListener(event, cb){this[event]=cb;}, focus(){}, remove(){body.splice(body.indexOf(this),1);}, querySelector(q){return q==='a'?this.link:(this.buttons??[])[0];}, querySelectorAll(){return this.buttons;}, set innerHTML(value){this.link={};this.buttons=['rejected','accepted'].map(value=>({...element('button'),dataset:{choice:value}}));}};}
 const document={currentScript:{dataset:{analyticsId:id,cookiePolicy:'/cookies'}},referrer:'https://example.com/path?private=1',cookie:'_ga=123',head:{appendChild(el){tags.push(el);}},body:{appendChild(el){body.push(el);}},createElement:element,querySelector(){return body.find(el=>el.className==='analytics-notice');}};
 const window={addEventListener(){}};
 vm.runInNewContext(source,{document,window,location:{origin:'https://turismosv.com',pathname:'/explorar',hostname:'turismosv.com'},localStorage:{getItem(k){if(blocked)throw Error();return storage.get(k)||null;},setItem(k,v){if(blocked)throw Error();storage.set(k,v);}},URL,Date});
 return {tags,body,window,document};
}
const scripts = result => result.tags.filter(el=>el.tag==='script');
test('no Google requests until acceptance; rejection stays blocked',()=>{const r=run();assert.equal(scripts(r).length,0);r.body.find(el=>el.buttons).buttons[0].click();assert.equal(scripts(r).length,0);});
test('acceptance loads once and strips query from page metadata; withdrawal disables',()=>{const r=run();r.body.find(el=>el.buttons).buttons[1].click();assert.equal(scripts(r).length,1);const config=r.window.dataLayer.find(a=>a[0]==='config')[2];assert.equal(config.page_location,'https://turismosv.com/explorar');assert.equal(config.page_referrer,'https://example.com');r.body[0].click();r.body.find(el=>el.buttons).buttons[0].click();assert.equal(r.window['ga-disable-G-75MT2GD44N'],true);});
test('saved acceptance resumes, expired acceptance does not',()=>{assert.equal(scripts(run({value:'accepted',expires:Date.now()+60000})).length,1);assert.equal(scripts(run({value:'accepted',expires:1})).length,0);});
test('private pages and unavailable storage do not load Google',()=>{assert.equal(scripts(run({value:'accepted',expires:Date.now()+60000},'')).length,0);assert.equal(scripts(run(null,'G-75MT2GD44N',true)).length,0);});
