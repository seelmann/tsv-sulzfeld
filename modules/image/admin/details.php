<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classImageInfo.php");
?>
<html>
  <head>
    <title>Details</title>
    <link rel="STYLESHEET" type="text/css" href="/css/admin.css"></link>
  </head>
  <body>
<?php
    $type = array();
    $type[1] = "GIF";
    $type[2] = "JPG";
    $type[3] = "PNG";
    $type[4] = "SWF";

    $imageInfo = new ImageInfo($error,$db);
    $imageInfo->createDetails($ID);

    printf("    <h1 align=\"center\">Detailansicht Bild Nr. %s</h1>\n", $imageInfo->getID());
    print ("    <table align=\"center\" cellpadding=\"5\">\n");
    print ("      <tr>\n");
    print ("        <td>Beschreibung:</td>\n");
    printf("        <td>%s</td>\n", $imageInfo->getDescription());
    print ("      </tr>\n");
    print ("      <tr>\n");
    print ("        <td>Bildgröße:</td>\n");
    printf("        <td>%s Bytes</td>\n", $imageInfo->getImageSize());
    print ("      </tr>\n");
    print ("      <tr>\n");
    print ("        <td>Bildhöhe:</td>\n");
    printf("        <td>%s Pixel</td>\n", $imageInfo->getImageHeight());
    print ("      </tr>\n");
    print ("      <tr>\n");
    print ("        <td>Bildbreite:</td>\n");
    printf("        <td>%s Pixel</td>\n", $imageInfo->getImageWidth());
    print ("      </tr>\n");
    print ("      <tr>\n");
    print ("        <td>Bildtyp:</td>\n");
    printf("        <td>%s</td>\n", $type[$imageInfo->getImageType()]);
    print ("      </tr>\n");

    if($imageInfo->getThumbFilename() != "")
    {
        print ("      <tr>\n");
        print ("        <td>Thumbnailgröße:</td>\n");
        printf("        <td>%s Bytes</td>\n", $imageInfo->getThumbSize());
        print ("      </tr>\n");
        print ("      <tr>\n");
        print ("        <td>Thumbnailhöhe:</td>\n");
        printf("        <td>%s Pixel</td>\n", $imageInfo->getThumbHeight());
        print ("      </tr>\n");
        print ("      <tr>\n");
        print ("        <td>Thumbnailbreite:</td>\n");
        printf("        <td>%s Pixel</td>\n", $imageInfo->getThumbWidth());
        print ("      </tr>\n");
        print ("      <tr>\n");
        print ("        <td>Thumbnailtyp:</td>\n");
        printf("        <td>%s</td>\n", $type[$imageInfo->getThumbType()]);
        print ("      </tr>\n");
        print ("      <tr>\n");
        print ("        <td colspan=\"2\" align=\"center\">");
        printf("           <a href=\"javascript:void(open('/images/%s', 'largeimage', 'width=%s,height=%s'))\"><img src=\"/images/%s\" width=\"%s\" height=\"%s\" border=\"0\"></img></a>\n", $imageInfo->getImageFilename(), $imageInfo->getImageWidth(), $imageInfo->getImageHeight(), $imageInfo->getThumbFilename(), $imageInfo->getThumbWidth(), $imageInfo->getThumbHeight());
        print ("        </td>");
        print ("      </tr>\n");

    }
    else if( ($imageInfo->getImageType() == 1) || ($imageInfo->getImageType() == 2) || ($imageInfo->getImageType() == 3) )
    {
        print ("      <tr>\n");
        print ("        <td colspan=\"2\" align=\"center\">");
        printf("           <a href=\"javascript:open('/images/%s', 'largeimage', 'width=%s,height=%s')\"><img src=\"/images/%s\" width=\"%s\" height=\"%s\" border=\"0\"></img></a>\n", $imageInfo->getImageFilename(), $imageInfo->getImageWidth(), $imageInfo->getImageHeight(), $imageInfo->getImageFilename(), $imageInfo->getImageWidth(), $imageInfo->getImageHeight());
        print ("        </td>");
        print ("      </tr>\n");
        print ("      <tr>\n");
        printf("        <td colspan=\"2\">Kein Thumbnail zu diesem Bild vorhanden. <a href=\"upload.php?go=details&id=%s&type=%s\">Thumbnail uploaden?</a></td>\n", $imageInfo->getID(), $imageInfo->getImageType());
        print ("      </tr>\n");
    }



    print ("    </table>\n");


?>
  </body>
</html>
