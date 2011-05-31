<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Malerei</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css" media="screen">
	@import url("/css/menu.css");
	@import url("/css/screen.css");
	@import url("/css/thickbox.css");
.leftAlignDiv {
	text-align: left;
	position: relative;
	float: left;
}
</style>
<link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />

<!--[if lt IE 7]>
<style type="text/css">
	@import url("/css/IE6.css");
</style>
<![endif]-->
<!--[if IE]>
<style type="text/css">
	@import url("/css/IE.css");
</style>
<![endif]-->

<script language="javascript" type="text/javascript" src="/scripts/dropdownmenu.js?language=d"></script>
<script src="/scripts/kunsthauswien.js" type="text/javascript"></script>

<script src="/scripts/jquery.js" language="javascript" type="text/javascript"></script>
<script src="/scripts/jquery.popupwindow.js" language="javascript" type="text/javascript"></script>
<script src="/scripts/jquery.thickbox.js" language="javascript" type="text/javascript"></script>
<script src="/scripts/addevent.js" language="javascript" type="text/javascript"></script>
<script src="/scripts/MM.js" language="javascript" type="text/javascript"></script>
<!--[if IE 6]>
<script src="/scripts/DD_belatedPNG_0.0.7a-min.js"></script>
<script type="text/javascript">
	DD_belatedPNG.fix('#containerRight ul');
	DD_belatedPNG.fix('#containerRight');
</script>
<![endif]-->
<script type="text/javascript">
	var picdesc = new Array();
	picdesc[50]= "<H1>104 KATHEDRALE I</H1><br/>Aquarell, Marrakesch, 1951";	
	picdesc[51]= "<H1>150 SINGENDER DAMPFER IN ULTRAMARIN III</H1><br/>Mixed Media, Wien, 1959";
	picdesc[52]= "<H1>557 H&Auml;USER IM SCHNEE IM SILBERREGEN MIT ROTEM WEG</H1><br/>Aquarell, Voglio/Bologna, 1962";
	picdesc[53]= "<H1>630 GELBE H&Auml;USER - MIT DER LIEBE WARTEN TUT WEH, WENN DIE LIEBE WOANDERS IST - EIFERSUCHT</H1><br/>Mixed Media, Venedig, 1966";
	picdesc[54]= "<H1>816 SCHALTBRETT</H1><br/>Mixed media, Porquerolles, 1980";
	picdesc[55]= "<H1>904 PELLESTRINA WOOD</H1><br/>Objekt, Hahns&auml;ge, 1988";

	var picpath = new Array();
	picpath[50]= "images/hundertwasser/malerei/0104_5c.jpg";	
	picpath[51]= "images/hundertwasser/malerei/0150_5c.jpg";
	picpath[52]= "images/hundertwasser/malerei/0557_4c.jpg";
	picpath[53]= "images/hundertwasser/malerei/0630_5c.jpg";
	picpath[54]= "images/hundertwasser/malerei/0816_4c.jpg";
	picpath[55]= "images/hundertwasser/malerei/0904_4c.jpg";
	
	function showPic(number) {
		var desc = picdesc[number];
		var pic = picpath[number];
		//top.location.href = "/showpic.php?theImage="+pic+"&theDesc="+desc;
		
		//theURL = "/showpic.php?theImage="+pic+"&theDesc="+desc;
		//fotoW = window.open(theURL, "_blank", "width=740,height=500,scrollbars,resizable");
		//fotoW.focus();
		document.theForm.theImage.value = pic;
		document.theForm.theDesc.value = desc;
		
		document.theForm.submit();
	}
</script>

<link rel="start" title="Home" href="/" />
<link rel="up" title="Nach oben" href="#totop" />
</head>

<body id="hundertwasser">

 

	<form action="/showpic.php" method="post" name="theForm" id="theForm" target="fotoW">
	<input type="hidden" name="theImage" value="" />
	<input type="hidden" name="theDesc" value="" />
	</form>

<div id="wrapper">
<!-- Navigation -->

