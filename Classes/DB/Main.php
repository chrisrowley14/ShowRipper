<?php
class DB{
	private $Read_Host;
	private $Read_User;
	private $Read_Pass;
	
	private $Write_Host;
	private $Write_User;
	private $Write_Pass;
	
	private $DB_Name;
	private $ConnectionHandler;
	
	private $Table_Shows;
	private $Table_Episodes;
	
	public function __construct(){
		$this->Read_Host = "localhost";
		$this->Read_User_Name = "autoanime_write";
		$this->Read_Password = "YtQSTc5rHgfCmwWE";
		
		$this->Write_Host = "localhost";
		$this->Write_User_Name = "autoanime_write";
		$this->Write_Password = "YtQSTc5rHgfCmwWE";
		
		$this->DB_Name = "autoanime";
		
		$this->Table_Shows = "shows";
		$this->Table_Episodes = "episodes";
	}
	
	private function DBConnectRead(){
		$this->ConnectionHandler = new PDO("mysql:host=$this->Read_Host;dbname=$this->DB_Name", $this->Read_User_Name, $this->Read_Password);
	}
	
	private function DBConnectWrite(){
		$this->ConnectionHandler = new PDO("mysql:host=$this->Write_Host;dbname=$this->DB_Name", $this->Write_User_Name, $this->Write_Password);
	}
	
	public function Escape($String){
		$this->DBConnectWrite();
		return $this->ConnectionHandler->quote($String);
	}
	
	public function UpdateShows($ShowArray,$SonarrHost){
		foreach($ShowArray as $Show){ # Loop each Show.
			var_dump($Show);
			echo "<hr>";
			$Title = $Show['title']; # Show Name.
			$SonarrID = $Show['id']; # Shows unique ID in Sonnary.
			
			$PosterImg = $SonarrHost . $Show['images'][2]['url']; # Main image for show.
			$FanArtImg = $SonarrHost . $Show['images'][0]['url']; # Fanart image for show.
			
			$TotalSeason = $Show['seasonCount']; # Total seasion(s) for show.
			$Status = $Show['status']; # Is show ended or still in progress...
			$Description = $Show['overview']; # Show description/synopsis.
			//TODO: Check timezones the air data is.
			$LastEpisodeAirDate = $Show['previousAiring']; # Data of last episode airing.
			$Network = $Show['network']; # What network the show airs on.
			$AirYear = $Show['year']; # Year it first aired.
			$ImdbId = $Show['imdbId']; # ImdbID, link to site or use api to get more show info.
			$GenresArray = $Show['genres']; # Genres in PHP Array.
			
			$this->UpdateShow($SonarrID,$Title,$PosterImg,$FanArtImg,$TotalSeason,$Status,$Description,$LastEpisodeAirDate,$Network,$AirYear,$ImdbId,$GenresArray);
		}
	}
	
