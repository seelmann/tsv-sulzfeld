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
    <h1 align="center">Katalog als Liste</h1>
    <table align="left" cellpadding="5">
<?php

        print ("      <tr>\n");
        print ("        <td>\n");
        print ("          Nr. \n");
        print ("        </td>\n");
        print ("        <td>\n");
        print ("          Beschreibung\n");
        print ("        </td>\n");
        print ("        <td>\n");
        print ("          Größe\n");
        print ("        </td>\n");
        print ("        <td>\n");
        print ("          Uploaddatum\n");
        print ("        </td>\n");
        print ("      </tr>\n");



    $imageInfo = new ImageInfo($error,$db);
    $imageInfo->createList();
    while($imageInfo->nextImage())
    {
        print ("      <tr>\n");
        print ("        <td>\n");
        printf("          %s\n", $imageInfo->getID());
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          %s\n", $imageInfo->getDescription());
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          %s\n", $imageInfo->getImageSize());
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          %s\n", $imageInfo->getCreateDate());
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          <a href=\"details.php?ID=%s\">Details</a>\n", $imageInfo->getID());
        print ("        </td>\n");
        print ("      </tr>\n");
    }


?>
    </table>
  </body>
</html>
