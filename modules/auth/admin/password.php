<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");

    global $sUser;
    //var_dump($sUser);
    //echo "<br>";

    if( isset($password1) && isset($password2) )
    {
        if( ($password1 == "") || ($password2 == "") )
        {
            printPasswordForm("Bitte Passwort eingeben und wiederholen.");
        }
        else if ( $password1 == $password2 )
        {
            $query = sprintf("update authUser set password=PASSWORD('%s') where username='%s'", $password1, $sUser["username"]);
            //echo $query;
            $db->executeQuery($query);
            print ("<html>");
            print ("  <head>");
            print ("    <title>Passwort ändern</title>\n");
            print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
            print ("  </head>");
            print ("  <body bgcolor=\"white\">");
            print ("    <h1 align=\"center\">Passwort wurde geändert</h1>");
            print ("  </body>");
            print ("</html>");
        }
        else
        {
            printPasswordForm("Passwörter ungleich, bitte nochmal eingeben.");
        }
    }
    else
    {
        printPasswordForm();
    }


    function printPasswordForm($message="")
    {
        global $PHP_SELF, $sUser;

        print ("<html>\n");
        print ("  <head>\n");
        print ("    <title>Passwort ändern</title>\n");
        print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
        print ("  </head>\n");
        print ("  <body bgcolor=\"white\">\n");
        print ("    <h1 align=\"center\">Passwort ändern</h1>\n");
        if($message != "")
            printf("    <h3 class=\"error\" align=\"center\">%s</h3>\n", $message);
        printf("    <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("      <table align=\"center\" border=\"0\" cellpadding=\"5\">\n");
        print ("        <tr>\n");
        print ("          <td>Name: </td>\n");
        printf("          <td><input type=\"Text\" size=\"32\" maxlength=\"32\" value=\"%s\" readonly></input></td>\n", $sUser["realname"]);
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td>Benutzername: </td>\n");
        printf("          <td><input type=\"Text\" size=\"32\" maxlength=\"32\" value=\"%s\" readonly></input></td>\n", $sUser["username"]);
        print ("        </tr>\n");
        print ("        <tr>\n");            print ("          <td>Passwort: </td>\n");
        print ("          <td><input type=\"password\" name=\"password1\" size=\"32\" maxlength=\"32\"></input></td>\n");
        print ("        </tr>\n");            print ("        <tr>\n");
        print ("          <td>Wiederholung: </td>\n");
        print ("          <td><input type=\"password\" name=\"password2\" size=\"32\" maxlength=\"32\"></input></td>\n");
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"Passwort ändern\"></td>\n");
        print ("        </tr>\n");
        print ("      </table>\n");
        print ("    </form>\n");
        print ("  </body>\n");
        print ("</html>\n");
        exit;
    }

?>
