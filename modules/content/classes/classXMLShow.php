<?php
    include_once("classXML.php");
    include_once("classImageInfo.php");

    class XMLShow extends XML
    {
        var $error;
        var $db;

        var $toggle = 0;

        function XMLShow($error, $db)
        {
            $this->error = $error;
            $this->db = $db;
            $this->XML();
            // $this->init();
        }

        function tagOpen($tag)
        {
            global $PHP_SELF, $HTTP_GET_VARS;
            switch($tag->getName())
            {
                case "HEADER":
                    printf("<h%s align=\"%s\">", $tag->getAttribute("SIZE"), $tag->getAttribute("ALIGN"));
                    break;

                case "TEXT":
                    if($tag->getAttribute("ALIGN") != "flowing")
                        printf("<p align=\"%s\">\n", $tag->getAttribute("ALIGN"));
                    break;

                case "FLOWTEXT":
                    break;

                case "COLOR":
                    if($tag->getAttribute("COL") != "")
                        printf("<font color=\"%s\">\n", $tag->getAttribute("COL"));
                    break;

                case "IMAGE":
                    printf("<div class=\"content\" align=\"%s\">\n", $tag->getAttribute("ALIGN"));
                    $image = new ImageInfo($this->error, $this->db);
                    $image->createDetails($tag->getAttribute("ID"));

                    if ($image->getThumbFilename() != "")
                    {
                        printf("<a href=\"javascript:void(open('/images/%s','%s','width=%s,height=%s,screenX=0,screenY=0,status=no,menu=no,locationbar=no'))\">", $image->getImageFilename(), "Bild".rand(), $image->getImageWidth(), $image->getImageHeight());
                        printf("<img src=\"/images/%s\" width=\"%s\" height=\"%s\" alt=\"%s\" border=\"0\"><br>%s</a>\n", $image->getThumbFilename(), $image->getThumbWidth(), $image->getThumbHeight(), $image->getDescription(), $image->getDescription());
                    }
                    else
                    {
                        printf("<img src=\"/images/%s\" width=\"%s\" height=\"%s\" alt=\"%s\"><br>%s\n", $image->getImageFilename(), $image->getImageWidth(), $image->getImageHeight(), $image->getDescription(), $image->getDescription());
                    }
                    break;

                case "LINK":
                    if($tag->getAttribute("ALIGN") != "flowing")
                        printf("<div align=\"%s\">\n", $tag->getAttribute("ALIGN"));
                    switch($tag->getAttribute("TYPE"))
                    {
                        case "free":
                            printf("<a href=\"%s\">\n", $tag->getAttribute("HREF"));
                            break;
                        case "freepopup":
                            printf("<a href=\"javascript:void(open('%s','info%s','width=%s,height=%s,status=no,menubar=no,locationbar=no'))\">", $tag->getAttribute("HREF"), time(), $tag->getAttribute("WIDTH")!=""?$tag->getAttribute("WIDTH"):400, $tag->getAttribute("HEIGHT")!=""?$tag->getAttribute("HEIGHT"):400);
                            break;
                        case "email":
                            printf("<a class=\"content\" href=\"mailto:%s\">\n", $tag->getAttribute("EMAIL"));
                            break;
                        case "internpage":
                            printf("<a class=\"content\" href=\"%s?type=page&ID=%s\">\n", $PHP_SELF, $tag->getAttribute("PAGEID"));
                            break;
                        case "interncat":
                            printf("<a class=\"content\" href=\"%s?type=cat&ID=%s\">\n", $PHP_SELF, $tag->getAttribute("CATID"));
                            break;
                        case "self":
                            printf("<a class=\"content\" href=\"%s?type=%s&ID=%s", $PHP_SELF, $HTTP_GET_VARS["type"], $HTTP_GET_VARS["ID"] );
                            if($tag->getAttribute("PARAMKEY")!="")
                                printf("&%s=%s", $tag->getAttribute("PARAMKEY"), $tag->getAttribute("PARAMVAL"));
                            if($tag->getAttribute("PARAMKEY2")!="")
                                printf("&%s=%s", $tag->getAttribute("PARAMKEY2"), $tag->getAttribute("PARAMVAL2"));
                            if($tag->getAttribute("PARAMKEY3")!="")
                                printf("&%s=%s", $tag->getAttribute("PARAMKEY3"), $tag->getAttribute("PARAMVAL3"));
                            if($tag->getAttribute("PARAMKEY4")!="")
                                printf("&%s=%s", $tag->getAttribute("PARAMKEY4"), $tag->getAttribute("PARAMVAL4"));
                            printf("\" onmouseover=\"status='%s';return true;\" onmouseout=\"status='';return true;\">", $tag->getAttribute("PARAMVAL"));
                            break;
                    }
                    break;

                case "LIST":
                    printf("<div class=\"content\" align=\"%s\">\n", $tag->getAttribute("ALIGN"));
                    switch($tag->getAttribute("TYPE"))
                    {
                        case ".":
                            print("<ul>\n");
                            break;
                        case "1":
                        case "I":
                        case "i":
                        case "A":
                        case "a":
                            printf("<ol type=\"%s\">\n", $tag->getAttribute("TYPE"));
                            break;
                    }
                    break;
                case "LISTITEM":
                    print ("<li>");
                    break;

                case "COLS":
                    print ("<table border=\"0\" width=\"90%\">\n<tr>\n");
                    break;
                case "COL":
                    print ("<td>\n");
                    break;

                case "SIMPLETABLE":
                case "TABLE":
                    $this->toggle = 2;
                    if($tag->getAttribute("WIDTH") == "")
                        $width = "";
                    else
                        $width = " width=\"".$tag->getAttribute("WIDTH")."\"";

                    printf("<table class=\"content\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" valign=\"top\" align=\"%s\" %s>\n", $tag->getAttribute("ALIGN"), $width);
                    break;
                case "SIMPLEROW":
                case "ROW":
                    if($tag->getAttribute("TOGGLE") == "false")
                        $this->toggle = $this->toggle + 2;
                    else if($tag->getAttribute("TOGGLE") == "reset")
                        $this->toggle = 1;
                    else
                        $this->toggle = ($this->toggle + 1) % 2;

                    if($tag->getAttribute("STRIKE") == "true")
                        print ("<tr style=\"text-decoration:line-through\">\n");

                    else
                        print ("<tr>\n");
                    break;
                case "SIMPLECELL":
                case "CELL":
                case "SIMPLECELLH":
                case "CELLH":
                    $valign = " valign=\"top\"";
                    if($tag->getAttribute("ALIGN")=="")
                        $align = "";
                    else
                        $align = " align=\"".$tag->getAttribute("ALIGN")."\"";

                    if($tag->getAttribute("SPAN")=="" && $tag->getAttribute("ROWSPAN")=="")
                        $colspan = "";
                    else if($tag->getAttribute("SPAN")!="")
                        $colspan = " colspan=\"".$tag->getAttribute("SPAN")."\"";
                    else if($tag->getAttribute("ROWSPAN")!="")
                        $colspan = " rowspan=\"".$tag->getAttribute("ROWSPAN")."\"";

                    if($this->toggle == 0)
                        $color = "bgcolor=\"#E0F0F0\"";
                    else if($this->toggle == 1)
                        $color = "bgcolor=\"#C0D0D0\"";
                    else
                        $color = "";

                    printf("<td %s%s%s%s>", $color, $valign, $align, $colspan );
                    break;

                case "FORM":
                    printf("<form method=\"post\" action=\"%s?type=%s&ID=%s\">\n", $PHP_SELF, $HTTP_GET_VARS["type"], $HTTP_GET_VARS["ID"]);
                    print ("<input type=\"hidden\" name=\"go\" value=\"1\"></input>\n");
                    print ("<table border=\"0\" cellpadding=\"5\" valign=\"top\" align=\"center\" width=\"80%\">\n");
                    break;
               case "SIMPLEFORM":
                    printf("<form method=\"post\" action=\"%s?type=%s&ID=%s\">\n", $PHP_SELF, $HTTP_GET_VARS["type"], $HTTP_GET_VARS["ID"]);
                    print ("<input type=\"hidden\" name=\"go\" value=\"1\"></input>\n");
                    break;
                case "INPUT":
                   switch($tag->getAttribute("TYPE"))
                   {
                        case "textfield":
                            print ("<tr>\n");
                            printf("<td align=\"right\" valign=\"top\">%s:</td>\n", $tag->getAttribute("TEXT"));
                            printf("<td><input type=\"text\" name=\"%s\" value=\"%s\" size=\"35\"></input></td>\n", $tag->getAttribute("NAME"), $tag->getAttribute("VALUE"));
                            print ("</tr>\n");
                            break;
                        case "textarea":
                            print ("<tr>\n");
                            printf("<td align=\"right\" valign=\"top\">%s:</td>\n", $tag->getAttribute("TEXT"));
                            printf("<td><textarea name=\"%s\" rows=\"10\" cols=\"35\">%s</textarea></td>\n", $tag->getAttribute("NAME"), $tag->getAttribute("VALUE"));
                            print ("</tr>\n");
                            break;
                        case "select":
                            print ("<tr>\n");
                            printf("<td align=\"right\" valign=\"top\">%s:</td>\n", $tag->getAttribute("TEXT"));
                            printf("<td><select name=\"%s\">\n", $tag->getAttribute("NAME") );
                            break;
                        case "option":
                            printf("<option value=\"%s\" %s>%s</option>\n", $tag->getAttribute("VALUE"), $tag->getAttribute("SELECTED")=="true"?"selected":"", $tag->getAttribute("DESC"));
                            break;
                        case "radio":
                            print ("<tr>\n");
                            printf("<td align=\"right\" valign=\"top\"><input type=\"radio\" name=\"%s\" value=\"%s\" %s></input></td>\n", $tag->getAttribute("NAME"), $tag->getAttribute("VALUE"), $tag->getAttribute("DEFAULT")==$tag->getAttribute("VALUE")?"checked":"");
                            printf("<td>%s ", $tag->getAttribute("TEXT"));
                            break;
                        case "checkbox":
                            print ("<tr>\n");
                            printf("<td align=\"right\" valign=\"top\"><input type=\"checkbox\" name=\"%s\" value=\"%s\" %s></input></td>\n", $tag->getAttribute("NAME"), $tag->getAttribute("VALUE"), $tag->getAttribute("DEFAULT")==$tag->getAttribute("VALUE")?"checked":"" );
                            printf("<td>%s ", $tag->getAttribute("TEXT"));
                            break;
                        case "flowingtextfield":
                            printf("<input type=\"text\" name=\"%s\" value=\"%s\" size=\"15\"></input>\n", $tag->getAttribute("NAME"), $tag->getAttribute("VALUE"));
                            break;
                   }
                   break;
                case "FREEFORM":
                    printf("<form method=\"post\" action=\"%s?type=%s&ID=%s\">\n", $PHP_SELF, $HTTP_GET_VARS["type"], $HTTP_GET_VARS["ID"]);
                    print ("<input type=\"hidden\" name=\"go\" value=\"1\"></input>\n");
                    break;
                case "FREEINPUT":
                    switch($tag->getAttribute("TYPE"))
                    {

                    }
                    break;
                case "INFOPOPUP":
                    // printf("<a href=\"infopopup.php?head=%s&text=%s\" target=\"new\"><img src=\"/images/pub/info.gif\" alt=\"weitere Informationen\"></img></a>", $tag->getAttribute("HEAD"), $tag->getAttribute("TEXT"));
                    // printf("<a href=\"#\" target=\"info\" onclick=\"open('infopopup.php?head=%s&text=%s','info','width=400,height=500,status=no,menubar=no,locationbar=no')\"><img src=\"/images/pub/info.gif\" alt=\"weitere Informationen\" border=\"0\"></img></a>", urlencode($tag->getAttribute("HEAD")), urlencode($tag->getAttribute("TEXT")));
                    // printf("<a href=\"javascript:void(open('infopopup.php?head=%s&text=%s','info','width=400,height=500,status=no,menubar=no,locationbar=no'))\"><img src=\"/images/pub/info.gif\" alt=\"weitere Informationen\" border=\"0\"></img></a>", urlencode($tag->getAttribute("HEAD")), urlencode($tag->getAttribute("TEXT")));
                    printf("<a href=\"javascript:void(open('infopopup.php?%s=%s','info','width=400,height=400,status=no,menubar=auto,locationbar=no'))\"><img src=\"/images/pub/info.gif\" alt=\"weitere Informationen\" border=\"0\"></img></a>", urlencode($tag->getAttribute("PARAMKEY")), urlencode($tag->getAttribute("PARAMVAL")));
                    break;
                case "B":
                    print ("<b>");
                    break;
                case "U":
                    print ("<u>");
                    break;
                case "LI":
                    print ("<li>\n");
                    break;

            }
        }

        function tagData($tag)
        {
            switch($tag->getName())
            {
                case "HEADER":
                case "TEXT":
                case "LI":
                case "LISTITEM":
                case "COL":
                case "CELL":
                case "SIMPLECELL":
                    printf("%s", nl2br(trim($tag->getData())));
                    // printf("%s", nl2br(chop($tag->getData())));
                    // printf("%s", chop($tag->getData()));
                    break;
                case "LINK":
                case "CELLH":
                case "SIMPLECELLH":
                    printf("<strong>%s</strong>", nl2br(trim($tag->getData())));
                    // printf("<strong>%s</strong>", nl2br(chop($tag->getData())));
                    // printf("<strong>%s</strong>", chop($tag->getData()));
                    break;
                case "SPACER":
                    printf("<br><br>");
                    breaK;
                case "FLOWTEXT":
                case "COLOR":
                case "B":
                case "U":
                    printf("%s", nl2br($tag->getData()));
                    break;

            }
        }

        function tagClose($tag)
        {
            switch($tag->getName())
            {
                case "HEADER":
                    printf("</h%s>\n", $tag->getAttribute("SIZE"));
                    break;

                case "TEXT":
                    if($tag->getAttribute("ALIGN") != "flowing")
                        print ("</p>\n");
                    break;

                case "FLOWTEXT":
                    break;

                case "COLOR":
                    print("</font>\n");
                    break;

                case "IMAGE":
                    print ("</img>\n</div>\n");
                    break;

                case "LINK":
                    print ("</a>\n");
                    if($tag->getAttribute("ALIGN") != "flowing")
                        print ("</div>\n");
                    break;

                case "LIST":
                    switch($tag->getAttribute("TYPE"))
                    {
                        case ".":
                            print ("</ul>\n</div>\n");
                            break;
                        case "1":
                        case "I":
                        case "i":
                        case "A":
                        case "a":
                            print ("</ol>\n</div>\n");
                            break;
                    }
                    break;
                case "LISTITEM":
                    print ("</li>\n");
                    break;

                case "COLS":
                    print("</tr></table>\n");
                    break;
                case "COL":
                    print ("</td>\n");
                    break;

                case "SIMPLETABLE":
                case "TABLE":
                    print ("</table>\n");
                    break;
                case "SIMPLEROW":
                case "ROW":
                    print ("</tr>\n");
                    break;
                case "SIMPLECELL":
                case "CELL":
                case "SIMPLECELLH":
                case "CELLH":
                    print ("</td>\n");
                    break;

                case "FORM":
                    // print ("<tr><td></td><td><input type=\"submit\" value=\"abschicken\"></input></td></tr>\n");
                    printf("<tr><td></td><td><input type=\"submit\" value=\"%s\"></input></td></tr>\n", $tag->getAttribute("BUTTONTEXT")==""?"abschicken":$tag->getAttribute("BUTTONTEXT"));
                    print ("</table>");
                    print ("</form>");
                    break;
                case "SIMPLEFORM":
                    // print ("<input type=\"submit\" value=\"abschicken\"></input>\n");
                    printf("<div align=\"center\"><input type=\"submit\" value=\"%s\"></input></div>\n", $tag->getAttribute("BUTTONTEXT")==""?"abschicken":$tag->getAttribute("BUTTONTEXT"));
                    print ("</form>");
                    break;
                case "INPUT":
                   switch($tag->getAttribute("TYPE"))
                   {
                       case "select":
                           print("</select></td></tr>\n");
                           break;
                        case "radio":
                            print ("</td>\n");
                            print ("</tr>\n");
                            break;
                   }
                   break;
                case "FREEFORM":
                    print ("</form>");
                    break;
                case "B":
                    print ("</b>");
                    break;
                case "U":
                    print ("</u>");
                    break;
                case "LI":
                    print ("</li>\n");
                    break;

            }
        }

    } // class XML

?>
