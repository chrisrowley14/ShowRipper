<?php
class Sonarr{
	private $Server;
	private $ApiKey;
	function __construct($Server,$ApiKey){
		$this->Server = $Server;
		$this->ApiKey = $ApiKey;
	}
	
	private function GetRequest($URL){ # Makes GET request.
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	public function GetShowArray(){
		$ShowArray = json_decode($this->GetRequest($this->Server . "/api/series?apikey=" . $this->ApiKey),true); # Data was in JSON so need to convert.
		/**
		foreach($ShowArray as $Show){
			$Title = $Show['title'];
			$TotalSeason = $Show['seasonCount'];
			$EpisodesThisSeason = $Show['episodeCount'];
			$Status = $Show['status'];
			$Description = $Show['overview'];
			$LastEpisodeAirDate = $Show['previousAiring'];
			$Network = $Show['network'];
			$PosterImg = "http://192.168.1.70:8989" . $Show['images'][2]['url'];
			$FanArtImg = "http://192.168.1.70:8989" . $Show['images'][0]['url'];
			$AirYear = $Show['year'];
			$ImdbId = $Show['imdbId'];
			$GenresArray = $Show['genres'];
			$SonarrID = $Show['id'];
			
			echo "Title: " . $Title . "<br>";
			echo "TotalSeason: " . $TotalSeason . "<br>";
			echo "EpisodesThisSeason: " . $EpisodesThisSeason . "<br>";
			echo "Status: " . $Status . "<br>";
			echo "Description: " . $Description . "<br>";
			echo "LastEpisodeAirDate: " . $LastEpisodeAirDate . "<br>";
			echo "Network: " . $Network . "<br>";
			echo "PosterImg: " . $PosterImg . "<br>";
			echo "FanArtImg: " . $FanArtImg . "<br>";
			echo "AirYear: " . $AirYear . "<br>";
			echo "ImdbId: <a href='http://www.imdb.com/title/" . $ImdbId . "'>" . $ImdbId . "</a><br>";
			echo "<br>";
			echo "<br>";
			
			
			var_dump($Show);
			exit;
		}
		**/
		return $ShowArray;

	}
	
	public function GetEpisodes($SonarrID){
		$EpisodeArray = json_decode($this->GetRequest($this->Server . "/api/episode?seriesId=" . $SonarrID . "&apikey=" . $this->ApiKey),true);
		# seasonNumber = 0 = Special
		return $EpisodeArray;
	}
}
?>