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
        include("classMatchReport.php");
        include("classTemplate.php");

        include("auth.php");

        session_register("mdMatchReport");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "matchReport.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "matchreport", "matchreportI");
        $t->set_block("matchreport", "createbuttons", "createbuttonsI");
        $t->set_block("matchreport", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");


        if(!empty($number))
        {
                $mdMatchReport = new MatchReport($db);
                $mdMatchReport->init($lmLeagueInSeason, $lmDayOfMatch);
                $loaded = $mdMatchReport->load($number);
        }

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                if( !($validate->countString($head)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Überschrift'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !($validate->countString($text)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiger 'Text'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        switch($job)
                        {
                                case "create":
                                        $mdMatchReport->setDB($db);
                                        if(!$mdMatchReport->create($head, $text))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spielbericht wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $mdMatchReport->setDB($db);
                                        if(!$mdMatchReport->modify($head, $text))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Spielbericht wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $mdMatchReport->setDB($db);
                                        if(!$mdMatchReport->delete())
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
                        $t->set_var(array("HEAD" => $head,
                                          "TEXT" => $text ) );
                        $t->parse("matchreportI", "matchreport", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                {
                        $t->set_var(array("HEAD" => isset($head)?$head:$mdMatchReport->getHead(),
                                          "TEXT" => isset($text)?$text:$mdMatchReport->getText() ) );
                        $t->parse("matchreportI", "matchreport", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
