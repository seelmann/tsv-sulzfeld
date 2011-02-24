<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classLeagueInSeason.php");
        include("classDayOfMatch.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "dayOfMatchList.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "season", "seasonI");
        $t->set_block("season", "dom", "domI");
        $t->set_block("page", "success", "successI");


        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                for($i=1; $i<=sizeof($dates); $i++)
                {
                        if(!empty($dates[$i]))
                        {
                                $isodates[$i] = $validate->transformDate($dates[$i]);
                                if( !$validate->checkDate($isodates[$i]) )
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültiges Datum in Spieltag ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                        if(!empty($times[$i]))
                        {
                                $isotimes[$i] = $validate->transformTime($times[$i]);
                                if( !$validate->checkTime($isotimes[$i]) )
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültige Uhrzeit in Spieltag ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                }

                if( $errors==0 )
                {
                        $lmDayOfMatchList->setDB($db);
                        $lmDayOfMatchList->init($lmLeagueInSeason);

                        if(!$lmDayOfMatchList->modifyList($isodates, $isotimes))
                                $error->printErrorPage("Fehler bei der Zuordnung aufgetreten");
                        $t->set_var("SUCCESS_TEXT", "Spieltage wurden erfolgreich eingetragen");
                        $t->parse("successI", "success", true);
                }
        }

        if(!isset($job))
        // print formular
        {
                // Teamliste erstellen
                $lmDayOfMatchList->setDB($db);
                $lmDayOfMatchList->init($lmLeagueInSeason);
                $lmDayOfMatchList->loadEditList();

                while($lmDayOfMatchList->hasNext())
                {
                        $day = $lmDayOfMatchList->next();
                        $t->set_var(array("DOM_NUMBER" => $day->getNumber(),
                                          "DOM_DATE" => isset($dates[$day->getNumber()])?$dates[$day->getNumber()]:$day->getDate(),
                                          "DOM_TIME" => isset($times[$day->getNumber()])?$times[$day->getNumber()]:$day->getTime() ) );
                        $t->parse("domI", "dom", true);
                }
                $t->parse("seasonI", "season", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");

?>
