<?php

    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classTeam.php");
        include("classLeagueInSeason.php");
        include("classTeamInLeagueInSeason.php");
        include("classOwnTeamInLeagueInSeason.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "ownTeamsInLeagueInSeason.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "otilis", "otilisI");
        $t->set_block("otilis", "team", "teamI");
        $t->set_block("page", "success", "successI");

        if(isset($job))
        // perform action
        {
                $errors = 0;

                if( $errors==0 )
                {
                        $lmOwnTeamInLeagueInSeasonList->setDB($db);
                        $lmOwnTeamInLeagueInSeasonList->init($lmLeagueInSeason);

                        if(!$lmOwnTeamInLeagueInSeasonList->modifyList($ownteams))
                                $error->printErrorPage("Fehler bei der Zuordnung aufgetreten");
                        $t->set_var("SUCCESS_TEXT", "Eigene Mannschaften wurden erfolgreich eingetragen");
                        $t->parse("successI", "success", true);
                }
        }

        if(!isset($job))
        // print formular
        {
                // Teamliste erstellen
                $lmOwnTeamInLeagueInSeasonList->setDB($db);
                $lmOwnTeamInLeagueInSeasonList->init($lmLeagueInSeason);
                $lmOwnTeamInLeagueInSeasonList->loadEditList();

                while($lmOwnTeamInLeagueInSeasonList->hasNext())
                {
                        $otilis = $lmOwnTeamInLeagueInSeasonList->next();
                        $t->set_var(array("TEAM_ID" => $otilis->getID(),
                                          "TEAM_NAME" => $otilis->getName(),
                                          "OTEAM_NAME" => $otilis->getOwnName() ) );
                        $t->parse("teamI", "team", true);
                }
                $t->parse("otilisI", "otilis", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
