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
        include("classMatchInfo.php");
        include("classTemplate.php");

        include("auth.php");

        session_register("mdMatchInfo");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "matchInfo.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "matchinfo", "matchinfoI");
        $t->set_block("matchinfo", "createbuttons", "createbuttonsI");
        $t->set_block("matchinfo", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");


        if(!empty($number))
        {
                $mdMatchInfo = new MatchInfo($db);
                $mdMatchInfo->init($lmLeagueInSeason, $lmDayOfMatch);
                $loaded = $mdMatchInfo->load($number);
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
                                        $mdMatchInfo->setDB($db);
                                        if(!$mdMatchInfo->create($head, $text))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Zusatzinfo wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $mdMatchInfo->setDB($db);
                                        if(!$mdMatchInfo->modify($head, $text))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Zusatzinfo wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $mdMatchInfo->setDB($db);
                                        if(!$mdMatchInfo->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Zusatzinfo wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !$loaded )
                // new
                {
                        $t->set_var(array("HEAD" => $head,
                                          "TEXT" => $text ) );
                        $t->parse("matchinfoI", "matchinfo", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit
                {
                        $t->set_var(array("HEAD" => isset($head)?$head:$mdMatchInfo->getHead(),
                                          "TEXT" => isset($text)?$text:$mdMatchInfo->getText() ) );
                        $t->parse("matchinfoI", "matchinfo", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
