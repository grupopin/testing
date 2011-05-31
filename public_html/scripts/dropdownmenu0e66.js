
var menuMOImages = new Array(5);
var baseURL = "../../interface/largemenu/index.html";

var language = "d";
var scripts = document.getElementsByTagName('script');
var myScript;
for(var i = 0; i < scripts.length; i++){
	if(scripts[i].src.indexOf("dropdownmenu")> -1){
		myScript = scripts[i];
	}
}
//alert(myScript.src);

var queryString = myScript.src.replace(/^[^\?]+\??/,'');

var params = parseQuery( queryString );

function parseQuery ( query ) {
   var Params = new Object ();
   if ( ! query ) return Params; // return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}

language = params["language"];

var cMenu = null;
var cMenuId = null;
var cSubMenu = null;


function preloadMenuImages(){
	
	for(var i = 0; i < menuMOImages.length; i++){
		menuMOImages[i] = new Image();
		menuMOImages[i].src = baseURL+"nav_"+language+"_"+i+"_1.png";
	}
}

preloadMenuImages();

function menuMOvr(menuId){
	closeSubMenu();
	cMenuId = menuId;
	cMenu = document.getElementById("menu"+menuId);
	if(cMenu != null){
		cMenu.src = menuMOImages[menuId].src;
		showSubMenu(menuId);
	}

}

function showSubMenu(menuId){
	if(menuId != 4){
		cSubMenu = document.getElementById("subMenu"+menuId);
		cSubMenu.style.visibility = "visible";
	}
}


function checkMousePosition(cDiv){
	
}

function closeMenu()
{
	if(cMenuId != null){
		var cMenu = document.getElementById("menu"+cMenuId);
		cMenu.src = baseURL+"nav_"+language+"_"+cMenuId+"_0.png";
		cMenuId = null;
	}
}

function closeSubMenu(){
	if(cSubMenu != null){
		cSubMenu.style.visibility = "hidden";
		cSubMenu = null;
	}
	closeMenu();
}

function onMenuMouseOut(e){
	if (!e) var e = window.event;
	var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
	
	if(reltg != null && reltg.nodeName != "TD" && reltg.nodeName != "A")
	{
		closeSubMenu();
	}
}

function subMenuMOvr(cTD){
	cTD.className = "subMenuTDMO";
}

function subMenuMOut(cTD){
	cTD.className = "subMenuTD";
}

function onSubMenuMouseOut(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;	
	//if (tg.nodeName != 'DIV') return;
	var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
	
	if(reltg.nodeName != "A" && reltg.nodeName != "TD" && reltg.nodeName != "IMG")
	{
		 closeSubMenu();
	}
}


function init(){
	if(document.getElementById("subMenu0")){
		document.getElementById("subMenu0").onmouseout = onSubMenuMouseOut;
		document.getElementById("subMenu1").onmouseout = onSubMenuMouseOut;
		document.getElementById("subMenu2").onmouseout = onSubMenuMouseOut;
		document.getElementById("subMenu3").onmouseout = onSubMenuMouseOut;
		
		document.getElementById("menuLink0").onmouseout = onMenuMouseOut;
		document.getElementById("menuLink1").onmouseout = onMenuMouseOut;
		document.getElementById("menuLink2").onmouseout = onMenuMouseOut;
		document.getElementById("menuLink3").onmouseout = onMenuMouseOut;
	}
}


function addEvent(obj, evType, fn){
 if (obj.addEventListener){
   obj.addEventListener(evType, fn, false);
   return true;
 } else if (obj.attachEvent){
   var r = obj.attachEvent("on"+evType, fn);
   return r;
 } else {
   return false;
 }
}

addEvent(window, 'load', init);