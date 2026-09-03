<?php
// Source in https://jsonjfi.de.cool/jfi.php
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
			else if (stristr($typ, "indexPHP") && file_exists($file . "/index.php"))
			{
				array_push($ausgabe, $file);
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
	echo (sizeOf($ausgabe) == 0 ? "[]" : "['".implode("','", $ausgabe)."']");
}
function getParent() 
{
	echo "'".str_replace("_"," ",str_replace("-"," ",basename(getcwd())))."'";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
	.scrollmenu { background-color: #333; overflow: auto; }
	.scrollmenu a
	{
		display: inline-block;
		color: white;
		text-align: center;
		padding: 2vh;
		text-decoration: none;
		font-family: Tahoma, Verdana, sans-serif;
		font-size: xx-large;
		border-style: outset;
	}
	.scrollmenu a:hover { background-color: #777; }
</style>
</head>
<body>
<script>
	var parent = <?=getParent()?>;
	document.write('<title>'+parent+'</title>');
	var files = <?=getFolder("indexPHP")?>;
	var text = '';
	if (files.length == 0)
	{
		document.write('keine Ordner mit index.php vorhanden');
	}
	else
	{
		document.write('<div class="scrollmenu">');
		for (let i = 0; i < files.length; i++) 
		{
			text = files[i]; text = text.replaceAll('-',' '); text = text.replaceAll('_',' ');
			document.write('<a href="'+ files[i] + '/index.php">'+text+'</a>');
		}
		document.write('</div>');
	}
</script>
<noscript>
	<h3>Der Browser unterst&uuml;tzt kein JavaScript, daher ist eine Anzeige der Medien nicht m&ouml;glich.</h3>
	<h3>Um eine Anzeige zu erm&ouml;glichen, m&uuml;sste tempor&auml;r JavaScript erlaubt werden.</h3>
</noscript>
</body>
</html>