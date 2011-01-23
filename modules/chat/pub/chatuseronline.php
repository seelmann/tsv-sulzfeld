<?php
ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes");
include_once("classError.php");
include_once("classDBmysql.php");

$error = new Error();
$db = new DBmysql($error);


// Logout Prüfen
$query = "SELECT nickname, (now()-lastaccess) as diff FROM moduleChatUserOnline WHERE endtime=0 AND lastaccess<now()-30";
$db->executeQuery($query);
while($db->nextRow()) {
  $db2 = new DBmysql($error);
  $query = sprintf("UPDATE moduleChatUserOnline SET endtime=lastaccess WHERE endtime=0 AND nickname='%s'", addslashes($db->getValue("nickname")));
  $db2->executeQuery($query);
  $query = sprintf("INSERT INTO moduleChat values ('', now(), '', '%s hat den Chat verlassen', '#000080')", addslashes($db->getValue("nickname")));
  $db2->executeQuery($query);
}

// User auslesen
$query = "SELECT nickname FROM moduleChatUserOnline WHERE endtime=0";
$db->executeQuery($query);

$refresh = $db->getNumRows() * 5;

?>
<html>
 <head>
  <meta http-equiv="refresh" content="<?php echo $refresh; ?>; URL=chatuseronline.php?nickname=<?php echo $nickname; ?>">
  <link rel="STYLESHEET" type="text/css" href="/css/pub.css"></link>
 </head>
 <body bgcolor="#D0E0E0" text="#000080">
  <p align="left"><u><b><?php echo $db->getNumRows(); ?> User Online:</b></u></p>
<?php

while($db->nextRow()) {
  echo "&nbsp;&nbsp;&nbsp;";
  if( $db->getValue("nickname") == $nickname )
    echo "<b>";
  echo $db->getValue("nickname");
  if( $db->getValue("nickname") == $nickname )
    echo "</b>";
  echo "<br>";
}

?>
 </body>
</html>
