<?php
function getMedien($auswahl="", $bereich="", $sepAt=-1, $nameAlsTooltip=false, $tooltipNigefuMarkieren=false) 
{
	$parent = basename(getcwd());
	$files = (stristr($bereich,"all") ? array_slice(scandir("../"), 2) : [$parent]);
	$ausgabe = array();
	foreach ($files as $file) 
	{
		$verz = (stristr($bereich,"all") ? "../" . $file : ".");
		if (is_dir($verz))
		{
			$separator = (stristr($bereich,"all") ? str_replace("_"," ",str_replace("-"," ",$file)) : "");
			$files2 = array_slice(scandir($verz), 2);
			$name = array();
			$laenge = array();
			$titel = array();
			if (file_exists($verz . "/.files.txt"))
			{
				$info = file($verz . "/.files.txt");
				foreach ($info as $infoz) 
				{
					$teil = explode("\t", $infoz);
					if ($teil[0] == "exif")
					{
						array_push($name,sizeof($teil) > 1 ? trim($teil[1]) : "");
						array_push($name,sizeof($teil) > 1 ? umlauteUFT8dreiByte2zweiByte(trim($teil[1])) : "");
						array_push($laenge,sizeof($teil) > 2 ? trim($teil[2]) : "");
						array_push($laenge,sizeof($teil) > 2 ? trim($teil[2]) : "");
						array_push($titel,sizeof($teil) > 5 ? trim($teil[5]) : "");
						array_push($titel,sizeof($teil) > 5 ? trim($teil[5]) : "");
					}
				}
			}
			else if (file_exists($verz . "/_videos.txt"))
			{
				$info = file($verz . "/_videos.txt");
				foreach ($info as $infoz) 
				{
					if (substr($infoz, 0, 2) != "$$$")
					{
						$teil = explode("	", $infoz);
						array_push($name,sizeof($teil) > 0 ? trim($teil[0]) : "");
						array_push($name,sizeof($teil) > 0 ? umlauteUFT8dreiByte2zweiByte(trim($teil[0])) : "");
						array_push($titel,sizeof($teil) > 1 ? trim($teil[1]) : "");
						array_push($titel,sizeof($teil) > 1 ? trim($teil[1]) : "");
						array_push($laenge,sizeof($teil) > 3 ? trim($teil[3]) : "");
						array_push($laenge,sizeof($teil) > 3 ? trim($teil[3]) : "");
					}
				}
			}
			else if (file_exists($verz . "/_files.txt"))
			{
				$info = file($verz . "/_files.txt");
				foreach ($info as $infoz) 
				{
					$teil = explode("$", $infoz);
					array_push($name,sizeof($teil) > 0 ? trim($teil[0]) : "");
					array_push($name,sizeof($teil) > 0 ? umlauteUFT8dreiByte2zweiByte(trim($teil[0])) : "");
					array_push($titel,sizeof($teil) > 1 ? trim($teil[1]) : "");
					array_push($titel,sizeof($teil) > 1 ? trim($teil[1]) : "");
					array_push($laenge,sizeof($teil) > 2 ? trim($teil[2]) : "");
					array_push($laenge,sizeof($teil) > 2 ? trim($teil[2]) : "");
				}
			}
			foreach ($files2 as $file2) 
			{
				$datei = (stristr($bereich,"all") ? $verz . "/" : "") . $file2;
				$path_parts = pathinfo($datei);
				$poster = "";
				$preview = "";
				$tooltip = ($nameAlsTooltip ? $path_parts["filename"] : "");
				$laengeNumerisch = "";
				if (($separator == "") && ($sepAt >= 10) && (sizeOf($ausgabe) > 0) && (sizeOf($ausgabe) % $sepAt == 0))
				{
					$separator = sizeOf($ausgabe) . " ... " . sizeOf($ausgabe);
				}
				if (sizeof($name) > 0)
				{
					if (in_array($path_parts["filename"], $name, true))
					{
						$titelFound = $titel[array_search($path_parts["filename"], $name)]; 
						$tooltip = ($titelFound == "" ? $tooltip : $titelFound); 
						$laengeFound = $laenge[array_search($path_parts["filename"], $name)]; 
						$tooltip = $tooltip . ($laengeFound == "" ? "" : ($tooltip == "" ? "" : " - ") . $laengeFound . (is_numeric($laengeFound) == 1 ? " s" : ""));
						$laengeNumerisch = (is_numeric($laengeFound) == 1 ? $laengeFound : "");
					}			
					else
					{
						$tooltip = $tooltip . ( $tooltipNigefuMarkieren ? " (!!!) " : "");
					}			
				}
				if (stristr($auswahl, "image") && strstr(mime_content_type($datei), "image/"))
				{
					$poster_k = (stristr($bereich,"all") ? $verz . "/" : "") . "Poster/" . $path_parts["filename"] . ".jpg"; 
					$poster_g = (stristr($bereich,"all") ? $verz . "/" : "") . "Poster/" . $path_parts["filename"] . ".JPG"; 
					if (file_exists($poster_k))
					{
						$poster = $poster_k;
					}
					else if (file_exists($poster_g))
					{
						$poster = $poster_g;
					}
					array_push($ausgabe, array($datei, mime_content_type($datei), $poster, $preview, $tooltip, $laengeNumerisch, $separator));
					$separator = '';
				}
				if (stristr($auswahl, "video") && strstr(mime_content_type($datei), "video/"))
				{
					$poster_work = (stristr($bereich,"all") ? $verz . "/" : "") . "Poster/" . $path_parts["filename"] . ".jpg"; 
					$gallery = (stristr($bereich,"all") ? $verz . "/" : "") . $path_parts["filename"] . "_gallery.jpg";
					if (file_exists($poster_work))
					{
						$poster = $poster_work;
					}
					else if (file_exists($gallery))
					{
						$poster = $gallery;
					}
					$preview_work = (stristr($bereich,"all") ? $verz . "/" : "") . "Preview/" . $path_parts["filename"] . ".jpg"; 
					if (file_exists($preview_work))
					{
						$preview = $preview_work;
					}
					array_push($ausgabe, array($datei, mime_content_type($datei), $poster, $preview, $tooltip, $laengeNumerisch, $separator));
					$separator = '';
				}
			}
		}
	}
	echo '['; $i=0; foreach ($ausgabe as $zeile) { echo '["'.implode('","', $zeile).'"]'; $i++; if ($i<sizeof($ausgabe)) { echo ','; } } echo ']';
}
function umlauteUFT8dreiByte2zweiByte($ein="") 
{
	$aus = $ein;
	$aus = str_replace(hex2bin("61CC88"), hex2bin("C3A4"), $aus);
	$aus = str_replace(hex2bin("6FCC88"), hex2bin("C3B6"), $aus);
	$aus = str_replace(hex2bin("75CC88"), hex2bin("C3BC"), $aus);
	$aus = str_replace(hex2bin("41CC88"), hex2bin("C384"), $aus);
	$aus = str_replace(hex2bin("4FCC88"), hex2bin("C396"), $aus);
	$aus = str_replace(hex2bin("55CC88"), hex2bin("C39C"), $aus);
	return $aus;
}
function getParent() 
{
	echo '"'.str_replace('_',' ',str_replace('-',' ',basename(getcwd()))).'"';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" href="https://jsonjfi.de.cool/anzeigeMedien.css">
</head>
<body>
<script>
	var parent = <?=getParent()?>;
	document.write('<title>'+parent+'</title>');
//	function getMedien($auswahl="", $bereich="", $sepAt=-1, $nameAlsTooltip=false, $tooltipNigefuMarkieren=false) 
//	getMedien("imagesOderVideos")?>;
//	getMedien("imagesOderVideos", "alle")?>;
//	getMedien("images")?>;
//	getMedien("imagesOderVideos","",-1,false,true)?>;
//	getMedien("imagesOderVideos","",-1,true)?>;
//	getMedien("imagesOderVideos","",-1,true,true)?>;
	var medienArray = <?=getMedien("Videos", "alle")?>;
//	getMedien("Videos","",-1,false,true)?>;
//	getMedien("Videos","",-1,true)?>;
//	getMedien("Videos","",-1,true,true)?>;
	
	if (medienArray.length == 0)
	{
		document.write('keine Bilder und keine Videos vorhanden');
	}
	else
	{
		var isDiashow = false;
		var diashowInterval = 3;
		var isShowStartStopButton = false;
		var isShowSteuerungButton = true;
		var isShowDownloadButton = false;

		var diashowIcon = "https://jsonjfi.de.cool/stopDiashow.png";
		var keineDiashowIcon = "https://jsonjfi.de.cool/startDiashow.png";
		var keineDiashowRueckwaertsIcon = "https://jsonjfi.de.cool/startDiashowRueckwaerts.png";
		var isDiashowRueckwaerts = false;
		var steuerungButton;
		var startStopButton;
		var showStartStopButton;
		var menu;
		var menuIcon = "https://jsonjfi.de.cool/menu.png";
		var isInMenu = false;
		var diashowButton;
		var diashowRueckwaertsButton;
		var selectForm;
		var downloadButton;
		var downloadIcon = "https://jsonjfi.de.cool/download.png";
		var showDownloadButton;
		var index = 0;
		var indexNext = 0;
		var indexPrev = 0;
		var expandImage;
		var expandImageFrame;
		var expandVideo;
		var expandVideoFrame;
		var expandVideoOhnePoster;
		var expandVideoFrameOhnePoster;
		var timeoutID;
		var videoDauer;
		var infoBox;
		var infoText;
		const isTouchScreen = ( 'ontouchstart' in window ) || ( navigator.maxTouchPoints > 0 ) || ( navigator.msMaxTouchPoints > 0 );
		document.write('<div class="container"><a><span class="contooltiptext" id="contooltiptext"></span></a>');
		document.write('<div class="scrollmenu">');
		for (let i = 0; i < medienArray.length; i++) 
		{
			if (medienArray[i][6].length > 0)
			{
				document.write('<a>' + formatSeparator(medienArray[i][6],9,3) + '</a>');
			}
			document.write('<a><' + (medienArray[i][2].length == 0 && medienArray[i][1].startsWith("video") ? "video" : "img") + ' id="' + i + '" src="' + medienArray[i][medienArray[i][2].length > 0 ? 2 : 0] + (medienArray[i][2].length == 0 && medienArray[i][1].startsWith("video") ? '#t=0.3' : '') + '" onclick="showMedium(this);">' + (isTouchScreen ? '' : '<span class="tooltiptext">' + medienArray[i][4] + '</span>') + '</a>');
		}
		document.write('</div>');
		document.write('<div id=swipeFrame>');
		document.write('	<div id=imageFrame class="container" style="display: none;">');
		document.write('		<img id="image" style="width: 97vw; height: auto;	max-width: none; max-height: 87vh; object-fit: contain; z-index: 1;">');
		document.write('	</div>');
		expandImage = document.getElementById("image");
		expandImageDownload = document.getElementById("imageDownloadButton");
		expandImageFrame = document.getElementById("imageFrame");
		document.write('	<div id=videoFrame style="display: none;">');
		document.write('		<video id="video" type="video/mp4" controls style="width: 97vw; height: auto; max-width: none; max-height: 87vh; z-index: 1;">Your browser does not support HTML video.</video>');
		document.write('	</div>');
		expandVideo = document.getElementById("video");
		expandVideoDownload = document.getElementById("videoDownload");
		expandVideoFrame = document.getElementById("videoFrame");
		document.write('	<div id=videoFrameOhnePoster style="display: none;">');
		document.write('		<video id="videoOhnePoster" type="video/mp4" controls style="width: 97vw; height: auto; max-width: none; max-height: 87vh; z-index: 1;">Your browser does not support HTML video.</video>');
		document.write('	</div>');
		expandVideoOhnePoster = document.getElementById("videoOhnePoster");
		expandVideoOhnePosterDownload = document.getElementById("videoOhnePosterDownload");
		expandVideoFrameOhnePoster = document.getElementById("videoFrameOhnePoster");
		document.write('<a style="z-index: 2; position: fixed; top: 16vh; left: 0vw; opacity: 0.5;"><img id="startStopButton" src="' + getDiashowIcon() + '" onclick="toggleDiashow();"></a>');
		startStopButton = document.getElementById("startStopButton");
		startStopButton.style.display = (isShowStartStopButton ? "inherit" : "none");
		document.write('<a id="steuerungButton" style="z-index: 2; position: absolute; bottom: 8vh; left: 0vw; opacity: 0.5;"><button type="button" onclick="switchMenu();"><img src="' + menuIcon + '"></button></a>');
		steuerungButton = document.getElementById("steuerungButton");
		steuerungButton.style.display = (isShowSteuerungButton ? "inherit" : "none");
		document.write('<a id="downloadButton" style="z-index: 2; position: absolute; bottom: 8vh; right: 0vw; opacity: 0.5;" href="' + medienArray[index][0] + '" download><img src="' + downloadIcon + '"></a>');
		downloadButton = document.getElementById("downloadButton");
		downloadButton.style.display = (isShowDownloadButton ? "inherit" : "none");
		document.write('</div>');
		document.write('<div id="menu" class="modal" style="z-index: 3; position: fixed; top: 22vh; left: 0vw; opacity: 0.9;">');
		menu = document.getElementById("menu");
		document.write('	<div class="modal-content">');
		document.write('			<span class="close" onclick="closeMenu();">&times;</span>');
		document.write('Diashow:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="diashowButton" ' + (isDiashow ? "checked" : "") + '><br><br>');
		diashowButton = document.getElementById("diashowButton");
		diashowButton.addEventListener("change", e => { isDiashow = e.target.checked; startStopDiashow() });
		document.write('Diashow r&uuml;ckw&auml;rts:&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="diashowRueckwaertsButton" ' + (isDiashowRueckwaerts ? "checked" : "") + '><br><br>');
		diashowRueckwaertsButton = document.getElementById("diashowRueckwaertsButton");
		diashowRueckwaertsButton.addEventListener("change", e => { isDiashowRueckwaerts = e.target.checked; startStopButton.src = getDiashowIcon(); });
		document.write('<form>');
		document.write('Diashow Intervall:&nbsp;&nbsp;&nbsp;&nbsp;');
		document.write('<select id="selectForm">');
		for (let i = 1; i < 10; i++) 
		{
			document.write('  <option value="' + i + '"' + (i == diashowInterval ? " selected": "") + '>' + i +',0</option>');
		}
		document.write('  <option value="0.6">0,6</option>');
		document.write('  <option value="0.3">0,3</option>');
		document.write('</select>');
		document.write('</form><br>');
		selectForm = document.getElementById("selectForm");
		selectForm.addEventListener("change", e => { diashowInterval = e.target.value; });
		document.write('StartStopbutton zeigen:&nbsp;&nbsp;<input type="checkbox" id="showStartStopButton" ' + (isShowStartStopButton ? "checked" : "") + '><br><br>');
		showStartStopButton = document.getElementById("showStartStopButton");
		showStartStopButton.addEventListener("change", e => { isShowStartStopButton = e.target.checked; startStopButton.style.display = ( isShowStartStopButton ? "inherit" : "none" );	});
		document.write('Downloadbutton zeigen:&nbsp;&nbsp;<input type="checkbox" id="showDownloadButton" ' + (isShowDownloadButton ? "checked" : "") + '>');
		showDownloadButton = document.getElementById("showDownloadButton");
		showDownloadButton.addEventListener("change", e => { isShowDownloadButton = e.target.checked; downloadButton.style.display = ( isShowDownloadButton ? "inherit" : "none" );	});
		document.write('	</div>');
		document.write('</div>');
		menu.style.display = isShowSteuerungButton;
		document.write('<div id="infoBox" class="modal" style="z-index: 3; position: fixed; top: 13vh; left:50%; opacity: 0.9;">');
		infoBox = document.getElementById("infoBox");
		document.write('<div class="modal-content">');
		document.write('<p id="infoText"></p>');
		infoText = document.getElementById("infoText");
		document.write('</div></div>');
		if (isDiashow)
		{
			// console.log("Init diashow");
			diashow();
		}
		else
		{
			// console.log("Init keine diashow");
			showMedium(document.getElementById(0));
		}
		function diashow()
		{
			showMedium(document.getElementById(index));
			if (medienArray[index][1].startsWith("video") && (medienArray[index][5].length == 0))
			{
				console.log("Diashow gestoppt, da f&uuml;r Video "+ medienArray[index][0] + " keine Dauer bekannt!");
				infoBox.style.display = "block";
				infoText.innerHTML = "Diashow gestoppt, da f&uuml;r Video "+ medienArray[index][0] + " keine Dauer bekannt!";
				setTimeout(function() { infoBox.style.display = "none"; }, 3000);
				isDiashow = false;
				startStopDiashow();
			}
			else
			{
				videoDauer = (medienArray[index][5].length > 0 ? parseFloat(medienArray[index][5]) : 0);
				console.log(medienArray[index][0] + " Waittime: " + (diashowInterval + videoDauer));
				if (isDiashowRueckwaerts)
				{
					index--;
					if (index < 0)
					{
						index = medienArray.length - 1;
					}
					// console.log("diashow next medium");
				}
				else
				{
					index++;
					if (index >= medienArray.length)
					{
						index = 0;
					}
					// console.log("diashow next medium");
				}    
				timeoutID = setTimeout(diashow, (diashowInterval + videoDauer) * 1000); 
			}
		}
		function nichtstun()
		{
		}
		function diashowStop()
		{
			// console.log("diashow stop");
			clearTimeout(timeoutID);
		}
		function toggleDiashow()
		{
			// console.log("toggleDiashow: in Menu ? "+ isInMenu);
			if (! isInMenu)
			{
				// console.log("toggleDiashow: "+ isDiashow + " -> " + (! isDiashow));
				isDiashow = ! isDiashow;
				startStopDiashow();
			}
		}
		function getDiashowIcon()
		{
			return (isDiashow ? diashowIcon : (isDiashowRueckwaerts ? keineDiashowRueckwaertsIcon : keineDiashowIcon));
		}
		function startStopDiashow()
		{
			startStopButton.src = getDiashowIcon();
			if (isDiashow) 
			{ 
				diashow();
			}
			else 
			{
				diashowStop();
			}
		}
		function switchMenu() 
		{
			if (isInMenu)
			{
				closeMenu();
			}
			else
			{
				diashowButton.checked = isDiashow;
				menu.style.display = "block";
				isInMenu = true;
			}
		}
		function closeMenu() 
		{
			// console.log("hide Menu");
			menu.style.display = "none";
			isInMenu = false;
		}
		function showMedium(previewImage) 
		{
			// bei altem Preview Umrandung entfernen
			for (let i = 0; i < medienArray.length; i++) 
			{
				document.getElementById(i).style.border="none";
			}
			index = parseInt(previewImage.id);
			// bei aktuellem Preview Umrandung setzen
			document.getElementById(index).style.border="thin solid black";
			if (navigator.userAgent.indexOf("Firefox") == -1 ) 
			{
				// Funktioniert bei nicht bei Firefox
				document.getElementById(index).scrollIntoViewIfNeeded(true);
			}
			indexNext = Math.min(index + 1, medienArray.length - 1);
			indexPrev = Math.max(index - 1, 0);
			expandImageFrame.style.display = "none";
			expandVideoFrame.style.display = "none";
			expandVideoFrameOhnePoster.style.display = "none";
			downloadButton.href = medienArray[index][0];
			if (medienArray[index][1].startsWith("image"))
			{
				expandImage.src = medienArray[index][0];
				expandImageFrame.style.display = "block";
			}
			else if (medienArray[index][1].startsWith("video"))
			{
				if (medienArray[index][3] == "")
				{
					expandVideoOhnePoster.src = medienArray[index][0];
					expandVideoOhnePoster.type = medienArray[index][1];
					expandVideoFrameOhnePoster.style.display = "inline";
					expandVideoOhnePoster.autoplay = isDiashow;
				}
				else
				{
					expandVideo.src = medienArray[index][0];
					expandVideo.type = medienArray[index][1];
					expandVideo.poster = medienArray[index][3];
					expandVideoFrame.style.display = "inline";
					expandVideo.autoplay = isDiashow;
				}
			}
			else
			{
				// kann nicht vorkommen, wird schon bei den previews abgefangen
			}
			// Tooltiptext setzen
			document.getElementById("contooltiptext").textContent = medienArray[index][4];
		}
		function formatSeparator(ein, laenge, anzahl)
		{
			var tab1 = ein.split(" "); var tab2 = []; var zeile = "";
			for (let i = 0; i < tab1.length; i++)
			{
				if ((zeile + tab1[i]).length > laenge)
				{
					if (tab1[i].length > laenge)
					{
						tab2.push(zeile + (zeile.length > 0 ? "&nbsp;" : "") + tab1[i].substring(0,laenge - zeile.length));
						zeile = tab1[i].substring(laenge - zeile.length);
					} 
					else { tab2.push(zeile); zeile = tab1[i]; }
				}
				else { zeile = zeile + (zeile.length > 0 ? "&nbsp;" : "") + tab1[i]; }
			}
			tab2.push(zeile);
			var aus = ""; var i;
			for (i = 0; ((i < tab2.length -1) && (i < anzahl - 1)); i++) { aus = aus + tab2[i] +"<br>"; }
			aus = aus + (i==tab2.length - 1 ? "" : "..&nbsp;") + tab2[tab2.length - 1];
			return aus;
		}
		var swiper = { touchStartX: 0, touchEndX: 0, minSwipePixels: 30, detectionZone: undefined,
		swiperCallback: function() {},
			init: function (detectionZone, callback) { swiper.swiperCallback = callback
			detectionZone.addEventListener("touchstart", function (event) {
				swiper.touchStartX = event.changedTouches[0].screenX; }, false);
			detectionZone.addEventListener("touchend", function (event) {
				swiper.touchEndX = event.changedTouches[0].screenX;
				swiper.handleSwipeGesture(); }, false); },
			handleSwipeGesture: function () {
				if (swiper.touchEndX < swiper.touchStartX && (indexNext > index)) { showMedium(document.getElementById(indexNext)) }
				if (swiper.touchEndX > swiper.touchStartX && (indexPrev < index)) { showMedium(document.getElementById(indexPrev)) } },
			swipe: function (direction, movedPixels) { var ret = {}
				ret.direction = direction, ret.movedPixels = movedPixels, swiper.swiperCallback(ret) } };
		var gestureZone = document.getElementById("swipeFrame"); swiper.init(gestureZone, function(e) { });
	}
</script>
<noscript>
	<h3>Der Browser unterst&uuml;tzt kein JavaScript, daher ist eine Anzeige der Medien nicht m&ouml;glich.</h3>
	<h3>Um eine Anzeige zu erm&ouml;glichen, m&uuml;sste tempor&auml;r JavaScript erlaubt werden.</h3>
</noscript>
</body>
</html>