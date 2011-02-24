<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("../auth/classPermcheck.php");
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
            printEditForm($db, "Berechtigung Seitenverwaltung ändern", $username);
        }

        else if ($go == "pef")
        {
            $query = sprintf("select realname from authUser where username='%s'", $username);
            $db->executeQuery($query);
            $db->nextRow();
            $name =  $db->getValue("realname");

            // delete old page permissions
            $query = sprintf("delete from authUserPage where username='%s'", $username);
            $db->executeQuery($query);

            // set new page permissions
            $query = "select ID from contentPage";
            $db->executeQuery($query);
            while($db->nextRow())
            {
                if($pagepermission[$db->getValue("ID")] == "yes")
                {
                    $query = sprintf("insert into authUserPage (username, pageID) values('%s', %s)", $username, $db->getValue("ID"));
                    $db2->executeQuery($query);
                }
            }

            // delete old cat permissions
            $query = sprintf("delete from authUserCat where username='%s'", $username);
            $db->executeQuery($query);

            // set new cat permissions
            $query = "select ID from contentCat";
            $db->executeQuery($query);
            while($db->nextRow())
            {
                if($catpermission[$db->getValue("ID")] == "yes")
                {
                    $query = sprintf("insert into authUserCat (username, catID) values('%s', %s)", $username, $db->getValue("ID"));
                    $db2->executeQuery($query);
                }
            }

            printFinished(sprintf("Berechtigungen für %s geändert", $username));
        }
    }
    else
    {
        printSelectUserForm($db, "", "Berechtigung Seitenverwaltung ändern", "yes");
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
        print ("        <tr>\n");
        print ("          <td>\n");
        print ("            <table align=\"center\" border=\"0\" cellpadding=\"2\">\n");

        // page permissions
        print ("              <tr>\n");
        print ("                <td colspan=\"3\"><h3>Berechtigungen für einzelne Seiten:</h3></td>\n");
        print ("              </tr>");
        $permPage = array();
        $query = sprintf("select pageID from authUserPage where username='%s'", $username);
        $db->executeQuery($query);
        while($db->nextRow())
            $permPage[$db->getValue("pageID")] = 1;

        $query = "select p.ID as ID, p.title as title, c.title as catname from contentPage p left join contentCat c on p.catID=c.ID order by c.superID, c.ord, p.ord";
        $db->executeQuery($query);
        while($db->nextRow())
        {
            print ("              <tr>\n");
            printf("                <td>%s</td>\n", $db->getValue("catname"));
            printf("                <td>%s</td>\n", $db->getValue("title"));
            printf("                <td><input type=\"checkbox\" name=\"pagepermission[%s]\" value=\"yes\" %s></input></td>", $db->getValue("ID"), $permPage[$db->getValue("ID")]==1?"checked":"");
            print ("              </tr>");
        }

        print ("            </table>\n");
        print ("          </td>\n");
        print ("          <td>\n");
        print ("            <table align=\"center\" border=\"0\" cellpadding=\"2\">\n");

        // cat permissions
        print ("              <tr>\n");
        print ("                <td colspan=\"2\"><h3>Berechtigungen für Kategorien (rekursiv):</h3></td>\n");
        print ("              </tr>");
        $permCat = array();
        $query = sprintf("select catID from authUserCat where username='%s'", $username);
        $db->executeQuery($query);
        while($db->nextRow())
            $permCat[$db->getValue("catID")] = 1;
        $query = "select ID, title from contentCat order by superID, ord";
        $db->executeQuery($query);
        while($db->nextRow())
        {
            print ("              <tr>\n");
            printf("                <td>%s</td>\n", $db->getValue("title"));
            printf("                <td><input type=\"checkbox\" name=\"catpermission[%s]\" value=\"yes\" %s></input></td>", $db->getValue("ID"), $permCat[$db->getValue("ID")]==1?"checked":"");
            print ("              </tr>");
        }

        print ("            </table>\n");
        print ("          </td>\n");
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
