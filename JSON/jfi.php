<?php
function getFiles($typ = "") 
{
	$files = array_slice(scandir('.'), 2);
	$ausgabe = array();
	foreach ($files as $file) 
	{
		if ($typ == "")
		{
			array_push($ausgabe, $file);
		}
		if (stristr($typ, "file") && is_file($file))
		{
			array_push($ausgabe, $file);
		}
		if (stristr($typ, "folder") && is_dir($file))
		{
			array_push($ausgabe, $file);
		}
	}
	echo (sizeOf($ausgabe) == 0 ? '[]' : '["'.implode('","', $ausgabe).'"]');
}
function getFolder($typ = "") 
{
	$files = array_slice(scandir('.'), 2);
	$ausgabe = array();
	foreach ($files as $file) 
	{
		if (is_dir($file))
		{
			if ($typ == "")
			{
				array_push($ausgabe, $file);
			}
			else if ((stristr($typ, "indexPHP") && file_exists($file . "/index.php")) || (stristr($typ, "indexHTM") && file_exists($file . "/index.htm")))
			{
				array_push($ausgabe, array($file, file_exists($file . "/index.php") ? "/index.php" : "/index.htm"));
			}
			else
			{
				$files2 = array_slice(scandir($file), 2);
				foreach ($files2 as $file2) 
				{
					$path_parts = pathinfo($file . "/" . $file2);
					if (stristr($typ, "image") && strstr(mime_content_type($file . "/" . $file2), "image/"))
					{
						array_push($ausgabe, $file);
						break;
					}
					if (stristr($typ, "video") && strstr(mime_content_type($file . "/" . $file2), "video/"))
					{
						array_push($ausgabe, $file);
						break;
					}
				}
			}
		}
	}
	if (stristr($typ, "index"))
	{
		echo '['; $i=0; foreach ($ausgabe as $zeile) { echo '["'.implode('","', $zeile).'"]'; $i++; if ($i<sizeof($ausgabe)) { echo ','; } } echo ']';
	} 
	else
	{
		echo (sizeOf($ausgabe) == 0 ? '[]' : '["'.implode('","', $ausgabe).'"]');
	}
}
function getMedien($auswahl="", $bereich="", $sepAt=-1, $nameAlsTooltip=false, $tooltipNigefuMarkieren=false) 
{
	$parent = basename(getcwd());
	$files = (stristr($bereich,"all") ? array_slice(scandir("../"), 2) : [$parent]);
	$ausgabe = array();
	$date = new DateTime();
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
						$teil = explode("\t", $infoz);
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
			for ($i = 0; $i < count($name); $i++)
			{
				if ($name[$i] == "*")
				{
					for ($j = 0; $j < count($name); $j++)
					{
						if ($titel[$j] == "")
						{
							$titel[$j] = $titel[$i];
						}
					}
					break;
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
				$laengeAufbereitet = "";
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
						$laengeAufbereitet = $laengeFound;
						if (is_numeric($laengeFound) == 1)
						{
							if ($laengeFound < 60)
							{
								$laengeAufbereitet = $laengeFound . " Sek.";
							}
							else
							{
								$date->setTime(0,0,$laengeFound);
								$laengeAufbereitet = date_format($date, ($laengeFound < 3600 ? "i:s" : "H:i:s"));
							}
						}
						$tooltip = $tooltip . ($laengeFound == "" ? "" : ($tooltip == "" ? "" : " - ") . $laengeAufbereitet);
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
function getPHPversion() 
{
	echo '"' . phpversion() . '"';
}
// Test der Funktionen
$test = [0,0,1,1,0,0,0,0,0];
//$test = [1,1,1,1,1,1,1,1];
if ($test[0] == 1)
{
	echo "\n\r\n\rgetFiles:\n\r"; 
	echo getFiles(); 
	echo "\n\r\n\rgetFiles file:\n\r"; 
	echo getFiles("file"); 
	echo "\n\r\n\rgetFiles folder:\n\r"; 
	echo getFiles("folder"); 
}
if ($test[1] == 1)
{
	echo "\n\r\n\rgetFolder:\n\r"; 
	echo getFolder(); 
	echo "\n\r\n\rgetFolder mit Images:\n\r"; 
	echo getFolder("Image"); 
	echo "\n\r\n\rgetFolder mit Videos:\n\r"; 
	echo getFolder("Video"); 
	echo "\n\r\n\rgetFolder mit Images oder Videos:\n\r"; 
	echo getFolder("ImagesOderVideos"); 
	echo "\n\r\n\rgetFolder mit index.php:\n\r"; 
	echo getFolder("indexPHP"); 
	echo "\n\r\n\rgetFolder mit index.htm:\n\r"; 
	echo getFolder("indexHTM"); 
	echo "\n\r\n\rgetFolder mit index.php oder index.htm:\n\r"; 
	echo getFolder("indexPHPoderindexHTM"); 
}
if ($test[2] == 1)
{
	echo "\n\r\n\rgetMedien :\n\r"; 
	echo getMedien(); 
	echo "\n\r\n\rgetMedien mit Images:\n\r"; 
	echo getMedien("Image"); 
	echo "\n\r\n\rgetMedien mit Videos:\n\r"; 
	echo getMedien("Video"); 
	echo "\n\r\n\rgetMedien mit Images oder Videos:\n\r"; 
	echo getMedien("ImagesOderVideos"); 
}
if ($test[3] == 1)
{
	echo "\n\r\n\rgetMedien , All:\n\r"; 
	echo getMedien("","All"); 
	echo "\n\r\n\rgetMedien mit Images, All:\n\r"; 
	echo getMedien("Images","All"); 
	echo "\n\r\n\rgetMedien mit Videos, All:\n\r"; 
	echo getMedien("Videos","All"); 
	echo "\n\r\n\rgetMedien mit Images oder Video, All:\n\r"; 
	echo getMedien("ImagesOderVideos","All"); 
}
if ($test[4] == 1)
{
	echo "\n\r\n\rgetMedien mit Images oder Videos:\n\r"; 
	echo getMedien("ImagesOderVideos"); 
	echo "\n\r\n\rgetMedien mit Images oder Videos, '', -1, true:\n\r"; 
	echo getMedien("ImagesOderVideos", "", -1, true); 
	echo "\n\r\n\rgetMedien mit Images oder Videos, '', -1, false, true:\n\r"; 
	echo getMedien("ImagesOderVideos", "", -1, false, true); 
	echo "\n\r\n\rgetMedien mit Images oder Videos, '', -1, true, true:\n\r"; 
	echo getMedien("ImagesOderVideos", "", -1, true, true); 
}
if ($test[5] == 1)
{
	echo "\n\r\n\rgetMedien mit Videos:\n\r"; 
	echo getMedien("Videos"); 
	echo "\n\r\n\rgetMedien mit Videos, '', -1, true:\n\r"; 
	echo getMedien("Videos", "", -1, true); 
	echo "\n\r\n\rgetMedien mit Videos, '', -1, false, true:\n\r"; 
	echo getMedien("Videos", "", -1, false, true); 
	echo "\n\r\n\rgetMedien mit Videos, '', -1, true, true:\n\r"; 
	echo getMedien("Videos", "", -1, true, true); 
}
if ($test[6] == 1)
{
	echo "\n\r\n\rgetMedien mit Videos, All:\n\r"; 
	echo getMedien("Video","all"); 
}
if ($test[7] == 1)
{
	echo "\n\r\n\rgetMedien mit Videos, '', 20:\n\r"; 
	echo getMedien("Video","", 20); 
}
if ($test[8] == 1)
{
	echo "\n\r\n\rgetPHPversion:\n\r"; 
	echo getPHPversion(); 
}
echo "\n\r"; 
?>