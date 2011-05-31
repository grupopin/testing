// JavaScript Document
var g_step,g_t, g_startValue, g_endValue, g_distance, g_callback, g_interval, g_property, g_frames, g_totalSteps;

var g_selectedArrow = null;
var g_maxFrames = 60;

var areaPositions = new Array();
areaPositions["start"] = 0;
areaPositions["exhibitions"] = 200;
areaPositions["khw"] = 800;
areaPositions["hundertwasser"] = 1200;
areaPositions["end"] = 1800;


function onBodyLoad(doScroll)
{
	MM_preloadImages('interface/cover/cover_m_de_0_1.png','interface/cover/cover_m_de_1_1.png','interface/cover/cover_m_de_2_1.png','interface/cover/cover_m_de_3_1.png','interface/cover/cover_m_de_4_1.png','interface/cover/cover_m_de_5_1.png','interface/cover/cover_m_de_6_1.png');	
	if(doScroll == 1)
	{
		document.body.parentNode.scrollLeft = 1800;
		gotoArea("start");
	}
}

function onBodyLoadEn(doScroll)
{
	MM_preloadImages('interface/cover/cover_m_en_0_1.png','interface/cover/cover_m_en_1_1.png','interface/cover/cover_m_en_2_1.png','interface/cover/cover_m_en_3_1.png','interface/cover/cover_m_en_4_1.png','interface/cover/cover_m_en_5_1.png','interface/cover/cover_m_en_6_1.png');
	if(doScroll == 1)
	{
		document.body.parentNode.scrollLeft = 1800;
		gotoArea("start");
	}
}



function switchLang(desiredLang,currentLang)
{
	ajax_loadContent('agreement','session.php/Wcd653b90ce11c.htm'+desiredLang,'1');
	if(desiredLang != currentLang)
	{
		if(desiredLang == "en")
		{
			document.location = "index_en.php.htm";
		}
		else
		{
			document.location = "index.php.htm";
		}
	}
}




function gotoArea(area){
	var prop = "document.body.scrollLeft";
	//alert(document.body.parentNode.scrollLeft);
	startMoveObject(document.body.parentNode.scrollLeft, areaPositions[area],  "finished()");
}

function finished(){

}

function startMoveObject(startValue, endValue,callback){
	g_startValue = startValue;
	g_endValue = endValue;
	g_distance = g_endValue - g_startValue;
	g_frames = Math.floor(g_distance / g_maxFrames);
	if(g_frames < g_maxFrames ) g_frames = g_maxFrames;
	//alert(frames);
	g_step = 1/g_frames;
	g_t = g_step;
	g_callback = callback;
	g_totalSteps = Math.abs( Math.ceil(g_distance/g_frames));
	//alert(g_totalSteps);
	g_interval = setInterval("animateObject()", 50);
}

var c = 0;

function animateObject(){
	
	if(g_t > 1){
		clearInterval(g_interval);
		eval(g_callback);
	}
	else {
		var value = easeOut(g_t);
		
		g_t += g_step;
		value = Math.round( g_startValue + g_distance * value);
		/*
		if(c < 5){
			//alert("value = "+value);
			c++;
		}
		*/
		//eval(g_property + "='"+ value +"px';");
		window.scroll(value, 0);
		//window.status ="prop = "+ "value="+value;
	}
}

function easeInOut(minValue,maxValue,totalSteps,actualStep,powr) { 
//Generic Animation Step Value Generator By www.hesido.com 
    var delta = maxValue - minValue; 
    var stepp = minValue+(Math.pow(((1 / totalSteps) * actualStep), powr) * delta); 
    return Math.ceil(stepp) 
    } 

function easeOut(t){
	return(Math.sin(t*Math.PI-Math.PI/2)+1)/2;
}

