<?php
$time_start = microtime(true); 
set_time_limit(30); # Thios script may take time to exectute....
require_once("./Config.php");
require_once("./Classes/Sonarr/Main.php");
$Sonarr = new Sonarr($SonarrServerURL,$SonarrAPIKey); # Connect To Sonarr API
require_once("./Classes/DB/Main.php");
$DB = new DB(); # Database Class


$ShowIDs = $DB->GetShowIDs();
foreach($ShowIDs as $ShowID){
	$Episodes = $Sonarr->GetEpisodes($ShowID['Show_Sonarr_ID']);
	$DB->UpdateEpisodes($Episodes);
}
echo "<br>";
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
?>