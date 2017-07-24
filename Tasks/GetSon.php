<?php
# This file would be on a timer as it forces all shows to update and new shows to be added.
require_once("./../Config.php");
require_once("./../Classes/Sonarr/Main.php");
$Sonarr = new Sonarr($SonarrServerURL,$SonarrAPIKey); # Connect To Sonarr API
require_once("./../Classes/DB/Main.php");
$DB = new DB(); # Database Class

$Shows = $Sonarr->GetShowArray(); # Pulls shows in JSON format.
#$Episodes = $Sonarr->GetEpisodes(1);
#$DB->UpdateEpisodes($Episodes);
$DB->UpdateShows($Shows,$SonarrServerURL); # Runs the UpdateShows function.
?>