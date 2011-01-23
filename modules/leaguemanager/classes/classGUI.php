<?php
        class GUI
        {
                // document elements
                function printStart()
                {
                        global $PHP_SELF;
                        print ("<html>\n");
                        print ("  <head>\n");
                        print ("    <link rel=\"STYLESHEET\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
                        print ("    <script language=\"JavaScript\" src=\"lm.js\" type=\"text/javascript\"></script>\n");
                        print ("  </head>\n");
                        print ("  <body>\n");
                        printf("    <form name=\"save\" action=\"%s\" method=\"post\" enctype=\"multipart/form-data\">\n", $PHP_SELF);
                        print ("      <table>\n");
                }
                function printStop()
                {
                        print ("      </table>\n");
                        print ("    </form>\n");
                        print ("  </body>\n");
                        print ("</html>\n");
                }
                function printReloadMenu()
                {
                        print ("<script language=\"JavaScript\">parent.menu.location.reload()</script>\n");
                }


                // text elements
                function printHeader($header)
                {
                        printf("<tr><td colspan=\"%s\"><h1>%s</h1></td></tr>\n", $this->cols, $header);
                }
                function printText($text)
                {
                        printf("<tr><td colspan=\"2\">%s</td></tr>\n", $text);
                }

                function printSpacer()
                {
                        print ("<tr><td colspan=\"2\">&nbsp;</td></tr>\n");
                }

                function printTextBox($text)
                {
                        printf("<tr><td colspan=\"%s\" class=\"action\">%s</td></tr>\n", $this->cols, $text);
                }

                function printErrorText($text)
                {
                        printf("<tr><td colspan=\"%s\" class=\"error\"><b>%s</b></td></tr>", $this->cols, $text);
                }








        }
?>
