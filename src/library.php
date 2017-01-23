<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// include getID3() library (can be in a different directory if full path is specified)
require_once('./getid3/getid3.php');

// Pseudo code pour l'instant 
function rec_scan($dir){ 
    $count = 0;
    $files = scandir($dir);
    $library = array();
    for ($i=0 ; $i < sizeof($files) ; $i++){
	if(strcmp($files[$i], "..") != 0 && strcmp($files[$i], ".") != 0){
	    if(is_dir($dir . '/' . $files[$i])){
		$library_tmp = rec_scan($dir . '/' . $files[$i]);
		for($j=0; $j<sizeof($library_tmp); $j++){
		    array_push($library, $library_tmp[$j]);
		}
	    }else{
		$file_parts = pathinfo($files[$i]);
		switch($file_parts['extension'])
		{
		    case "mp3":
		    array_push($library, $dir . '/' . $files[$i]);
		    break;
		}
	    }
	}
    }
    return $library;
}
function clean_table($link, $table){
    $sql = 'DELETE FROM '. $table;
    if(!mysqli_query($link, $sql)){
	echo "Error: " . $sql . "</br>" . mysqli_error($link);
    }
    $sql = 'ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1';
    if(!mysqli_query($link, $sql)){
	echo "Error: " . $sql . "</br>" . mysqli_error($link);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Jumpy's library</title>
	<style>
	 * {
	     margin: 0;
	     padding: 0;
	 }
	 #library {
	     font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	     border-collapse: collapse;
	     width: 100%;
	 }

	 #library td, #library th {
	     border: 1px solid #ddd;
	     padding: 8px;
	 }

	 #library tr:nth-child(even){background-color: #f2f2f2;}

	 #library tr:hover {background-color: #ddd;}

	 #library th {
	     padding-top: 12px;
	     padding-bottom: 12px;
	     text-align: left;
	     background-color: #4CAF50;
	     color: white;
	 }
	 h1{
	     color: #222;
	     font: 30px/1 Helvetica, Verdana, sans-serif;
	     text-transform: uppercase;
	     margin:15px;
	 }

	 
	 nav {
	     margin: 50px;
	 }
	 
	 ul {
	     overflow: auto;
	     list-style-type: none;
	 }
	 
	 li {
	     height: 25px;
	     float: left;
	     margin-right: 0px;
	     border-right: 1px solid #aaa;
	     padding: 0 20px;
	 }
	 
	 li:last-child {
	     border-right: none;
	 }
	 
	 li a {
	     text-decoration: none;
	     color: #ccc;
	     font: 16px/1 Helvetica, Verdana, sans-serif;
	     text-transform: uppercase;
	     
	     -webkit-transition: all 0.5s ease;
	     -moz-transition: all 0.5s ease;
	     -o-transition: all 0.5s ease;
	     -ms-transition: all 0.5s ease;
	     transition: all 0.5s ease;
	 }
	 
	 li a:hover {
	     color: #666;
	 }
	 
	 li.active a {
	     font-weight: bold;
	     color: #333;
	 }
	</style>
    </head>
    <body>

	<?php
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

	    // We start by cleaning the database
	    clean_table($link, "AlbumsData");	    
	    clean_table($link, "ArtistsData");
	    clean_table($link, "Albums");
	    clean_table($link, "Artists");
	    clean_table($link, "Songs");
	     
	    $lib_path = "./media";
	    $lib = rec_scan($lib_path);
	    // Initialize getID3 engine
	    $getID3 = new getID3;
	    $numberNewArtists = 0;
	    $numberNewAlbums = 0;
	    $numberNewSongs = 0;
	    $numberAlreadyExistsArtists = 0;
	    $numberAlreadyExistsAlbums = 0;
	    $numberAlreadyExistsSongs = 0;

	    for($i=0; $i < sizeof($lib); $i++){
		// We want to process every file: find the tags info and store it into the DB
		// get_tags($lib_path . $lib[$i]);
		
		$song_path = $lib[$i];
		// Analyze file and store returned data in $ThisFileInfo
		$ThisFileInfo = $getID3->analyze($song_path);		
		/*
		   Optional: copies data from all subarrays of [tags] into [comments] so
		   metadata is all available in one location for all tag formats
		   metainformation is always available under [tags] even if this is not called
		 */
		getid3_lib::CopyTagsToComments($ThisFileInfo);
		//	    print_r($ThisFileInfo);
		$song_path = mysqli_real_escape_string($link, $song_path);            // song path
		$artist = mysqli_real_escape_string($link, $ThisFileInfo['comments_html']['artist'][0]); // artist from any/all available tag formats
		$album = mysqli_real_escape_string($link, $ThisFileInfo['comments_html']['album'][0]);
		$title = mysqli_real_escape_string($link, $ThisFileInfo['tags']['id3v2']['title'][0]);  // title from ID3v2
		$track_number = mysqli_real_escape_string($link, $ThisFileInfo['tags']['id3v2']['track_number'][0]); 
		$duration = mysqli_real_escape_string($link, $ThisFileInfo['playtime_seconds']);            // playtime
		
		// We look if the artist already exists
		// First, we have to make sure that there is only one artist or if multiple ones, we have to split
		$artist_list = preg_split('/ (,|\/) /', $artist);
		foreach($artist_list as &$artistName){
		    // Add the new artist(s) if not created yet.
		    $sql = "SELECT * FROM Artists WHERE Artists.ArtistName = '" . $artistName  .  "'";
		    if($result = mysqli_query($link, $sql)){
			// We can insert the artist
			if (mysqli_num_rows($result) == 0){
			    $sql = "INSERT INTO Artists (ArtistName) 
					VALUES ('" . $artistName  . "')";
			    if($result = mysqli_query($link, $sql)){
				$numberNewArtists++;
			    }else{
				echo "Error: " . $sql . "</br>" . mysqli_error($link);
			    }
			}else{
			    $numberAlreadyExistsArtists++;
			}
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    // Now we add the album 
		    $sql = "SELECT * FROM Albums WHERE AlbumName = '" . $album  .  "'";
		    if($result = mysqli_query($link, $sql)){
			// We can insert the album
			if (mysqli_num_rows($result) == 0){
			    $sql = "INSERT INTO Albums (AlbumName) 
					VALUES ('" . $album  . "')";
			    if($result = mysqli_query($link, $sql)){
				$numberNewAlbums++;
			    }else{
				echo "Error: " . $sql . "</br>" . mysqli_error($link);
			    }
			}else{
			    $numberAlreadyExistsAlbums++;
			}
		    }else{
			echo "	Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    // Finally we add the Song
		    $sql = "SELECT * FROM Songs WHERE SongName = '" . $title . "' AND Duration = '" . $duration . "'"; 
		    if($result = mysqli_query($link, $sql)){
			// We can insert the song
			if (mysqli_num_rows($result) == 0){
			    $sql = "INSERT INTO Songs (SongName, TrackNumber, Duration, FullPath)
				   VALUES ('" . $title . "', '" . $track_number . "', '" . $duration . "', '" . $song_path  . "')";
			    if($result = mysqli_query($link, $sql)){
				$numberNewSongs++;
			    }else{
				echo "Error: " . $sql . "</br>" . mysqli_error($link);
			    }
   
			}else{
			    $numberAlreadyExistsSongs++;
			}
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    // Now we update the links
		    // We want the ids of the new entries
		    $artistId = -1;
		    $sql = "SELECT ArtistId FROM Artists WHERE ArtistName = '" . $artistName  . "'";
		    if($result = mysqli_query($link, $sql)){
			$row = mysqli_fetch_array($result, MYSQLI_NUM);
			$artistId = $row[0];
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    $sql = "SELECT Albums.AlbumId FROM Albums WHERE Albums.AlbumName = '" . $album  . "'";
		    if($result = mysqli_query($link, $sql)){
			$row = mysqli_fetch_array($result);
			$albumId = $row[0];			
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    $sql = "SELECT SongId FROM Songs WHERE SongName = '" . $title  . "' AND Duration = '" . $duration  . "'";
		    
		    if($result = mysqli_query($link, $sql)){
			$row = mysqli_fetch_array($result);
			$songId = $row[0];			
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    // We create the links
		    $sql = "SELECT * FROM ArtistsData 
				WHERE ArtistId = '" . $artistId . "' AND AlbumId = '" . $albumId ."'";
		    if($result = mysqli_query($link, $sql)){
			// We can insert the new link
			if (mysqli_num_rows($result) == 0){
			    $sql = "INSERT INTO ArtistsData (ArtistId, AlbumId)
				VALUES ('" . $artistId  . "', '" . $albumId  . "')";
			    
			    if(!mysqli_query($link, $sql)){
				echo "Error: " . $sql . "</br>" . mysqli_error($link);
			    }
			    
			}
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }
		    $sql = "SELECT * FROM AlbumsData
				WHERE AlbumId = '" . $albumId . "' AND SongId = '" . $songId ."'";
		    if($result = mysqli_query($link, $sql)){
			// We can insert the new link
			if (mysqli_num_rows($result) == 0){
			    $sql = "INSERT INTO AlbumsData (AlbumId, SongId)
				VALUES ('" . $albumId  . "', '" . $songId  . "')";
			    
			    if(!mysqli_query($link, $sql)){
				echo "Error: " . $sql . "</br>" . mysqli_error($link);
			    }
			    
			}
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }
		    
		    $sql = "SELECT * FROM SongsData
				WHERE ArtistId = '" . $artistId . "' AND SongId = '" . $songId ."'";
		    if($result = mysqli_query($link, $sql)){
			// We can insert the new link
			if (mysqli_num_rows($result) == 0){
			    $sql = "INSERT INTO SongsData (ArtistId, SongId)
				VALUES ('" . $artistId  . "', '" . $songId  . "')";
			    
			    if(!mysqli_query($link, $sql)){
				echo "Error: " . $sql . "</br>" . mysqli_error($link);
			    }
			    
			}
		    }else{
			echo "Error: " . $sql . "</br>" . mysqli_error($link);
		    }

		    

//		    $sql = "SELECT * FROM ArtistsData WHERE ArtistId = "
		}
		   // echo $artist_list[$i] . '</br>';
		    /* 

		       // Now we add the album and we create the link
		       $sql = "INSERT INTO Albums (AlbumName) VALUES ('" . $album  . "') 
		       IF NOT EXISTS (SELECT Albums.AlbumName FROM Albums WHERE Albums.AlbumName = '" . $album . "')";
		       $result = mysqli_query($link, $sql);

		       // We retrieve the ids
		       $sql = "SELECT Artists.ArtistId, Albums.AlbumId
 		       FROM Artists, Albums
		       LEFT JOIN Artists.ArtistName ON Artists.ArtistName = '" . $artist_list[$i]. "'
		       LEFT JOIN Albums.AlbumName ON Albums.AlbumName = '" . $album . "'";
		       $ids = mysqli_query($link, $sql);

		       // We create the link beween the artist and the album
		       $sql = "INSERT INTO ArtistsData (ArtistId, AlbumId)
		       VALUES ('" . $ids[0] . "', '" . $ids[1] . "')
		       IF NOT EXISTS (SELECT * FROM ArtistsData 
		       WHERE ArtistId = '" . $ids[0] . "' 
		       AND AlbumId = '" . $ids[1] . "'";		    
		       $result = mysqli_query($link, $sql);
		
		// We do the same for the songs now
		/* $sql = "INSERT INTO Songs (SongName, TrackNumber, Duration)
		   VALUES ('" . $title . "', '" . $track_number . "', '" . $duration . "')";
		   $result = mysqli_query($link, $sql);*/

		/* And we create the link
		   $sql = "SELECT SongId FROM Songs WHERE SongName = '" . $title . "'";
		   $song_id = mysqli_query($link, $sql);
		   $row = mysqli_fetch_assoc($result);
		   $sql = "INSERT INTO AlbumsData (AlbumId, SongId) 
		   VALUES ('" . $ids[1] . "', '" . $row["SongId"] . "')";
		   $result = mysqli_query($link, $sql);*/
		//}
	    }
	    


	    
	    
	    /* if empty($artist)
	       //We look for the corresponding artist 
	       $sql = "SELECT id FROM Artists WHERE name=" . $artist;
	       // We look for the corresponding album
	       $sql = "SELECT id FROM Albums WHERE name=" . $album;
	       
	       // Perform SQL operations
	       $sql = "INSERT INTO Songs VALUES ('', '". $title ."', '". $number  ."', '". $album_id ."', '". $artist_id ."')";*/

	    // Show the result of the library process
	    echo '<h1>Library Reset</h1>';
	    echo '<nav><ul>';
	    echo '<li><a>' . $numberNewArtists . ' new Artist(s) created.</a></li>';
	    echo '<li><a>' . $numberNewAlbums . ' new Album(s) created.</a></li>';
	    echo '<li><a>' . $numberNewSongs . ' new Song(s) created.</a></li>';
	    echo '<li><a>' . $numberAlreadyExistsArtists . ' Artist(s) were already existing.</a></li>';
	    echo '<li><a>' . $numberAlreadyExistsAlbums . ' Album(s) were already existing.</a></li>';
	    echo '<li><a>' . $numberAlreadyExistsSongs . ' Song(s) were already existing.</a></li>';
	    echo '</ul></nav>';
	    /* We get all the songs from an artist */
	    $sql = "SELECT Songs.SongId, Songs.SongName, Albums.AlbumName, Artists.ArtistName
			FROM Songs
			INNER JOIN AlbumsData ON AlbumsData.SongId = Songs.SongId
			INNER JOIN Albums ON Albums.AlbumId = AlbumsData.AlbumId
			INNER JOIN ArtistsData ON AlbumsData.AlbumId = ArtistsData.AlbumId
			INNER JOIN Artists ON Artists.ArtistId = ArtistsData.ArtistId";
	    $result = mysqli_query($link, $sql);

	    if(mysqli_num_rows($result) > 0){
		// Output the result
		echo '<h1>Library Status</h1>';
		echo '<table id="library">';
		echo '<tr>';
		echo '<th>Song Id</th>';		
		echo '<th>Artist Name</th>';
		echo '<th>Album Name</th>';
		echo '<th>Song Name</th>';
		echo '</tr>';
		while($row = mysqli_fetch_assoc($result)){
		    echo '<tr>';
		    echo '<td>' . $row["SongId"] . '</td>';
		    echo '<td>' . $row["ArtistName"] . '</td>';
		    echo '<td>' . $row["AlbumName"] . '</td>';
		    echo '<td>' . $row["SongName"] . '</td>';
		    echo '</tr>';
		}
		echo '</table>';
	    }
	    mysqli_close($link);
	}


	    
	    
	



	

	/* 
		if(isset($_POST['update']) && isset($_POST['directory'])){ // If we want to update the DB
		echo "Please wait, updating the database...";
		$files = scandir($_POST['directory']); // Get the array of files and directory
		// We should do it recursively
		// Process the filenames to update the database and retrieve the information
		


		}else if(isset($_POST['request'])){ // We want to display stuff from the DB

		}*/


	
	?>
	
</body>
</html>
