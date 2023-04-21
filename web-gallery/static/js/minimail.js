var MiniMail=Class.create();MiniMail.prototype={initialize:function(G){var W=this;var K={pageSize:3,start:0,total:0,bodyMaxLength:4096,secondLevel:false,label:"inbox",conversationId:null,unreadOnly:false,nextPage:function(){W.options.start+=W.options.pageSize;if(W.options.start>W.options.total){W.options.lastPage()
}},prevPage:function(){W.options.start-=W.options.pageSize;if(W.options.start<0){W.options.start=0}},firstPage:function(){W.options.start=0},lastPage:function(){W.options.start=W.options.total-W.options.total%W.options.pageSize-(W.options.total%W.options.pageSize==0?W.options.pageSize:0)},reset:function(){W.options.start=0;
W.options.unreadOnly=false}};W.options=Object.extend(K,G||{});W.element=$("minimail");if(W.options.secondLevel){W.element.addClassName("second-level-auth")}W.updateTarget=W.element.down(".minimail-contents");$("message-compose").observe("submit",Event.stop);var P=function(f){return $(f).up(".message-item").id.substring(4)
};var F=function(f){W.updateTarget.setStyle({visibility:"hidden"});X(f,"error",20000)};var I=function(f,h){var g=function(i,j){return function(l,k){if(403==l.status){F(l.responseText);return }if(k&&(!!k.errorMessage||!!k.subjectError||!!k.bodyError)){if(!!$("message-subject")){if(!!k.subjectError){$("message-subject").addClassName("error")
}else{$("message-subject").removeClassName("error")}}if(!!$("message-body")){if(!!k.bodyError){$("message-body").addClassName("error")}else{$("message-body").removeClassName("error")}}X([k.errorMessage||null,k.subjectError||null,k.bodyError||null].compact().join("<br />"),"error");W.updateTarget.select(".navigation .progress").invoke("update","");
if(typeof j=="function"){j()}return }$("message-compose").hide();W.updateTarget.update(l.responseText);W.updateTarget.select(".navigation .progress").invoke("update","");if(k){W.options.total=k.totalMessages}if(typeof i=="function"){i()}if(k&&k.message){X(k.message)}if(W.element.down("div.re-auth")!=null){W.element.addClassName("second-level-auth")
}Rounder.init()}};h.onComplete=g(h.onComplete,h.onError);W.updateTarget.select(".navigation .progress").each(Element.wait);new Ajax.Request(f,h)};var d=function(f){W.options.label=f;W.options.reset();I(habboReqPath+"/minimail/loadMessages",{parameters:{label:W.options.label}})};var Z=function(g){g.apply(this);I(habboReqPath+"/minimail/loadMessages",{parameters:{label:W.options.label,start:W.options.start,conversationId:W.options.conversationId,unreadOnly:W.options.unreadOnly}})
};var R=function(){var j=$("message-subject");var i=$("message-body");var g=function(k,l){if(!k(l)){l.addClassName("error");return false}else{l.removeClassName("error")}return true};var h=g(function(k){return W.recipientInput.bits.size()>0},W.recipientInput.holder);var f=g(function(k){return k.value.length>0
},j);return h&&f&&L(i)};var L=function(f){if(f.value.length==0){return confirm(L10N.get("minimail.empty_body.confirm"))}return true};var V=function(f){var h=W.recipientInput.bits.values().join(",");if(!R()){return }var i=$("message-subject");var g=$("message-body");I(habboReqPath+"/minimail/sendMessage",{parameters:{recipientIds:h,subject:i.value,body:g.value},onComplete:W.options.reset})
};var M=[];var Y=function(f,h){var g=$("msg-"+h);$(f).next().toggle();if($(f).next().visible()){var i=g.down("div.message-body").down("div");Element.wait(i);new Ajax.Request(habboReqPath+"/minimail/loadMessage",{method:"get",parameters:{messageId:h,label:W.options.label},onComplete:function(k,j){if(k.status==403){F(k.responseText);
return }i.update(k.responseText)}});g.addClassName("opened");if(g.hasClassName("unread")){$(g).down(".message-preview").morph("background-color: #DCDCDC",{duration:4});$(g).down(".message-body").morph("background-color: #DCDCDC",{duration:4});$(g).down(".message-body-bottom").morph("background-color: #DCDCDC",{duration:4})
}}else{g.removeClassName("opened");g.removeClassName("unread").addClassName("read")}if($("msg-"+h).visible()&&$(f).getAttribute("status")=="unread"&&M.indexOf(h)==-1){M.push(h);$$(".minimail-unread").each(function(j){var k=parseInt(j.down("b").innerHTML);k--;if(k>0){j.down("b").update(k)}else{j.remove()
}})}};var A=function(i,g){var h=$(i).up(2).next("div");var f=h.down("textarea");Q(f);Utils.limitTextarea(f,W.options.bodyMaxLength);$(i).up(2).hide();h.show();$(f).focus();$(i).up(".contents").addClassName("replying")};var Q=function(g){if(!g.hasClassName("bbcode-enabled")){var h=new Control.TextArea.ToolBar.BBCode(g);
var f={red:["#d80000",L10N.get("bbcode.colors.red")],orange:["#fe6301",L10N.get("bbcode.colors.orange")],yellow:["#ffce00",L10N.get("bbcode.colors.yellow")],green:["#6cc800",L10N.get("bbcode.colors.green")],cyan:["#00c6c4",L10N.get("bbcode.colors.cyan")],blue:["#0070d7",L10N.get("bbcode.colors.blue")],gray:["#828282",L10N.get("bbcode.colors.gray")],black:["#000000",L10N.get("bbcode.colors.black")]};
h.addColorSelect(L10N.get("bbcode.colors.label"),f,false);h.addHabboLinkTools();g.addClassName("bbcode-enabled")}};var O=function(f){$(f).up(2).hide();$(f).up(2).previous("div").show();$(f).up(".contents").removeClassName("replying")};var D=function(h,g){var f=$(h).up(2).down("textarea");if(L(f)){I(habboReqPath+"/minimail/sendMessage",{parameters:{messageId:g,body:f.value},onComplete:W.options.reset})
}};var b=function(f,g){var g=g||P(f);$(f).up(3).remove();if(W.options.total-W.options.start==1){W.options.prevPage()}I(habboReqPath+"/minimail/deleteMessage",{parameters:{messageId:g,start:W.options.start,label:W.options.label,conversationId:W.options.conversationId}})};var J=function(f,g){var g=g||P(f);$(f).up(3).remove();
if(W.options.total-W.options.start==1){W.options.prevPage()}I(habboReqPath+"/minimail/undeleteMessage",{parameters:{messageId:g,start:W.options.start,label:W.options.label}})};var H={loading:false,loaded:false};var U=function(f,g){$("message-list").hide();$(f).update(L10N.get("minimail.cancel"));if(W.options.friendCount==0){$("message-compose").show();
return }if(!!Cookie.get("friendlist")){Cookie.erase("friendlist");H.loaded=false}if(!H.loaded&&!H.loading){var h=$("message-compose-wait");h.wait();h.setStyle({height:$("message-list").getHeight()+"px"});Q($("message-compose").down("textarea"));E(function(){h.hide();Utils.limitTextarea("message-body",W.options.bodyMaxLength);
B(g)})}else{B(g)}};var C=function(f){$("message-compose").hide();$("message-list").show();$(f).update(L10N.get("minimail.compose"));if(!!W.recipientInput){W.recipientInput.hideInstructions()}W.recipientInput.clear()};var B=function(g){if(!!g){g.split(",").each(function(k){var j=W.recipientInput.bits.values();
var i=W.recipientInput.data.find(function(l){return l.id==k&&j.indexOf(parseInt(k))<0});if(!!i){W.recipientInput.add(i)}})}var h=$("message-subject");var f=$("message-body");h.removeClassName("error");h.value="";f.removeClassName("error");f.value="";$("message-compose").select(".preview-area-container").invoke("remove");
$("message-compose").show();if(!g){W.recipientInput.clear()}};var E=function(f){H.loading=true;new Ajax.Request(habboReqPath+"/minimail/recipients",{method:"get",onComplete:function(g){if(g.status==403){F(g.responseText);return }H.loading=false;if(!W.recipientInput){W.recipientInput=new AutocompleteList("message-recipients","message-recipients-auto",{data:g.responseText.evalJSON(true),maxLength:32,maxRecipients:W.options.maxRecipients||10,maxRecipientsReachedCallback:function(){$("message-subject").focus()
},onInputFocus:function(h){h.autoShow()},onInputBlur:function(h){(h.lastinput||h.maininput.retrieveData("input")).clear();h.hideInstructions()}})}else{W.recipientInput.data=g.responseText.evalJSON(true)}H.loaded=true;if(!!f){f()}}})};var S=function(g,f,h){W.options.label="conversation";W.options.conversationId=h;
W.options.reset();I(habboReqPath+"/minimail/loadMessages",{parameters:{label:W.options.label,messageId:f,conversationId:h}})};var e=function(){W.updateTarget.select(".navigation .progress").each(Element.wait);I(habboReqPath+"/minimail/emptyTrash",{})};var X=function(j,i,f){var h="notification "+(i||"");var g=new Element("div",{className:h});
g.update(j);$("minimail").appendChild(g);var k=window.setTimeout(function(){Effect.Fade(g,{afterFinish:function(){try{g.remove()}catch(l){}}})},f||2000);g.observe("click",function(l){window.clearTimeout(k);g.remove()})};var T=function(g,i){Overlay.show();var h=function(){if(!!$("minimail-report")){$("minimail-report").remove()
}Overlay.hide()};var j=function(){h();if(H.loaded){E()}};var f=Dialog.createDialog("minimail-report",L10N.get("minimail.report.title"),false,false,false,h);Dialog.moveDialogToCenter(f);Dialog.setAsWaitDialog(f);f.observe("click",Event.delegate({"a.cancel-report > *":function(k){Event.stop(k);h()},"a.send-report > *":function(k){Event.stop(k);
Dialog.setAsWaitDialog(f);I(habboReqPath+"/minimail/report",{parameters:{messageId:i,start:W.options.start,label:W.options.label},onComplete:j,onError:j})}}));new Ajax.Request(habboReqPath+"/minimail/confirmReport",{parameters:{messageId:i},onComplete:function(k){Dialog.setDialogBody(f,k.responseText)}})};var N=function(h){var i=h.up(2);
var f=i.down("textarea");var j;var g=i.select(".preview-area");if(g.size()>0){j=g.first()}else{j=new Element("div",{className:"preview-area"});i.insert(j)}j.wait();new Ajax.Request(habboReqPath+"/minimail/preview",{parameters:{body:f.value},onComplete:function(k){j.update(k.responseText);if(!j.hasClassName("rounded-done")){Rounder.addCorners(j,4,4,"preview-area-container")
}}})};Event.observe(W.element,"click",Event.delegate({".labels a":function(f){Event.stop(f);d(Event.element(f).getAttribute("label"))},".labels a *":function(f){Event.stop(f);d(Event.element(f).up("*[label]").getAttribute("label"))},".compose > *":function(f){Event.stop(f);if($("message-compose").visible()){C(Event.element(f))
}else{U(Event.element(f))}},".message-preview":function(f){Event.stop(f);Y(Event.element(f),P(Event.element(f)))},".message-preview > *":function(f){Event.stop(f);Y(Event.element(f).up(),P(Event.element(f)))},".navigation .oldest":function(f){Event.stop(f);Z(W.options.lastPage)},".navigation .older":function(f){Event.stop(f);
Z(W.options.nextPage)},".navigation .newest":function(f){Event.stop(f);Z(W.options.firstPage)},".navigation .newer":function(f){Event.stop(f);Z(W.options.prevPage)},".related-messages":function(f){Event.stop(f);S(this,P(Event.element(f)),Event.element(f).id.substring(4))},".send > *":function(f){Event.stop(f);
V(Event.element(f))},".reply > *":function(f){Event.stop(f);A(this,P(Event.element(f)))},".delete > *":function(f){Event.stop(f);b(this,P(Event.element(f)))},".undelete > *":function(f){Event.stop(f);J(this,P(Event.element(f)))},".send-reply > *":function(f){Event.stop(f);D(this,P(Event.element(f)))},".cancel-reply > *":function(f){Event.stop(f);
O(this)},".unread-only":function(f){W.options.unreadOnly=Event.element(f).checked;I(habboReqPath+"/minimail/loadMessages",{parameters:{label:W.options.label,unreadOnly:W.options.unreadOnly}})},".empty-trash":function(f){Event.stop(f);e()},".report":function(f){Event.stop(f);T(Event.element(f),P(Event.element(f)))
},".preview > *":function(f){Event.stop(f);N(Event.element(f))}}));HashHistory.observe("mail/.*",function(g){window.focus();W.element.scrollTo();var f=g.split("/");if(f[1]=="compose"){U(W.element.select(".compose b").first(),f[2])}else{if(f[1]=="inbox"){d("inbox")}}})}};var PrettyDate={};PrettyDate.prettifyDates=function(){var A=function(E){var C=new Date((E||"").replace(/-/g,"/").replace(/[TZ]/g," ")),D=((new Date().getTime()+new Date().getTimezoneOffset()*60000-C.getTime())/1000),B=Math.floor(D/86400);
if(isNaN(B)||B<0||B>=31){return }return B==0&&(D<60&&L10N.get("date.pretty.just_now")||D<120&&L10N.get("date.pretty.one_minute_ago")||D<3600&&L10N.get("date.pretty.minutes_ago",Math.floor(D/60))||D<7200&&L10N.get("date.pretty.one_hour_ago")||D<86400&&L10N.get("date.pretty.hours_ago",Math.floor(D/3600)))||B==1&&L10N.get("date.pretty.yesterday")||B<7&&L10N.get("date.pretty.days_ago",B)||B==7&&L10N.get("date.pretty.one_week_ago")||B<31&&L10N.get("date.pretty.weeks_ago",Math.ceil(B/7))
};$$(".message-tstamp").each(function(B){var C=A(B.getAttribute("isotime"));if(C){B.update(C)}})};HabboView.add(function(){setInterval(PrettyDate.prettifyDates,30000)});
/* Copyright: InteRiders <http://interiders.com/> - Distributed under MIT - Keep this message! */
var ResizableTextbox=Class.create({options:$H({minWidth:72,maxWidth:500,step:12}),initialize:function(B,A){var C=this;
this.options.update(A);this.el=$(B);this.width=this.el.offsetWidth;this.el.observe(Prototype.Browser.IE?"keydown":"keypress",function(){var D=C.options.get("step")*$F(this).length;if(D<C.options.get("minWidth")){D=C.options.get("minWidth")}if(D>C.options.get("maxWidth")){D=C.options.get("maxWidth")}this.setStyle({width:D+"px"})
}).observe("keydown",function(){this.cacheData("rt-value",$F(this).length)})}});var TextboxList=Class.create({options:$H({resizable:{},className:"bit",separator:"###",extrainputs:true,startinput:true,hideempty:true,fetchFile:undefined,results:10,wordMatch:false}),initialize:function(B,A){this.options.update(A);
this.element=$(B).hide();this.bits=new Hash();this.events=new Hash();this.count=0;this.current=false;this.maininput=this.createInput({"class":"maininput"});this.holder=new Element("ul",{"class":"tbl-holder"}).insert(this.maininput);this.element.insert({before:this.holder});this.holder.observe("click",function(C){C.stop();
if(this.maininput!=this.current){this.focus(this.maininput)}}.bind(this));this.makeResizable(this.maininput);this.setEvents()},setEvents:function(){document.observe(Prototype.Browser.IE?"keydown":"keypress",function(A){if(!this.current){return }if(this.current.retrieveData("type")=="box"&&A.keyCode==Event.KEY_BACKSPACE){A.stop()
}}.bind(this));document.observe("keyup",function(A){A.stop();if(!this.current){return }switch(A.keyCode){case Event.KEY_LEFT:return this.move("left");case Event.KEY_RIGHT:return this.move("right");case Event.KEY_DELETE:case Event.KEY_BACKSPACE:return this.moveDispose()}}.bind(this)).observe("click",function(){document.fire("blur")
}.bindAsEventListener(this))},update:function(){this.element.value=this.bits.values().join(this.options.get("separator"));return this},add:function(C,A){var D=this.options.get("className")+"-"+this.count++;var B=this.createBox($pick(A,C),{id:D});(this.current||this.maininput).insert({before:B});B.observe("click",function(E){E.stop();
this.focus(B)}.bind(this));this.bits.set(D,C.id);if(this.options.get("extrainputs")&&(this.options.get("startinput")||B.previous())){this.addSmallInput(B,"before")}return B},addSmallInput:function(C,B){var A=this.createInput({"class":"smallinput"});C.insert({}[B]=A);A.cacheData("small",true);this.makeResizable(A);
if(this.options.get("hideempty")){A.hide()}return A},dispose:function(A){this.bits.unset(A.id);if(A.previous()&&A.previous().retrieveData("small")){A.previous().remove()}if(this.current==A){this.focus(A.next())}A.remove();return this},focus:function(B,A){if(!this.current){B.fire("focus")}else{if(this.current==B){return this
}}this.blur();B.addClassName(this.options.get("className")+"-"+B.retrieveData("type")+"-focus");if(B.retrieveData("small")){B.setStyle({display:"block"})}if(B.retrieveData("type")=="input"){if(!!this.options.get("onInputFocus")){this.options.get("onInputFocus")(this)}if(!A){this.callEvent(B.retrieveData("input"),"focus")
}}else{B.fire("onBoxFocus")}this.current=B;return this},blur:function(B){if(!this.current){return this}if(this.current.retrieveData("type")=="input"){var A=this.current.retrieveData("input");if(!B){this.callEvent(A,"blur")}if(!!this.options.get("onInputBlur")){this.options.get("onInputBlur")(this)}}else{this.current.fire("onBoxBlur")
}if(this.current.retrieveData("small")&&!A.get("value")&&this.options.get("hideempty")){this.current.hide()}this.current.removeClassName(this.options.get("className")+"-"+this.current.retrieveData("type")+"-focus");this.current=false;return this},createBox:function(B,A){return new Element("li",A).addClassName(this.options.get("className")+"-box").update(B).cacheData("type","box")
},createInput:function(B){var A=new Element("li",{"class":this.options.get("className")+"-input"});var D=new Element("input",Object.extend(B,{type:"text"}));D.setAttribute("autocomplete","off");if(!!this.options.get("maxLength")){D.setAttribute("maxlength",this.options.get("maxLength"))}D.observe("click",function(E){E.stop()
}).observe("focus",function(E){if(!this.isSelfEvent("focus")){this.focus(A,true)}}.bind(this)).observe("blur",function(){if(!this.isSelfEvent("blur")){this.blur(true)}}.bind(this)).observe("keydown",function(E){this.cacheData("lastvalue",this.value).cacheData("lastcaret",this.getCaretPosition())});var C=A.cacheData("type","input").cacheData("input",D).insert(D);
return C},callEvent:function(B,A){this.events.set(A,B);B[A]()},isSelfEvent:function(A){return(this.events.get(A))?!!this.events.unset(A):false},makeResizable:function(A){var B=A.retrieveData("input");B.cacheData("resizable",new ResizableTextbox(B,Object.extend(this.options.get("resizable"))));return this
},checkInput:function(){var A=this.current.retrieveData("input");return(!A.retrieveData("lastvalue")||(A.getCaretPosition()===0&&A.retrieveData("lastcaret")===0))},move:function(B){var A=this.current[(B=="left"?"previous":"next")]();if(A&&(!this.current.retrieveData("input")||((this.checkInput()||B=="right")))){this.focus(A)
}return this},moveDispose:function(){if(this.current.retrieveData("type")=="box"){return this.dispose(this.current)}if(this.checkInput()&&this.bits.keys().length&&this.current.previous()){return this.focus(this.current.previous())}},clear:function(){this.bits.keys().each(function(A){this.dispose($(A))
}.bind(this))}});Element.addMethods({getCaretPosition:function(){if(this.createTextRange){var A=document.selection.createRange().duplicate();A.moveEnd("character",this.value.length);if(A.text===""){return this.value.length}return this.value.lastIndexOf(A.text)}else{return this.selectionStart}},cacheData:function(B,A,C){if(Object.isUndefined(this[$(B).identify()])||!Object.isHash(this[$(B).identify()])){this[$(B).identify()]=$H()
}this[$(B).identify()].set(A,C);return B},retrieveData:function(B,A){return this[$(B).identify()].get(A)},filter:function(H,G){var I=[];for(var J=0,F=this.length;J<F;J++){if(H.call(G,this[J],J,this)){I.push(this[J])}}return I}});function $pick(){for(var D=0,C=arguments.length;D<C;D++){if(!Object.isUndefined(arguments[D])){return arguments[D]
}}return null}var AutocompleteList=Class.create(TextboxList,{loptions:$H({autocomplete:{maxresults:10,minchars:1}}),initialize:function($super,C,E,A,D){$super(C,A);this.data=A.data;this.autoholder=$(E);this.autoholder.observe("mouseover",function(){this.curOn=true}.bind(this)).observe("mouseout",function(){this.curOn=false
}.bind(this));this.autoresults=this.autoholder.select("ul").first();var B=this.autoresults.select("li");B.each(function(F){this.add({id:F.readAttribute("value"),name:F.innerHTML})},this)},autoShow:function(B,E){var C=0;if(!!E||(!!B&&!!B.strip()&&!!B.length&&B.length>=this.loptions.get("autocomplete").minchars)){this.resultsshown=true;
this.hideInstructions();this.autoresults.show();this.autoresults.update("");var D=new RegExp(B,"i");var A=this.bits.values();this.data.filter(function(F){return A.indexOf(F.id)==-1}).filter(function(F){if(E){return F}else{return F?D.test(F.name):false}}).each(function(F,H){C++;if(H>=this.loptions.get("autocomplete").maxresults){return 
}var I=this;var G=new Element("li");if(F.pic){G.setStyle({backgroundImage:"url("+F.pic+")"})}G.observe("click",function(J){J.stop();I.autoAdd(this)}).observe("mouseover",function(){I.autoFocus(this)}).update(this.autoHighlight(F.name,B));this.autoresults.insert(G);G.cacheData("result",F);if(H==0){this.autoFocus(G)
}},this)}if(C==0){this.autoHide();this.showInstructions()}else{if(C>this.options.get("results")){this.autoresults.setStyle({height:(this.options.get("results")*24)+"px"})}else{this.autoresults.setStyle({height:(C?(C*24):0)+"px"})}}return this},autoHighlight:function(B,A){if(!!A){return B.gsub(new RegExp(A,"i"),function(C){return"<em>"+C[0]+"</em>"
})}else{return B}},autoHide:function(){this.resultsshown=false;this.autoresults.hide();return this},autoFocus:function(A){if(!A){return }if(this.autocurrent){this.autocurrent.removeClassName("auto-focus")}this.autocurrent=A.addClassName("auto-focus");return this},autoMove:function(A){if(!this.resultsshown){return 
}this.autoFocus(this.autocurrent[(A=="up"?"previous":"next")]());this.autoresults.scrollTop=this.autocurrent.positionedOffset()[1]-this.autocurrent.getHeight();return this},autoAdd:function(C){var A=C.retrieveData("result");if(!C||!A){return }this.add(A);delete this.data[this.data.indexOf(Object.toJSON(A))];
this.autoHide();var B=this.lastinput||this.maininput.retrieveData("input");B.clear().focus();this.showInstructions();return this},createInput:function($super,C){var A=$super(C);var B=A.retrieveData("input");B.setAttribute("tabindex","1");B.observe("keydown",function(D){this.dosearch=false;switch(D.keyCode){case Event.KEY_UP:D.stop();
return this.autoMove("up");case Event.KEY_DOWN:if(this.resultsshown||B.value!=""){D.stop();return this.autoMove("down")}case Event.KEY_TAB:if(!this.resultsshown){break}case Event.KEY_RETURN:D.stop();if(!this.autocurrent){break}if(this.resultsshown){this.autoAdd(this.autocurrent)}this.autocurrent=false;
this.autoenter=true;break;case Event.KEY_ESC:this.autoHide();if(this.current&&this.current.retrieveData("input")){this.current.retrieveData("input").clear()}break;default:this.dosearch=true}}.bind(this));B.observe("keyup",function(D){switch(D.keyCode){case Event.KEY_UP:case Event.KEY_RETURN:case Event.KEY_ESC:break;
case Event.KEY_DOWN:if(!this.resultsshown&&B.value==""){this.autoShow(B.value,true)}break;default:if(this.dosearch){this.autoShow(B.value)}}}.bind(this));B.observe(Prototype.Browser.IE?"keydown":"keypress",function(D){this.autoenter=false}.bind(this));if(Prototype.Browser.Gecko){new Form.Element.Observer(B,0.2,function(D,E){if(this.dosearch){this.autoShow(B.value)
}}.bind(this))}return A},createBox:function($super,C,B){var A=$super(C.name,B);A.observe("mouseover",function(){this.addClassName("bit-hover")}).observe("mouseout",function(){this.removeClassName("bit-hover")});return A},add:function($super,D,B){var C=$super(D,B);if(D.pic){C.setStyle({backgroundImage:"url("+D.pic+")"})
}if(this.options.get("maxRecipients")>0&&this.bits.size()>=this.options.get("maxRecipients")){this.maxRecipientsReached=true;this.hideInstructions();var A=this.lastinput||this.maininput.retrieveData("input");A.hide();if(!!this.options.get("maxRecipientsReachedCallback")){this.options.get("maxRecipientsReachedCallback")()
}}return C},dispose:function($super,B){$super(B);if(this.options.get("maxRecipients")==0||this.bits.size()<this.options.get("maxRecipients")){this.maxRecipientsReached=false;this.showInstructions();var A=this.lastinput||this.maininput.retrieveData("input");A.show().focus()}},showInstructions:function(){if(!this.maxRecipientsReached){this.autoholder.select(".default").first().show()
}},hideInstructions:function(){this.autoholder.select(".default").first().hide()}});if(typeof (Object.Event)=="undefined"){Object.Event={eventHandlers:{},observe:function(B,A){if(!this.eventHandlers[B]){this.eventHandlers[B]=$A([])}this.eventHandlers[B].push(A)},stopObserving:function(B,A){this.eventHandlers[B]=this.eventHandlers[B].without(A)
},fireEvent:function(A){if(this.eventHandlers[A]){this.eventHandlers[A].each(function(B){B(this)}.bind(this))}}};Object.Event.createEvent=Object.Event.fireEvent}if(typeof (Control)=="undefined"){Control={}}Control.TextArea=Class.create();Object.extend(Control.TextArea.prototype,Object.Event);Object.extend(Control.TextArea.prototype,{onChangeTimeoutLength:500,textarea:false,onChangeTimeout:false,initialize:function(A){this.textarea=$(A);
$(this.textarea).observe("keyup",this.doOnChange.bindAsEventListener(this));$(this.textarea).observe("paste",this.doOnChange.bindAsEventListener(this));$(this.textarea).observe("input",this.doOnChange.bindAsEventListener(this))},doOnChange:function(A){if(this.onChangeTimeout){window.clearTimeout(this.onChangeTimeout)
}this.onChangeTimeout=window.setTimeout(function(){this.createEvent("change")}.bind(this),this.onChangeTimeoutLength)},getValue:function(){return this.textarea.value},getSelection:function(){if(typeof (document.selection)!="undefined"){return document.selection.createRange().text}else{if(typeof (this.textarea.setSelectionRange)!="undefined"){return this.textarea.value.substring(this.textarea.selectionStart,this.textarea.selectionEnd)
}else{return false}}},replaceSelection:function(B){if(typeof (document.selection)!="undefined"){this.textarea.focus();var A=document.selection.createRange();A.text=B;A.collapse(false);A.select()}else{if(typeof (this.textarea.setSelectionRange)!="undefined"){selection_start=this.textarea.selectionStart;
this.textarea.value=this.textarea.value.substring(0,selection_start)+B+this.textarea.value.substring(this.textarea.selectionEnd);this.textarea.setSelectionRange(selection_start+B.length,selection_start+B.length)}}this.doOnChange();this.textarea.focus()},wrapSelection:function(A,B){this.replaceSelection(A+this.getSelection()+B)
},insertBeforeSelection:function(A){this.replaceSelection(A+this.getSelection())},insertAfterSelection:function(A){this.replaceSelection(this.getSelection()+A)},injectEachSelectedLine:function(C,A,B){this.replaceSelection((A||"")+$A(this.getSelection().split("\n")).inject([],C).join("\n")+(B||""))},insertBeforeEachSelectedLine:function(C,A,B){this.injectEachSelectedLine(function(E,D){E.push(C+D);
return E},A,B)}});Control.TextArea.ToolBar=Class.create();Object.extend(Control.TextArea.ToolBar.prototype,{textarea:false,toolbar:false,initialize:function(A,B){this.textarea=A;if(B){this.toolbar=$(B)}else{this.toolbar=$(document.createElement("ul"));this.textarea.textarea.parentNode.insertBefore(this.toolbar,this.textarea.textarea)
}},attachButton:function(A,B){A.onclick=function(){return false};$(A).observe("click",B.bindAsEventListener(this.textarea))},addButton:function(A,C,B){c=document.createElement("li");c.className="control-button";link=document.createElement("a");link.href="#";this.attachButton(link,C);c.appendChild(link);
if(B){for(a in B){link[a]=B[a]}}if(A){span=document.createElement("span");span.innerHTML=A;link.appendChild(span)}this.toolbar.appendChild(c)}});Control.TextArea.ToolBar.BBCode=Class.create();Object.extend(Control.TextArea.ToolBar.BBCode.prototype,{textarea:false,toolbar:false,options:{preview:false},initialize:function(A,B){this.textarea=new Control.TextArea(A);
this.toolbar=new Control.TextArea.ToolBar(this.textarea);this.toolbar.toolbar.addClassName("bbcode_toolbar");if(B){for(o in B){this.options[o]=B[o]}}this.toolbar.addButton("Bold",function(){this.wrapSelection("[b]","[/b]")},{id:"bbcode_bold_button"});this.toolbar.addButton("Italics",function(){this.wrapSelection("[i]","[/i]")
},{id:"bbcode_italics_button"});this.toolbar.addButton("Underline",function(){this.wrapSelection("[u]","[/u]")},{id:"bbcode_underline_button"});this.toolbar.addButton("Quote",function(){this.wrapSelection("[quote]","[/quote]")},{id:"bbcode_quote_button"});this.toolbar.addButton("Small size",function(){this.wrapSelection("[size=small]","[/size]")
},{id:"bbcode_smallsize_button"});this.toolbar.addButton("Large size",function(){this.wrapSelection("[size=large]","[/size]")},{id:"bbcode_largesize_button"});this.toolbar.addButton("Code",function(){this.wrapSelection("[code]","[/code]")},{id:"bbcode_code_button"});this.toolbar.addButton("Link",function(){var D=this.getSelection();
var E=D;var F="http://";var C=D.match(/^\s*(\w+:\/*)?([^\(\)\?&"'\s]*)([^:\(\)"'\s]*).*/);if(C!=null){D=C[2]+C[3];E=C[2];if(D.search(/\./)==-1){F="";D=C[2]}D=D.replace(/\[.*?\]/g,"")}this.replaceSelection("[url="+F+D+"]"+E+"[/url]")},{id:"bbcode_link_button"})},addColorSelect:function(G,C,E){var B=document.createElement("select");
if(E){var H=170;if(navigator.appVersion.match(/\bMSIE\b/)){H+=4}B.style.width=(Element.getDimensions(this.textarea.textarea).width-H-3)+"px"}Event.observe(B,"change",function(J){Event.stop(J);if(B.selectedIndex==0){return }var I=Event.element(J).value;B.selectedIndex=0;this.textarea.wrapSelection("[color="+I+"]","[/color]")
}.bind(this));var F=document.createElement("option");F.innerHTML=G;B.appendChild(F);B.selectedIndex=0;for(var D in C){F=document.createElement("option");F.innerHTML=C[D][1];F.style.color=C[D][0];F.value=D;B.appendChild(F)}var A=new Element("li",{className:"control-button"});A.appendChild(B);this.toolbar.toolbar.appendChild(A)
},addHabboLinkTools:function(){var A=new Element("li",{className:"linktools"});var B=new Element("div");B.insert(L10N.get("linktool.find.label")+" ");var D=function(G,J,I){var H={name:"linktool-scope",type:"radio",value:J};if(I){H.checked="checked"}B.appendChild(new Element("input",H));B.insert(G+" ")
};D(L10N.get("linktool.scope.habbos"),1,true);D(L10N.get("linktool.scope.rooms"),2);D(L10N.get("linktool.scope.groups"),3);var C=new Element("input",{name:"linktool-query",type:"text",size:20});B.appendChild(C);A.appendChild(B);A.insert(" ");var F=new Element("a",{href:"#",className:"new-button search-icon"});
F.appendChild(new Element("b")).appendChild(new Element("span"));F.appendChild(new Element("i"));A.appendChild(F);var E=new Element("div",{className:"linktool-results"});A.appendChild(E);new LinkTool(this.textarea,{button:F,input:C,results:E,scopeButtons:A.select('input[name="linktool-scope"]')});this.toolbar.toolbar.appendChild(A)
}});