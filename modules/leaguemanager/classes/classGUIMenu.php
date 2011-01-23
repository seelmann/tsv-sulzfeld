<?php
        class GUIMenu extends GUI
        {
                var $cols = 4;

                function list2select($desc, $name, $list, $object)
                {
                        print ("<tr>\n");
                        printf("<td>%s:</td>", $desc);
                        print ("<td>\n");
                        printf("<select name=\"%s\" onchange=\"doSelect()\">\n", $name."ID");
                        printf("<option value=\"\"%s></option>\n", $object==null?" selected":"");
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                if(is_object($object))
                                {
                                        printf("<option value=\"%s\"%s>%s</option>\n", $o->getID(), $o->getID()==$object->getID()?" selected":"", $o->getName());
                                }
                                else
                                        printf("<option value=\"%s\">%s</option>\n", $o->getID(), $o->getName());

                        }
                        print ("</select>\n");
                        print ("</td>\n");
                        // printf("<td><a href=\"javascript:doEdit('%s')\"><img src=\"/images/admin/edit.gif\" border=\"0\"></img></a></td>", $name);
                        // printf("<td><a href=\"javascript:doNew('%s')\"><img src=\"/images/admin/document.gif\" border=\"0\"></img></a></td>", $name);
                        print ("</tr>\n");
                }

                function list2domselect($desc, $name, $list, $object)
                {
                        print ("<tr>\n");
                        printf("<td>%s:</td>", $desc);
                        print ("<td>\n");
                        printf("<select name=\"%s\" onchange=\"doSelect()\">\n", $name."Number");
                        printf("<option value=\"\"%s></option>\n", $object==null?" selected":"");
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                if(is_object($object))
                                {
                                        printf("<option value=\"%s\"%s>Spieltag %s ( %s erfasst )</option>\n", $o->getNumber(), $o->getNumber()==$object->getNumber()?" selected":"", $o->getNumber(), $o->getNumberOfRegisteredMatches());
                                }
                                else
                                        printf("<option value=\"%s\">Spieltag %s</option>\n", $o->getNumber(), $o->getNumber());

                        }
                        print ("</select>\n");
                        print ("</td>\n");
                        // printf("<td><a href=\"javascript:doEdit('%s')\"><img src=\"/images/admin/edit.gif\" border=\"0\"></img></a></td>", $name);
                        // printf("<td><a href=\"javascript:doNew('%s')\"><img src=\"/images/admin/document.gif\" border=\"0\"></img></a></td>", $name);
                        print ("</tr>\n");
                }


                function printButtonNew($desc, $name)
                {
                        printf("<tr><td colspan=\"4\" class=\"action\" align=\"center\"><input type=\"button\" value=\"%s\" onclick=\"doNew('%s')\"></input></td></tr>\n", $desc, $name);
                }
                function printButtonEdit($desc, $name)
                {
                        printf("<tr><td colspan=\"4\" class=\"action\" align=\"center\"><input type=\"button\" value=\"%s\" onclick=\"doEdit('%s')\"></input></td></tr>\n", $desc, $name);
                }

        }

?>
