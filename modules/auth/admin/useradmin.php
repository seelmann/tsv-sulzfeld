<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if( $permcheck->hasUserAdminPermission() == false )
    {
        $error->printAccessDenied();
        exit;
    }


    if(isset($do))
    {
        if($do == "create")
        {
            if( isset($go) && ($go == "pef") )
            {
                if( ($name != "") && ($username != "") && ($password != "") && ($email != "") )
                {
                    if( (strlen($username) < 4) || (strlen($username) > 20) )
                    {
                        printEditForm($do, "Benutzer anlegen", $name, $username, $email, "Benutzername muß aus 4 bis 20 Buchstaben bestehen.");
                    }
                    else
                    {
                        $query = sprintf("select * from authUser where username='%s'", $username);
                        $db->executeQuery($query);
                        if($db->getNumRows() > 0)
                        {
                            printEditForm($do, "Benutzer anlegen", $name, $username, $email, "Benutzername ist schon vorhanden, bitte anderen wählen.");
                        }
                        else
                        {
                            $query = sprintf("insert into authUser (username, password, realname, email, createDate, active) values ('%s', PASSWORD('%s'), '%s', '%s',now(), 'yes')", $username, $password, $name, $email);
                            $db->executeQuery($query);
                            printFinished(sprintf("Benutzer %s wurde angelegt", $name));
                        }
                    }
                }
                else
                {
                    printEditForm($do, "Benutzer anlegen", $name, $username, $email, "Bitte alle Felder ausfüllen.");
                }

            }
            else
            {
                printEditForm($do, "Benutzer anlegen");
            }
        } // create

        else if($do == "edit")
        {
            if( isset($go) )
            {
                if($go == "psuf")
                {
                    $query = sprintf("select realname, email from authUser where username='%s'", $username);
                    $db->executeQuery($query);
                    $db->nextRow();
                    printEditForm($do, "Benutzer ändern", $db->getValue("realname"), $username, $db->getValue("email"));
                }
                else if ($go == "pef")
                {
                    if($password == "")
                        $query = sprintf("update authUser set realname='%s', email='%s' where username='%s'", $name, $email, $username);
                    else
                        $query = sprintf("update authUser set realname='%s', email='%s', password=PASSWORD('%s') where username='%s'", $name, $email, $password, $username);
                    $db->executeQuery($query);
                    printFinished(sprintf("Benutzer %s wurde geändert", $name));
                }
            }
            else
            {
                printSelectUserForm($db, $do, "Benutzer ändern", "yes");
            }
        }

        else if($do == "deactivate")
        {
            if( isset($go) && ( $go == "psuf" ) )
            {
                // get name of user
                $query = sprintf("select realname from authUser where username='%s'", $username);
                $db->executeQuery($query);
                $db->nextRow();
                $name =  $db->getValue("realname");

                // check if user is admin
                $query = sprintf("select * from authUserAdmin where username='%s' and perm='admin'", $username);
                $db->executeQuery($query);
                if($db->getNumRows() > 0)
                {
                    // user is admin, check if he is the only one
                    $query = sprintf("select * from authUserAdmin where username!='%s' and perm='admin'", $username);
                    $db->executeQuery($query);
                    if($db->getNumRows() == 0)
                    {
                        $error->printErrorPage(sprintf("Benutzer \"%s\" ist der einzige Admin, deaktivieren nicht möglich", $name));
                    }
                }
                $query = sprintf("update authUser set active='no' where username='%s'", $username);
                $db->executeQuery($query);
                printFinished(sprintf("Benutzer %s wurde deaktiviert", $name));
            }
            else
            {
                printSelectUserForm($db, $do, "Benutzer deaktivieren", "yes");
            }
        }

        else if($do == "activate")
        {
            if( isset($go) && ( $go == "psuf" ) )
            {
                // get name of user
                $query = sprintf("select realname from authUser where username='%s'", $username);
                $db->executeQuery($query);
                $db->nextRow();
                $name =  $db->getValue("realname");

                $query = sprintf("update authUser set active='yes' where username='%s'", $username);
                $db->executeQuery($query);
                printFinished(sprintf("Benutzer %s wurde aktiviert", $name));
            }
            else
            {
                printSelectUserForm($db, $do, "Benutzer aktivieren", "no");
            }
        }
    }



    function printEditForm($do, $header="", $name="", $username="", $email="", $message="")
    {
        global $PHP_SELF;

        print ("<html>\n");
        print ("  <head>\n");
        print ("    <title></title>\n");
        print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
        print ("  </head>\n");
        print ("  <body bgcolor=\"white\">\n");
        printf("    <h1 align=\"center\">%s</h1>\n", $header);
        if($message != "")
            printf("    <h3 class=\"error\" align=\"center\">%s</h3>\n", $message);
        printf("    <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        printf("    <input type=\"hidden\" name=\"do\" value=\"%s\"></input>\n", $do);
        print ("    <input type=\"hidden\" name=\"go\" value=\"pef\"></input>\n");
        print ("      <table align=\"center\" border=\"0\" cellpadding=\"5\">\n");
        print ("        <tr>\n");
        print ("          <td>Name: </td>\n");
        printf("          <td><input type=\"Text\" name=\"name\" size=\"32\" maxlength=\"50\" value=\"%s\"></input></td>\n", $name);
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td>Benutzername: </td>\n");
        if($do == "create")
            printf("          <td><input type=\"Text\" name=\"username\" size=\"32\" maxlength=\"20\" value=\"%s\"></input></td>\n", $username);
        else
            printf("          <td><input type=\"Text\" name=\"username\" size=\"32\" maxlength=\"20\" value=\"%s\" readonly></input></td>\n", $username);
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td>E-Mail: </td>\n");
        printf("          <td><input type=\"Text\" name=\"email\" size=\"32\" maxlength=\"50\" value=\"%s\"></input></td>\n", $email);
        print ("        </tr>\n");
        print ("        <tr>\n");
        print ("          <td>Passwort: </td>\n");
        print ("          <td><input type=\"password\" name=\"password\" size=\"32\"></input></td>\n");
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
        if($message != "")
            printf("          <h3 class=\"error\" align=\"center\">%s</h3>\n", $message);
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



    function printDeleteCommit($do, $username="", $name="", $header)
    {
        global $PHP_SELF;

        print ("<html>\n");
        print ("  <head>\n");
        print ("    <title></title>\n");
        print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
        print ("  </head>\n");
        print ("  <body bgcolor=\"white\">\n");
        printf("    <h1 align=\"center\">%s</h1>\n", $header);
        print ("    <table align=\"center\" border=\"0\" cellpadding=\"5\">\n");
        print ("      <tr>\n");
        printf("        <td colspan=\"2\">Benutzer %s wirklich löschen?</td>\n", $name);
        print ("      </tr>\n");
        print ("      <tr>\n");
        printf("        <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        printf("          <input type=\"hidden\" name=\"do\" value=\"%s\"></input>\n", $do);
        print ("          <input type=\"hidden\" name=\"go\" value=\"pdc\"></input>\n");
        printf("          <input type=\"hidden\" username=\"username\" value=\"%s\"></input>\n", $username);
        print ("          <td><input type=\"submit\" value=\"ja\"></input></td>\n");
        print ("        </form>\n");
        printf("        <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
        printf("          <input type=\"hidden\" name=\"do\" value=\"%s\"></input>\n", $do);
        print ("          <td><input type=\"submit\" value=\"nein\"></input></td>\n");
        print ("        </form>\n");
        print ("      </tr>\n");
        print ("    </table>\n");
        print ("  </body>\n");
        print ("</html>\n");
        exit;
    }


?>
