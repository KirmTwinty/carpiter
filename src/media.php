<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "127.0.0.1";
$username = "root";
$password = "";
$db = "music_library";
$link = mysqli_connect($servername, $username, $password, $db);
if(!$link){
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno" . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error" . mysqli_connect_error() . PHP_EOL;
    exit;
    
}else{
?>
    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Jumpy's Interface</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
  <link rel="stylesheet" type="text/css" href="font/font-awesome-4.6.3/css/font-awesome.min.css" />
  <link rel="stylesheet" type="text/css" media="all" href="css/mediaplayer.css"/>
  <link rel="stylesheet" type="text/css" media="all" href="css/media.css"/>
  <script type="text/javascript" src="js/jQuery.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
  <script type="text/javascript" src="js/mediaelement-and-player.min.js"></script>
</head>
<body>
    <div id="content">
	<div style="display:none;" class="page_info">mediapage</div>
	<ul class="media-menu">
	    <li id="previousbtn" class="media-btn" href="index.html" onclick="action_button(this);">


		<i class="fa fa-home" aria-hidden="true"></i>



	    </li>
	    <li class="media-btn" href="media.php?orderby=artist" onclick="action_button(this);">
		<i class="fa fa-users" aria-hidden="true"></i>
	    </li>
	    <li class="media-btn" href="media.php?orderby=album" onclick="action_button(this);">
		<i class="fa fa-eercast" aria-hidden="true"></i>

	    </li>
	    <li class="media-btn" onclick="search">
		<i class="fa fa-search" aria-hidden="true"></i>

	    </li>
	</ul>

	<ul class="media-content">

	    <li class="header">
		<span class="song-name">Song Name</span>
		<span class="artist">Artist</span>
		<span class="album">Album</span>
		<span class="duration">Duration</span>
	    </li>
	    <?php
	    if (isset($_GET['album']) && isset($_GET['artist']) && isset($_GET['name'])){
		if ($_GET['album'] == 'all'){
		    $sql = "SELECT Songs.SongName, Songs.Duration, Songs.FullPath, Albums.AlbumName FROM Songs
				INNER JOIN SongsData ON SongsData.SongId = Songs.SongId
				INNER JOIN AlbumsData ON SongsData.SongId = AlbumsData.SongId
				INNER JOIN Albums ON Albums.AlbumId = AlbumsData.AlbumId
				WHERE SongsData.ArtistId = '". $_GET['artist']  . "'";
		    
		    $result = mysqli_query($link, $sql);
		    if(mysqli_num_rows($result) > 0){
			$song_index = 0;
			while($row = mysqli_fetch_assoc($result)){
			    $audio_player_title = $_GET['name'] . ' - ' . $row["SongName"];
			    if (strlen($audio_player_title) > 50){
				$audio_player_title = substr($audio_player_title, 0, 50) . '...';
			    }
			    $duration_mn = floor($row["Duration"]/60);
			    $duration_s = floor($row["Duration"] - $duration_mn*60);
			    if ($duration_mn < 10){
				$duration_mn = '0' . $duration_mn;
			    }
			    if ($duration_s < 10){
				$duration_s = '0' . $duration_s;
			    }
			    $duration = $duration_mn . ':' . $duration_s;
			    echo '<li class="song-item" onclick="play(this);">';
			    echo '<span class="song-name">' . $row["SongName"]  . '</span>';
			    echo '<span class="artist">' . $_GET['name'] . '</span>';
			    echo '<span class="album">' . $row["AlbumName"] . '</span>';
			    echo '<span class="duration">' . $duration . '</span>';
			    echo '<span class="fullpath" style="display:none;">' . $row["FullPath"] . '</span>';
			    echo '<span class="title" style="display:none;">' . $audio_player_title . '</span>';
			    echo '<span class="song-index" style="display:none;">' .$song_index . '</span>';
			    echo '</li>';
			    $song_index++;

			}
		    }

		}else{
		    $sql = "SELECT Songs.SongName, Songs.Duration, Songs.FullPath, Albums.AlbumName FROM Songs
				INNER JOIN SongsData ON SongsData.SongId = Songs.SongId
				INNER JOIN AlbumsData ON SongsData.SongId = AlbumsData.SongId
				INNER JOIN Albums ON Albums.AlbumId = AlbumsData.AlbumId
				WHERE SongsData.ArtistId = '". $_GET['artist']  . "' AND AlbumsData.AlbumId = '" . $_GET['album'] . "'";
		    $result = mysqli_query($link, $sql);
		    if(mysqli_num_rows($result) > 0){
			$song_index = 0;
			while($row = mysqli_fetch_assoc($result)){
			    $audio_player_title = $_GET['name'] . ' - ' . $row["SongName"];
			    if (strlen($audio_player_title) > 50){
				$audio_player_title = substr($audio_player_title, 0, 50) . '...';
			    }
			    $duration_mn = floor($row["Duration"]/60);
			    $duration_s = floor($row["Duration"] - $duration_mn*60);
			    if ($duration_mn < 10){
				$duration_mn = '0' . $duration_mn;
			    }
			    if ($duration_s < 10){
				$duration_s = '0' . $duration_s;
			    }
			    $duration = $duration_mn . ':' . $duration_s;
			    echo '<li class="song-item" onclick="play(this);">';
			    echo '<span class="song-name">' . $row["SongName"]  . '</span>';
			    echo '<span class="artist">' . $_GET['name'] . '</span>';
			    echo '<span class="album">' . $row["AlbumName"] . '</span>';
			    echo '<span class="duration">' . $duration . '</span>';
			    echo '<span class="fullpath" style="display:none;">' . $row["FullPath"] . '</span>';
			    echo '<span class="title" style="display:none;">' . $audio_player_title . '</span>';
			    echo '<span class="song-index" style="display:none;">' .$song_index . '</span>';
			    echo '</li>';
			    $song_index++;
			}
		    }   
		}
	    }elseif (isset($_GET['artist']) && isset($_GET['name'])){
		
		$sql = "SELECT Albums.AlbumId, Albums.AlbumName FROM Albums 
			INNER JOIN ArtistsData ON ArtistsData.AlbumId = Albums.AlbumId
			 INNER JOIN Artists ON ArtistsData.ArtistId = '" . $_GET['artist'] . "'
			GROUP BY Albums.AlbumId";
		$result = mysqli_query($link, $sql);
		echo '<li class="song-item" href="media.php?album=all&artist='. $_GET['artist'] . '&name=' . $_GET['name'] . '" onclick="action_button(this);">';
		echo '<span class="song-name">*</span>';
		echo '<span class="artist">' . $_GET['name'] . '</span>';
		echo '<span class="album">ALL SONGS</span>';
		echo '<span class="duration">*</span>';
		echo '</li>';
		if(mysqli_num_rows($result) > 0){
		    while($row = mysqli_fetch_assoc($result)){
			echo '<li class="song-item" href="media.php?album='. $row["AlbumId"] . '&artist='. $_GET['artist'] . '&name=' . $_GET['name'] . '" onclick="action_button(this);">';
			echo '<span class="song-name">*</span>';
			echo '<span class="artist">' . $_GET['name'] . '</span>';
			echo '<span class="album">' . $row["AlbumName"] . '</span>';
			echo '<span class="duration">*</span>';
			echo '</li>';

		    }
		}
	    }elseif (isset($_GET['album'])){
		$sql = "SELECT Songs.SongName, Songs.Duration, Songs.FullPath, Albums.AlbumName, Artists.ArtistName FROM Songs
				INNER JOIN SongsData ON SongsData.SongId = Songs.SongId
				INNER JOIN AlbumsData ON SongsData.SongId = AlbumsData.SongId
				INNER JOIN Albums ON Albums.AlbumId = AlbumsData.AlbumId
				INNER JOIN Artists ON SongsData.ArtistId = Artists.ArtistId
				WHERE AlbumsData.AlbumId = '" . $_GET['album'] .  "'
				ORDER BY Artists.ArtistName";

		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result) > 0){
		    $song_index = 0;
		    while($row = mysqli_fetch_assoc($result)){
			$audio_player_title = $row["ArtistName"] . ' - ' . $row["SongName"];
			if (strlen($audio_player_title) > 50){
			    $audio_player_title = substr($audio_player_title, 0, 50) . '...';
			}
			$duration_mn = floor($row["Duration"]/60);
			$duration_s = floor($row["Duration"] - $duration_mn*60);
			if ($duration_mn < 10){
			    $duration_mn = '0' . $duration_mn;
			}
			if ($duration_s < 10){
			    $duration_s = '0' . $duration_s;
			}
			$duration = $duration_mn . ':' . $duration_s;
			echo '<li class="song-item" onclick="play(this);">';
			echo '<span class="song-name">' . $row["SongName"]  . '</span>';
			echo '<span class="artist">' . $row["ArtistName"] . '</span>';
			echo '<span class="album">' . $row["AlbumName"] . '</span>';
			echo '<span class="duration">' . $duration . '</span>';
			echo '<span class="fullpath" style="display:none;">' . $row["FullPath"] . '</span>';
			echo '<span class="title" style="display:none;">' . $audio_player_title . '</span>';
			echo '<span class="song-index" style="display:none;">' .$song_index . '</span>';
			echo '</li>';
			$song_index++;

		    }
		}
	    }elseif (isset($_GET['orderby'])){
		if (strcmp($_GET['orderby'], 'album') == 0){
		    $sql = "SELECT AlbumId, AlbumName FROM Albums ORDER BY AlbumName";
		    $result = mysqli_query($link, $sql);
		    if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){			    
			    echo '<li class="song-item" href="media.php?album='. $row["AlbumId"] . '" onclick="action_button(this);">';
			    echo '<span class="song-name">*</span>';
			    echo '<span class="artist">*</span>';
			    echo '<span class="album">' . $row["AlbumName"] . '</span>';
			    echo '<span class="duration">*</span>';
			    echo '</li>';

			}
		    }
		}else{ // By default, orderby artist
		    $sql = "SELECT ArtistId, ArtistName FROM Artists ORDER BY ArtistName";
		    $result = mysqli_query($link, $sql);
		    if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
			    echo '<li class="song-item" href="media.php?artist='. $row["ArtistId"] . '&name=' . $row["ArtistName"] . '" onclick="action_button(this);">';
			    echo '<span class="song-name">*</span>';
			    echo '<span class="artist">' . $row["ArtistName"] . '</span>';
			    echo '<span class="album">*</span>';
			    echo '<span class="duration">*</span>';
			    echo '</li>';

			}
		    }

		}

	    }else{
		
		$sql = "SELECT Songs.FullPath, Songs.Duration, Songs.SongName, Albums.AlbumName, Artists.ArtistName
			FROM Songs
			INNER JOIN AlbumsData ON AlbumsData.SongId = Songs.SongId
			INNER JOIN Albums ON Albums.AlbumId = AlbumsData.AlbumId
			INNER JOIN ArtistsData ON AlbumsData.AlbumId = ArtistsData.AlbumId
			INNER JOIN Artists ON Artists.ArtistId = ArtistsData.ArtistId";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result) > 0){
		    $song_index = 0;
		    while($row = mysqli_fetch_assoc($result)){
			$audio_player_title = $row["ArtistName"] . ' - ' . $row["SongName"];
			if (strlen($audio_player_title) > 50){
			    $audio_player_title = substr($audio_player_title, 0, 50) . '...';
			}
			$duration_mn = floor($row["Duration"]/60);
			$duration_s = floor($row["Duration"] - $duration_mn*60);
			if ($duration_mn < 10){
			    $duration_mn = '0' . $duration_mn;
			}
			if ($duration_s < 10){
			    $duration_s = '0' . $duration_s;
			}
			$duration = $duration_mn . ':' . $duration_s;
			
			echo '<li class="song-item" onclick="play(this);">';
			echo '<span class="song-name">' . $row["SongName"]. '</span>';
			echo '<span class="artist">' . $row["ArtistName"] . '</span>';
			echo '<span class="album">' . $row["AlbumName"] . '</span>';
			echo '<span class="duration">' . $duration . '</span>';
			echo '<span class="fullpath" style="display:none;">' . $row["FullPath"] . '</span>';
			echo '<span class="title" style="display:none;">' . $audio_player_title . '</span>';
			echo '<span class="song-index" style="display:none;">' .$song_index . '</span>';
			echo '</li>';
			$song_index++;
		    }
		}

	    }	    
	    mysqli_close($link);
	    
	    ?>
	  </ul>
	</div>
	
</body>

</html>
<?php
}
?>
