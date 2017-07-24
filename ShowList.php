<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
require_once("./Classes/DB/Main.php");
$DB = new DB();

foreach($DB->GetShows() as $Show){
	foreach ($Show as $key => $value) {
		if(!is_int($key)){
			echo "{$key}:  {$value} <br>";	
		}
	}
	#echo "<img src='" . $Show['Show_FanArt'] . "'>";
	#var_dump($Show);
	echo "<br>";
	#echo "Show_ID: " . $Show['Show_ID'] . "<br>";
	
}
?>
</body>
</html>
