<?php
require_once("./Classes/DB/Main.php");
$DB = new DB();
require_once("./Config.php");

//TODO: Check this is supplied and is an int.
$ShowID = $_GET['Show'];
$ShowArry = $DB->GetShowByID($ShowID);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>1 Col Portfolio - Start Bootstrap Template</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/1-col-portfolio.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body style="background: url(<?php echo $ShowArry[0]['Show_FanArt'];?>);background-color: #272727;background-repeat: repeat-y;;margin-bottom: 100px;">


    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><?php echo $SiteName; ?></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <!--<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#">About</a>
                    </li>
                    <li>
                        <a href="#">Services</a>
                    </li>
                    <li>
                        <a href="#">Contact</a>
                    </li>
                </ul>
            </div>-->
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container panel panel-primary" style="background: rgba(46, 51, 56, 0.7)!important; color:#FFF">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $ShowArry[0]['Show_Name']; ?></h1>
            </div>
        </div>
        <!-- /.row -->
		<div class="row">
            <div class="col-md-2">
                <a href="#">
                    <img class="img-responsive" src="<?php echo $ShowArry[0]['Show_Poster']; ?>" alt="">
                </a>
            </div>
            <div class="col-md-10">
                <p><?php echo $ShowArry[0]['Show_Description']; ?></p>
                <p>Network: <?php echo $ShowArry[0]['Show_Network']; ?></p>
                <p>Air Year: <?php echo $ShowArry[0]['Show_AirYear']; ?></p>
                <p>Genres: <?php echo $ShowArry[0]['Show_Genres']; ?></p>
                <!--//TODO: Check this is set-->
                <p>IMDB: <a href="http://www.imdb.com/title/<?php echo $ShowArry[0]['Show_ImdbId']; ?>">Link</a></p>
            </div>
        </div>
        <hr>
        <div>
          <h2>Downloads</h2>
          <div class="panel-group">
          <div class="panel panel-default" style="background: rgba(46, 51, 56, 0.7)!important; color:#FFF">
          <?php
		    $SeasonsArray = $DB->GetSeasons($ShowArry[0]['Show_Sonarr_ID']);
			$PopUpHTMLTemplate = '
			<div class="modal fade product_view" id="product_view">
                <div class="modal-dialog">
                    <div class="modal-content" style="background-color:#333;">
                        <div class="modal-header">
                            <a href="#" data-dismiss="modal" class="class pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                            <h3 class="modal-title">Episode_Title</h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 product_content">
                                    <p>Episode_Overview</p>
                                </div>
                            </div>
							<div class="btn-ground text-center">
								<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-download"></span> Direct</button>
								<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-magnet"></span> Torrent</button>
							</div>
                        </div>
                    </div>
                </div>
              </div>
			';
		  	$PopUpHTML = "";
			
			foreach($SeasonsArray as $DD){
				if($DD['Episode_SeasonNumber'] == 0){
					$SeasonName = "Specials";
				}else{
					$SeasonName = "Season " . $DD['Episode_SeasonNumber'];	
				}
				echo '
				<div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" href="#collapse' . $DD['Episode_SeasonNumber'] . '">' . $SeasonName . '</a>
                </h4>
              </div>
              <div id="collapse' . $DD['Episode_SeasonNumber'] . '" class="panel-collapse collapse">
                <div class="container panel-body">
				<div class="row-fluid">
                    	<div class="col-lg-1" align="left">#</div>
                        <div class="col-lg-5" align="left">Title</div>
                        <div class="col-lg-3">Air Date</div>
                        <div class="col-lg-3">Status</div>
                    </div>
					
				';
				$EpisodeArray = $DB->GetEpisodes($ShowArry[0]['Show_Sonarr_ID'],$DD['Episode_SeasonNumber']);
				foreach($EpisodeArray as $Episode){
					if(empty($Episode['Episode_HasFile'])){
						$Episode['Episode_HasFile'] = "Not Available";
					}
					echo '
					<a data-toggle="modal" data-target="#product_view_' . $DD['Episode_SeasonNumber'] . $Episode['Episode_EpisodeNumber'] . '"><div class="row-fluid">
                    	<div class="col-lg-1" align="left">' . $Episode['Episode_EpisodeNumber'] . '</div>
                        <div class="col-lg-5" align="left">' . $Episode['Episode_Title'] . '</div>
                        <div class="col-lg-3">' . substr($Episode['Episode_AirDateUtc'],0,10) . '</div>
                        <div class="col-lg-3">' . $Episode['Episode_HasFile'] . '</div>
                    </div></a>
					';
					$LookingForArray = array('Episode_Title','Episode_Overview','id="product_view"');
					$ReplaceArray = array($Episode['Episode_Title'],$Episode['Episode_Overview'],'id="product_view_' . $DD['Episode_SeasonNumber'] . $Episode['Episode_EpisodeNumber'] . '"');
					$PopUpHTML = $PopUpHTML . str_replace($LookingForArray,$ReplaceArray,$PopUpHTMLTemplate);
				}
				echo '
				</div>
                </div>
				';
				echo "<br>";
				echo $PopUpHTML;	
			}
		  ?>
            
            
            
          </div>
        </div>
        <hr>
        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; BlackNightPrograms 2017</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>
<style>
.row-fluid{
     white-space: nowrap;
}
.row-fluid .col-lg-3{
     display: inline-block;
}
</style>
</html>