<h1 id="siteTitle"><a href="/index.php" rel="home" title="Hundertwasser Home">Hundertwasser</a></h1>

<p id="skip"><a href="#content" accesskey="s">[menu&uuml; &uuml;berspringen]</a></p>
<div id="top"><a href="#" name="top"></a></div>
<div id="navigation">

  <div id="nav_ballDiv" ><img src="/interface/largemenu/ball_1_b.png" width="44" height="39" alt="" /></div>
	<div id="nav_titDiv"><a href="/deutsch/werk/index.php"><img src="/interface/largemenu/title_d_1.png" width="245" height="37" alt="" /></a></div>
	<div id="nav_IconDiv" ><img src="/interface/nav_icons/4.png" alt="" /></div>

<div id="menuDiv">
	<table cellspacing="0" cellpadding="0">

		<tr>
			<td><a href="/deutsch/hundertwasser/hwueberhw.php" onmouseover="menuMOvr(0)" id="menuLink0" name="menuLink0"><img src="/interface/largemenu/nav_d_0_0.png" alt="Hundertwasser" width="184" height="54" id="menu0" name="menu0" alt="" /></a></td>
		  <td><a href="/deutsch/werk/index.php" onmouseover="menuMOvr(1)" id="menuLink1" name="menuLink1"><img src="/interface/largemenu/nav_d_1_0.png" alt="Ausstellungen" width="125" height="54" id="menu1" name="menu1" alt="" /></a></td>
		  <td><a href="/deutsch/texte/philosophie.php" onmouseover="menuMOvr(2)" id="menuLink2" name="menuLink2"><img src="/interface/largemenu/nav_d_2_0.png" alt="Texte" width="132" height="54" id="menu2" name="menu2" alt="" /></a></td>
		  <td><a href="/deutsch/ausstellungen/index.php" onmouseover="menuMOvr(3)" id="menuLink3" name="menuLink3"><img src="/interface/largemenu/nav_d_3_0.png" alt="Ausstellungen" width="133" height="54" id="menu3" name="menu3" alt="" /></a></td>
		  <td><a href="/deutsch/news/index.php" onmouseover="menuMOvr(4)" id="menuLink4" name="menuLink4"><img src="/interface/largemenu/nav_d_4_0.png" alt="News" width="142" height="54" id="menu4" name="menu4" alt="" /></a></td>
	  </tr>
	</table>
</div>

 
<div id="subMenu0">
	<table cellspacing="0" cellpadding="0">

		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/hundertwasser/hwueberhw.php" class="subMenuLink subMenuLinkTop">Hundertwasser &uuml;ber Hundertwasser</a></td>
		</tr>
		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/hundertwasser/biographie.php" class="subMenuLink">Biographie</a></td>

		</tr>
		<tr>
		<td><img src="/interface/colorsubs/sub_bottom0.png" width="178" height="27" alt="" /></td>
		</tr>
	</table>
</div>

<div id="subMenu1">
	<table cellspacing="0" cellpadding="0">
    <tr>

		<td class="subMenuTD">&nbsp;</td>
		</tr>
		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/werk/malerei/malerei.php" class="subMenuLink">Malerei</a></td>
		</tr>
		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/werk/graphic/graphik.php" class="subMenuLink">Graphik</a></td>
		</tr>

        <tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/werk/tapis/tapisserie.php" class="subMenuLink">Tapisserie</a></td>
		</tr>
        <tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/werk/ankunst/angewandtekunst.php" class="subMenuLink">Angewandte Kunst</a></td>
		</tr>
		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/werk/arch/architektur.php" class="subMenuLink">Architektur</a></td>

		</tr>

		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/werk/eco/oekologie.php" class="subMenuLink">&Ouml;kologie</a></td>
		</tr>

		<tr>
		<td><img src="/interface/colorsubs/sub_bottom3.png" width="178" height="31" alt="" /></td>
		</tr>

	</table>
</div>


