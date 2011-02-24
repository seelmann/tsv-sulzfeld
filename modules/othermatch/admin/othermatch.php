<?php
        ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/othermatch/classes");
        include_once("classValidation.php");
        include("classSport.php");
        include("classOthermatch.php");
        include("classTemplate.php");
        include("../classPermcheck.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "othermatch.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "othermatch", "othermatchI");
        $t->set_block("othermatch", "sportid", "sportidI");
        $t->set_block("othermatch", "createbuttons", "createbuttonsI");
        $t->set_block("othermatch", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdOthermatch");
        if(!empty($othermatchID))
        {
                $mdOthermatch = new Othermatch($db);
                if(!$mdOthermatch->load($othermatchID))
                        session_unregister("mdOthermatch");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdOthermatch");
                $mdOthermatch = null;
        }

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                $isodate = $validate->transformDate($date);
                if( !$validate->checkDate($isodate) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Datum ");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                $isotime = $validate->transformTime($time);
                if( !$validate->checkTime($isotime) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige Uhrzeit");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( !($validate->countString($home)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Heimmannschaft'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !($validate->countString($guest)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Gastmannschaft'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( !empty($head) && !($validate->countString($head)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Überschrift'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !empty($head) && !($validate->countString($text)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiger 'Text'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                $sport = new Sport($db);
                if( !$sport->load($sportid) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige Sportart");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        $permcheck = new Permcheck($db, $error);

                        switch($job)
                        {
                                case "create":
                                        if( $permcheck->hasUserOthermatchCreatePermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $othermatch = new Othermatch($db);
                                        if(!$othermatch->create($isodate, $isotime, $home, $guest, $head, $text, $sportid))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spiel wurde eingetragen");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        if( $permcheck->hasUserOthermatchModifyPermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdOthermatch->setDB($db);
                                        if(!$mdOthermatch->modify($isodate, $isotime, $home, $guest, $head, $text, $sportid)) {
                                                //echo "FEHLER";
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        }
                                        $t->set_var("SUCCESS_TEXT", "Spiel wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        if( $permcheck->hasUserOthermatchDeletePermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdOthermatch->setDB($db);
                                        if(!$mdOthermatch->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spiel wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;

                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                // Sportliste erstellen
                $sportIterator = new SportIterator($db);
                $sportIterator->createIterator();

                if( !is_object($mdOthermatch) )
                // new
                {
                        while($sportIterator->hasNext())
                        {
                            $sport = $sportIterator->next();
                            $t->set_var(array("OM_SPORTID" => $sport->getID(),
                                              "OM_SPORTNAME" => $sport->getName(),
                                              "OM_SPORTCHECKED" => $sport->getID()==$sportid?" selected":""
                                              ) );
                            $t->parse("sportidI", "sportid", true);
                        }

                        $t->set_var(array("OM_HEADER" => "Neues Spiel eintragen",
                                          "OM_DATE" => $date,
                                          "OM_TIME" => $time,
                                          "OM_HOME" => $home,
                                          "OM_GUEST" => $guest,
                                          "OM_HEAD" => $head,
                                          "OM_TEXT" => $text
                                          ) );
                        $t->parse("othermatchI", "othermatch", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit
                {
                        while($sportIterator->hasNext())
                        {
                            $sport = $sportIterator->next();
                            $t->set_var(array("OM_SPORTID" => $sport->getID(),
                                              "OM_SPORTNAME" => $sport->getName(),
                                              "OM_SPORTCHECKED" => isset($sportid)?($sport->getID()==$sportid?" selected":""):($sport->getID()==$mdOthermatch->getSportID()?" selected":"")
                                              ) );
                            $t->parse("sportidI", "sportid", true);
                        }

                        $t->set_var(array("OM_HEADER" => "Spiel bearbeiten",
                                          "OM_DATE" => isset($date)?$date:$mdOthermatch->getDateEdit(),
                                          "OM_TIME" => isset($time)?$time:$mdOthermatch->getTime(),
                                          "OM_HOME" => isset($home)?$home:$mdOthermatch->getHome(),
                                          "OM_GUEST" => isset($guest)?$guest:$mdOthermatch->getGuest(),
                                          "OM_HEAD" => isset($head)?$head:$mdOthermatch->getInfoHead(),
                                          "OM_TEXT" => isset($text)?$text:$mdOthermatch->getInfoText(),
                                          ) );
                        $t->parse("othermatchI", "othermatch", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
