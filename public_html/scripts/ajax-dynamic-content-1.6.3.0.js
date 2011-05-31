/*

  @name:        AJAX dynamic content for Simple AJAX Code-Kit (SACK)
  @version:     1.6.3.0
  @copyright:   (c) 2005-2006 Alf Magne Kalleland
                (c) 2007-2009 Fusion-Datastore Limited

  @url:         http://www.dhtmlgoodies.com/
                http://www.fusion-datastore.org/

  @licence:     Software licenced under a modified X11 licence,
                see documentation or authors website for more details.

*/

var enableCache = false;
var jsCache = new Array();

var dynamicContent_ajaxObjects = new Array();

function ajax_showContent(divId,ajaxIndex,url)
{
  if (document.getElementById(divId)) {
	  var targetObj = document.getElementById(divId);
	  targetObj.innerHTML = dynamicContent_ajaxObjects[ajaxIndex].response;
	  if(enableCache){
	  	jsCache[url] = 	dynamicContent_ajaxObjects[ajaxIndex].response;
	  }
  	dynamicContent_ajaxObjects[ajaxIndex] = false;
	
	  ajax_parseJs(targetObj)
  }
}

function ajax_loadContent(divId,url,enableCache)
{
  // loading activity
  document.getElementById(divId).innerHTML = '';
  
  // no liveapp-loading (for hw-website)
  //document.getElementById(divId).innerHTML = '<div class="liveapp-loading"></div>';

	if(enableCache && jsCache[url]){
		document.getElementById(divId).innerHTML = jsCache[url];
		ajax_parseJs(document.getElementById(divId))
		evaluateCss(document.getElementById(divId))
		return;
	}
	
	var ajaxIndex = dynamicContent_ajaxObjects.length;
	//document.getElementById(divId).innerHTML = '';
	dynamicContent_ajaxObjects[ajaxIndex] = new sack();
	
	if(url.indexOf('?')>=0){
		dynamicContent_ajaxObjects[ajaxIndex].method='GET';
		var string = url.substring(url.indexOf('?'));
		url = url.replace(string,'');
		string = string.replace('?','');
		var items = string.split(/&/g);
		for(var no=0;no<items.length;no++){
			var tokens = items[no].split('=');
			if(tokens.length==2){
				dynamicContent_ajaxObjects[ajaxIndex].setVar(tokens[0],tokens[1]);
			}	
		}	
		url = url.replace(string,'');
	}

	
	dynamicContent_ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
	dynamicContent_ajaxObjects[ajaxIndex].onCompletion = function(){ ajax_showContent(divId,ajaxIndex,url); };	// Specify function that will be executed after file has been found
	dynamicContent_ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function	
	
	
}

function ajax_parseJs(obj)
{
	var scriptTags = obj.getElementsByTagName('SCRIPT');
	var string = '';
	var jsCode = '';
	for(var no=0;no<scriptTags.length;no++){	
		if(scriptTags[no].src){
	        var head = document.getElementsByTagName("head")[0];
	        var scriptObj = document.createElement("script");
	
	        scriptObj.setAttribute("type", "text/javascript");
	        scriptObj.setAttribute("src", scriptTags[no].src);  	
		}else{
			if(navigator.userAgent.toLowerCase().indexOf('opera')>=0){
				jsCode = jsCode + scriptTags[no].text + '\n';
			}
			else
				jsCode = jsCode + scriptTags[no].innerHTML;	
		}
		
	}

	if(jsCode)ajax_installScript(jsCode);
}


function ajax_installScript(script)
{		
    if (!script)
        return;		
    if (window.execScript){        	
    	window.execScript(script)
    }else if(window.jQuery && jQuery.browser.safari){ // safari detection in jQuery
        window.setTimeout(script,0);
    }else{        	
        window.setTimeout(script,0);
    } 
}	
	
	
function evaluateCss(obj)
{
	var cssTags = obj.getElementsByTagName('STYLE');
	var head = document.getElementsByTagName('HEAD')[0];
	for(var no=0;no<cssTags.length;no++){
		head.appendChild(cssTags[no]);
	}	
}
