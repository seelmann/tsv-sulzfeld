<?php
ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes");
include_once("classError.php");
include_once("classDBmysql.php");

if(empty($nickname)) {
    printLoginform();
    exit();
}

$error = new Error();
$db = new DBmysql($error);

// Logout Prüfen
$query = "SELECT nickname, (now()-lastaccess) as diff FROM moduleChatUserOnline WHERE endtime=0 AND lastaccess<now()-30";
$db->executeQuery($query);
while($db->nextRow()) {
  $db2 = new DBmysql($error);
  $query = sprintf("UPDATE moduleChatUserOnline SET endtime=lastaccess WHERE endtime=0 AND nickname='%s'", addslashes($db->getValue("nickname")));
  $db2->executeQuery($query);
  if($db->getValue("diff") < 60) {
    $query = sprintf("INSERT INTO moduleChat values ('', now(), '', '%s hat den Chat verlassen', '#000080')", addslashes($db->getValue("nickname")));
    $db2->executeQuery($query);
  }
}

// Anzahl der Chatter prüfen
$query = "SELECT count(*) as num FROM moduleChatUserOnline WHERE endtime=0";
$db->executeQuery($query);
if($db->nextRow()) {
  if($db->getValue("num") >=10 ) {
    printLoginform("Zur Zeit sind schon 10 Chatter Online. Diese Begrenzung ist aus Gründen der Netz- und Serverbelastung notwendig. Bitte später nochmal versuchen.");
    exit();
  }
}

// Prüfen, ob Nickname schon vorhanden ist
$query = sprintf("SELECT * FROM moduleChatUserOnline WHERE nickname='%s' AND endtime=0", addslashes($nickname));
$db->executeQuery($query);
if($db->nextRow()) {
    printLoginform("Der Nickname $nickname ist bereits vergeben.");
    exit();
}

// Nickname in UserOnline Tabelle eintragen
$query = sprintf("INSERT INTO moduleChatUserOnline values (now(), now(), '', '%s')", addslashes($nickname));
$db->executeQuery($query);

// Begrüßungsmeldung in Chat Tabelle eintragen
$query = sprintf("INSERT INTO moduleChat values ('', now(), '', '%s betritt den Chat', '#000080')", addslashes($nickname));
$db->executeQuery($query);

printChatframe($nickname);
exit();



function printLoginform($errormessage="")
{
?>
<html>
 <head>
  <title>TSV Sulzfeld - Chat</title>
  <link rel="STYLESHEET" type="text/css" href="/css/pub.css"></link>
 </head>
 <body bgcolor="#D0E0E0" text="#000080" onload="document.loginform.nickname.focus();">
  <h1 align="center">Login</h1>
<?php
    if(!empty($errormessage)) {
        echo "<p /><p align=\"center\"><b>" . $errormessage . "</b></p";
    }
?>
  <p />
  <p align="center">Bitte zum einloggen einen Nicknamen eingeben.</p>
  <p />
  <form name="loginform" action="chatstart.php" method="get">
   <p align="center">
   Nickname:
   <input type="text" name="nickname" size="10" maxlength="10"></input>
   <input type="submit" value="los geht's"></input>
   </p>
  </form>
 </body>
</html>
<?php
}


function printChatframe($nickname)
{
?>
<html>
 <head>
  <title>TSV Sulzfeld - Chat</title>
  <link rel="STYLESHEET" type="text/css" href="/css/pub.css"></link>
 </head>
 <frameset rows="0%,10%,75%,15%" border="0">
  <frame name="controller" scrolling="no" src="chatcontroller.php?nickname=<?php echo $nickname ?>">
  <frame name="header" scrolling="no" src="chatheader.php">
  <frameset cols="80%,20%" border="0">
   <frame name="view">
   <frame name="useronline" src="chatuseronline.php?nickname=<?php echo $nickname ?>">
  </frameset>
  <frame name="input" scrollint="no" src="chatinput.php?nickname=<?php echo $nickname ?>">
 </frameset>
 <noframes>Der TSV-Chat funktioniert leider nur mit Frames.</noframes>
</html>
<?php
}
