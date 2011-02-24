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
    <h1 align="center">Katalog mit Bildvorschau</h1>
    <table align="left" cellpadding="5">
      <tr>

<?php

    $imageInfo = new ImageInfo($error,$db);
    $imageInfo->createList();
    $count = 0;
    while($imageInfo->nextImage())
    {
        $count++;
        if($count > 4)
        {
                $count = 1;
                print("</tr><tr>");
        }

        print ("        <td align=\"center\">\n");
        printf("          Bild Nr.: %s<br clear=\"all\">\n", $imageInfo->getID());
        if($imageInfo->getThumbFilename() != "")
        {
            if($imageInfo->getThumbWidth() > 150)
                $divisor = $imageInfo->getThumbWidth() / 150;
            else
                $divisor = 1;
            printf("          <img src=\"/images/%s\" width=\"%s\" height=\"%s\" border=\"0\"></img>\n", $imageInfo->getThumbFilename(), $imageInfo->getThumbWidth()/$divisor, $imageInfo->getThumbHeight()/$divisor);
        }
        else
        {
            if($imageInfo->getImageWidth() > 150)
                $divisor = $imageInfo->getImageWidth() / 150;
            else
                $divisor = 1;

            printf("          <img src=\"/images/%s\" width=\"%s\" height=\"%s\" border=\"0\"></img>\n", $imageInfo->getImageFilename(), $imageInfo->getImageWidth()/$divisor, $imageInfo->getImageHeight()/$divisor);
        }

        printf("          <br clear=\"all\">%s\n", $imageInfo->getDescription());
        printf("          <br clear=\"all\">Größe: %s Bytes\n", $imageInfo->getImageSize());
        printf("          <br clear=\"all\"><a href=\"details.php?ID=%s\">Details</a>\n", $imageInfo->getID());

        print ("        </td>\n");

    }
?>
      </tr>
    </table>
    </form>
  </body>
</html>
