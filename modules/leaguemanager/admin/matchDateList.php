<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classTeam.php");
        include("classLeagueInSeason.php");
        include("classTeamInLeagueInSeason.php");
        include("classDayOfMatch.php");
        include("classMatchOfLeague.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "matchDateList.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "list", "listI");
        $t->set_block("list", "match", "matchI");
        $t->set_block("match", "hometeam", "hometeamI");
        $t->set_block("match", "guestteam", "guestteamI");
        $t->set_block("page", "success", "successI");

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                $tilis = new TeamInLeagueInSeasonList($db);
                $tilis->init($lmLeagueInSeason);
                $tilis->loadList();
                // for($i=1; $i<=sizeof($dates); $i++)
                foreach($dates as $i => $x)
                {
                        if(!empty($dates[$i]))
                        {
                                $isodates[$i] = $validate->transformDate($dates[$i]);
                                if( !$validate->checkDate($isodates[$i]) )
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültiges Datum in Spiel ".$i);
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
                                        $t->set_var("ERROR_TEXT", "Ungültige Uhrzeit in Spiel ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                        if(!empty($hometeams[$i]) && ($hometeams[$i] != 0))
                        {
                                if( !$tilis->isInList($hometeams[$i]))
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültige Heimmannschaft in Spiel ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                        if(!empty($guestteams[$i]) && ($guestteams[$i] != 0))
                        {
                                if( !$tilis->isInList($guestteams[$i]))
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültige Gastmannschaft in Spiel ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                }

                if( $errors==0 )
                {
                        $matchList = new MatchOfLeagueList($db);
                        $matchList->init($lmLeagueInSeason, $lmDayOfMatch);

                        if(!$matchList->modifyList($isodates, $isotimes, $hometeams, $guestteams))
                                $error->printErrorPage("Fehler bei der Zuordnung aufgetreten");
                        $t->set_var("SUCCESS_TEXT", "Spiele wurden erfolgreich eingetragen");
                        $t->parse("successI", "success", true);
                }

        }

        if(!isset($job))
        // print formular
        {
                // Teamliste erstellen
                $matchList = new MatchOfLeagueList($db);
                $matchList->init($lmLeagueInSeason, $lmDayOfMatch);
                $matchList->loadEditList();

                $teamList = new TeamInLeagueInSeasonList($db);
                $teamList->init($lmLeagueInSeason);
                $teamList->loadList();

                while($matchList->hasNext())
                {
                        $match = $matchList->next();
                        $teamList->reset();
                        $append = false;
                        while($teamList->hasNext())
                        {
                                $team = $teamList->next();
                                $t->set_var(array("HOME_TEAM_ID" => $team->getID(),
                                                  "HOME_TEAM_NAME" => $team->getName(),
                                                  "HOME_TEAM_SELECTED" => isset($hometeams[$match->getNumber()])?($team->getID()==$hometeams[$match->getNumber()]?"selected":""):($team->getID()==$match->getHomeTeamID()?"selected":"") ) );
                                $t->parse("hometeamI", "hometeam", $append);
                                $append = true;
                        }
                        $teamList->reset();
                        $append = false;
                        while($teamList->hasNext())
                        {
                                $team = $teamList->next();
                                $t->set_var(array("GUEST_TEAM_ID" => $team->getID(),
                                                  "GUEST_TEAM_NAME" => $team->getName(),
                                                  "GUEST_TEAM_SELECTED" => isset($guestteams[$match->getNumber()])?($team->getID()==$guestteams[$match->getNumber()]?"selected":""):($team->getID()==$match->getGuestTeamID()?"selected":"")  ) );
                                $t->parse("guestteamI", "guestteam", $append);
                                $append = true;
                        }

                        $t->set_var(array("MATCH_NUMBER" => $match->getNumber(),
                                          "MATCH_DATE" => isset($dates[$match->getNumber()])?$dates[$match->getNumber()]:( ($match->getDate()=="")?$lmDayOfMatch->getDate():$match->getDate()),
                                          "MATCH_TIME" => isset($times[$match->getNumber()])?$times[$match->getNumber()]:( ($match->getTime()=="")?$lmDayOfMatch->getTime():$match->getTime()) ));

                        $t->parse("matchI", "match", true);
                }
                $t->parse("listI", "list", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");

?>
