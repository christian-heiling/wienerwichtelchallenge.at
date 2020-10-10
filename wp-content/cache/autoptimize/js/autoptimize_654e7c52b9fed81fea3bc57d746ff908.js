var ExactMetrics=function(){var lastClicked=[];var internalAsOutboundCategory='';this.setLastClicked=function(valuesArray,fieldsArray,tracked){valuesArray=typeof valuesArray!=='undefined'?valuesArray:[];fieldsArray=typeof fieldsArray!=='undefined'?fieldsArray:[];tracked=typeof tracked!=='undefined'?tracked:false;lastClicked.valuesArray=valuesArray;lastClicked.fieldsArray=fieldsArray;};this.getLastClicked=function(){return lastClicked;};this.setInternalAsOutboundCategory=function(category){internalAsOutboundCategory=category;};this.getInternalAsOutboundCategory=function(){return internalAsOutboundCategory;};this.sendEvent=function(fieldsArray){__gaTrackerSend([],fieldsArray);};function __gaTrackerIsDebug(){if(window.exactmetrics_debug_mode){return true;}else{return false;}}
function __gaTrackerSend(valuesArray,fieldsArray){valuesArray=typeof valuesArray!=='undefined'?valuesArray:[];fieldsArray=typeof fieldsArray!=='undefined'?fieldsArray:{};__gaTracker('send',fieldsArray);lastClicked.valuesArray=valuesArray;lastClicked.fieldsArray=fieldsArray;lastClicked.tracked=true;__gaTrackerLog('Tracked: '+valuesArray.type);__gaTrackerLog(lastClicked);}
function __gaTrackerNotSend(valuesArray){valuesArray=typeof valuesArray!=='undefined'?valuesArray:[];lastClicked.valuesArray=valuesArray;lastClicked.fieldsArray=[];lastClicked.tracked=false;__gaTrackerLog('Not Tracked: '+valuesArray.exit);__gaTrackerLog(lastClicked);}
function __gaTrackerLog(message){if(__gaTrackerIsDebug()){console.dir(message);}}
function __gaTrackerStringTrim(x){return x.replace(/^\s+|\s+$/gm,'');}
function __gaTrackerGetDomain(){var i=0,currentdomain=document.domain,p=currentdomain.split('.'),s='_gd'+(new Date()).getTime();while(i<(p.length-1)&&document.cookie.indexOf(s+'='+s)==-1){currentdomain=p.slice(-1-(++i)).join('.');document.cookie=s+"="+s+";domain="+currentdomain+";";}
document.cookie=s+"=;expires=Thu, 01 Jan 1970 00:00:01 GMT;domain="+currentdomain+";";return currentdomain;}
function __gaTrackerGetExtension(extension){extension=extension.toString();extension=extension.substring(0,(extension.indexOf("#")==-1)?extension.length:extension.indexOf("#"));extension=extension.substring(0,(extension.indexOf("?")==-1)?extension.length:extension.indexOf("?"));extension=extension.substring(extension.lastIndexOf("/")+1,extension.length);if(extension.length>0&&extension.indexOf('.')!==-1){extension=extension.substring(extension.indexOf(".")+1);return extension;}else{return"";}}
function __gaTrackerLoaded(){return typeof(__gaTracker)!=='undefined'&&__gaTracker&&__gaTracker.hasOwnProperty("loaded")&&__gaTracker.loaded==true;}
function __gaTrackerTrackedClick(event){return event.which==1||event.which==2||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey;}
function __gaTrackerGetDownloadExtensions(){var download_extensions=[];if(typeof exactmetrics_frontend.download_extensions=='string'){download_extensions=exactmetrics_frontend.download_extensions.split(",");}
return download_extensions;}
function __gaTrackerGetInboundPaths(){var inbound_paths=[];if(typeof exactmetrics_frontend.inbound_paths=='string'){inbound_paths=JSON.parse(exactmetrics_frontend.inbound_paths);}
return inbound_paths;}
function __gaTrackerTrackedClickType(event){if(event.which==1){return'event.which=1';}else if(event.which==2){return'event.which=2';}else if(event.metaKey){return'metaKey';}else if(event.ctrlKey){return'ctrlKey';}else if(event.shiftKey){return'shiftKey';}else if(event.altKey){return'altKey';}else{return'';}}
function __gaTrackerLinkType(el){var download_extensions=__gaTrackerGetDownloadExtensions();var inbound_paths=__gaTrackerGetInboundPaths();var type='unknown';var link=el.href;var extension=__gaTrackerGetExtension(el.href);var currentdomain=__gaTrackerGetDomain();var hostname=el.hostname;var protocol=el.protocol;var pathname=el.pathname;link=link.toString();var index,len;var category=el.getAttribute("data-vars-ga-category");if(category){return category;}
if(link.match(/^javascript\:/i)){type='internal';}else if(protocol&&protocol.length>0&&(__gaTrackerStringTrim(protocol)=='tel'||__gaTrackerStringTrim(protocol)=='tel:')){type="tel";}else if(protocol&&protocol.length>0&&(__gaTrackerStringTrim(protocol)=='mailto'||__gaTrackerStringTrim(protocol)=='mailto:')){type="mailto";}else if(hostname&&currentdomain&&hostname.length>0&&currentdomain.length>0&&!hostname.endsWith('.'+currentdomain)&&hostname!==currentdomain){type="external";}else if(pathname&&JSON.stringify(inbound_paths)!="{}"&&pathname.length>0){var inbound_paths_length=inbound_paths.length;for(var inbound_paths_index=0;inbound_paths_index<inbound_paths_length;inbound_paths_index++){if(inbound_paths[inbound_paths_index].path&&inbound_paths[inbound_paths_index].label&&inbound_paths[inbound_paths_index].path.length>0&&inbound_paths[inbound_paths_index].label.length>0&&pathname.startsWith(inbound_paths[inbound_paths_index].path)){type="internal-as-outbound";internalAsOutboundCategory="outbound-link-"+inbound_paths[inbound_paths_index].label;break;}}}else if(hostname&&window.exactmetrics_experimental_mode&&hostname.length>0&&document.domain.length>0&&hostname!==document.domain){type="cross-hostname";}
if(extension&&(type==='unknown'||'external'===type)&&download_extensions.length>0&&extension.length>0){for(index=0,len=download_extensions.length;index<len;++index){if(download_extensions[index].length>0&&(link.endsWith(download_extensions[index])||download_extensions[index]==extension)){type="download";break;}}}
if(type==='unknown'){type='internal';}
return type;}
function __gaTrackerLinkTarget(el,event){var target=(el.target&&!el.target.match(/^_(self|parent|top)$/i))?el.target:false;if(event.ctrlKey||event.shiftKey||event.metaKey||event.which==2){target="_blank";}
return target;}
function __gaTrackerGetTitle(el){if(el.getAttribute("data-vars-ga-label")&&el.getAttribute("data-vars-ga-label").replace(/\n/ig,'')){return el.getAttribute("data-vars-ga-label").replace(/\n/ig,'');}else if(el.title&&el.title.replace(/\n/ig,'')){return el.title.replace(/\n/ig,'');}else if(el.innerText&&el.innerText.replace(/\n/ig,'')){return el.innerText.replace(/\n/ig,'');}else if(el.getAttribute('aria-label')&&el.getAttribute('aria-label').replace(/\n/ig,'')){return el.getAttribute('aria-label').replace(/\n/ig,'');}else if(el.alt&&el.alt.replace(/\n/ig,'')){return el.alt.replace(/\n/ig,'');}else if(el.textContent&&el.textContent.replace(/\n/ig,'')){return el.textContent.replace(/\n/ig,'');}else{return undefined;}}
function __gaTrackerGetInnerTitle(el){var children=el.children;var count=0;var child;var value;for(var i=0;i<children.length;i++){child=children[i];value=__gaTrackerGetTitle(child);if(value){return value;}
if(count==99){return undefined;}
count++;}
return undefined;}
function __gaTrackerClickEvent(event){var el=event.srcElement||event.target;var valuesArray=[];var fieldsArray;valuesArray.el=el;valuesArray.ga_loaded=__gaTrackerLoaded();valuesArray.click_type=__gaTrackerTrackedClickType(event);if(!__gaTrackerLoaded()||!__gaTrackerTrackedClick(event)){valuesArray.exit='loaded';__gaTrackerNotSend(valuesArray);return;}
while(el&&(typeof el.tagName=='undefined'||el.tagName.toLowerCase()!='a'||!el.href)){el=el.parentNode;}
if(el&&el.href&&!el.hasAttribute('xlink:href')){var link=el.href;var extension=__gaTrackerGetExtension(el.href);var download_extensions=__gaTrackerGetDownloadExtensions();var inbound_paths=__gaTrackerGetInboundPaths();var home_url=exactmetrics_frontend.home_url;var currentdomain=__gaTrackerGetDomain();var type=__gaTrackerLinkType(el);var target=__gaTrackerLinkTarget(el,event);var action=el.getAttribute("data-vars-ga-action");var label=el.getAttribute("data-vars-ga-label");valuesArray.el=el;valuesArray.el_href=el.href;valuesArray.el_protocol=el.protocol;valuesArray.el_hostname=el.hostname;valuesArray.el_port=el.port;valuesArray.el_pathname=el.pathname;valuesArray.el_search=el.search;valuesArray.el_hash=el.hash;valuesArray.el_host=el.host;valuesArray.debug_mode=__gaTrackerIsDebug();valuesArray.download_extensions=download_extensions;valuesArray.inbound_paths=inbound_paths;valuesArray.home_url=home_url;valuesArray.link=link;valuesArray.extension=extension;valuesArray.type=type;valuesArray.target=target;valuesArray.title=__gaTrackerGetTitle(el);if(!valuesArray.label&&!valuesArray.title){valuesArray.title=__gaTrackerGetInnerTitle(el);}
if(type!=='internal'&&type!=='javascript'){var __gaTrackerHitBackRun=false;var __gaTrackerHitBack=function(){if(__gaTrackerHitBackRun){return;}
__gaTrackerHitBackRun=true;window.location.href=link;};var __gaTrackerNoRedirectExternal=function(){valuesArray.exit='external';__gaTrackerNotSend(valuesArray);};var __gaTrackerNoRedirectInboundAsExternal=function(){valuesArray.exit='internal-as-outbound';__gaTrackerNotSend(valuesArray);};var __gaTrackerNoRedirectCrossHostname=function(){valuesArray.exit='cross-hostname';__gaTrackerNotSend(valuesArray);};if(target||type=='mailto'||type=='tel'){if(type=='download'){fieldsArray={hitType:'event',eventCategory:'download',eventAction:action||link,eventLabel:label||valuesArray.title,};__gaTrackerSend(valuesArray,fieldsArray);}else if(type=='tel'){fieldsArray={hitType:'event',eventCategory:'tel',eventAction:action||link,eventLabel:label||valuesArray.title.replace('tel:',''),};__gaTrackerSend(valuesArray,fieldsArray);}else if(type=='mailto'){fieldsArray={hitType:'event',eventCategory:'mailto',eventAction:action||link,eventLabel:label||valuesArray.title.replace('mailto:',''),};__gaTrackerSend(valuesArray,fieldsArray);}else if(type=='internal-as-outbound'){fieldsArray={hitType:'event',eventCategory:internalAsOutboundCategory,eventAction:action||link,eventLabel:label||valuesArray.title,};__gaTrackerSend(valuesArray,fieldsArray);}else if(type=='external'){fieldsArray={hitType:'event',eventCategory:'outbound-link',eventAction:action||link,eventLabel:label||valuesArray.title,};__gaTrackerSend(valuesArray,fieldsArray);}else if(type=='cross-hostname'){fieldsArray={hitType:'event',eventCategory:'cross-hostname',eventAction:action||link,eventLabel:label||valuesArray.title,};__gaTrackerSend(valuesArray,fieldsArray);}else{if(type&&type!='internal'){fieldsArray={hitType:'event',eventCategory:type,eventAction:action||link,eventLabel:label||valuesArray.title,};__gaTrackerSend(valuesArray,fieldsArray);}else{valuesArray.exit='type';__gaTrackerNotSend(valuesArray);}}}else{if(type!='cross-hostname'&&type!='external'&&type!='internal-as-outbound'){if(!event.defaultPrevented){if(event.preventDefault){event.preventDefault();}else{event.returnValue=false;}}}
if(type=='download'){fieldsArray={hitType:'event',eventCategory:'download',eventAction:action||link,eventLabel:label||valuesArray.title,hitCallback:__gaTrackerHitBack,};__gaTrackerSend(valuesArray,fieldsArray);}else if(type=='internal-as-outbound'){window.onbeforeunload=function(e){if(!event.defaultPrevented){if(event.preventDefault){event.preventDefault();}else{event.returnValue=false;}}
fieldsArray={hitType:'event',eventCategory:internalAsOutboundCategory,eventAction:action||link,eventLabel:label||valuesArray.title,hitCallback:__gaTrackerHitBack,};if(navigator.sendBeacon){fieldsArray.transport='beacon';}
__gaTrackerSend(valuesArray,fieldsArray);setTimeout(__gaTrackerHitBack,1000);};}else if(type=='external'){window.onbeforeunload=function(e){if(!event.defaultPrevented){if(event.preventDefault){event.preventDefault();}else{event.returnValue=false;}}
fieldsArray={hitType:'event',eventCategory:'outbound-link',eventAction:action||link,eventLabel:label||valuesArray.title,hitCallback:__gaTrackerHitBack,};if(navigator.sendBeacon){fieldsArray.transport='beacon';}
__gaTrackerSend(valuesArray,fieldsArray);setTimeout(__gaTrackerHitBack,1000);};}else if(type=='cross-hostname'){window.onbeforeunload=function(e){if(!event.defaultPrevented){if(event.preventDefault){event.preventDefault();}else{event.returnValue=false;}}
fieldsArray={hitType:'event',eventCategory:'cross-hostname',eventAction:action||link,eventLabel:label||valuesArray.title,hitCallback:__gaTrackerHitBack,};if(navigator.sendBeacon){fieldsArray.transport='beacon';}
__gaTrackerSend(valuesArray,fieldsArray);setTimeout(__gaTrackerHitBack,1000);};}else{if(type&&type!=='internal'){fieldsArray={hitType:'event',eventCategory:type,eventAction:action||link,eventLabel:label||valuesArray.title,hitCallback:__gaTrackerHitBack,};__gaTrackerSend(valuesArray,fieldsArray);}else{valuesArray.exit='type';__gaTrackerNotSend(valuesArray);}}
if(type!='external'&&type!='cross-hostname'&&type!='internal-as-outbound'){setTimeout(__gaTrackerHitBack,1000);}else{if(type=='external'){setTimeout(__gaTrackerNoRedirectExternal,1100);}else if(type=='cross-hostname'){setTimeout(__gaTrackerNoRedirectCrossHostname,1100);}else{setTimeout(__gaTrackerNoRedirectInboundAsExternal,1100);}}}}else{valuesArray.exit='internal';__gaTrackerNotSend(valuesArray);}}else{valuesArray.exit='notlink';__gaTrackerNotSend(valuesArray);}}
var prevHash=window.location.hash;function __gaTrackerHashChangeEvent(){if(exactmetrics_frontend.hash_tracking==="true"&&prevHash!=window.location.hash){prevHash=window.location.hash;__gaTracker('set','page',location.pathname+location.search+location.hash);__gaTracker('send','pageview');__gaTrackerLog("Hash change to: "+location.pathname+location.search+location.hash);}else{__gaTrackerLog("Hash change to (untracked): "+location.pathname+location.search+location.hash);}}
var __gaTrackerWindow=window;if(__gaTrackerWindow.addEventListener){__gaTrackerWindow.addEventListener("load",function(){document.body.addEventListener("click",__gaTrackerClickEvent,false);},false);window.addEventListener("hashchange",__gaTrackerHashChangeEvent,false);}else{if(__gaTrackerWindow.attachEvent){__gaTrackerWindow.attachEvent("onload",function(){document.body.attachEvent("onclick",__gaTrackerClickEvent);});window.attachEvent("onhashchange",__gaTrackerHashChangeEvent);}}
if(typeof String.prototype.endsWith!=='function'){String.prototype.endsWith=function(suffix){return this.indexOf(suffix,this.length-suffix.length)!==-1;};}
if(typeof String.prototype.startsWith!=='function'){String.prototype.startsWith=function(prefix){return this.indexOf(prefix)===0;};}
if(typeof Array.prototype.lastIndexOf!=='function'){Array.prototype.lastIndexOf=function(searchElement){'use strict';if(this===void 0||this===null){throw new TypeError();}
var n,k,t=Object(this),len=t.length>>>0;if(len===0){return-1;}
n=len-1;if(arguments.length>1){n=Number(arguments[1]);if(n!=n){n=0;}
else if(n!=0&&n!=(1/0)&&n!=-(1/0)){n=(n>0||-1)*Math.floor(Math.abs(n));}}
for(k=n>=0?Math.min(n,len-1):len-Math.abs(n);k>=0;k--){if(k in t&&t[k]===searchElement){return k;}}
return-1;};}};var ExactMetricsObject=new ExactMetrics();
function member_widget_click_handler(){jQuery(".widget div#members-list-options a").on("click",function(){var e=this;return jQuery(e).addClass("loading"),jQuery(".widget div#members-list-options a").removeClass("selected"),jQuery(this).addClass("selected"),jQuery.post(ajaxurl,{action:"widget_members",cookie:encodeURIComponent(document.cookie),_wpnonce:jQuery("input#_wpnonce-members").val(),"max-members":jQuery("input#members_widget_max").val(),filter:jQuery(this).attr("id")},function(t){jQuery(e).removeClass("loading"),member_widget_response(t)}),!1})}function member_widget_response(e){e=e.substr(0,e.length-1),"-1"!==(e=e.split("[[SPLIT]]"))[0]?jQuery(".widget ul#members-list").fadeOut(200,function(){jQuery(".widget ul#members-list").html(e[1]),jQuery(".widget ul#members-list").fadeIn(200)}):jQuery(".widget ul#members-list").fadeOut(200,function(){var t="<p>"+e[1]+"</p>";jQuery(".widget ul#members-list").html(t),jQuery(".widget ul#members-list").fadeIn(200)})}jQuery(document).ready(function(){member_widget_click_handler(),"undefined"!=typeof wp&&wp.customize&&wp.customize.selectiveRefresh&&wp.customize.selectiveRefresh.bind("partial-content-rendered",function(){member_widget_click_handler()})});
function bp_get_querystring(n){var t=location.search.split(n+"=")[1];return t?decodeURIComponent(t.split("&")[0]):null};
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e("object"==typeof exports?require("jquery"):jQuery)}(function(e){function n(e){return u.raw?e:encodeURIComponent(e)}function o(e){return u.raw?e:decodeURIComponent(e)}function i(e){return n(u.json?JSON.stringify(e):String(e))}function r(e){0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e=decodeURIComponent(e.replace(c," ")),u.json?JSON.parse(e):e}catch(e){}}function t(n,o){var i=u.raw?n:r(n);return e.isFunction(o)?o(i):i}var c=/\+/g,u=e.cookie=function(r,c,f){if(void 0!==c&&!e.isFunction(c)){if("number"==typeof(f=e.extend({},u.defaults,f)).expires){var a=f.expires,d=f.expires=new Date;d.setTime(+d+864e5*a)}return document.cookie=[n(r),"=",i(c),f.expires?"; expires="+f.expires.toUTCString():"",f.path?"; path="+f.path:"",f.domain?"; domain="+f.domain:"",f.secure?"; secure":""].join("")}for(var p=r?void 0:{},s=document.cookie?document.cookie.split("; "):[],m=0,x=s.length;m<x;m++){var v=s[m].split("="),k=o(v.shift()),l=v.join("=");if(r&&r===k){p=t(l,c);break}r||void 0===(l=t(l))||(p[k]=l)}return p};u.defaults={},e.removeCookie=function(n,o){return void 0!==e.cookie(n)&&(e.cookie(n,"",e.extend({},o,{expires:-1})),!e.cookie(n))}});
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e("object"==typeof exports?require("jquery"):jQuery)}(function(e){function t(t){return e.isFunction(t)||"object"==typeof t?t:{top:t,left:t}}var n=e.scrollTo=function(t,n,o){return e(window).scrollTo(t,n,o)};return n.defaults={axis:"xy",duration:parseFloat(e.fn.jquery)>=1.3?0:1,limit:!0},n.window=function(){return e(window)._scrollable()},e.fn._scrollable=function(){return this.map(function(){var t=this;if(!(!t.nodeName||-1!==e.inArray(t.nodeName.toLowerCase(),["iframe","#document","html","body"])))return t;var n=(t.contentWindow||t).document||t.ownerDocument||t;return/webkit/i.test(navigator.userAgent)||"BackCompat"===n.compatMode?n.body:n.documentElement})},e.fn.scrollTo=function(o,r,i){return"object"==typeof r&&(i=r,r=0),"function"==typeof i&&(i={onAfter:i}),"max"===o&&(o=9e9),i=e.extend({},n.defaults,i),r=r||i.duration,i.queue=i.queue&&i.axis.length>1,i.queue&&(r/=2),i.offset=t(i.offset),i.over=t(i.over),this._scrollable().each(function(){function s(e){u.animate(l,r,i.easing,e&&function(){e.call(this,c,i)})}if(null!==o){var a,f=this,u=e(f),c=o,l={},d=u.is("html,body");switch(typeof c){case"number":case"string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(c)){c=t(c);break}if(!(c=d?e(c):e(c,this)).length)return;case"object":(c.is||c.style)&&(a=(c=e(c)).offset())}var m=e.isFunction(i.offset)&&i.offset(f,c)||i.offset;e.each(i.axis.split(""),function(e,t){var o="x"===t?"Left":"Top",r=o.toLowerCase(),h="scroll"+o,p=f[h],y=n.max(f,t);if(a)l[h]=a[r]+(d?0:p-u.offset()[r]),i.margin&&(l[h]-=parseInt(c.css("margin"+o))||0,l[h]-=parseInt(c.css("border"+o+"Width"))||0),l[h]+=m[r]||0,i.over[r]&&(l[h]+=c["x"===t?"width":"height"]()*i.over[r]);else{var b=c[r];l[h]=b.slice&&"%"===b.slice(-1)?parseFloat(b)/100*y:b}i.limit&&/^\d+$/.test(l[h])&&(l[h]=l[h]<=0?0:Math.min(l[h],y)),!e&&i.queue&&(p!==l[h]&&s(i.onAfterFirst),delete l[h])}),s(i.onAfter)}}).end()},n.max=function(t,n){var o="x"===n?"Width":"Height",r="scroll"+o;if(!e(t).is("html,body"))return t[r]-e(t)[o.toLowerCase()]();var i="client"+o,s=t.ownerDocument.documentElement,a=t.ownerDocument.body;return Math.max(s[r],a[r])-Math.min(s[i],a[i])},n});
CLI_ACCEPT_COOKIE_NAME=(typeof CLI_ACCEPT_COOKIE_NAME!=='undefined'?CLI_ACCEPT_COOKIE_NAME:'viewed_cookie_policy');CLI_PREFERNCE_COOKIE=(typeof CLI_PREFERNCE_COOKIE!=='undefined'?CLI_PREFERNCE_COOKIE:'CookieLawInfoConsent');CLI_ACCEPT_COOKIE_EXPIRE=(typeof CLI_ACCEPT_COOKIE_EXPIRE!=='undefined'?CLI_ACCEPT_COOKIE_EXPIRE:365);CLI_COOKIEBAR_AS_POPUP=(typeof CLI_COOKIEBAR_AS_POPUP!=='undefined'?CLI_COOKIEBAR_AS_POPUP:false);var CLI_Cookie={set:function(name,value,days){if(days){var date=new Date();date.setTime(date.getTime()+(days*24*60*60*1000));var expires="; expires="+date.toGMTString();}else
var expires="";document.cookie=name+"="+value+expires+"; path=/";if(days<1)
{host_name=window.location.hostname;document.cookie=name+"="+value+expires+"; path=/; domain=."+host_name+";";if(host_name.indexOf("www")!=1)
{var host_name_withoutwww=host_name.replace('www','');document.cookie=name+"="+value+expires+"; path=/; domain="+host_name_withoutwww+";";}
host_name=host_name.substring(host_name.lastIndexOf(".",host_name.lastIndexOf(".")-1));document.cookie=name+"="+value+expires+"; path=/; domain="+host_name+";";}},read:function(name){var nameEQ=name+"=";var ca=document.cookie.split(';');for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' '){c=c.substring(1,c.length);}
if(c.indexOf(nameEQ)===0){return c.substring(nameEQ.length,c.length);}}
return null;},erase:function(name){this.set(name,"",-10);},exists:function(name){return(this.read(name)!==null);},getallcookies:function()
{var pairs=document.cookie.split(";");var cookieslist={};for(var i=0;i<pairs.length;i++){var pair=pairs[i].split("=");cookieslist[(pair[0]+'').trim()]=unescape(pair[1]);}
return cookieslist;}}
var CLI={bar_config:{},showagain_config:{},allowedCategories:[],js_blocking_enabled:false,set:function(args)
{if(typeof JSON.parse!=="function")
{console.log("CookieLawInfo requires JSON.parse but your browser doesn't support it");return;}
if(typeof args.settings!=='object')
{this.settings=JSON.parse(args.settings);}
else
{this.settings=args.settings;}
this.js_blocking_enabled=Boolean(Cli_Data.js_blocking);this.settings=args.settings;this.bar_elm=jQuery(this.settings.notify_div_id);this.showagain_elm=jQuery(this.settings.showagain_div_id);this.settingsModal=jQuery('#cliSettingsPopup');this.main_button=jQuery('.cli-plugin-main-button');this.main_link=jQuery('.cli-plugin-main-link');this.reject_link=jQuery('.cookie_action_close_header_reject');this.delete_link=jQuery(".cookielawinfo-cookie-delete");this.settings_button=jQuery('.cli_settings_button');if(this.settings.cookie_bar_as=='popup')
{CLI_COOKIEBAR_AS_POPUP=true;}
this.addStyleAttribute();this.configBar();this.toggleBar();this.attachDelete();this.attachEvents();this.configButtons();var cli_hidebar_on_readmore=this.hideBarInReadMoreLink();if(Boolean(this.settings.scroll_close)===true&&cli_hidebar_on_readmore===false)
{window.addEventListener("scroll",CLI.closeOnScroll,false);}},hideBarInReadMoreLink:function()
{if(Boolean(CLI.settings.button_2_hidebar)===true&&this.main_link.length>0&&this.main_link.hasClass('cli-minimize-bar'))
{this.hideHeader();cliBlocker.cookieBar(false);this.showagain_elm.slideDown(this.settings.animate_speed_show);return true;}
return false;},attachEvents:function()
{jQuery('.cli_action_button').click(function(e){e.preventDefault();var elm=jQuery(this);var button_action=elm.attr('data-cli_action');var open_link=elm[0].hasAttribute("href")&&elm.attr("href")!='#'?true:false;var new_window=false;if(button_action=='accept')
{CLI.accept_close();new_window=Boolean(CLI.settings.button_1_new_win)?true:false;}else if(button_action=='reject')
{CLI.reject_close();new_window=Boolean(CLI.settings.button_3_new_win)?true:false;}
if(open_link)
{if(new_window)
{window.open(elm.attr("href"),'_blank');}else
{window.location.href=elm.attr("href");}}});this.settingsPopUp();this.settingsTabbedAccordion();this.toggleUserPreferenceCheckBox();this.hideCookieBarOnClose();this.cookieLawInfoRunCallBacks();},toggleUserPreferenceCheckBox:function()
{jQuery('.cli-user-preference-checkbox').each(function(){categoryCookie='cookielawinfo-'+jQuery(this).attr('data-id');categoryCookieValue=CLI_Cookie.read(categoryCookie);if(categoryCookieValue==null)
{if(jQuery(this).is(':checked'))
{CLI_Cookie.set(categoryCookie,'yes',CLI_ACCEPT_COOKIE_EXPIRE);}else
{CLI_Cookie.set(categoryCookie,'no',CLI_ACCEPT_COOKIE_EXPIRE);}}
else
{if(categoryCookieValue=="yes")
{jQuery(this).prop("checked",true);}
else
{jQuery(this).prop("checked",false);}}});jQuery('.cli-user-preference-checkbox').click(function(){var dataID=jQuery(this).attr('data-id');var currentToggleElm=jQuery('.cli-user-preference-checkbox[data-id='+dataID+']');if(jQuery(this).is(':checked'))
{CLI_Cookie.set('cookielawinfo-'+dataID,'yes',CLI_ACCEPT_COOKIE_EXPIRE);currentToggleElm.prop('checked',true);}else
{CLI_Cookie.set('cookielawinfo-'+dataID,'no',CLI_ACCEPT_COOKIE_EXPIRE);currentToggleElm.prop('checked',false);}
CLI.checkCategories();CLI.generateConsent();});},settingsPopUp:function()
{jQuery('.cli_settings_button').click(function(e){e.preventDefault();CLI.settingsModal.addClass("cli-show").css({'opacity':0}).animate({'opacity':1});CLI.settingsModal.removeClass('cli-blowup cli-out').addClass("cli-blowup");jQuery('body').addClass("cli-modal-open");jQuery(".cli-settings-overlay").addClass("cli-show");jQuery("#cookie-law-info-bar").css({'opacity':.1});if(!jQuery('.cli-settings-mobile').is(':visible'))
{CLI.settingsModal.find('.cli-nav-link:eq(0)').click();}});jQuery('#cliModalClose').click(function(){CLI.settingsPopUpClose();});CLI.settingsModal.click(function(e){if(!(document.getElementsByClassName('cli-modal-dialog')[0].contains(e.target)))
{CLI.settingsPopUpClose();}});jQuery('.cli_enable_all_btn').click(function(){var cli_toggle_btn=jQuery(this);var enable_text=cli_toggle_btn.attr('data-enable-text');var disable_text=cli_toggle_btn.attr('data-disable-text');if(cli_toggle_btn.hasClass('cli-enabled')){CLI.disableAllCookies();cli_toggle_btn.html(enable_text);}
else
{CLI.enableAllCookies();cli_toggle_btn.html(disable_text);}
jQuery(this).toggleClass('cli-enabled');});this.privacyReadmore();},settingsTabbedAccordion:function()
{jQuery(".cli-tab-header").on("click",function(e){if(!(jQuery(e.target).hasClass('cli-slider')||jQuery(e.target).hasClass('cli-user-preference-checkbox')))
{if(jQuery(this).hasClass("cli-tab-active")){jQuery(this).removeClass("cli-tab-active");jQuery(this).siblings(".cli-tab-content").slideUp(200);}else{jQuery(".cli-tab-header").removeClass("cli-tab-active");jQuery(this).addClass("cli-tab-active");jQuery(".cli-tab-content").slideUp(200);jQuery(this).siblings(".cli-tab-content").slideDown(200);}}});},settingsPopUpClose:function()
{this.settingsModal.removeClass('cli-show');this.settingsModal.addClass('cli-out');jQuery('body').removeClass("cli-modal-open");jQuery(".cli-settings-overlay").removeClass("cli-show");jQuery("#cookie-law-info-bar").css({'opacity':1});},privacyReadmore:function()
{var el=jQuery('.cli-privacy-content .cli-privacy-content-text');if(el.length>0){var clone=el.clone(),originalHtml=clone.html(),originalHeight=el.outerHeight(),Trunc={addReadmore:function(textBlock)
{if(textBlock.html().length>250)
{jQuery('.cli-privacy-readmore').show();}
else
{jQuery('.cli-privacy-readmore').hide();}},truncateText:function(textBlock){var strippedText=jQuery('<div />').html(textBlock.html());strippedText.find('table').remove();textBlock.html(strippedText.html());currentText=textBlock.text();if(currentText.trim().length>250){var newStr=currentText.substring(0,250);textBlock.empty().html(newStr).append('...');}},replaceText:function(textBlock,original){return textBlock.html(original);}};Trunc.addReadmore(el);Trunc.truncateText(el);jQuery('a.cli-privacy-readmore').click(function(e){e.preventDefault();if(jQuery('.cli-privacy-overview').hasClass('cli-collapsed'))
{Trunc.truncateText(el);jQuery('.cli-privacy-overview').removeClass('cli-collapsed');el.css('height','100%');}
else
{jQuery('.cli-privacy-overview').addClass('cli-collapsed');Trunc.replaceText(el,originalHtml);}});}},attachDelete:function()
{this.delete_link.click(function(){CLI_Cookie.erase(CLI_ACCEPT_COOKIE_NAME);for(var k in Cli_Data.nn_cookie_ids)
{CLI_Cookie.erase(Cli_Data.nn_cookie_ids[k]);}
CLI.generateConsent();return false;});},configButtons:function()
{this.main_button.css('color',this.settings.button_1_link_colour);if(Boolean(this.settings.button_1_as_button))
{this.main_button.css('background-color',this.settings.button_1_button_colour);this.main_button.hover(function(){jQuery(this).css('background-color',CLI.settings.button_1_button_hover);},function(){jQuery(this).css('background-color',CLI.settings.button_1_button_colour);});}
this.main_link.css('color',this.settings.button_2_link_colour);if(Boolean(this.settings.button_2_as_button))
{this.main_link.css('background-color',this.settings.button_2_button_colour);this.main_link.hover(function(){jQuery(this).css('background-color',CLI.settings.button_2_button_hover);},function(){jQuery(this).css('background-color',CLI.settings.button_2_button_colour);});}
this.reject_link.css('color',this.settings.button_3_link_colour);if(Boolean(this.settings.button_3_as_button))
{this.reject_link.css('background-color',this.settings.button_3_button_colour);this.reject_link.hover(function(){jQuery(this).css('background-color',CLI.settings.button_3_button_hover);},function(){jQuery(this).css('background-color',CLI.settings.button_3_button_colour);});}
this.settings_button.css('color',this.settings.button_4_link_colour);if(Boolean(this.settings.button_4_as_button))
{this.settings_button.css('background-color',this.settings.button_4_button_colour);this.settings_button.hover(function(){jQuery(this).css('background-color',CLI.settings.button_4_button_hover);},function(){jQuery(this).css('background-color',CLI.settings.button_4_button_colour);});}},toggleBar:function()
{if(CLI_COOKIEBAR_AS_POPUP)
{this.barAsPopUp(1);}
if(CLI.settings.cookie_bar_as=='widget')
{this.barAsWidget(1);}
if(!CLI_Cookie.exists(CLI_ACCEPT_COOKIE_NAME))
{this.displayHeader();}else
{this.hideHeader();}
if(Boolean(this.settings.show_once_yn))
{setTimeout(function(){CLI.close_header();},CLI.settings.show_once);}
if(CLI.js_blocking_enabled===false){if(Boolean(Cli_Data.ccpaEnabled)===true){if(Cli_Data.ccpaType==='ccpa'&&Boolean(Cli_Data.ccpaBarEnabled)===false){cliBlocker.cookieBar(false);}}else{jQuery('.wt-cli-ccpa-opt-out,.wt-cli-ccpa-checkbox,.wt-cli-ccpa-element').remove();}}
this.showagain_elm.click(function(e){e.preventDefault();CLI.showagain_elm.slideUp(CLI.settings.animate_speed_hide,function()
{CLI.bar_elm.slideDown(CLI.settings.animate_speed_show);if(CLI_COOKIEBAR_AS_POPUP)
{CLI.showPopupOverlay();}});});},configShowAgain:function()
{this.showagain_config={'background-color':this.settings.background,'color':this.l1hs(this.settings.text),'position':'fixed','font-family':this.settings.font_family};if(Boolean(this.settings.border_on))
{var border_to_hide='border-'+this.settings.notify_position_vertical;this.showagain_config['border']='1px solid '+this.l1hs(this.settings.border);this.showagain_config[border_to_hide]='none';}
var cli_win=jQuery(window);var cli_winw=cli_win.width();var showagain_x_pos=this.settings.showagain_x_position;if(cli_winw<300)
{showagain_x_pos=10;this.showagain_config.width=cli_winw-20;}else
{this.showagain_config.width='auto';}
var cli_defw=cli_winw>400?500:cli_winw-20;if(CLI_COOKIEBAR_AS_POPUP)
{var sa_pos=this.settings.popup_showagain_position;var sa_pos_arr=sa_pos.split('-');if(sa_pos_arr[1]=='left')
{this.showagain_config.left=showagain_x_pos;}else if(sa_pos_arr[1]=='right')
{this.showagain_config.right=showagain_x_pos;}
if(sa_pos_arr[0]=='top')
{this.showagain_config.top=0;}else if(sa_pos_arr[0]=='bottom')
{this.showagain_config.bottom=0;}
this.bar_config['position']='fixed';}else if(this.settings.cookie_bar_as=='widget')
{this.showagain_config.bottom=0;if(this.settings.widget_position=='left')
{this.showagain_config.left=showagain_x_pos;}else if(this.settings.widget_position=='right')
{this.showagain_config.right=showagain_x_pos;}}
else
{if(this.settings.notify_position_vertical=="top")
{this.showagain_config.top='0';}
else if(this.settings.notify_position_vertical=="bottom")
{this.bar_config['position']='fixed';this.bar_config['bottom']='0';this.showagain_config.bottom='0';}
if(this.settings.notify_position_horizontal=="left")
{this.showagain_config.left=showagain_x_pos;}else if(this.settings.notify_position_horizontal=="right")
{this.showagain_config.right=showagain_x_pos;}}
this.showagain_elm.css(this.showagain_config);},configBar:function()
{this.bar_config={'background-color':this.settings.background,'color':this.settings.text,'font-family':this.settings.font_family};if(this.settings.notify_position_vertical=="top")
{this.bar_config['top']='0';if(Boolean(this.settings.header_fix)===true)
{this.bar_config['position']='fixed';}}else
{this.bar_config['bottom']='0';}
this.configShowAgain();this.bar_elm.css(this.bar_config).hide();},l1hs:function(str)
{if(str.charAt(0)=="#"){str=str.substring(1,str.length);}else{return"#"+str;}
return this.l1hs(str);},close_header:function()
{CLI_Cookie.set(CLI_ACCEPT_COOKIE_NAME,'yes',CLI_ACCEPT_COOKIE_EXPIRE);this.hideHeader();},accept_close:function()
{this.hidePopupOverlay();this.generateConsent();this.cookieLawInfoRunCallBacks();CLI_Cookie.set(CLI_ACCEPT_COOKIE_NAME,'yes',CLI_ACCEPT_COOKIE_EXPIRE);if(Boolean(this.settings.notify_animate_hide))
{if(CLI.js_blocking_enabled===true){this.bar_elm.slideUp(this.settings.animate_speed_hide,cliBlocker.runScripts);}else{this.bar_elm.slideUp(this.settings.animate_speed_hide);}}else
{if(CLI.js_blocking_enabled===true){this.bar_elm.hide(cliBlocker.runScripts);}else{this.bar_elm.hide();}}
if(Boolean(this.settings.showagain_tab))
{this.showagain_elm.slideDown(this.settings.animate_speed_show);}
if(Boolean(this.settings.accept_close_reload)===true)
{this.reload_current_page();}
return false;},reject_close:function()
{this.hidePopupOverlay();this.generateConsent();this.cookieLawInfoRunCallBacks();for(var k in Cli_Data.nn_cookie_ids)
{CLI_Cookie.erase(Cli_Data.nn_cookie_ids[k]);}
CLI_Cookie.set(CLI_ACCEPT_COOKIE_NAME,'no',CLI_ACCEPT_COOKIE_EXPIRE);if(Boolean(this.settings.notify_animate_hide))
{if(CLI.js_blocking_enabled===true){this.bar_elm.slideUp(this.settings.animate_speed_hide,cliBlocker.runScripts);}else{this.bar_elm.slideUp(this.settings.animate_speed_hide);}}else
{if(CLI.js_blocking_enabled===true){this.bar_elm.hide(cliBlocker.runScripts);}else{this.bar_elm.hide();}}
if(Boolean(this.settings.showagain_tab))
{this.showagain_elm.slideDown(this.settings.animate_speed_show);}
if(Boolean(this.settings.reject_close_reload)===true)
{this.reload_current_page();}
return false;},reload_current_page:function()
{if(typeof cli_flush_cache!=='undefined'&&cli_flush_cache===true)
{window.location.href=this.add_clear_cache_url_query();}else
{window.location.reload(true);}},add_clear_cache_url_query:function()
{var cli_rand=new Date().getTime()/1000;var cli_url=window.location.href;var cli_hash_arr=cli_url.split('#');var cli_urlparts=cli_hash_arr[0].split('?');if(cli_urlparts.length>=2)
{var cli_url_arr=cli_urlparts[1].split('&');cli_url_temp_arr=new Array();for(var cli_i=0;cli_i<cli_url_arr.length;cli_i++)
{var cli_temp_url_arr=cli_url_arr[cli_i].split('=');if(cli_temp_url_arr[0]=='cli_action')
{}else
{cli_url_temp_arr.push(cli_url_arr[cli_i]);}}
cli_urlparts[1]=cli_url_temp_arr.join('&');cli_url=cli_urlparts.join('?')+(cli_url_temp_arr.length>0?'&':'')+'cli_action=';}else
{cli_url=cli_hash_arr[0]+'?cli_action=';}
cli_url+=cli_rand;if(cli_hash_arr.length>1)
{cli_url+='#'+cli_hash_arr[1];}
return cli_url;},closeOnScroll:function()
{if(window.pageYOffset>100&&!CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME))
{CLI.accept_close();if(Boolean(CLI.settings.scroll_close_reload)===true)
{window.location.reload();}
window.removeEventListener("scroll",CLI.closeOnScroll,false);}},displayHeader:function()
{if(Boolean(this.settings.notify_animate_show))
{this.bar_elm.slideDown(this.settings.animate_speed_show);}else
{this.bar_elm.show();}
this.showagain_elm.hide();if(CLI_COOKIEBAR_AS_POPUP)
{this.showPopupOverlay();}},hideHeader:function()
{if(Boolean(this.settings.showagain_tab))
{if(Boolean(this.settings.notify_animate_show))
{this.showagain_elm.slideDown(this.settings.animate_speed_show);}else{this.showagain_elm.show();}}else
{this.showagain_elm.hide();}
this.bar_elm.slideUp(this.settings.animate_speed_show);this.hidePopupOverlay();},hidePopupOverlay:function()
{jQuery('body').removeClass("cli-barmodal-open");jQuery(".cli-popupbar-overlay").removeClass("cli-show");},showPopupOverlay:function()
{if(this.bar_elm.length){if(Boolean(this.settings.popup_overlay))
{jQuery('body').addClass("cli-barmodal-open");jQuery(".cli-popupbar-overlay").addClass("cli-show");}}},barAsWidget:function(a)
{var cli_elm=this.bar_elm;cli_elm.attr('data-cli-type','widget');var cli_win=jQuery(window);var cli_winh=cli_win.height()-40;var cli_winw=cli_win.width();var cli_defw=cli_winw>400?300:cli_winw-30;cli_elm.css({'width':cli_defw,'height':'auto','max-height':cli_winh,'overflow':'auto','position':'fixed','box-sizing':'border-box'});if(this.checkifStyleAttributeExist()===false){cli_elm.css({'padding':'25px 15px'});}
if(this.settings.widget_position=='left')
{cli_elm.css({'left':'15px','right':'auto','bottom':'15px','top':'auto'});}else
{cli_elm.css({'left':'auto','right':'15px','bottom':'15px','top':'auto'});}
if(a)
{this.setResize();}},barAsPopUp:function(a)
{if(typeof cookie_law_info_bar_as_popup==='function')
{return false;}
var cli_elm=this.bar_elm;cli_elm.attr('data-cli-type','popup');var cli_win=jQuery(window);var cli_winh=cli_win.height()-40;var cli_winw=cli_win.width();var cli_defw=cli_winw>700?500:cli_winw-20;cli_elm.css({'width':cli_defw,'height':'auto','max-height':cli_winh,'bottom':'','top':'50%','left':'50%','margin-left':(cli_defw/2)*-1,'margin-top':'-100px','overflow':'auto'}).addClass('cli-bar-popup cli-modal-content');if(this.checkifStyleAttributeExist()===false){cli_elm.css({'padding':'25px 15px'});}
cli_h=cli_elm.height();li_h=cli_h<200?200:cli_h;cli_elm.css({'top':'50%','margin-top':((cli_h/2)+30)*-1});setTimeout(function(){cli_elm.css({'bottom':''});},100);if(a)
{this.setResize();}},setResize:function()
{var resizeTmr=null;jQuery(window).resize(function(){clearTimeout(resizeTmr);resizeTmr=setTimeout(function()
{if(CLI_COOKIEBAR_AS_POPUP)
{CLI.barAsPopUp();}
if(CLI.settings.cookie_bar_as=='widget')
{CLI.barAsWidget();}
CLI.configShowAgain();},500);});},enableAllCookies:function()
{jQuery('.cli-user-preference-checkbox').each(function(){var cli_chkbox_elm=jQuery(this);var cli_chkbox_data_id=cli_chkbox_elm.attr('data-id');if(cli_chkbox_data_id!='checkbox-necessary')
{cli_chkbox_elm.prop('checked',true);CLI_Cookie.set('cookielawinfo-'+cli_chkbox_data_id,'yes',CLI_ACCEPT_COOKIE_EXPIRE);}});},hideCookieBarOnClose:function(){jQuery(document).on('click','.cli_cookie_close_button',function(e){e.preventDefault();var elm=jQuery(this);var button_action=elm.attr('data-cli_action');if(Cli_Data.ccpaType==='ccpa')
{CLI.enableAllCookies();}
CLI.accept_close();});},checkCategories:function()
{var cliAllowedCategories=[];var cli_categories={};jQuery('.cli-user-preference-checkbox').each(function()
{var status=false;cli_chkbox_elm=jQuery(this);cli_chkbox_data_id=cli_chkbox_elm.attr('data-id');cli_chkbox_data_id=cli_chkbox_data_id.replace('checkbox-','');cli_chkbox_data_id_trimmed=cli_chkbox_data_id.replace('-','_')
if(jQuery(cli_chkbox_elm).is(':checked'))
{status=true;cliAllowedCategories.push(cli_chkbox_data_id);}
cli_categories[cli_chkbox_data_id_trimmed]=status;});CLI.allowedCategories=cliAllowedCategories;},cookieLawInfoRunCallBacks:function()
{this.checkCategories();if(CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME)=='yes')
{if("function"==typeof CookieLawInfo_Accept_Callback){CookieLawInfo_Accept_Callback();}}},generateConsent:function()
{var preferenceCookie=CLI_Cookie.read(CLI_PREFERNCE_COOKIE);cliConsent={};if(preferenceCookie!==null){cliConsent=window.atob(preferenceCookie);cliConsent=JSON.parse(cliConsent);}
cliConsent.ver=Cli_Data.consentVersion;categories=[];jQuery('.cli-user-preference-checkbox').each(function(){categoryVal='';cli_chkbox_data_id=jQuery(this).attr('data-id');cli_chkbox_data_id=cli_chkbox_data_id.replace('checkbox-','');if(jQuery(this).is(':checked'))
{categoryVal=true;}
else
{categoryVal=false;}
cliConsent[cli_chkbox_data_id]=categoryVal;});cliConsent=JSON.stringify(cliConsent);cliConsent=window.btoa(cliConsent);CLI_Cookie.set(CLI_PREFERNCE_COOKIE,cliConsent,CLI_ACCEPT_COOKIE_EXPIRE);},cookieLawInfoRunCallBacks:function()
{this.checkCategories();if(CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME)=='yes')
{if("function"==typeof CookieLawInfo_Accept_Callback){CookieLawInfo_Accept_Callback();}}},addStyleAttribute:function()
{var bar=this.bar_elm;var styleClass='';if(jQuery(bar).find('.cli-bar-container').length>0)
{styleClass=jQuery('.cli-bar-container').attr('class');styleClass=jQuery.trim(styleClass.replace('cli-bar-container',''));jQuery(bar).attr('data-cli-style',styleClass);}},CookieLawInfo_Callback:function(enableBar,enableBlocking){enableBar=typeof enableBar!=='undefined'?enableBar:true;enableBlocking=typeof enableBlocking!=='undefined'?enableBlocking:true;if(CLI.js_blocking_enabled===true&&Boolean(Cli_Data.custom_integration)===true){cliBlocker.cookieBar(enableBar);cliBlocker.runScripts(enableBlocking);}},checkifStyleAttributeExist:function()
{var exist=false;var attr=this.bar_elm.attr('data-cli-style');if(typeof attr!==typeof undefined&&attr!==false){exist=true;}
return exist;}}
var cliBlocker={blockingStatus:true,scriptsLoaded:false,ccpaEnabled:false,ccpaRegionBased:false,ccpaApplicable:false,ccpaBarEnabled:false,cliShowBar:true,checkPluginStatus:function(callbackA,callbackB)
{this.ccpaEnabled=Boolean(Cli_Data.ccpaEnabled);this.ccpaRegionBased=Boolean(Cli_Data.ccpaRegionBased);this.ccpaBarEnabled=Boolean(Cli_Data.ccpaBarEnabled);if(Boolean(Cli_Data.custom_integration)===true){callbackA(false);}
else{if(this.ccpaEnabled===true){this.ccpaApplicable=true;if(Cli_Data.ccpaType==='ccpa'){if(this.ccpaBarEnabled!==true){this.cliShowBar=false;this.blockingStatus=false;}}}else{jQuery('.wt-cli-ccpa-opt-out,.wt-cli-ccpa-checkbox,.wt-cli-ccpa-element').remove();}
callbackA(this.cliShowBar);callbackB(this.blockingStatus);}},cookieBar:function(showbar)
{showbar=typeof showbar!=='undefined'?showbar:true;cliBlocker.cliShowBar=showbar;if(cliBlocker.cliShowBar===false)
{CLI.bar_elm.hide();CLI.showagain_elm.hide();CLI.settingsModal.removeClass('cli-blowup cli-out');CLI.hidePopupOverlay();jQuery(".cli-settings-overlay").removeClass("cli-show");}
else
{if(!CLI_Cookie.exists(CLI_ACCEPT_COOKIE_NAME))
{CLI.displayHeader();}
else
{CLI.hideHeader();}
CLI.settingsModal.show();jQuery('.cli-modal-backdrop').show();}},runScripts:function(blocking)
{blocking=typeof blocking!=='undefined'?blocking:true;cliBlocker.blockingStatus=blocking;srcReplaceableElms=['iframe','IFRAME','EMBED','embed','OBJECT','object','IMG','img'];var genericFuncs={renderByElement:function()
{cliScriptFuncs.renderScripts();cliBlocker.scriptsLoaded=true;},};var cliScriptFuncs={scriptsDone:function()
{if(typeof Cli_Data.triggerDomRefresh!=='undefined'){if(Boolean(Cli_Data.triggerDomRefresh)===true)
{var DOMContentLoadedEvent=document.createEvent('Event')
DOMContentLoadedEvent.initEvent('DOMContentLoaded',true,true)
window.document.dispatchEvent(DOMContentLoadedEvent);}}},seq:function(arr,callback,index){if(typeof index==='undefined'){index=0}
arr[index](function(){index++
if(index===arr.length){callback()}else{cliScriptFuncs.seq(arr,callback,index)}})},insertScript:function($script,callback){var s='';var scriptType=$script.getAttribute('data-cli-script-type');var elementPosition=$script.getAttribute('data-cli-element-position');var isBlock=$script.getAttribute('data-cli-block');var s=document.createElement('script');var ccpaOptedOut=cliBlocker.ccpaOptedOut();s.type='text/plain';if($script.async)
{s.async=$script.async;}
if($script.defer)
{s.defer=$script.defer;}
if($script.src){s.onload=callback
s.onerror=callback
s.src=$script.src}else{s.textContent=$script.innerText}
var attrs=jQuery($script).prop("attributes");for(var ii=0;ii<attrs.length;++ii){if(attrs[ii].nodeName!=='id'){s.setAttribute(attrs[ii].nodeName,attrs[ii].value);}}
if(cliBlocker.blockingStatus===true)
{if((CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME)=='yes'&&CLI.allowedCategories.indexOf(scriptType)!==-1))
{s.setAttribute('data-cli-consent','accepted');s.type='text/javascript';}
if(cliBlocker.ccpaApplicable===true){if(ccpaOptedOut===true||CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME)==null){s.type='text/plain';}}}
else
{s.type='text/javascript';}
if($script.type!=s.type)
{if(elementPosition==='head'){document.head.appendChild(s);}else{document.body.appendChild(s);}
if(!$script.src){callback()}
$script.parentNode.removeChild($script);}
else{callback();}},renderScripts:function()
{var $scripts=document.querySelectorAll('script[data-cli-class="cli-blocker-script"]');if($scripts.length>0)
{var runList=[]
var typeAttr
Array.prototype.forEach.call($scripts,function($script){typeAttr=$script.getAttribute('type')
runList.push(function(callback){cliScriptFuncs.insertScript($script,callback)})})
cliScriptFuncs.seq(runList,cliScriptFuncs.scriptsDone);}}};genericFuncs.renderByElement();},ccpaOptedOut:function(){var ccpaOptedOut=false;var preferenceCookie=CLI_Cookie.read(CLI_PREFERNCE_COOKIE);if(preferenceCookie!==null){cliConsent=window.atob(preferenceCookie);cliConsent=JSON.parse(cliConsent);if(typeof cliConsent.ccpaOptout!=='undefined'){ccpaOptedOut=cliConsent.ccpaOptout;}}
return ccpaOptedOut;}}
jQuery(document).ready(function(){if(typeof cli_cookiebar_settings!='undefined')
{CLI.set({settings:cli_cookiebar_settings});if(CLI.js_blocking_enabled===true){cliBlocker.checkPluginStatus(cliBlocker.cookieBar,cliBlocker.runScripts);}}});