<div id="subMenu2">
	<table cellspacing="0" cellpadding="0">

		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/texte/philosophie.php" class="subMenuLink subMenuLinkTop">Hundertwasser Manifeste und Texte</a></td>
		</tr>
		<tr>

		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/texte/literatur.php" class="subMenuLink">Literatur zu Hundertwasser</a></td>
		</tr>

		<tr>
		<td><img src="/interface/colorsubs/sub_bottom2.png" width="179" height="27" alt="" /></td>
		</tr>
	</table>
</div>

<div id="subMenu3">

	<table cellspacing="0" cellpadding="0">

		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/ausstellungen/hw_im_khw.php" class="subMenuLink subMenuLinkTop" title="Hundertwasser-Ausstellung">Hundertwasser im KunstHausWien</a></td>
		</tr>
        
        <tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/ausstellungen/ausstellungsprogramm.php"  class="subMenuLink"  title="Ausstellungsprogramm">Ausstellungsprogramm</a></td>
		</tr>

		
		<tr>
		<td class="subMenuTD" onmouseover="subMenuMOvr(this)" onmouseout="subMenuMOut(this)"><a href="/deutsch/ausstellungen/ausstellungsprogramm.php?archiv=true" class="subMenuLink" title="Archiv der alten Ausstellungen">Ausstellungen Archiv</a></td>
		</tr>
		<tr>
		<td><img src="/interface/colorsubs/sub_bottom1.png" width="178" height="24" alt="" /></td>
		</tr>
	</table>
</div>

<!-- Kontakt und Impressum -->

<div id="sub_kontakt">
	<ul>
		<li><a href="/deutsch/kontakt.php">Kontakt</a> \ </li>
		<li><a href="/deutsch/impressum.php">Impressum</a></li>
	</ul>
</div> <!-- /sub_kontakt -->

</div> <!-- /#navigation -->

<div id="contentwrapper">

 
   <div id="content"> 
  		<h2>Malerei</h2>
  		
 	<div class="newsItem alternative">
 	    <p><a href="javascript:showPic(50)"><img src="/images/hundertwasser/malerei/0104_5c_sml.jpg" width="135" height="174" alt=""></a></p>
 	    <p>Hundertwasser malte, wo auch immer er sich aufhielt, zu Hause, in der Natur und unterwegs, in Kaffeeh&auml;usern und Restaurants, im Zug oder im Flugzeug, in Hotels oder bei Freunden und Bekannten, wo er zu Gast war. Er hatte kein Atelier und malte auch nicht an der Staffelei, sondern hatte die Leinwand oder das Papier flach vor sich liegen.</p>

      <div style="height: 25px;"></div>
      
      <div style="height: 105px;"><a href="javascript:showPic(51)"><img src="/images/hundertwasser/malerei/0150_5c_sml.jpg" alt=""></a></div>

 	    <p>War er unterwegs, konnte es sein, da&szlig; er das Papier faltete und jeweils nur den sichtbaren Teil des Werkes &uuml;berblickend malte. Entweder nahm er seine Bilder auf Reisen mit, oder er lie&szlig; sie an seinen Wohnorten zur&uuml;ck und malte daran weiter, sobald er wieder zur&uuml;ckgekehrt war. </p>


		<p>Hundertwasser stellte viele seiner Farben selbst her. Er malte mit Wasserfarben, mit &Ouml;lfarben und Eitempera, mit gl&auml;nzenden Lacken und zerriebenen Erden. Er verwendete die unterschiedlichsten Farben in einem Bild, setzte sie nebeneinander, so da&szlig; sie nicht nur in ihrer Farbigkeit kontrastieren, sondern auch in ihrer Beschaffenheit. </p>

  
  <p>

	<a href="javascript:showPic(52)"><img src="/images/hundertwasser/malerei/0557_4c_sml.jpg" alt=""></a>
  	<a href="javascript:showPic(53)"><img src="/images/hundertwasser/malerei/0630_5c_sml.jpg" alt=""></a>

<!-- START::Mediaplayer -->

