<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classTeam.php");
        include("classLeagueInSeason.php");
        include("classTeamInLeagueInSeason.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "teamsInLeagueInSeason.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "tilis", "tilisI");
        $t->set_block("tilis", "team", "teamI");
        $t->set_block("page", "success", "successI");

        if(isset($job))
        // perform action
        {
                $errors = 0;
                // validate fields
                if( sizeof($teams) > $lmLeagueInSeason->getNumberOfTeams() )
                {
                        $t->set_var("ERROR_TEXT", "Zuviele Mannschaften ausgewählt");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        $lmTeamInLeagueInSeasonList->setDB($db);
                        $lmTeamInLeagueInSeasonList->init($lmLeagueInSeason);

                        if(!$lmTeamInLeagueInSeasonList->modifyList($teams))
                                $error->printErrorPage("Fehler bei der Zuordnung aufgetreten");
                        $t->set_var("SUCCESS_TEXT", "Mannschaften wurden erfolgreich zugeordnet");
                        $t->parse("successI", "success", true);
                }
        }

        if(!isset($job))
        // print formular
        {
                // Teamliste erstellen
                $lmTeamInLeagueInSeasonList->setDB($db);
                $lmTeamInLeagueInSeasonList->init($lmLeagueInSeason);
                $lmTeamInLeagueInSeasonList->loadEditList();

                while($lmTeamInLeagueInSeasonList->hasNext())
                {
                        $tilis = $lmTeamInLeagueInSeasonList->next();
                        $t->set_var(array("TEAM_ID" => $tilis->getID(),
                                          "TEAM_NAME" => $tilis->getName(),
                                          "TEAM_CHECKED" => isset($teams)?($teams[$tilis->getID()]):($tilis->getIsInLeagueInSeason()?"checked":"") ) );
                        $t->parse("teamI", "team", true);
                }
                $t->parse("tilisI", "tilis", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
