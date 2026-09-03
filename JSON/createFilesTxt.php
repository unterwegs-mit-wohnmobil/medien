<?php
$files = array_slice(scandir('.'), 2);
sort($files);
$ausgabe = array();
foreach ($files as $file) 
{
	$path_parts = pathinfo($file);
	if (strstr(mime_content_type($file ), "image/"))
	{
		array_push($ausgabe, $path_parts["filename"] . "$\n");
	}
	if (strstr(mime_content_type($file ), "video/"))
	{
		array_push($ausgabe, $path_parts["filename"] . "$$\n");
	}
}
if (sizeOf($ausgabe) > 0)
{
	file_put_contents("_files.txt", $ausgabe);
}
?>