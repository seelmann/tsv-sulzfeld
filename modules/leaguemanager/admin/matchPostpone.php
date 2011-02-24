<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classTeam.php");
        include("classLeagueInSeason.php");
        include("classTeamInLeagueInSeason.php");
        include("classDayOfMatch.php");
        include("classMatchOfLeague.php");
        include("classMatchPostpone.php");
        include("classTemplate.php");

        include("auth.php");

        session_register("mdMatchPostpone");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "matchPostpone.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "matchpostpone", "matchpostponeI");
        $t->set_block("matchpostpone", "createbuttons", "createbuttonsI");
        $t->set_block("matchpostpone", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");


        if(!empty($number))
        {
                $mdMatchPostpone = new MatchPostpone($db);
                $mdMatchPostpone->init($lmLeagueInSeason, $lmDayOfMatch);
                $loaded = $mdMatchPostpone->load($number);
        }

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                if(!empty($newdate))
                {
                        $newisodate = $validate->transformDate($newdate);
                        if( !$validate->checkDate($newisodate) )
                        {
                                $t->set_var("ERROR_TEXT", "Ungültiges Datum in Spiel ".$i);
                                $t->parse("errorI", "error", true);
                                unset($job);
                                $errors++;
                        }
                }

                if(!empty($newtime))
                {
                        $newisotime = $validate->transformTime($newtime);
                        if( !$validate->checkTime($newisotime) )
                        {
                                $t->set_var("ERROR_TEXT", "Ungültige Uhrzeit in Spiel ".$i);
                                $t->parse("errorI", "error", true);
                                unset($job);
                                $errors++;
                        }
                }

                if( $errors==0 )
                {
                        switch($job)
                        {
                                case "create":
                                        $mdMatchPostpone->setDB($db);
                                        if(!$mdMatchPostpone->create($reason, $newisodate, $newisotime))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spielbericht wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $mdMatchPostpone->setDB($db);
                                        if(!$mdMatchPostpone->modify($reason, $newisodate, $newisotime))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spielbericht wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $mdMatchPostpone->setDB($db);
                                        if(!$mdMatchPostpone->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spielbericht wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !$loaded )
                {
                        $t->set_var(array("REASON" => $reason,
                                          "NEWDATE" => $newdate,
                                          "NEWTIME" => $newtime ) );
                        $t->parse("matchpostponeI", "matchpostpone", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                {
                        $t->set_var(array("REASON" => isset($reason)?$reason:$mdMatchPostpone->getReason(),
                                          "NEWDATE" => isset($newdate)?$newdate:$mdMatchPostpone->getNewdate(),
                                          "NEWTIME" => isset($newtime)?$newtime:$mdMatchPostpone->getNewtime() ) );
                        $t->parse("matchpostponeI", "matchpostpone", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
