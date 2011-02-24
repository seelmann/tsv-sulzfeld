<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classImageUpload.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if( $permcheck->hasUserImageUploadPermission() == false )
    {
        $error->printAccessDenied();
        exit;
    }
?>
<html>
  <head>
    <title></title>
    <link rel="STYLESHEET" type="text/css" href="/css/admin.css"></link>
  </head>
  <body>
<?php

    if(isset($go))
    {
        $imageUpload = new ImageUpload($error, $db);

        if($go == "puif") // upload des Bildes
        {
            if( isset($file) && ( $file != "") && file_exists($file) )
            {
                $imageUpload->create($description);
                $imageUpload->uploadImage($file, $file_size);

                switch($imageUpload->getImageType())
                {
                    case(1): // gif
                        printUploadGifThumbForm($imageUpload->getID());
                        break;
                    case(2): // jpg
                        printUploadJpgThumbForm($imageUpload->getID());
                        break;
                    case(3): // png
                        printUploadPngThumbForm($imageUpload->getID());
                        break;
                    case(4):
                        $imageUpload->endOfUpload($id);
                        printFinished("Bild wurde übertragen.");
                        break;
                    default:
                        $imageUpload->endOfUpload($id);
                        printFinished("Fehler beim Upload.");
                        break;
                }
            }
        }
        else if($go == "putf") // upload des Thumb
        {
            if( isset($file) && ( $file != "") && file_exists($file) )
            {
                $imageUpload->uploadThumb($file, $file_size, $id);
                $imageUpload->endOfUpload($id);
                printFinished("Thumbnail wurde übertragen.");
            }
        }
        else if($go == "pugtf") // soll Thumb für gif hochgeladen werden?
        {
            if(isset($do) && ($do == "yes"))
            {
                printUploadThumbForm($id);
            }
            else
            {
                $imageUpload->endOfUpload($id);
                printFinished("Bild wurde übertragen.");
            }
        }
        else if($go == "pujtf") // soll Thumb für jpg hochgeladen werden?
        {
            if(isset($do) && ($do == "yes"))
            {
                printUploadThumbForm($id);
            }
            else
            {
                $imageUpload->endOfUpload($id);
                printFinished("Bild wurde übertragen.");
            }
        }
        else if($go == "puptf") // soll Thumb für png hochgeladen werden?
        {
            if(isset($do) && ($do == "yes"))
            {
                printUploadThumbForm($id);
            }
            else if(isset($do) && ($do == "auto"))
            {
                $imageUpload->createThumb($id);
                $imageUpload->endOfUpload($id);
                printFinished("Bild wurde übertragen und Thumbnail erzeugt.");
            }
            else
            {
                $imageUpload->endOfUpload($id);
                printFinished("Bild wurde übertragen.");
            }
        }
        else if($go == "details") // vom details.php
        {
            $imageUpload->setID($id);
            switch($type)
            {
                case(1): // gif
                    printUploadGifThumbForm($id);
                    break;
                case(2): // jpg
                    printUploadJpgThumbForm($id);
                    break;
                case(3): // png
                    printUploadPngThumbForm($id);
                    break;
                default:
                    $imageUpload->endOfUpload($id);
                    printFinished("Fehler beim Upload.");
                    break;
            }
        }
    }
    else
    {
        printUploadImageForm();
    }



    function printUploadImageForm()
    {
        global $PHP_SELF;
        print ("    <h3 align=\"center\">Bild hochladen</h3>\n");
        printf("    <form method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">\n", $PHP_SELF);
        print ("    <input type=\"hidden\" name=\"go\" value=\"puif\"></input>\n");
        print ("      <table border=\"0\" align=\"center\" cellpadding=\"5\">\n");
        print ("        <tr>\n");
        print ("          <td>Bild: </td>\n");
        print ("          <td><input size=\"30\" type=\"file\" name=\"file\"></input></td>\n");
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td>Beschreibung des Bildes: </td>\n");
        print ("          <td><input size=\"30\" type=\"text\" name=\"description\"></input></td>\n");
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td colspan=\"2\"><input type=\"submit\" value=\"Upload starten\"></input></td>\n");
        print ("        </tr>\n");
        print ("      </table>\n");
        print ("    </form>\n");
    }

    function printUploadGifThumbForm($ID)
    {
        global $PHP_SELF;
        print ("    <h3 align=\"center\">Das hochgeladene Bild war im GIF-Format. Wollen Sie noch ein Thumbnail hochladen?</h3>\n");
        print ("    <table border=\"0\" align=\"center\" cellpadding=\"5\">\n");
        print ("      <tr>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"pugtf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"yes\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"ja\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"pugtf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"no\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"nein\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        </tr>\n");
        print ("      <tr>\n");
        print ("    </table>\n");
    }

    function printUploadJpgThumbForm($ID)
    {
        global $PHP_SELF;
        print ("    <h3 align=\"center\">Das hochgeladene Bild war im JPG-Format. Wollen Sie noch ein Thumbnail hochladen?</h3>\n");
        print ("    <table border=\"0\" align=\"center\" cellpadding=\"5\">\n");
        print ("      <tr>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"pujtf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"yes\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"ja\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"pujtf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"no\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"nein\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        </tr>\n");
        print ("      <tr>\n");
        print ("    </table>\n");
    }

    function printUploadPngThumbForm($ID)
    {
        global $PHP_SELF;
        print ("    <h3 align=\"center\">Das hochgeladene Bild war im PNG-Format. Wollen Sie noch ein Thumbnail hochladen oder automatisch erstellen lassen?</h3>\n");
        print ("    <table border=\"0\" align=\"center\" cellpadding=\"5\">\n");
        print ("      <tr>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"puptf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"auto\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"automatisch erstellen\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"puptf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"yes\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"ja\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        <td>\n");
        printf("          <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("            <input type=\"hidden\" name=\"go\" value=\"puptf\"></input>\n");
        print ("            <input type=\"hidden\" name=\"do\" value=\"no\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("            <input type=\"submit\" value=\"nein\"></input>\n");
        print ("          </form>\n");
        print ("        </td>\n");
        print ("        </tr>\n");
        print ("      <tr>\n");
        print ("    </table>\n");
    }



    function printUploadThumbForm($ID)
    {
        global $PHP_SELF;
        print ("    <h3 align=\"center\">Thumbnail hochladen</h3>\n");
        printf("    <form method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">\n", $PHP_SELF);
        print ("    <input type=\"hidden\" name=\"go\" value=\"putf\"></input>\n");
        printf("            <input type=\"hidden\" name=\"id\" value=\"%s\"></input>\n", $ID);
        print ("      <table border=\"0\" align=\"center\" cellpadding=\"5\">\n");
        print ("        <tr>\n");
        print ("          <td>Bild: </td>\n");
        print ("          <td><input size=\"30\" type=\"file\" name=\"file\"></input></td>\n");
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td colspan=\"2\"><input type=\"submit\" value=\"Upload starten\"></input></td>\n");
        print ("        </tr>\n");
        print ("      </table>\n");
        print ("    </form>\n");
    }

    function printFinished($header="")
    {
        global $PHP_SELF;
        print ("<html>\n");
        print ("  <head>\n");
        print ("    <title></title>\n");
        print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
        print ("  </head>\n");
        print ("  <body bgcolor=\"white\">\n");
        printf("    <h1 align=\"center\">%s</h1>\n", $header);
        print ("  </body>\n");
        print ("</html>\n");
        exit;
    }
?>
  </body>
</html>






