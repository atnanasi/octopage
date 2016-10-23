<?php
//Octopage atnanasi

//Load Markdown
spl_autoload_register(function($class){
	require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});

// Get Markdown class
use \Michelf\Markdown;

//Config
$page_root = "/srv/html/www/test";
$access_token = "someUUID";
//###TEXT### is replaced by body text
$header_text = file_get_contents("header.html");

//Check pageroot
if (!(file_exists($page_root))) {
	echo "Error:Invalid folder";
	exit ;
}

//Check git file
if (!(file_exists("{$page_root}/.git"))) {
	echo "Error:Invalid git";
	exit ;
}

//Check token
if ($_GET["access_token"] != $access_token) {
	echo "Error:Invalid token";
	exit ;
}

//Pull from git
shell_exec("cd {$page_root} && git reset --hard HEAD && git pull");

//Parse md and write html file
foreach (glob("{$page_root}/*.txt") as $filename) {
	//Get filename without extension
	preg_match("/(.*)(?:\.([^.]+$))/",$filename,$filenames);

	//Parse Markdown text
	$text = file_get_contents($filename);
	$parsed = Markdown::defaultTransform($text);

	//Put text to header
	$html = str_replace("###TEXT###", $parsed, $header_text);

	//write html file.
	file_put_contents ("{$filenames[1]}.html", $html);
}
