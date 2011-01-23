<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
?>
<html>
  <head>
    <title>Spieleverwaltung</title>
  </head>
  <frameset border="5" cols="40%,60%">
    <frame name="menu" src="othermatchList.php">
    <frame name="edit" src="empty.php">
  </frameset>
</html>