<div class="newsItem">
<a href="javascript:void(0);" onclick="window.open('/mediaplayer/?file=D-Malen ist trauumen', 'mediaplayer', 'width=640,height=480,status=no,scrollbars=no,resizable=no');" style="cursor: pointer;">
<table border="0">
 <tr><td align="left"><img src="/mediaplayer/covert5-0.gif" alt="" border="0"></td></tr>
 <tr><td style="font-size: 11px;">Malen ist tr&auml;umen</td></tr>
</table>
</a>
</div>
<!-- END::Mediaplayer -->
  </p>

  </div>

  



 	<div class="newsItem">

	   <p>Die "Chassis" seiner Gem&auml;lde hat er meist selbst angefertigt und fast immer die Leinw&auml;nde selbst aufgezogen. Er hat viele Techniken erprobt und neue entwickelt. Er malte auf den verschiedensten Papieren, mit Vorliebe auf gebrauchten Packpapieren, nicht selten montierte er sie auf die verschiedensten Bildtr&auml;ger wie Holzfaserplatten, Hanf oder Leinen. </p>

 	
	<p><a href="javascript:showPic(54)"><img src="/images/hundertwasser/malerei/0816_4c_sml.jpg" alt=""></a></p>
	   <p><br />
        Er malte auf gefundenen Materialien, auf Sperrholzst&uuml;cken, die er zusammenf&uuml;gte, wie das Werk 904 <em> Pellestrina Wood </em>zeigt, oder auf einem Schaltbrett wie 816 <em>Switch Board </em>. </p>

<p>&nbsp;</p>


    <p>&nbsp;</p>
    <p><a href="javascript:showPic(55)"><img align="right" src="/images/hundertwasser/malerei/0904_4c_sml.jpg" alt="" /></a>Fast immer hat Hundertwasser auf der Vorder- oder R&uuml;ckseite seiner Bilder notiert, wo und wann er sie gemalt hat. Auf r&uuml;ckw&auml;rts angebrachten Aufklebern vermerkte er die technischen Angaben des Werkes und die Datierung.</p>

    <p class="reference">Aus: Hundertwasser 1928-2000, Catalogue Raisonn&eacute;, K&ouml;ln 2002, Band 2 </p>

    </div>
    


<!-- START::Mediaplayer -->
<div class="newsItem">
<a href="javascript:void(0);" onclick="window.open('/mediaplayer/?file=D-Maler sein', 'mediaplayer', 'width=640,height=480,status=no,scrollbars=no,resizable=no');" style="cursor: pointer;">
<table border="0">
 <tr><td align="center"><img src="/mediaplayer/covert5-0.gif" alt=""/></td></tr>
 <tr><td style="font-size: 11px;">Maler sein</td></tr>
</table>
</a>
</div>
<!-- END::Mediaplayer -->

		</div>


	</div> <!-- /content -->
</div> <!-- /contentwrapper -->


<div id="containerRight">
	<p></p>
	<ul>

    	<li class="active"><a href="malerei.php">Malerei</a></li>
			<ul>
				<li><a href="malerei_diespirale.php">Hundertwasser zur Spirale</a></li>
				<li><a href="malerei_zitate.php">Wieland Schmied zu Hundertwassers Malerei</a></li>
			</ul>    	
	  	<li><a href="../graphic/graphik.php">Graphik</a></li>
        <li><a href="../tapis/tapisserie.php">Tapisserie</a></li>

        <li><a href="../ankunst/angewandtekunst.php">Angewandte Kunst</a></li>
        <li><a href="../arch/architektur.php">Architektur</a></li>
    	
    	<li><a href="../eco/oekologie.php">&Ouml;kologie</a></li>
  </ul>
</div>

</div> <!-- /wrapper -->
<div id="jump"><a href="#top" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('toparrow','','/interface/totop1.gif',1)"><img src="/interface/totop0.gif" title="Zum Seitenanfang" name="toparrow" width="48" height="50" alt="" /></a></div>

</body>
</html>
