function isCompatible(ua){return!!((function(){'use strict';return!this&&Function.prototype.bind;}())&&'querySelector'in document&&'localStorage'in window&&!ua.match(/MSIE 10|NetFront|Opera Mini|S40OviBrowser|MeeGo|Android.+Glass|^Mozilla\/5\.0 .+ Gecko\/$|googleweblight|PLAYSTATION|PlayStation/));}if(!isCompatible(navigator.userAgent)){document.documentElement.className=document.documentElement.className.replace(/(^|\s)client-js(\s|$)/,'$1client-nojs$2');while(window.NORLQ&&NORLQ[0]){NORLQ.shift()();}NORLQ={push:function(fn){fn();}};RLQ={push:function(){}};}else{if(window.performance&&performance.mark){performance.mark('mwStartup');}(function(){'use strict';var con=window.console;function logError(topic,data){if(con.log){var e=data.exception;var msg=(e?'Exception':'Error')+' in '+data.source+(data.module?' in module '+data.module:'')+(e?':':'.');con.log(msg);if(e&&con.warn){con.warn(e);}}}function Map(){this.values=Object.create(null);}Map.prototype={constructor:Map,get:function(
selection,fallback){if(arguments.length<2){fallback=null;}if(typeof selection==='string'){return selection in this.values?this.values[selection]:fallback;}var results;if(Array.isArray(selection)){results={};for(var i=0;i<selection.length;i++){if(typeof selection[i]==='string'){results[selection[i]]=selection[i]in this.values?this.values[selection[i]]:fallback;}}return results;}if(selection===undefined){results={};for(var key in this.values){results[key]=this.values[key];}return results;}return fallback;},set:function(selection,value){if(arguments.length>1){if(typeof selection==='string'){this.values[selection]=value;return true;}}else if(typeof selection==='object'){for(var key in selection){this.values[key]=selection[key];}return true;}return false;},exists:function(selection){return typeof selection==='string'&&selection in this.values;}};var log=function(){};log.warn=con.warn?Function.prototype.bind.call(con.warn,con):function(){};var mw={now:function(){var perf=window.performance;
var navStart=perf&&perf.timing&&perf.timing.navigationStart;mw.now=navStart&&perf.now?function(){return navStart+perf.now();}:Date.now;return mw.now();},trackQueue:[],track:function(topic,data){mw.trackQueue.push({topic:topic,data:data});},trackError:function(topic,data){mw.track(topic,data);logError(topic,data);},Map:Map,config:new Map(),messages:new Map(),templates:new Map(),log:log};window.mw=window.mediaWiki=mw;}());(function(){'use strict';var StringSet,store,hasOwn=Object.hasOwnProperty;function defineFallbacks(){StringSet=window.Set||function(){var set=Object.create(null);return{add:function(value){set[value]=true;},has:function(value){return value in set;}};};}defineFallbacks();function fnv132(str){var hash=0x811C9DC5;for(var i=0;i<str.length;i++){hash+=(hash<<1)+(hash<<4)+(hash<<7)+(hash<<8)+(hash<<24);hash^=str.charCodeAt(i);}hash=(hash>>>0).toString(36).slice(0,5);while(hash.length<5){hash='0'+hash;}return hash;}var isES6Supported=typeof Promise==='function'&&Promise.
prototype.finally&&/./g.flags==='g'&&(function(){try{new Function('(a = 0) => a');return true;}catch(e){return false;}}());var registry=Object.create(null),sources=Object.create(null),handlingPendingRequests=false,pendingRequests=[],queue=[],jobs=[],willPropagate=false,errorModules=[],baseModules=["jquery","mediawiki.base"],marker=document.querySelector('meta[name="ResourceLoaderDynamicStyles"]'),lastCssBuffer,rAF=window.requestAnimationFrame||setTimeout;function addToHead(el,nextNode){if(nextNode&&nextNode.parentNode){nextNode.parentNode.insertBefore(el,nextNode);}else{document.head.appendChild(el);}}function newStyleTag(text,nextNode){var el=document.createElement('style');el.appendChild(document.createTextNode(text));addToHead(el,nextNode);return el;}function flushCssBuffer(cssBuffer){if(cssBuffer===lastCssBuffer){lastCssBuffer=null;}newStyleTag(cssBuffer.cssText,marker);for(var i=0;i<cssBuffer.callbacks.length;i++){cssBuffer.callbacks[i]();}}function addEmbeddedCSS(cssText,callback
){if(!lastCssBuffer||cssText.slice(0,7)==='@import'){lastCssBuffer={cssText:'',callbacks:[]};rAF(flushCssBuffer.bind(null,lastCssBuffer));}lastCssBuffer.cssText+='\n'+cssText;lastCssBuffer.callbacks.push(callback);}function getCombinedVersion(modules){var hashes=modules.reduce(function(result,module){return result+registry[module].version;},'');return fnv132(hashes);}function allReady(modules){for(var i=0;i<modules.length;i++){if(mw.loader.getState(modules[i])!=='ready'){return false;}}return true;}function allWithImplicitReady(module){return allReady(registry[module].dependencies)&&(baseModules.indexOf(module)!==-1||allReady(baseModules));}function anyFailed(modules){for(var i=0;i<modules.length;i++){var state=mw.loader.getState(modules[i]);if(state==='error'||state==='missing'){return modules[i];}}return false;}function doPropagation(){var didPropagate=true;var module;while(didPropagate){didPropagate=false;while(errorModules.length){var errorModule=errorModules.shift(),
baseModuleError=baseModules.indexOf(errorModule)!==-1;for(module in registry){if(registry[module].state!=='error'&&registry[module].state!=='missing'){if(baseModuleError&&baseModules.indexOf(module)===-1){registry[module].state='error';didPropagate=true;}else if(registry[module].dependencies.indexOf(errorModule)!==-1){registry[module].state='error';errorModules.push(module);didPropagate=true;}}}}for(module in registry){if(registry[module].state==='loaded'&&allWithImplicitReady(module)){execute(module);didPropagate=true;}}for(var i=0;i<jobs.length;i++){var job=jobs[i];var failed=anyFailed(job.dependencies);if(failed!==false||allReady(job.dependencies)){jobs.splice(i,1);i-=1;try{if(failed!==false&&job.error){job.error(new Error('Failed dependency: '+failed),job.dependencies);}else if(failed===false&&job.ready){job.ready();}}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'load-callback'});}didPropagate=true;}}}willPropagate=false;}function setAndPropagate(module,
state){registry[module].state=state;if(state==='ready'){store.add(module);}else if(state==='error'||state==='missing'){errorModules.push(module);}else if(state!=='loaded'){return;}if(willPropagate){return;}willPropagate=true;mw.requestIdleCallback(doPropagation,{timeout:1});}function sortDependencies(module,resolved,unresolved){if(!(module in registry)){throw new Error('Unknown module: '+module);}if(typeof registry[module].skip==='string'){var skip=(new Function(registry[module].skip)());registry[module].skip=!!skip;if(skip){registry[module].dependencies=[];setAndPropagate(module,'ready');return;}}if(!unresolved){unresolved=new StringSet();}var deps=registry[module].dependencies;unresolved.add(module);for(var i=0;i<deps.length;i++){if(resolved.indexOf(deps[i])===-1){if(unresolved.has(deps[i])){throw new Error('Circular reference detected: '+module+' -> '+deps[i]);}sortDependencies(deps[i],resolved,unresolved);}}resolved.push(module);}function resolve(modules){var resolved=baseModules.
slice();for(var i=0;i<modules.length;i++){sortDependencies(modules[i],resolved);}return resolved;}function resolveStubbornly(modules){var resolved=baseModules.slice();for(var i=0;i<modules.length;i++){var saved=resolved.slice();try{sortDependencies(modules[i],resolved);}catch(err){resolved=saved;mw.log.warn('Skipped unavailable module '+modules[i]);if(modules[i]in registry){mw.trackError('resourceloader.exception',{exception:err,source:'resolve'});}}}return resolved;}function resolveRelativePath(relativePath,basePath){var relParts=relativePath.match(/^((?:\.\.?\/)+)(.*)$/);if(!relParts){return null;}var baseDirParts=basePath.split('/');baseDirParts.pop();var prefixes=relParts[1].split('/');prefixes.pop();var prefix;while((prefix=prefixes.pop())!==undefined){if(prefix==='..'){baseDirParts.pop();}}return(baseDirParts.length?baseDirParts.join('/')+'/':'')+relParts[2];}function makeRequireFunction(moduleObj,basePath){return function require(moduleName){var fileName=resolveRelativePath(
moduleName,basePath);if(fileName===null){return mw.loader.require(moduleName);}if(hasOwn.call(moduleObj.packageExports,fileName)){return moduleObj.packageExports[fileName];}var scriptFiles=moduleObj.script.files;if(!hasOwn.call(scriptFiles,fileName)){throw new Error('Cannot require undefined file '+fileName);}var result,fileContent=scriptFiles[fileName];if(typeof fileContent==='function'){var moduleParam={exports:{}};fileContent(makeRequireFunction(moduleObj,fileName),moduleParam,moduleParam.exports);result=moduleParam.exports;}else{result=fileContent;}moduleObj.packageExports[fileName]=result;return result;};}function addScript(src,callback){var script=document.createElement('script');script.src=src;script.onload=script.onerror=function(){if(script.parentNode){script.parentNode.removeChild(script);}if(callback){callback();callback=null;}};document.head.appendChild(script);return script;}function queueModuleScript(src,moduleName,callback){pendingRequests.push(function(){if(moduleName
!=='jquery'){window.require=mw.loader.require;window.module=registry[moduleName].module;}addScript(src,function(){delete window.module;callback();if(pendingRequests[0]){pendingRequests.shift()();}else{handlingPendingRequests=false;}});});if(!handlingPendingRequests&&pendingRequests[0]){handlingPendingRequests=true;pendingRequests.shift()();}}function addLink(url,media,nextNode){var el=document.createElement('link');el.rel='stylesheet';if(media){el.media=media;}el.href=url;addToHead(el,nextNode);return el;}function domEval(code){var script=document.createElement('script');if(mw.config.get('wgCSPNonce')!==false){script.nonce=mw.config.get('wgCSPNonce');}script.text=code;document.head.appendChild(script);script.parentNode.removeChild(script);}function enqueue(dependencies,ready,error){if(allReady(dependencies)){if(ready){ready();}return;}var failed=anyFailed(dependencies);if(failed!==false){if(error){error(new Error('Dependency '+failed+' failed to load'),dependencies);}return;}if(ready||
error){jobs.push({dependencies:dependencies.filter(function(module){var state=registry[module].state;return state==='registered'||state==='loaded'||state==='loading'||state==='executing';}),ready:ready,error:error});}dependencies.forEach(function(module){if(registry[module].state==='registered'&&queue.indexOf(module)===-1){queue.push(module);}});mw.loader.work();}function execute(module){if(registry[module].state!=='loaded'){throw new Error('Module in state "'+registry[module].state+'" may not execute: '+module);}registry[module].state='executing';var runScript=function(){var script=registry[module].script;var markModuleReady=function(){setAndPropagate(module,'ready');};var nestedAddScript=function(arr,offset){if(offset>=arr.length){markModuleReady();return;}queueModuleScript(arr[offset],module,function(){nestedAddScript(arr,offset+1);});};try{if(Array.isArray(script)){nestedAddScript(script,0);}else if(typeof script==='function'){if(module==='jquery'){script();}else{script(window.$,
window.$,mw.loader.require,registry[module].module);}markModuleReady();}else if(typeof script==='object'&&script!==null){var mainScript=script.files[script.main];if(typeof mainScript!=='function'){throw new Error('Main file in module '+module+' must be a function');}mainScript(makeRequireFunction(registry[module],script.main),registry[module].module,registry[module].module.exports);markModuleReady();}else if(typeof script==='string'){domEval(script);markModuleReady();}else{markModuleReady();}}catch(e){setAndPropagate(module,'error');mw.trackError('resourceloader.exception',{exception:e,module:module,source:'module-execute'});}};if(registry[module].messages){mw.messages.set(registry[module].messages);}if(registry[module].templates){mw.templates.set(module,registry[module].templates);}var cssPending=0;var cssHandle=function(){cssPending++;return function(){cssPending--;if(cssPending===0){var runScriptCopy=runScript;runScript=undefined;runScriptCopy();}};};if(registry[module].style){for(
var key in registry[module].style){var value=registry[module].style[key];if(key==='css'){for(var i=0;i<value.length;i++){addEmbeddedCSS(value[i],cssHandle());}}else if(key==='url'){for(var media in value){var urls=value[media];for(var j=0;j<urls.length;j++){addLink(urls[j],media,marker);}}}}}if(module==='user'){var siteDeps;var siteDepErr;try{siteDeps=resolve(['site']);}catch(e){siteDepErr=e;runScript();}if(!siteDepErr){enqueue(siteDeps,runScript,runScript);}}else if(cssPending===0){runScript();}}function sortQuery(o){var sorted={};var list=[];for(var key in o){list.push(key);}list.sort();for(var i=0;i<list.length;i++){sorted[list[i]]=o[list[i]];}return sorted;}function buildModulesString(moduleMap){var str=[];var list=[];var p;function restore(suffix){return p+suffix;}for(var prefix in moduleMap){p=prefix===''?'':prefix+'.';str.push(p+moduleMap[prefix].join(','));list.push.apply(list,moduleMap[prefix].map(restore));}return{str:str.join('|'),list:list};}function makeQueryString(params)
{var str='';for(var key in params){str+=(str?'&':'')+encodeURIComponent(key)+'='+encodeURIComponent(params[key]);}return str;}function batchRequest(batch){if(!batch.length){return;}var sourceLoadScript,currReqBase,moduleMap;function doRequest(){var query=Object.create(currReqBase),packed=buildModulesString(moduleMap);query.modules=packed.str;query.version=getCombinedVersion(packed.list);query=sortQuery(query);addScript(sourceLoadScript+'?'+makeQueryString(query));}batch.sort();var reqBase={"lang":"fr","skin":"monobook"};var splits=Object.create(null);for(var b=0;b<batch.length;b++){var bSource=registry[batch[b]].source;var bGroup=registry[batch[b]].group;if(!splits[bSource]){splits[bSource]=Object.create(null);}if(!splits[bSource][bGroup]){splits[bSource][bGroup]=[];}splits[bSource][bGroup].push(batch[b]);}for(var source in splits){sourceLoadScript=sources[source];for(var group in splits[source]){var modules=splits[source][group];currReqBase=Object.create(reqBase);if(group===0&&mw.
config.get('wgUserName')!==null){currReqBase.user=mw.config.get('wgUserName');}var currReqBaseLength=makeQueryString(currReqBase).length+23;var length=0;moduleMap=Object.create(null);for(var i=0;i<modules.length;i++){var lastDotIndex=modules[i].lastIndexOf('.'),prefix=modules[i].slice(0,Math.max(0,lastDotIndex)),suffix=modules[i].slice(lastDotIndex+1),bytesAdded=moduleMap[prefix]?suffix.length+3:modules[i].length+3;if(length&&length+currReqBaseLength+bytesAdded>mw.loader.maxQueryLength){doRequest();length=0;moduleMap=Object.create(null);}if(!moduleMap[prefix]){moduleMap[prefix]=[];}length+=bytesAdded;moduleMap[prefix].push(suffix);}doRequest();}}}function asyncEval(implementations,cb){if(!implementations.length){return;}mw.requestIdleCallback(function(){try{domEval(implementations.join(';'));}catch(err){cb(err);}});}function getModuleKey(module){return module in registry?(module+'@'+registry[module].version):null;}function splitModuleKey(key){var index=key.lastIndexOf('@');if(index===-
1||index===0){return{name:key,version:''};}return{name:key.slice(0,index),version:key.slice(index+1)};}function registerOne(module,version,dependencies,group,source,skip){if(module in registry){throw new Error('module already registered: '+module);}version=String(version||'');if(version.slice(-1)==='!'){if(!isES6Supported){return;}version=version.slice(0,-1);}registry[module]={module:{exports:{}},packageExports:{},version:version,dependencies:dependencies||[],group:typeof group==='undefined'?null:group,source:typeof source==='string'?source:'local',state:'registered',skip:typeof skip==='string'?skip:null};}mw.loader={moduleRegistry:registry,maxQueryLength:2000,addStyleTag:newStyleTag,addScriptTag:addScript,addLinkTag:addLink,enqueue:enqueue,resolve:resolve,work:function(){store.init();var q=queue.length,storedImplementations=[],storedNames=[],requestNames=[],batch=new StringSet();while(q--){var module=queue[q];if(mw.loader.getState(module)==='registered'&&!batch.has(module)){registry[
module].state='loading';batch.add(module);var implementation=store.get(module);if(implementation){storedImplementations.push(implementation);storedNames.push(module);}else{requestNames.push(module);}}}queue=[];asyncEval(storedImplementations,function(err){store.stats.failed++;store.clear();mw.trackError('resourceloader.exception',{exception:err,source:'store-eval'});var failed=storedNames.filter(function(name){return registry[name].state==='loading';});batchRequest(failed);});batchRequest(requestNames);},addSource:function(ids){for(var id in ids){if(id in sources){throw new Error('source already registered: '+id);}sources[id]=ids[id];}},register:function(modules){if(typeof modules!=='object'){registerOne.apply(null,arguments);return;}function resolveIndex(dep){return typeof dep==='number'?modules[dep][0]:dep;}for(var i=0;i<modules.length;i++){var deps=modules[i][2];if(deps){for(var j=0;j<deps.length;j++){deps[j]=resolveIndex(deps[j]);}}registerOne.apply(null,modules[i]);}},implement:
function(module,script,style,messages,templates){var split=splitModuleKey(module),name=split.name,version=split.version;if(!(name in registry)){mw.loader.register(name);}if(registry[name].script!==undefined){throw new Error('module already implemented: '+name);}if(version){registry[name].version=version;}registry[name].script=script||null;registry[name].style=style||null;registry[name].messages=messages||null;registry[name].templates=templates||null;if(registry[name].state!=='error'&&registry[name].state!=='missing'){setAndPropagate(name,'loaded');}},load:function(modules,type){if(typeof modules==='string'&&/^(https?:)?\/?\//.test(modules)){if(type==='text/css'){addLink(modules);}else if(type==='text/javascript'||type===undefined){addScript(modules);}else{throw new Error('Invalid type '+type);}}else{modules=typeof modules==='string'?[modules]:modules;enqueue(resolveStubbornly(modules));}},state:function(states){for(var module in states){if(!(module in registry)){mw.loader.register(
module);}setAndPropagate(module,states[module]);}},getState:function(module){return module in registry?registry[module].state:null;},require:function(moduleName){if(mw.loader.getState(moduleName)!=='ready'){throw new Error('Module "'+moduleName+'" is not loaded');}return registry[moduleName].module.exports;}};var hasPendingWrites=false;function flushWrites(){store.prune();while(store.queue.length){store.set(store.queue.shift());}try{localStorage.removeItem(store.key);var data=JSON.stringify(store);localStorage.setItem(store.key,data);}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-update'});}hasPendingWrites=false;}mw.loader.store=store={enabled:null,items:{},queue:[],stats:{hits:0,misses:0,expired:0,failed:0},toJSON:function(){return{items:store.items,vary:store.vary,asOf:Math.ceil(Date.now()/1e7)};},key:"MediaWikiModuleStore:inr",vary:"monobook:1:fr",init:function(){if(this.enabled===null){this.enabled=false;if(true){this.load();}else{this.
clear();}}},load:function(){try{var raw=localStorage.getItem(this.key);this.enabled=true;var data=JSON.parse(raw);if(data&&data.vary===this.vary&&data.items&&Date.now()<(data.asOf*1e7)+259e7){this.items=data.items;}}catch(e){}},get:function(module){if(this.enabled){var key=getModuleKey(module);if(key in this.items){this.stats.hits++;return this.items[key];}this.stats.misses++;}return false;},add:function(module){if(this.enabled){this.queue.push(module);this.requestUpdate();}},set:function(module){var args,encodedScript,descriptor=registry[module],key=getModuleKey(module);if(key in this.items||!descriptor||descriptor.state!=='ready'||!descriptor.version||descriptor.group===1||descriptor.group===0||[descriptor.script,descriptor.style,descriptor.messages,descriptor.templates].indexOf(undefined)!==-1){return;}try{if(typeof descriptor.script==='function'){encodedScript=String(descriptor.script);}else if(typeof descriptor.script==='object'&&descriptor.script&&!Array.isArray(descriptor.script
)){encodedScript='{'+'main:'+JSON.stringify(descriptor.script.main)+','+'files:{'+Object.keys(descriptor.script.files).map(function(file){var value=descriptor.script.files[file];return JSON.stringify(file)+':'+(typeof value==='function'?value:JSON.stringify(value));}).join(',')+'}}';}else{encodedScript=JSON.stringify(descriptor.script);}args=[JSON.stringify(key),encodedScript,JSON.stringify(descriptor.style),JSON.stringify(descriptor.messages),JSON.stringify(descriptor.templates)];}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-json'});return;}var src='mw.loader.implement('+args.join(',')+');';if(src.length>1e5){return;}this.items[key]=src;},prune:function(){for(var key in this.items){if(getModuleKey(splitModuleKey(key).name)!==key){this.stats.expired++;delete this.items[key];}}},clear:function(){this.items={};try{localStorage.removeItem(this.key);}catch(e){}},requestUpdate:function(){if(!hasPendingWrites){hasPendingWrites=true;setTimeout(
function(){mw.requestIdleCallback(flushWrites);},2000);}}};}());mw.requestIdleCallbackInternal=function(callback){setTimeout(function(){var start=mw.now();callback({didTimeout:false,timeRemaining:function(){return Math.max(0,50-(mw.now()-start));}});},1);};mw.requestIdleCallback=window.requestIdleCallback?window.requestIdleCallback.bind(window):mw.requestIdleCallbackInternal;(function(){var queue;mw.loader.addSource({"local":"/wiki/load.php"});mw.loader.register([["site","yffjj",[1]],["site.styles","wdt8m",[],2],["filepage","1ljys"],["user","1tdkc",[],0],["user.styles","18fec",[],0],["user.options","12s5i",[],1],["mediawiki.skinning.interface","1rkvg"],["jquery.makeCollapsible.styles","ocevp"],["mediawiki.skinning.content.parsoid","154n0"],["jquery","p9z7x"],["es6-polyfills","1xwex",[],null,null,"return Array.prototype.find\u0026\u0026Array.prototype.findIndex\u0026\u0026Array.prototype.includes\u0026\u0026typeof Promise==='function'\u0026\u0026Promise.prototype.finally;"],[
"web2017-polyfills","5cxhc",[10],null,null,"return'IntersectionObserver'in window\u0026\u0026typeof fetch==='function'\u0026\u0026typeof URL==='function'\u0026\u0026'toJSON'in URL.prototype;"],["mediawiki.base","yj4pg",[9]],["jquery.chosen","fjvzv"],["jquery.client","1jnox"],["jquery.color","1y5ur"],["jquery.confirmable","16915",[109]],["jquery.cookie","emj1l"],["jquery.form","1djyv"],["jquery.fullscreen","1lanf"],["jquery.highlightText","a2wnf",[83]],["jquery.hoverIntent","1cahm"],["jquery.i18n","1pu0k",[108]],["jquery.lengthLimit","k5zgm",[67]],["jquery.makeCollapsible","1gmpe",[7,83]],["jquery.spinner","1rx3f",[26]],["jquery.spinner.styles","153wt"],["jquery.suggestions","1g6wh",[20]],["jquery.tablesorter","45s3x",[29,110,83]],["jquery.tablesorter.styles","rwcx6"],["jquery.textSelection","m1do8",[14]],["jquery.throttle-debounce","1p2bq"],["jquery.tipsy","5uv8c"],["jquery.ui","1w3m2"],["moment","wtlja",[106,83]],["vue","zfi8r!"],["@vue/composition-api","scw0q!",[35]],["vuex","1twvy!"
,[35]],["wvui","v4ef5!",[36]],["wvui-search","1nhzn!",[35]],["@wikimedia/codex","r6zyv!",[35]],["@wikimedia/codex-search","1p7vn!",[35]],["mediawiki.template","bca94"],["mediawiki.template.mustache","199kg",[42]],["mediawiki.apipretty","19n2s"],["mediawiki.api","1ei34",[73,109]],["mediawiki.content.json","h3m91"],["mediawiki.confirmCloseWindow","q7x1a"],["mediawiki.debug","d8is9",[193]],["mediawiki.diff","paqy5"],["mediawiki.diff.styles","na4y2"],["mediawiki.feedback","adqpa",[344,201]],["mediawiki.feedlink","1yq8n"],["mediawiki.filewarning","1brek",[193,205]],["mediawiki.ForeignApi","6vgsr",[55]],["mediawiki.ForeignApi.core","llzm2",[80,45,189]],["mediawiki.helplink","wjdrt"],["mediawiki.hlist","18xzz"],["mediawiki.htmlform","1nlfh",[23,83]],["mediawiki.htmlform.ooui","1m5pb",[193]],["mediawiki.htmlform.styles","1mdmd"],["mediawiki.htmlform.ooui.styles","t3imb"],["mediawiki.icon","17xpk"],["mediawiki.inspect","88qa7",[67,83]],["mediawiki.notification","1u1la",[83,89]],[
"mediawiki.notification.convertmessagebox","1kd6x",[64]],["mediawiki.notification.convertmessagebox.styles","19vc0"],["mediawiki.String","1vc9s"],["mediawiki.pager.styles","eo2ge"],["mediawiki.pager.tablePager","1tupc"],["mediawiki.pulsatingdot","1i1zo"],["mediawiki.searchSuggest","1x01t",[27,45]],["mediawiki.storage","1jgwf",[83]],["mediawiki.Title","1345o",[67,83]],["mediawiki.Upload","ooev2",[45]],["mediawiki.ForeignUpload","1vzwu",[54,74]],["mediawiki.Upload.Dialog","3zjeu",[77]],["mediawiki.Upload.BookletLayout","18tfe",[74,81,34,196,201,206,207]],["mediawiki.ForeignStructuredUpload.BookletLayout","vtwr9",[75,77,113,172,166]],["mediawiki.toc","1jhap",[86]],["mediawiki.Uri","5izs0",[83]],["mediawiki.user","1fogn",[45,86]],["mediawiki.userSuggest","1hhzv",[27,45]],["mediawiki.util","18ryi",[14,11]],["mediawiki.checkboxtoggle","159pl"],["mediawiki.checkboxtoggle.styles","1b0zv"],["mediawiki.cookie","1qhhq",[17]],["mediawiki.experiments","dhcyy"],["mediawiki.editfont.styles","12q5o"],
["mediawiki.visibleTimeout","xcitq"],["mediawiki.action.delete","15sn4",[23,193]],["mediawiki.action.edit","mstk4",[30,92,45,88,168]],["mediawiki.action.edit.styles","1o953"],["mediawiki.action.edit.collapsibleFooter","za3yf",[24,62,72]],["mediawiki.action.edit.preview","7amm8",[25,119,81]],["mediawiki.action.history","cpbx3",[24]],["mediawiki.action.history.styles","g8wz5"],["mediawiki.action.protect","hvd6k",[23,193]],["mediawiki.action.view.metadata","85fwb",[104]],["mediawiki.action.view.categoryPage.styles","z9xgj"],["mediawiki.action.view.postEdit","1bv2b",[109,64,193,212]],["mediawiki.action.view.redirect","iqcjx"],["mediawiki.action.view.redirectPage","pdxld"],["mediawiki.action.edit.editWarning","ihdqq",[30,47,109]],["mediawiki.action.view.filepage","mbna9"],["mediawiki.action.styles","g8x3w"],["mediawiki.language","1uuvc",[107]],["mediawiki.cldr","w8zqb",[108]],["mediawiki.libs.pluralruleparser","1kwne"],["mediawiki.jqueryMsg","1enr3",[67,106,83,5]],[
"mediawiki.language.months","q3h25",[106]],["mediawiki.language.names","159lr",[106]],["mediawiki.language.specialCharacters","1ilb4",[106]],["mediawiki.libs.jpegmeta","1h4oh"],["mediawiki.page.gallery","19ugl",[115,83]],["mediawiki.page.gallery.styles","16scj"],["mediawiki.page.gallery.slideshow","1nhx0",[45,196,215,217]],["mediawiki.page.ready","e3x08",[45]],["mediawiki.page.watch.ajax","1ktd6",[45]],["mediawiki.page.preview","54y7x",[24,30,45,49,50,193]],["mediawiki.page.image.pagination","kn7b4",[25,83]],["mediawiki.rcfilters.filters.base.styles","cjxte"],["mediawiki.rcfilters.highlightCircles.seenunseen.styles","1if6x"],["mediawiki.rcfilters.filters.ui","14vjj",[24,80,81,163,202,209,211,212,213,215,216]],["mediawiki.interface.helpers.styles","131ge"],["mediawiki.special","ch7qn"],["mediawiki.special.apisandbox","13ovt",[24,80,183,169,192]],["mediawiki.special.block","1n3h1",[58,166,182,173,183,180,209]],["mediawiki.misc-authed-ooui","1iw6h",[59,163,168]],[
"mediawiki.misc-authed-pref","16eja",[5]],["mediawiki.misc-authed-curate","18hmi",[16,25,45]],["mediawiki.special.changeslist","19kr3"],["mediawiki.special.changeslist.watchlistexpiry","1g8zy",[125,212]],["mediawiki.special.changeslist.enhanced","1kflq"],["mediawiki.special.changeslist.legend","13azi"],["mediawiki.special.changeslist.legend.js","qa88i",[24,86]],["mediawiki.special.contributions","1luqq",[24,109,166,192]],["mediawiki.special.edittags","ff6qa",[13,23]],["mediawiki.special.import.styles.ooui","1hzv9"],["mediawiki.special.changecredentials","f9fqt"],["mediawiki.special.changeemail","10bxu"],["mediawiki.special.preferences.ooui","1pdi4",[47,88,65,72,173,168]],["mediawiki.special.preferences.styles.ooui","174y4"],["mediawiki.special.revisionDelete","6556w",[23]],["mediawiki.special.search","11pp3",[185]],["mediawiki.special.search.commonsInterwikiWidget","rmzms",[80,45]],["mediawiki.special.search.interwikiwidget.styles","cxv8q"],["mediawiki.special.search.styles","1murh"],[
"mediawiki.special.unwatchedPages","lr2ux",[45]],["mediawiki.special.upload","1a4t1",[25,45,47,113,125,42]],["mediawiki.special.userlogin.common.styles","1q3ah"],["mediawiki.special.userlogin.login.styles","1w9oo"],["mediawiki.special.createaccount","lruio",[45]],["mediawiki.special.userlogin.signup.styles","2q1sd"],["mediawiki.special.userrights","4k0n6",[23,65]],["mediawiki.special.watchlist","alhqr",[45,193,212]],["mediawiki.ui","8x5he"],["mediawiki.ui.checkbox","it5y7"],["mediawiki.ui.radio","y2vak"],["mediawiki.ui.anchor","1yxgk"],["mediawiki.ui.button","uqcns"],["mediawiki.ui.input","1q0fk"],["mediawiki.ui.icon","vzvsl"],["mediawiki.widgets","wtd3c",[45,164,196,206,207]],["mediawiki.widgets.styles","1x5du"],["mediawiki.widgets.AbandonEditDialog","19an4",[201]],["mediawiki.widgets.DateInputWidget","1qdkc",[167,34,196,217]],["mediawiki.widgets.DateInputWidget.styles","1a3gk"],["mediawiki.widgets.visibleLengthLimit","m325n",[23,193]],["mediawiki.widgets.datetime","1n4t3",[83,193,212
,216,217]],["mediawiki.widgets.expiry","m5uji",[169,34,196]],["mediawiki.widgets.CheckMatrixWidget","k9si1",[193]],["mediawiki.widgets.CategoryMultiselectWidget","1d0e7",[54,196]],["mediawiki.widgets.SelectWithInputWidget","yzuek",[174,196]],["mediawiki.widgets.SelectWithInputWidget.styles","vkr7h"],["mediawiki.widgets.SizeFilterWidget","2cqot",[176,196]],["mediawiki.widgets.SizeFilterWidget.styles","ceybj"],["mediawiki.widgets.MediaSearch","1ym8z",[54,81,196]],["mediawiki.widgets.Table","135ha",[196]],["mediawiki.widgets.TagMultiselectWidget","1erse",[196]],["mediawiki.widgets.UserInputWidget","jsk5k",[45,196]],["mediawiki.widgets.UsersMultiselectWidget","1m6vb",[45,196]],["mediawiki.widgets.NamespacesMultiselectWidget","pwj2l",[196]],["mediawiki.widgets.TitlesMultiselectWidget","gt95w",[163]],["mediawiki.widgets.TagMultiselectWidget.styles","1rjw4"],["mediawiki.widgets.SearchInputWidget","z70j2",[71,163,212]],["mediawiki.widgets.SearchInputWidget.styles","9327p"],[
"mediawiki.watchstar.widgets","4bhjc",[192]],["mediawiki.deflate","1ci7b"],["oojs","ewqeo"],["mediawiki.router","1ugrh",[191]],["oojs-router","m96yy",[189]],["oojs-ui","1jh3r",[199,196,201]],["oojs-ui-core","44un3",[106,189,195,194,203]],["oojs-ui-core.styles","180te"],["oojs-ui-core.icons","1kp5m"],["oojs-ui-widgets","1gjvk",[193,198]],["oojs-ui-widgets.styles","1a1mk"],["oojs-ui-widgets.icons","1fvsh"],["oojs-ui-toolbars","40f6b",[193,200]],["oojs-ui-toolbars.icons","1f52w"],["oojs-ui-windows","1msw5",[193,202]],["oojs-ui-windows.icons","y40nr"],["oojs-ui.styles.indicators","7own2"],["oojs-ui.styles.icons-accessibility","xjh0f"],["oojs-ui.styles.icons-alerts","1p92v"],["oojs-ui.styles.icons-content","1js1u"],["oojs-ui.styles.icons-editing-advanced","7eq2t"],["oojs-ui.styles.icons-editing-citation","j17ae"],["oojs-ui.styles.icons-editing-core","1ypxt"],["oojs-ui.styles.icons-editing-list","1b4i1"],["oojs-ui.styles.icons-editing-styling","soeqr"],["oojs-ui.styles.icons-interactions",
"4r9fl"],["oojs-ui.styles.icons-layout","65w5l"],["oojs-ui.styles.icons-location","1361n"],["oojs-ui.styles.icons-media","1813m"],["oojs-ui.styles.icons-moderation","7dwxp"],["oojs-ui.styles.icons-movement","s1pev"],["oojs-ui.styles.icons-user","40v79"],["oojs-ui.styles.icons-wikimedia","14a12"],["skins.minerva.base.styles","11req"],["skins.minerva.content.styles.images","tky7r"],["skins.minerva.icons.loggedin","cs67v"],["skins.minerva.amc.styles","bf5y6"],["skins.minerva.overflow.icons","2zov3"],["skins.minerva.icons.wikimedia","hqkhw"],["skins.minerva.icons.images.scripts.misc","1rjuw"],["skins.minerva.icons.page.issues.uncolored","1f11m"],["skins.minerva.icons.page.issues.default.color","7rvgq"],["skins.minerva.icons.page.issues.medium.color","z8nf5"],["skins.minerva.mainPage.styles","1em1d"],["skins.minerva.userpage.styles","19xse"],["skins.minerva.talk.styles","5gxxp"],["skins.minerva.personalMenu.icons","mu738"],["skins.minerva.mainMenu.advanced.icons","h87z5"],[
"skins.minerva.mainMenu.icons","4711o"],["skins.minerva.mainMenu.styles","1z108"],["skins.minerva.loggedin.styles","1bz3m"],["skins.minerva.scripts","qfz0d",[80,87,118,190,72,159,81,341,226,228,229,227,235,236,239,43]],["skins.minerva.messageBox.styles","1e46u"],["skins.minerva.categories.styles","ytejd"],["skins.monobook.styles","ad0dz"],["skins.monobook.scripts","dhvl7",[81,205]],["skins.timeless","1d5yr"],["skins.timeless.js","158q7"],["skins.vector.user","1b93e",[],0],["skins.vector.user.styles","1rlz1",[],0],["skins.vector.search","ophj2!",[41,80]],["skins.vector.styles.legacy","1uiba"],["skins.vector.AB.styles","1ut64"],["skins.vector.styles","hwz44"],["skins.vector.icons.js","11dwc"],["skins.vector.icons","5ju1h"],["skins.vector.es6","1g1o4!",[87,117,118,81,251]],["skins.vector.js","10gsu",[117,251]],["skins.vector.legacy.js","omaiv",[117]],["ext.categoryTree","1g4id",[45]],["ext.categoryTree.styles","1d80w"],["ext.cite.styles","1o8is"],["ext.cite.style","6t36z"],[
"ext.cite.visualEditor.core","lpm1q",[323]],["ext.cite.visualEditor","gygqo",[259,258,260,205,208,212]],["ext.cite.ux-enhancements","9p7cf"],["ext.confirmEdit.editPreview.ipwhitelist.styles","11y4q"],["ext.confirmEdit.visualEditor","rlq1b",[343]],["ext.confirmEdit.simpleCaptcha","14a9d"],["ext.math.styles","1esxo"],["ext.math.scripts","9r3ia"],["mw.widgets.MathWbEntitySelector","zc14e",[54,163,"mw.config.values.wbRepo",201]],["ext.math.visualEditor","gmezu",[266,315]],["ext.math.visualEditor.mathSymbolsData","ltjso",[269]],["ext.math.visualEditor.mathSymbols","18eu7",[270]],["ext.math.visualEditor.chemSymbolsData","ar9ku",[269]],["ext.math.visualEditor.chemSymbols","s750r",[272]],["ext.templateData","1xaeh"],["ext.templateDataGenerator.editPage","1e7eh"],["ext.templateDataGenerator.data","2zdar",[189]],["ext.templateDataGenerator.editTemplatePage.loading","60i01"],["ext.templateDataGenerator.editTemplatePage","1jbxr",[274,279,276,30,342,45,196,201,212,213,216]],[
"ext.templateData.images","1wvuw"],["socket.io","1g15q"],["dompurify","jdu0z"],["color-picker","jq79v"],["unicodejs","1r04c"],["papaparse","1vu5u"],["rangefix","1ext9"],["spark-md5","9kzx3"],["ext.visualEditor.supportCheck","13rwp",[],3],["ext.visualEditor.sanitize","1m52e",[281,304],3],["ext.visualEditor.progressBarWidget","1hccq",[],3],["ext.visualEditor.tempWikitextEditorWidget","k7mf7",[88,81],3],["ext.visualEditor.desktopArticleTarget.init","1ieh7",[289,287,290,301,30,80,117,72],3],["ext.visualEditor.desktopArticleTarget.noscript","dyk3f"],["ext.visualEditor.targetLoader","1xdd9",[303,301,30,80,72,81],3],["ext.visualEditor.desktopTarget","5rg6q",[],3],["ext.visualEditor.desktopArticleTarget","m174q",[307,312,294,317],3],["ext.visualEditor.collabTarget","wcg2c",[305,311,88,163,212,213],3],["ext.visualEditor.collabTarget.desktop","1fh3e",[296,312,294,317],3],["ext.visualEditor.collabTarget.init","etlza",[287,163,192],3],["ext.visualEditor.collabTarget.init.styles","8xxz4"],[
"ext.visualEditor.ve","1l3o4",[],3],["ext.visualEditor.track","1ma8w",[300],3],["ext.visualEditor.core.utils","pwn7s",[301,192],3],["ext.visualEditor.core.utils.parsing","yk6md",[300],3],["ext.visualEditor.base","174lf",[302,303,283],3],["ext.visualEditor.mediawiki","1r9ng",[304,293,28,342],3],["ext.visualEditor.mwsave","tlc90",[315,23,25,49,50,212],3],["ext.visualEditor.articleTarget","wiy9q",[316,306,165],3],["ext.visualEditor.data","rbhfn",[305]],["ext.visualEditor.core","12abl",[288,287,14,284,285,286],3],["ext.visualEditor.commentAnnotation","11jy5",[309],3],["ext.visualEditor.rebase","1j0xk",[282,326,310,218,280],3],["ext.visualEditor.core.desktop","1ncrc",[309],3],["ext.visualEditor.welcome","bygbx",[192],3],["ext.visualEditor.switching","1bbiw",[45,192,204,207,209],3],["ext.visualEditor.mwcore","elabd",[327,305,314,313,124,70,8,163],3],["ext.visualEditor.mwextensions","1jh3r",[308,338,331,333,318,335,320,332,321,323],3],["ext.visualEditor.mwextensions.desktop","1jh3r",[316,322,
78],3],["ext.visualEditor.mwformatting","qik2m",[315],3],["ext.visualEditor.mwimage.core","13isj",[315],3],["ext.visualEditor.mwimage","alu91",[319,177,34,215,219],3],["ext.visualEditor.mwlink","n9w96",[315],3],["ext.visualEditor.mwmeta","1ngf7",[321,102],3],["ext.visualEditor.mwtransclusion","11sxt",[315,180],3],["treeDiffer","1i331"],["diffMatchPatch","1rln1"],["ext.visualEditor.checkList","uikde",[309],3],["ext.visualEditor.diffing","fccsv",[325,309,324],3],["ext.visualEditor.diffPage.init.styles","1u8sf"],["ext.visualEditor.diffLoader","1rup1",[293],3],["ext.visualEditor.diffPage.init","1xnb3",[329,192,204,207],3],["ext.visualEditor.language","144y5",[309,342,111],3],["ext.visualEditor.mwlanguage","1ygsh",[309],3],["ext.visualEditor.mwalienextension","erzjn",[315],3],["ext.visualEditor.mwwikitext","az76q",[321,88],3],["ext.visualEditor.mwgallery","vo1pt",[315,115,177,215],3],["ext.visualEditor.mwsignature","1dqfm",[323],3],["ext.visualEditor.experimental","1jh3r",[],3],[
"ext.visualEditor.icons","1jh3r",[339,340,205,206,207,209,210,211,212,213,216,217,218,203],3],["ext.visualEditor.moduleIcons","lf2a4"],["ext.visualEditor.moduleIndicators","bld8d"],["mobile.startup","1pa3r",[71]],["jquery.uls.data","149oa"],["ext.confirmEdit.CaptchaInputWidget","r2m8j",[193]],["mediawiki.messagePoster","13b1w",[54]]]);mw.config.set(window.RLCONF||{});mw.loader.state(window.RLSTATE||{});mw.loader.load(window.RLPAGEMODULES||[]);queue=window.RLQ||[];RLQ=[];RLQ.push=function(fn){if(typeof fn==='function'){fn();}else{RLQ[RLQ.length]=fn;}};while(queue[0]){RLQ.push(queue.shift());}NORLQ={push:function(){}};}());}