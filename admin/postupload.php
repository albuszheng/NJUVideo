<?php

function get_extension($filename){
    return pathinfo($filename, PATHINFO_EXTENSION);
}


$title = $_POST["title"];
$description = $_POST["description"];
$cat = $_POST["cat"];

if($title=="" || $description=="")
{
	die("sorry");
}

/*
$conn = mysql_connect("localhost", "lldev", "lilystudio");
if (!$conn)
{
	die('Could not connect: ' . mysql_error());
}
mysql_query("set character set 'utf8'");
mysql_query("set names 'utf8'");
mysql_select_db("54", $conn);
*/

$dsn = 'mysql:dbname=njuvideo;host=localhost';
$user = 'njuvideo';
$password = 'videoPWD';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->exec("set names 'utf8'");

$stmt = $dbh->prepare("SHOW TABLE STATUS WHERE name='video'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$n = $row["Auto_increment"]; 

$vfile = "";
$vf = "";
$html = $_POST['html'];
if ($html=="") {
	$vfile = $_POST["flash_uploader_0_tmpname"];
	$vf = $n.".".get_extension($vfile);
	$html = NULL;
}

$tfile = $_FILES['thumbnail']['name'];
$tf = $n.".".get_extension($tfile);


$time = date("Y-n-j H:i:s", time());
if (!$html) {
	$stmt = $dbh->prepare('INSERT INTO video(title, description, cat_id, publish_time, video_file, thumbnail_file) VALUES(?, ?, ?, ?, ?, ?)');
	$stmt->execute(array($title, $description, $cat, $time, $vf, $tf));
} else {
	$stmt = $dbh->prepare('INSERT INTO video(title, description, cat_id, publish_time, video_file, thumbnail_file, html) VALUES(?, ?, ?, ?, ?, ?, ?)');
	$stmt->execute(array($title, $description, $cat, $time, $vf, $tf, $html));
}


//echo $sql."\n";
//if(!mysql_query($sql))
//{
//	echo mysql_error();
//}

if (!$html) {
	$dir = "../video/";
	rename($dir.$vfile, $dir.$vf);
}
$dir = "../thumbnail/";
move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir.$tf);

if (!$html) {
	$dir = "../video/";
	if(preg_match("/mp4/i", get_extension($vfile))) {
		system("qtfaststart/bin/qtfaststart -d ".$dir.$vf);
	}
}

die("ok");
