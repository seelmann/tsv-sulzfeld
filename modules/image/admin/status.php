<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classImageInfo.php");
?>
<html>
  <head>
    <title></title>
    <link rel="STYLESHEET" type="text/css" href="/css/admin.css"></link>
  </head>
  <body>
  <h1 align="center">allgemeine Informationen</h1>
<?php
    $imageInfo = new ImageInfo($error, $db);
     printf("    <h3>Anzahl der Bilder: %s</h3>\n", $imageInfo->getNumberOfImages());
     printf("    <h3>Belegter Speicherplatz: %s MB</h3>\n", sprintf("%01.2f", $imageInfo->getUsedMemory()/1024/1024) );
?>
  </body>
</html>
