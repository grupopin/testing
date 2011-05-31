<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Graphik &middot; Hundertwasser</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css" media="screen">
	@import url("/css/reset.css");
	@import url("/css/menu.css");
	@import url("/css/screen.css");
	@import url("/css/thickbox.css");
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
<script src="/scripts/addevent.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
	var picdesc = new Array();
	picdesc[30]= "<H1>220A NACHTZUG</H1><br/>Serigraphie, Venedig, 1978";	// - TRAIN DE NUIT
	picdesc[31]= "<H1>414 DIE FLUCHT DES DALAI LAMA</H1><br/>Lithographie, Basel, 1959"; //- THE FLIGHT OF THE DALAI LAMA - LA FUITE DU DALAI LAMA
	picdesc[32]= "<H1>852 GESPR&Auml;CH MIT JENSEITS</H1><br/>Radierung, Wien, 1985"; // - COMMUNICATION WITH THE BEYOND - COMMUNICATION AVEC L'AU-DELA
	picdesc[33]= "<H1>859 IL ROTOLANTE - DAS IST EIN KRIECHER</H1><br/>Fotolithographie/Serigraphie, Venedig, 1983"; // - THIS IS A SLEEPER - CECI EST UN QUI DORT - QUESTO E UN DORMITORE
	picdesc[34]= "<H1>869A KORALLENBLUMEN</H1><br/>Japanischer Farbholzschnitt, Kioto, 1987";

	var picpath = new Array();
	picpath[30]= "images/hundertwasser/grafik/0220_A.jpg";	
	picpath[31]= "images/hundertwasser/grafik/0414.jpg";
	picpath[32]= "images/hundertwasser/grafik/0852.jpg";
	picpath[33]= "images/hundertwasser/grafik/0859_1.jpg";
	picpath[34]= "images/hundertwasser/grafik/0869_A.jpg";
	
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
	
	<h2>Graphik</h2>
	
	<div class="newsItem alternative">  
	<p><a href="javascript:showPic(30);"><img src="/images/hundertwasser/grafik/0220_A_sml.jpg" width="282" height="220" alt="" /></a></p>	
	<p>Hundertwasser beherrschte und erneuerte viele graphische Techniken: Lithographie, Siebdruck, Radierung, Farbholzschnitt und andere mehr. Er war einer der ersten, der eine v&ouml;llige Transparenz der Technik, der Entstehungsdaten und Auflage f&uuml;r jedes einzelne Blatt gefordert und eingehalten hat.</p>

	<p>Hundertwasser hat nie wirklich hohe Auflagen von ein- und derselben Graphik geschaffen. Seine Graphikauflagen bestehen aus mehreren Farbversionen und Varianten, die nicht separat numeriert sind, sondern durch die gesamte Auflage durchnumeriert wurden. Sein Ziel war es, lauter Unikate in der Kunst der Graphik herzustellen und damit die Maschine zu &uuml;berlisten.</p>
	</div>
	
	
	<div class="newsItem">
	<p><a href="javascript:showPic(31);"><img src="/images/hundertwasser/grafik/0414_sml.jpg" width="180" height="138" alt="" /></a></p>
	<p>Hundertwasser war stets darauf bedacht, auf den graphischen Bl&auml;ttern selbst genaue Werkangaben zu machen, um zu einer m&ouml;glichst l&uuml;ckenlosen Offenlegung der Techniken und Entstehungsdaten des Werkes zu gelangen.</p>

  </div>
	<div class="newsItem">
	<p><a href="javascript:showPic(33);"><img src="/images/hundertwasser/grafik/0859_1_sml.jpg" width="224" height="300" alt="" /></a></p>
	<p>Auf den Graphiken finden sich:</p>
    <ul>
		<li>&middot; Hundertwassers Signatur (handschriftlich und in Form von japanischen Inkans) </li>
		<li>&middot; Datum und Ort der Signatur</li>

		<li>&middot; Oeuvre-Nummer</li>
		<li>&middot; in vielen F&auml;llen der Name des Werkes </li>
		<li>&middot; Exemplarnummer </li>
		<li>&middot; Nennungen bzw. Stempel und Pr&auml;gungen von Verlegern, Druckern, Papier- und Farbenfabrikanten oder der eingesetzten Koordinatoren</li>

		<li>&middot; Farbauszugspunkte.</li>
	  </ul>
	<p style="height: 20px;"></p>
  </div>
	
	<div class="newsItem">

<table width="100%">
 <tr>
  <td><a href="javascript:showPic(32);"><img src="/images/hundertwasser/grafik/0852_sml.jpg" width="266" height="210" alt="" /></a></td>

  <td><a href="javascript:showPic(34);"><img src="/images/hundertwasser/grafik/0869_A_sml.jpg" width="284" height="216" alt="" /></a></td>
 </tr>
</table>

	<ul>
		<li>&middot; Auf vielen Graphiken finden sich gepr&auml;gt, gestempelt oder mitgedruckt Auflistungen von Farbvarianten, technischen Versionen und Auflagenangaben. </li>
		<li>&middot; In der Platte, im Stein oder im Sieb wurden h&auml;ufig eine zus&auml;tzliche Signatur, aber auch Werknummer und Name, manchmal auch der Name der Vorlage, Entstehungsort und -datum notiert. </li>

		<li>&middot; In vielen japanischen Farbholzschnitten ist der Titel des Werkes in japanischen Schriftzeichen mitgedruckt.</li>
	</ul>
	</div>
	
	<div class="newsItem alternative">
	<p>Mitunter lie&szlig; Hundertwasser die R&uuml;ckseite einer Graphik mit all diesen Informationen bedrucken, ging noch dar&uuml;ber hinaus und hielt dort den gesamten Entwicklungs- und Entstehungsproze&szlig; der Graphik fest. </p>

	
	<p>Hundertwasser hat am Bildrand der Graphiken mit runden Punkten die Arbeit der Techniker und Drucker und somit das vom Ausgangswerk &Uuml;bernommene dokumentiert; quadratische oder numerierte Punkte bezeichnen Hundertwassers eigenh&auml;ndige Arbeit f&uuml;r die Graphik, das f&uuml;r das Werk Neu-Entwickelte. </p>
	</div>

	</div> <!-- /content -->
</div> <!-- /contentwrapper -->

<div id="containerRight">

	<p><a href="../cover.php">Hundertwasser</a></p>
	<ul>
    	<li><a href="../malerei/malerei.php">Malerei</a></li> 	
	  	<li class="active"><a href="../graphic/graphik.php">Graphik</a></li>
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
