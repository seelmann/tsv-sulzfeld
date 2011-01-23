<?php
ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes");
include_once("classError.php");
include_once("classDBmysql.php");

$error = new Error();
$db = new DBmysql($error);
$ID = $lastID;
$scroll = 0;
$text = "";

if( !isset($lastTimestamp) && !isset($lastRefresh) ) {
  $text .= "<html><head><link rel=\"STYLESHEET\" type=\"text/css\" href=\"/css/pub.css\"></link></head><body bgcolor=\"#D0E0E0\" text=\"#000080\">";
 }

// Letzer Zugriff aktualisieren
$query = sprintf("UPDATE moduleChatUserOnline SET lastaccess=now() WHERE nickname='%s' AND endtime=0", addslashes($nickname));
$db->executeQuery($query);

// Neue Einträge abholen
if(!is_numeric($lastID)) {
  $query = "SELECT * FROM moduleChat WHERE timestamp > now()-60";
}
else {
  $query = "SELECT * FROM moduleChat WHERE ID > $lastID";
}
$db->executeQuery($query);
while($db->nextRow()) {
  $ID = $db->getValue("ID");
  $text .= "<font color=\"" . $db->getValue("color") . "\">";
  if($db->getValue("nickname") == "") {
    $text .= "<b>" . htmlentities(addslashes(($db->getValue("text")))) . "</b><br>";
  }
  else {
    $text .= "<b>" . htmlentities(addslashes(($db->getValue("nickname")))) . ":</b> " . htmlentities(addslashes(($db->getValue("text")))) . "<br>";
  }

  $text .= "</font>";
  $scroll += 100;
}
//echo $text . "<br>";


$timestamp=time();
$refresh = 5;



?>
<html>
 <head>
  <meta http-equiv="refresh" content="<?php echo $refresh; ?>; URL=chatcontroller.php?nickname=<?php echo $nickname ?>&lastID=<?php echo $ID; ?>&lastTimestamp=<?php echo $timestamp; ?>&lastRefresh=<?php echo $refresh; ?>">
  <script type="text/javascript">
  <!--
   var view = parent.frames["view"];
   view.document.write('<?php echo $text; ?>');
   view.scrollBy(0,<?php echo $scroll; ?>);
  //-->
</script>
 </head>
 <body bgcolor="#D0E0E0" text="#000080">
 </body>
</html>
