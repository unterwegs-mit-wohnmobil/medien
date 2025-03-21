<?php
$ausgabe = file("_files.txt");
$files = array_slice(scandir('.'), 2);
sort($files);
foreach ($files as $file) 
{
	$path_parts = pathinfo($file);
	$search = preg_quote($path_parts["filename"] . "$", "~");
	$result = preg_filter('~' . $search . '~', null, $ausgabe);
	if ( (sizeOf($result) == 0))
	{
		if (strstr(mime_content_type($file ), "image/"))
		{
			array_push($ausgabe, $path_parts["filename"] . "$\n");
		}
		if (strstr(mime_content_type($file ), "video/"))
		{
			array_push($ausgabe, $path_parts["filename"] . "$$\n");
		}
	}
}
if (sizeOf($ausgabe) > 0)
{
	file_put_contents("_files.txt", $ausgabe);
}
?>