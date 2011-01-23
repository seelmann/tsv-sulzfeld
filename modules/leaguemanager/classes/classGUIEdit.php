<?php
        class GUIEdit extends GUI
        {
                var $cols = 2;

                function list2select($desc, $name, $list, $object)
                {
                        print ("<tr>\n");
                        printf("<td>%s:</td>", $desc);
                        print ("<td>\n");
                        printf("<select name=\"%s\">\n", $name."ID");
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
                        print ("</tr>\n");
                }


                function list2linktable($name, $list)
                {
                        printf("<tr><td><a href=\"%s.php?new=new\">neu</a></td></tr>\n", $name);
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                printf("<tr><td><a href=\"%s.php?%sID=%s\">%s</a></td></tr>\n", $name, $name, $o->getID(), $o->getName());
                        }
                }

                function list2selecttable($name, $name2, $list)
                {
                        print ("<tr><td>Mannschaft</td><td>In Liga</td><td>Eigene Mannschaft</td></tr>\n");
                        while($list->hasNext())
                        {
                                $list->next();
                                printf("<tr><td>%s</td><td><input type=\"checkbox\" name=\"%s\" value=\"%s\" %s></input></td>   <td><input type=\"checkbox\" name=\"%s\" value=\"%s\" %s></input></td>   </tr>\n", $list->getName(), $name."[".$list->getID()."]", "true", $list->getChecked(), $name2."[".$list->getID()."]", "true", $list->getCheckedOwn());
                        }
                }

                function list2tilistable($name, $name2, $name3, $list)
                {
                        print ("<tr><td>Mannschaft</td><td>In Liga</td><td>Eigene Mannschaft</td><td>interne Benennung</td></tr>\n");
                        while($list->hasNext())
                        {
                                $list->next();
                                printf("<tr><td>%s</td><td><input type=\"checkbox\" name=\"%s\" value=\"%s\" %s></input></td>   <td><input type=\"checkbox\" name=\"%s\" value=\"%s\" %s></input></td>   <td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>   </tr>\n", $list->getName(), $name."[".$list->getID()."]", "true", $list->getChecked(), $name2."[".$list->getID()."]", "true", $list->getCheckedOwn(), $name3."[".$list->getID()."]", $list->getOwnName());
                        }
                }


                function list2datetimetable($name, $list, $date, $time)
                {
                        printf("<tr><td>Spieltag</td><td>Datum</td><td>Uhrzeit</td></tr>\n", $name);
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                printf("<tr><td>%s</td><td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td><td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td></tr>\n",
                                        $o->getNumber(), $name."Date[".$o->getNumber()."]", isset($date[$o->getNumber()])?$date[$o->getNumber()]:$o->getDate(), $name."Time[".$o->getNumber()."]", isset($time[$o->getNumber()])?$time[$o->getNumber()]:$o->getTime());
                        }
                }

                function list2domtable($name, $list, $date, $time, $home, $guest, $teamList)
                {
                        print ("<tr><td>Spielnummer</td><td>Datum</td><td>Uhrzeit</td><td>Heim</td><td>Gast</td></tr>\n");
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                printf("<tr><td>%s</td>
                                            <td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>
                                            <td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>",
                                        $o->getNumber(),
                                        $name."Date[".$o->getNumber()."]", isset($date[$o->getNumber()])?$date[$o->getNumber()]:$o->getDate(),
                                        $name."Time[".$o->getNumber()."]", isset($time[$o->getNumber()])?$time[$o->getNumber()]:$o->getTime()
                                        );


                                if(!empty($home[$o->getNumber()]))
                                        $id = $home[$o->getNumber()];
                                else
                                        $id = $o->getHomeTeamID();
                                print ("<td>");
                                printf("<select name=\"%s\">\n", $name."Home[".$o->getNumber()."]");
                                printf("<option value=\"\"%s></option>\n", (($id==0) || ($id==""))?" selected":"");
                                while($teamList->hasNext())
                                {
                                        $teamList->next();
                                        printf("<option value=\"%s\"%s>%s</option>\n", $teamList->getID(), $teamList->getID()==$id?" selected":"", $teamList->getName());
                                }
                                print ("</select>\n");
                                print ("</td>");
                                $teamList->resetIndex();


                                if(!empty($guest[$o->getNumber()]))
                                        $id = $guest[$o->getNumber()];
                                else
                                        $id = $o->getGuestTeamID();
                                print ("<td>");
                                printf("<select name=\"%s\">\n", $name."Guest[".$o->getNumber()."]");
                                printf("<option value=\"\"%s></option>\n", (($id==0) || ($id==""))?" selected":"");
                                while($teamList->hasNext())
                                {
                                        $teamList->next();
                                                printf("<option value=\"%s\"%s>%s</option>\n", $teamList->getID(), $teamList->getID()==$id?" selected":"", $teamList->getName());
                                }
                                print ("</select>\n");
                                print ("</td></tr>");

                                $teamList->resetIndex();
                        }
                }

                function list2domresulttable($name, $list, $resultHome, $resultGuest)
                {
                        print ("<tr><td>Heim</td><td>Gast</td><td>Ergebnis Heim</td><td>&nbsp;</td><td>Ergebnis Gast</td></tr>\n");
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                printf("<tr><td>%s</td><td>%s</td>", $o->getHomeTeamName(), $o->getGuestTeamName());
                                printf("<td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>", $name."Home[".$o->getNumber()."]", isset($resultHome[$o->getNumber()])?$resultHome[$o->getNumber()]:$o->getHomeResult() );
                                print ("<td>:</td>");
                                printf("<td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>", $name."Guest[".$o->getNumber()."]", isset($resultGuest[$o->getNumber()])?$resultGuest[$o->getNumber()]:$o->getGuestResult() );

                                print ("</tr>\n");
                        }
                }


                function list2matchestable($name, $list, $date, $time, $home, $guest, $teamList)
                {
                        print ("<tr><td>Spielnummer</td><td>Datum</td><td>Uhrzeit</td><td>Heim</td><td>Gast</td></tr>\n");
                        while($list->hasNext())
                        {
                                $o = $list->next();
                                printf("<tr><td>%s</td>
                                            <td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>
                                            <td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td>",
                                        $o->getNumber(),
                                        $name."Date[".$o->getNumber()."]", isset($date[$o->getNumber()])?$date[$o->getNumber()]:$o->getDate(),
                                        $name."Time[".$o->getNumber()."]", isset($time[$o->getNumber()])?$time[$o->getNumber()]:$o->getTime()
                                        );


                                if(!empty($home[$o->getNumber()]))
                                        $id = $home[$o->getNumber()];
                                else
                                        $id = $o->getHomeTeamID();
                                print ("<td>");
                                printf("<select name=\"%s\">\n", $name."Home[".$o->getNumber()."]");
                                printf("<option value=\"\"%s></option>\n", (($id==0) || ($id==""))?" selected":"");
                                while($teamList->hasNext())
                                {
                                        $teamList->next();
                                        printf("<option value=\"%s\"%s>%s</option>\n", $teamList->getID(), $teamList->getID()==$id?" selected":"", $teamList->getName());
                                }
                                print ("</select>\n");
                                print ("</td>");
                                $teamList->resetIndex();


                                if(!empty($guest[$o->getNumber()]))
                                        $id = $guest[$o->getNumber()];
                                else
                                        $id = $o->getGuestTeamID();
                                print ("<td>");
                                printf("<select name=\"%s\">\n", $name."Guest[".$o->getNumber()."]");
                                printf("<option value=\"\"%s></option>\n", (($id==0) || ($id==""))?" selected":"");
                                while($teamList->hasNext())
                                {
                                        $teamList->next();
                                                printf("<option value=\"%s\"%s>%s</option>\n", $teamList->getID(), $teamList->getID()==$id?" selected":"", $teamList->getName());
                                }
                                print ("</select>\n");
                                print ("</td></tr>");

                                $teamList->resetIndex();
                        }
                }


                // formular elements
                function printInputField($desc, $name, $default="")
                {
                        printf("<tr><td>%s:</td><td><input type=\"text\" name=\"%s\" value=\"%s\"></input></td></tr>\n", $desc, $name, $default);
                }
                function printInputFieldRO($desc, $default)
                {
                        printf("<tr><td>%s:</td><td><input type=\"text\" name=\"\" value=\"%s\" readonly></input></td></tr>\n", $desc, $default);
                }
                function printInputDate($desc, $name, $default="")
                {
                        printf("<tr><td>%s:</td><td><input type=\"text\" name=\"%s\" value=\"%s\" onchange=\"checkDate(document.save.%s)\"></input></td></tr>\n", $desc, $name, $default, $name);
                }
                function printInputHidden($name, $value)
                {
                        printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>", $name, $value);
                }
                function printRadioButtons($desc, $name, $array, $default="")
                {
                        printf("<tr><td>%s:</td><td>", $desc);
                        while(list($k, $v) = each($array))
                                printf("<input type=\"radio\" name=\"%s\" value=\"%s\"%s>%s &nbsp;&nbsp;&nbsp; </input>", $name, $k, $k==$default?" checked":"", $v);
                        print ("</td></tr>\n");
                }

                function printButtonsNew($name)
                {
                        printf("<input type=\"hidden\" name=\"job\" value=\"%s\"></input>", $job);
                        printf("<tr><td colspan=\"2\"><input type=\"button\" value=\"abbrechen\" onclick=\"doCancel('%s')\"></input>&nbsp;<input type=\"button\" value=\"erstellen\" onclick=\"doCreate()\"></input></td></tr>\n", $name);
                }
                function printButtonsEdit($name)
                {
                        printf("<input type=\"hidden\" name=\"job\" value=\"%s\"></input>", $job);
                        printf("<tr><td colspan=\"2\"><input type=\"button\" value=\"abbrechen\" onclick=\"doCancel('%s')\"></input>&nbsp;<input type=\"button\" value=\"ändern\" onclick=\"doModify()\"></input>&nbsp;<input type=\"button\" value=\"löschen\" onclick=\"doDelete()\"></input></td></tr>\n", $name);
                }
                function printButtonsOK($name)
                {
                        printf("<input type=\"hidden\" name=\"job\" value=\"%s\"></input>", $job);
                        printf("<tr><td colspan=\"2\"><input type=\"button\" value=\"abbrechen\" onclick=\"doCancel('%s')\"></input>&nbsp;<input type=\"button\" value=\"OK\" onclick=\"doSubmit()\"></input></td></tr>\n", $name);
                }

        }
?>
