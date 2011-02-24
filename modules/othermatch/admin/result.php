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
        $t->set_file(array("page" => "result.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "othermatch", "othermatchI");
        $t->set_block("page", "success", "successI");

        session_register("mdOthermatch");
        if(!empty($othermatchID))
        {
                $mdOthermatch = new Othermatch($db);
                if(!$mdOthermatch->load($othermatchID))
                        session_unregister("mdOthermatch");
        }

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();

                if( !empty($homeresult) && !empty($guestresult) && !(is_numeric($homeresult)) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Heimergebnis $homeresult");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !empty($homeresult) && !empty($guestresult) && !(is_numeric($guestresult)) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Gastergebnis $guestresult");
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


                if( $errors==0 )
                {
                        $permcheck = new Permcheck($db, $error);

                        switch($job)
                        {
                                case "modify":
                                        if( $permcheck->hasUserOthermatchResultPermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdOthermatch->setDB($db);
                                        if(!$mdOthermatch->result($homeresult, $guestresult, $head, $text, $canceled)) {
                                                //echo "FEHLER";
                                                $error->printErrorPage("Fehler beim Eintragen aufgetreten");
                                        }
                                        $t->set_var("SUCCESS_TEXT", "Ergebnis wurde eingetragen");
                                        $t->parse("successI", "success", true);
                                        break;

                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !is_object($mdOthermatch) )
                {
                }
                else
                {
                        $sport = new Sport($db);
                        $sport->load($mdOthermatch->getSportID());

                        $t->set_var(array("OM_HEADER" => "Ergebnis eintragen",
                                          "OM_SPORTNAME" => $sport->getName(),
                                          "OM_DATE" => $mdOthermatch->getDateEdit(),
                                          "OM_TIME" => $mdOthermatch->getTime(),
                                          "OM_HOME" => $mdOthermatch->getHome(),
                                          "OM_GUEST" => $mdOthermatch->getGuest(),
                                          "OM_HOMERESULT" => isset($homeresult)?$homeresult:$mdOthermatch->getHomeResult(),
                                          "OM_GUESTRESULT" => isset($guestresult)?$guestresult:$mdOthermatch->getGuestResult(),
                                          "OM_CANCELED" => isset($canceled)?($canceled=='yes'?" checked":""):($mdOthermatch->getCanceled()=='yes'?" checked":""),
                                          "OM_HEAD" => $mdOthermatch->getReportHead(),
                                          "OM_TEXT" => $mdOthermatch->getReportText(),
                                          ) );
                        $t->parse("othermatchI", "othermatch", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
