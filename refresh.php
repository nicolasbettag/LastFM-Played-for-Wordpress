
<?php
$vonseite = "http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=mangorausch&api_key=b3f34d8652bf87d8d1dcbfa5c53d245d";
$response = @simplexml_load_file($vonseite) or die ("Fehler!");

echo "<table>";

foreach ($response->recenttracks->track as $tracks) {

    if ($tracks->name)   	echo $tracks->name . "<br>";
    if ($tracks->artist)    echo $tracks->artist . "<br>";
    if ($tracks->mbid)      echo $tracks->mbid;

echo "<br><br>";
}
?>