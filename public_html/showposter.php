
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Poster &middot; Hundertwasser</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="css/showpic.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">

var imgWidth = ;
var imgHeight = ;
var doingResize = true;

function resizeImg() {

	if(doingResize)
	{
		if (parseInt(navigator.appVersion)>3) {
		  if (navigator.appName=="Netscape") {
			browserWidth  = window.innerWidth-(100);
			browserHeight = window.innerHeight-(150);
		  }
		  if (navigator.appName.indexOf("Microsoft")!=-1) {
			browserWidth  = document.documentElement.clientWidth-(100);
			browserHeight = document.documentElement.clientHeight-(150);
		  }
		}
		
		var wFactor = browserWidth/imgWidth;
		var hFactor = browserHeight/imgHeight;
		var factor;
		if(wFactor < hFactor)
		{
			factor = wFactor;	
		}
		else
		{
			factor = hFactor;	
		}
		if(factor > 1)
		{
			factor = 1;	
			document.getElementById("zoomLink").innerHTML = "";
		}
		else
		{
			document.getElementById("zoomLink").innerHTML = "Bild in voller Gr&ouml;&szlig;e anzeigen / Show in full size";
		}
		
		var newW = imgWidth * factor;
		var newH = imgHeight * factor;
		var cImage = document.getElementById("theImage");
		cImage.width = newW;
		cImage.height = newH;
	}
}

function resizeFull() {
	if(doingResize)
	{
		var cImage = document.getElementById("theImage");
		cImage.width = imgWidth;
		cImage.height = imgHeight;
		document.getElementById("zoomLink").innerHTML = "Verkleinen / Zoom Out";
		doingResize = false;
	}
	else
	{
		document.getElementById("zoomLink").innerHTML = "Bild in voller Gr&ouml;&szlig;e anzeigen / Show in full size";
		doingResize = true;
		resizeImg();
	}
}

</script>
</head>

<body onLoad="resizeImg();" onResize="resizeImg();">

<body>
<table border="0" align="center">
  <tr>
    <td width="800" align="center"><img id="theImage" src="" width="" height=""/></td>
	<td><img src="interface/spacer.gif" width="10" height="1"></td>
    <td width="200" align="left" class="posterdesc"></td>
  </tr>
  <tr>
    <td colspan="3" align="center"></td>
  </tr>
    <tr>
    <td colspan="3" align="center">
    <p><a href="javascript:resizeFull();" id="zoomLink">Bild in voller Gr&ouml;&szlig;e anzeigen / Show full size</a></p>
    <p><a href="javascript:window.close();">Fenster schliessen / Close window</a></p>
    </td>
  </tr>
     <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
</table>


</body>
</html>
