var habboclub={closeSubscriptionWindow:function(A){if(A){Event.stop(A)}if($("subscription_dialog")){Element.remove("subscription_dialog")}if($("subscription_result")){Element.remove("subscription_result")}Overlay.hide()},showSubscriptionResultWindow:function(A,B){Element.wait($("hc_confirm_box"));var C={optionNumber:A};
if(!!$("settings-gender")){C.newGender=$F("settings-gender")}if(!!$("settings-figure")){C.figureData=$F("settings-figure")}new Ajax.Request(habboReqPath+"/habboclub/habboclub_subscribe",{method:"post",parameters:C,onComplete:function(E,D){if(D!=null){var H=D.daysLeft;var F=$$("#clubdaysleft a")[0];if(F){F.innerHTML=""+H
}if($("clubdaysleft")){$("clubdaysleft").show()}if($("joinclub")){$("joinclub").hide()}}if($("subscription_dialog")){Element.remove("subscription_dialog")}habboclub.updateMembership();var G=Dialog.createDialog("subscription_result",B,"9003",0,-1000,habboclub.closeSubscriptionWindow);Dialog.appendDialogBody(G,E.responseText,true);
Dialog.moveDialogToCenter(G)}})},catalogUpdate:function(B,A){new Ajax.Request(habboReqPath+"/habblet/ajax/habboclub_gift",{method:"post",parameters:"month="+encodeURIComponent(B)+"&catalogpage="+encodeURIComponent(A),onComplete:function(D,C){($("hc-gift-catalog")).innerHTML=D.responseText}});return false
},updateMembership:function(){new Ajax.Request(habboReqPath+"/habblet/ajax/habboclub_enddate",{method:"get",onComplete:habboclub._updateMembershipCallback})},buttonClick:function(C,A){var B=Dialog.createDialog("subscription_dialog",A,9001,0,-1000,habboclub.closeSubscriptionWindow);Dialog.setAsWaitDialog();
Dialog.moveDialogToCenter(B);Overlay.show();var D={optionNumber:C};if(!!$("settings-gender")){D.newGender=$F("settings-gender")}if(!!$("settings-figure")){D.figureData=$F("settings-figure")}new Ajax.Request(habboReqPath+"/habboclub/habboclub_confirm",{method:"post",parameters:D,onComplete:function(F,E){Dialog.setDialogBody(B,F.responseText)
}});return false},_updateMembershipCallback:function(B,A){$("hc-membership-info").innerHTML=B.responseText}};