<?php
        ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classDate.php");
        include("classTemplate.php");
        include("../classPermcheck.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "date.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "date", "dateI");
        $t->set_block("date", "createbuttons", "createbuttonsI");
        $t->set_block("date", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdDate");
        if(!empty($dateID))
        {
                $mdDate = new Date($db);
                if(!$mdDate->load($dateID))
                        session_unregister("mdDate");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdDate");
                $mdDate = null;
        }

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                $isofromdate = $validate->transformDate($fromdate);
                if( !$validate->checkDate($isofromdate) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Von-Datum ");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                $isofromtime = $validate->transformTime($fromtime);
                if( !empty($fromtime) && !$validate->checkTime($isofromtime) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige Von-Uhrzeit");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                $isotodate = $validate->transformDate($todate);
                if( !empty($todate) && !$validate->checkDate($isotodate) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Bis-Datum ");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                $isototime = $validate->transformTime($totime);
                if( !empty($totime) && !$validate->checkTime($isototime) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige Bis-Uhrzeit");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( !($validate->countString($event)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Veranstaltung'");
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
                                case "create":
                                        if( $permcheck->hasUserDateCreatePermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $date = new Date($db);
                                        if(!$date->create($isofromdate, $isofromtime, $isotodate, $isototime, $event, $head, $text))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Termin wurde eingetragen");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        if( $permcheck->hasUserDateModifyPermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdDate->setDB($db);
                                        if(!$mdDate->modify($isofromdate, $isofromtime, $isotodate, $isototime, $event, $head, $text)) {
                                        echo "FEHLER";
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        }
                                        $t->set_var("SUCCESS_TEXT", "Termin wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        if( $permcheck->hasUserDateDeletePermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdDate->setDB($db);
                                        if(!$mdDate->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Termin wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !is_object($mdDate) )
                // new
                {
                        $t->set_var(array("DATE_HEADER" => "Neuen Termin eintragen",
                                          "DATE_FROMDATE" => $fromdate,
                                          "DATE_FROMTIME" => $fromtime,
                                          "DATE_TODATE" => $todate,
                                          "DATE_TOTIME" => $totime,
                                          "DATE_EVENT" => $event,
                                          "DATE_HEAD" => $head,
                                          "DATE_TEXT" => $text
                                          ) );
                        $t->parse("dateI", "date", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit
                {
                        $t->set_var(array("DATE_HEADER" => "Termin bearbeiten",
                                          "DATE_FROMDATE" => isset($fromdate)?$fromdate:$mdDate->getFromDateEdit(),
                                          "DATE_FROMTIME" => isset($fromtime)?$fromtime:$mdDate->getFromTime(),
                                          "DATE_TODATE" => isset($todate)?$todate:$mdDate->getToDateEdit(),
                                          "DATE_TOTIME" => isset($totime)?$totime:$mdDate->getToTime(),
                                          "DATE_EVENT" => isset($event)?$event:$mdDate->getEvent(),
                                          "DATE_HEAD" => isset($head)?$head:$mdDate->getHead(),
                                          "DATE_TEXT" => isset($text)?$text:$mdDate->getText()
                                           ) );
                        $t->parse("dateI", "date", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