	//TODO: Can we upload images to imgur to take load off server???
	private function grab_image($url,$saveto){ # Saves images to disk given URL and save location (location is rel to root of website).
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);
		if(file_exists($saveto)){
			unlink($saveto);
		}
		$fp = fopen($saveto,'x');
		fwrite($fp, $raw);
		fclose($fp);
	}

	public function UpdateShow($SonarrID,$Title,$PosterImgURL,$FanArtImgURL,$TotalSeason,$Status,$Description,$LastEpisodeAirDate,$Network,$AirYear,$ImdbId,$GenresArray){ # If the show is already in the database update it, else add it.
		$this->DBConnectWrite();
		$Query = $this->ConnectionHandler->prepare("SELECT Show_ID FROM $this->Table_Shows WHERE(Show_Sonarr_ID = $SonarrID);");
		$Query->execute();
		$Return = $Query->fetchAll();
		
		#Escape everything.
		$Title = $this->Escape($Title);
		$PosterImgPath = $this->Escape("./AutoImg/" . $SonarrID . "_P_.jpg");
		$FanArtImgPath = $this->Escape("./AutoImg/" . $SonarrID . "_F_.jpg");
		$Status = $this->Escape($Status);
		$Description = $this->Escape($Description);
		$LastEpisodeAirDate = str_replace("Z","",$this->Escape($LastEpisodeAirDate));
		$Network = $this->Escape($Network);
		$AirYear = $this->Escape($AirYear);
		$ImdbId = $this->Escape($ImdbId);
		$GenresArray = $this->Escape(implode(",",$GenresArray));
		if(empty($Return)){
			#New Show
			$QueryNew = $this->ConnectionHandler->prepare("INSERT INTO $this->Table_Shows (`Show_Sonarr_ID`,`Show_Name`,`Show_Poster`,`Show_FanArt`,`Show_Seasons`,`Show_Status`,`Show_Description`,`Show_LastAir`,`Show_Network`,`Show_AirYear`,`Show_ImdbId`,`Show_Genres`) VALUES ($SonarrID,$Title,$PosterImgPath,$FanArtImgPath,$TotalSeason,$Status,$Description,$LastEpisodeAirDate,$Network,$AirYear,$ImdbId,$GenresArray);");
			$QueryNew->execute();
			$this->grab_image($PosterImgURL,"./../AutoImg/" . $SonarrID . "_P_.jpg");
			$this->grab_image($FanArtImgURL,"./../AutoImg/" . $SonarrID . "_F_.jpg");
		}else{
			#Update Show
			//TODO: Update everything :)
			//TODO: Update images or add arg to??
			$QueryUpdate = $this->ConnectionHandler->prepare("UPDATE $this->Table_Shows SET `Show_Name` = $Title WHERE Show_Sonarr_ID = $SonarrID;");
			$QueryUpdate->execute();
		}
	}
	
	public function GetShows(){ # Returns all data on all shows.
		$this->DBConnectWrite();
		//TODO: Limit the query to so many results...
		$Query = $this->ConnectionHandler->prepare("SELECT * FROM $this->Table_Shows;");
		$Query->execute();
		$Return = $Query->fetchAll();
		return $Return;
	}
	
	public function GetShowByID($ID){ # Gets all data on whatever show matches ID.
		//TODO: What if this returns nothing?
		$this->DBConnectWrite();
		$Query = $this->ConnectionHandler->prepare("SELECT * FROM $this->Table_Shows WHERE(Show_ID = $ID);");
		$Query->execute();
		$Return = $Query->fetchAll();
		return $Return;
	}
	
	public function GetShowIDs(){
		$this->DBConnectWrite();
		$Query = $this->ConnectionHandler->prepare("SELECT Show_Sonarr_ID FROM $this->Table_Shows;");
		$Query->execute();
		$Return = $Query->fetchAll();
		return $Return;
	}
	
	
	public function UpdateEpisodes($EpisodeArray){
		foreach($EpisodeArray as $Episode){ # Loop each Episode.
		if(empty($Episode['overview'])){
			$Episode['overview'] = "";	
		}
		if(empty($Episode['airDateUtc'])){
			$Episode['airDateUtc'] = "";	
		}
			echo "seriesId: " . $Episode['seriesId'] . "<br>";
			echo "episodeFileId: " . $Episode['episodeFileId'] . "<br>";
			echo "seasonNumber: " . $Episode['seasonNumber'] . "<br>";
			echo "episodeNumber: " . $Episode['episodeNumber'] . "<br>";
			echo "title: " . $Episode['title'] . "<br>";
			#echo "airDate: " . $Episode['airDate'] . "<br>";
			echo "airDateUtc: " . $Episode['airDateUtc'] . "<br>";
			echo "overview: " . $Episode['overview'] . "<br>";
			echo "hasFile: " . $Episode['hasFile'] . "<br>";
			echo "monitored: " . $Episode['monitored'] . "<br>";
			echo "unverifiedSceneNumbering: " . $Episode['unverifiedSceneNumbering'] . "<br>";
			echo "id: " . $Episode['id'] . "<br>";
			$this->UpdateEpisode($Episode['id'],$Episode['seriesId'],$Episode['episodeFileId'],$Episode['seasonNumber'],$Episode['episodeNumber'],$Episode['title'],$Episode['airDateUtc'],$Episode['overview'],$Episode['hasFile'],$Episode['monitored'],$Episode['unverifiedSceneNumbering']);
		}
	}
	
	private function UpdateEpisode($SonarrID,$SeriesId,$EpisodeFileId,$SeasonNumber,$EpisodeNumber,$Title,$AirDateUtc,$Overview,$HasFile,$Monitored,$UnverifiedSceneNumbering){
		$this->DBConnectWrite();
		$Query = $this->ConnectionHandler->prepare("SELECT Episode_Show_Sonarr_ID FROM $this->Table_Episodes WHERE(Episode_Sonarr_ID = $SonarrID);");
		$Query->execute();
		$Return = $Query->fetchAll();
		$SonarrID = $this->Escape($SonarrID);
		$SeriesId = $this->Escape($SeriesId);
		$EpisodeFileId = $this->Escape($EpisodeFileId);
		$SeasonNumber = $this->Escape($SeasonNumber);
		$EpisodeNumber = $this->Escape($EpisodeNumber);
		$Title = $this->Escape($Title);
		$AirDateUtc = str_replace("Z","",$this->Escape($AirDateUtc));
		$Overview = $this->Escape($Overview);
		$HasFile = $this->Escape($HasFile);
		$Monitored = $this->Escape($Monitored);
		$UnverifiedSceneNumbering = $this->Escape($UnverifiedSceneNumbering);
		if(empty($Return)){
			#New Episode
			echo "new<br><br>";
			$QueryNew = $this->ConnectionHandler->prepare("INSERT INTO $this->Table_Episodes (`Episode_Sonarr_ID`, `Episode_Show_Sonarr_ID`, `Episode_FileId`, `Episode_SeasonNumber`, `Episode_EpisodeNumber`, `Episode_Title`, `Episode_AirDateUtc`, `Episode_Overview`, `Episode_HasFile`, `Episode_Monitored`, `Episode_UnverifiedSceneNumbering`) VALUES ($SonarrID, $SeriesId, $EpisodeFileId, $SeasonNumber, $EpisodeNumber, $Title, $AirDateUtc, $Overview, $HasFile, $Monitored, $UnverifiedSceneNumbering);");
			$QueryNew->execute();
		}else{
			#Update Episode
			#$Return[0]['Episode_Show_Sonarr_ID']
			echo "update<br><br>";
			//TODO: Actually update stuff.....
			$QueryUpdate = $this->ConnectionHandler->prepare("UPDATE $this->Table_Episodes SET `Episode_FileId` = $EpisodeFileId, `Episode_SeasonNumber` = $SeasonNumber, `Episode_EpisodeNumber` = $EpisodeNumber, `Episode_Title` = $Title, `Episode_AirDateUtc` = $AirDateUtc, `Episode_Overview` = $Overview, `Episode_HasFile` = $HasFile, `Episode_Monitored` = $Monitored, `Episode_UnverifiedSceneNumbering` = $UnverifiedSceneNumbering WHERE(`Episode_ID` = $Return[0]['Episode_Show_Sonarr_ID']);");
			$QueryUpdate->execute();
		}
		
	}
	
	public function GetEpisodes($ID,$Season){
		$this->DBConnectWrite();
		$Query = $this->ConnectionHandler->prepare("SELECT * FROM $this->Table_Episodes WHERE(Episode_Show_Sonarr_ID = $ID AND Episode_SeasonNumber = $Season) ORDER BY Episode_EpisodeNumber DESC;");
		$Query->execute();
		$Return = $Query->fetchAll();
		return $Return;
	}
	
	public function GetSeasons($ID){
		$this->DBConnectWrite();
		$Query = $this->ConnectionHandler->prepare("SELECT DISTINCT Episode_SeasonNumber FROM $this->Table_Episodes WHERE(Episode_Show_Sonarr_ID = $ID) ORDER BY Episode_SeasonNumber DESC;");
		$Query->execute();
		$Return = $Query->fetchAll();
		return $Return;
	}
}
?>