<?php
$conn = mysql_connect("localhost", "njuvideo", "videoPWD");
if (!$conn)
{
    die('Could not connect: ' . mysql_error());
}
mysql_query("set character set 'utf8'");
mysql_query("set names 'utf8'");
mysql_select_db("njuvideo", $conn);