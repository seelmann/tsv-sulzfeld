<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if( $permcheck->hasUserAdminPermission() == false )
    {
        $error->printAccessDenied();
        exit;
    }


    if(isset($go))
    {
        if( $go == "psuf" )
        {
            printEditForm($db, "Berechtigung Benutzerverwaltung ändern", $username);
        }

        else if ($go == "pef")
        {
            $query = sprintf("select realname from authUser where username='%s'", $username);
            $db->executeQuery($query);
            $db->nextRow();
            $name =  $db->getValue("realname");

            if( isset($admin) )
            {
                echo "admin ist gesetzt";

                $query = sprintf("select perm from authUserAdmin where username='%s'", $username);
                $db->executeQuery($query);
                if($db->getNumRows() == 0)
                {
                    $query = sprintf("insert into authUserAdmin (username, perm) values ('%s', 'admin')", $username);
                    $db->executeQuery($query);
                    printFinished(sprintf("Berechtigung für Benutzer %s wurde geändert", $name));
                }
                else
                    printFinished(sprintf("Berechtigung für Benutzer %s wurde nicht geändert", $name));
            }
            else
            {
                $query = sprintf("select * from authUserAdmin where username!='%s' and perm='admin'", $username);
                $db->executeQuery($query);
                if($db->getNumRows() > 0)
                {
                    $query = sprintf("delete from authUserAdmin where username='%s' and perm='admin'", $username);
                    $db->executeQuery($query);
                    printFinished(sprintf("Berechtigung für Benutzer %s wurde geändert", $name));
                }
                else
                    printFinished(sprintf("%s ist der einzige Admin, Änderung nicht möglich", $name));
            }
        }
    }
    else
    {
        printSelectUserForm($db, "", "Berechtigung Benutzerverwaltung ändern", "yes");
    }

    function printEditForm($db, $header="", $username="", $message="")
    {
        global $PHP_SELF;

        $query = sprintf("select realname from authUser where username='%s'", $username);
        $db->executeQuery($query);
        $db->nextRow();
        $name =  $db->getValue("realname");

        print ("<html>\n");
        print ("  <head>\n");
        print ("    <title></title>\n");
        print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
        print ("  </head>\n");
        print ("  <body bgcolor=\"white\">\n");
        printf("    <h1 align=\"center\">%s</h1>\n", $header);
        printf("          <h3 align=\"center\">für %s</h3>\n", $name);
        if($message != "")
            printf("    <h3 class=\"error\" align=\"center\">%s</h3>\n", $message);
        printf("    <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        print ("    <input type=\"hidden\" name=\"go\" value=\"pef\"></input>\n");
        printf("    <input type=\"hidden\" name=\"username\" value=\"%s\"></input>\n", $username);
        print ("      <table align=\"center\" border=\"0\" cellpadding=\"5\">\n");
        $query = sprintf("select perm from authUserAdmin where username='%s'", $username);
        $db->executeQuery($query);
        if($db->getNumRows() > 0)
            $checked = "checked";
        print ("        <tr>\n");
        print ("          <td>admin: </td>\n");
        printf("          <td align=\"center\"><input type=\"checkbox\" name=\"admin\" value\"yes\" %s></td>\n", $checked);
        print ("        </tr>\n");
        print ("        <tr>\n");
        printf("          <td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"%s\"></td>\n", $header);
        print ("        </tr>\n");
        print ("      </table>\n");
        print ("    </form>\n");
        print ("  </body>\n");
        print ("</html>\n");
        exit;
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

    function printSelectUserForm($db, $do, $header="", $active="yes")
    {
        global $PHP_SELF;

        print ("<html>\n");
        print ("  <head>\n");
        print ("    <title></title>\n");
        print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
        print ("  </head>\n");
        print ("  <body bgcolor=\"white\">\n");
        printf("    <h1 align=\"center\">%s</h1>\n", $header);
        printf("    <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        printf("    <input type=\"hidden\" name=\"do\" value=\"%s\"></input>\n", $do);
        print ("    <input type=\"hidden\" name=\"go\" value=\"psuf\"></input>\n");
        print ("      <table align=\"center\" border=\"0\" cellpadding=\"5\">\n");
        print ("        <tr>\n");
        print ("          <td>Benutzer auswählen: </td>\n");
        print ("          <td>\n");
        print ("            <select name=\"username\" size=\"1\" onChange=\"submit()\">\n");
        print ("              <option value=\"\"></option>\n");
        $query = sprintf("select realname, username from authUser where active='%s'", $active);
        $db->executeQuery($query);
        while($db->nextRow())
            printf("              <option value=\"%s\">%s</option>\n", $db->getValue("username"), $db->getValue("realname"));
        print ("            </select>\n");
        print ("          </td>\n");
        print ("          <td><input type=\"submit\" value=\"weiter\"></input></td>\n");
        print ("        </tr>\n");
        print ("      </table>\n");
        print ("    </form>\n");
        print ("  </body>\n");
        print ("</html>\n");
        exit;
    }

?>
