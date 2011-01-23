<?php

    class Error
    {

        function Error()
        {
        }

        function printErrorPage($message)
        {
            //exit;

            print ("<html>\n");
            print ("  <head>\n");
            print ("    <title>Fehler</title>\n");
            print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
            print ("  </head>\n");
            print ("  <body bgcolor=\"white\">\n");
            printf("    <h1 align=\"center\">%s</h1>\n", $message);
            print ("    <a href=\"javascript:history.back()\">zur&uuml;ck</a>\n");
            print ("  </body>\n");
            print ("</html>\n");
            exit;


        }

        function printAccessDenied($message="Keine Berechtigung")
        {
            print ("<html>\n");
            print ("  <head>\n");
            print ("    <title>Fehler</title>\n");
            print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
            print ("  </head>\n");
            print ("  <body bgcolor=\"white\">\n");
            printf("    <h1 align=\"center\">%s</h1>\n", $message);
            print ("    <a href=\"javascript:history.back()\">zur&uuml;ck</a>\n");
            print ("  </body>\n");
            print ("</html>\n");
            exit;
        }

    } // class Error

?>
