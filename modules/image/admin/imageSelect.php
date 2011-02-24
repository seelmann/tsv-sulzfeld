<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classImageInfo.php");
?>
<html>
  <head>
    <title></title>
    <link rel="STYLESHEET" type="text/css" href="/css/admin.css"></link>

    <script language="javascript">
      function send()
      {
        // alert(element.value);

        for (i=0; i<document.selectimage.imageID.length; i++)
        {
          // alert(document.selectimage.imageID[i].checked);
          if (document.selectimage.imageID[i].checked)
          {
            element.value = document.selectimage.imageID[i].value;
            // alert(document.selectimage.imageID[i].value);
          }
        }

        // alert(element.value);

        opener.document.save.submit();

        self.close();
      }

      function selectImage(id)
      {
        element.value = id;
        opener.document.save.submit();
        self.close();

      }

    </script>

  </head>
  <body>
    <h1 align="center">Bild auswählen</h1>
    <form name="selectimage">
    <table align="left" cellpadding="5">
      <tr>

<?php
        print ("        <td align=\"center\">\n");
        print ("          <a href=\"javascript:selectImage('0')\">");
        print ("          kein Bild\n");
        print ("          </a>");
        print ("        </td>\n");


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
        printf("          <a href=\"javascript:selectImage('%s')\">", $imageInfo->getID());
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

        print ("          </a>");
        print ("        </td>\n");

    }
?>
      </tr>
    </table>
    </form>
  </body>
</html>
