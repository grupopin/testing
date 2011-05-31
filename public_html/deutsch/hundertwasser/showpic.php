<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<!-- Mirrored from www.hundertwasser.at/showpic.php?theImage= by HTTrack Website Copier/3.x [XR&CO'2010], Wed, 16 Feb 2011 07:31:14 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8"><!-- /Added by HTTrack -->
<head>
<title>Foto / Photo &middot; Hundertwasser</title>
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


<table style="text-align:center;border:0;margin: 0 auto;">

  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
    
   <img id="theImage" src="#" width="" height=""/>
    
</td>
  </tr>
  <tr>
    <td align="center" class="posterdesc"></td>
  </tr>
  <tr>
    <td align="center">
    
   		<a href="javascript:resizeFull();" id="zoomLink">Bild in voller Gr&ouml;&szlig;e anzeigen</a>
    
	</td>
  </tr>
  <tr>
    <td style="text-align:center;"><a href="javascript:window.close();">Fenster schliessen / Close window</a></td>
  </tr>
     <tr>
    <td>&nbsp;</td>
  </tr>
</table>

</body>

<!-- Mirrored from www.hundertwasser.at/showpic.php?theImage= by HTTrack Website Copier/3.x [XR&CO'2010], Wed, 16 Feb 2011 07:31:14 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8"><!-- /Added by HTTrack -->
</html>
