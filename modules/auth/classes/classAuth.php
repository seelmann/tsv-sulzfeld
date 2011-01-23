<?php

    class Auth
    {
        var $db;
        var $error;
        var $username, $realname, $email;

        function Auth($db, $error, $username="", $password="")
        {

            $this->error = $error;
            $this->db = $db;

            global $sUser;
            if(!isset($sUser))
                $this->checkAuth($username, $password);
        }

        function checkAuth($username="", $password="")
        {
            global $sUser;
            session_register("sUser");

            if( ($username != "") && ($password != "") )
            {
                // check the Permission
                $query = sprintf("select username,
                                         realname,
                                         email,
                                         active
                          from   authUser
                          where  username = '%s' and
                                 password = PASSWORD('%s')
                         ", $username, $password);
                $result = mysql_query($query, $this->db->getLinkID());

                if(mysql_num_rows($result) != 1)
                {
                    $this->printLoginForm($username, "Falsche Benutzername/Passwort Kombination.");
                }
                else
                {
                    $row = mysql_fetch_array($result);
                    if($row["active"] == "yes")
                    {
                        $sUser["username"] = $row["username"];
                        $sUser["realname"] = $row["realname"];
                        $sUser["email"] = $row["email"];
                        $this->username = $sUser["username"];
                        $this->realname = $sUser["realname"];
                        $this->email = $sUser["email"];
                    }
                    else
                        $this->error->printErrorPage("Das Benutzerkonto wurde deaktiviert.");
                }
            }
            else if( ($password == "") && ($username != "") )
            {
                $this->printLoginForm($username, "Bitte Passwort eingeben");
            }
            else
            {
                $this->printLoginForm($username);
            } // else
        } // function checkAuth()

        function logout()
        {
            session_unset();
            $this->printLogout(session_destroy());
        }

        function printLoginForm($username="", $message="")
        {
            global $PHP_SELF;
            print ("<html>\n");
            print ("  <head>\n");
            print ("    <title>Login</title>\n");
            print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
            print ("  </head>\n");
            print ("  <body bgcolor=\"white\">\n");
            print ("    <h1 align=\"center\">Anmeldung</h1>\n");
            if($message != "")
                printf("          <h3 class=\"error\" align=\"center\">%s</h3>\n", $message);
            printf("    <form method=\"post\" action=\"%s\">\n", $PHP_SELF);
            print ("      <table align=\"center\" border=\"0\" cellpadding=\"5\">\n");
                        print ("        <tr>\n");
            print ("          <td>Benutzername: </td>\n");
            printf("          <td><input type=\"Text\" name=\"username\" size=\"32\" maxlength=\"32\" value=\"%s\"></td>\n", $username);
            print ("        </tr>\n");
            print ("        <tr>\n");
            print ("          <td>Passwort: </td>\n");
            print ("          <td><input type=\"Password\" name=\"password\" size=\"32\" maxlength=\"32\"></td>\n");
            print ("        </tr>\n");
            print ("        <tr>\n");
            print ("          <td>&nbsp;</td>\n");
            print ("          <td><input type=\"Submit\" value=\"weiter\"></td>\n");
            print ("        </tr>\n");
            print ("      </table>\n");
            print ("    </form>\n");
            print ("  </body>\n");
            print ("</html>\n");
            exit;
        }

    function printLogout($sucess)
    {
        ?>
        <html>
          <head>
            <title>Logout</title>
            <link rel="stylesheet" type="text/css" href="/css/admin.css"></link>
          </head>
          <body bgcolor="white">
        <?php
            if($sucess)
                print("<h1 align=\"center\">Abmeldung war erfolgreich</h1>");
            else
                print("<h1 align=\"center\">Abmeldung war <u><b>nicht</b></u> erfolgreich</h1>");
        ?>
            <table align="center" border="0" cellpadding="5">
              <tr>
                <td><a href="index.php">zur Anmeldemaske</a></td>
              </tr>
              <tr>
                <td><a href="../index.php">zur Homepage</a></td>
              </tr>
            </table>
          </body>
        </html>
        <?php
        exit;
    }

} // class Auth

?>