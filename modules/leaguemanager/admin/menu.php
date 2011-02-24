<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classLeagueInSeason.php");
        include("classTeamInLeagueInSeason.php");
        include("classOwnTeamInLeagueInSeason.php");
        include("classDayOfMatch.php");
        include("classMatchOfLeague.php");
        include("classTemplate.php");
        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "menu.ihtml"));
        $t->set_block("page", "league", "leagueI");
        $t->set_block("league", "select_season", "select_seasonI");
        $t->set_block("select_season", "select_season_option", "select_season_optionI");
        $t->set_block("league", "select_sport", "select_sportI");
        $t->set_block("select_sport", "select_sport_option", "select_sport_optionI");
        $t->set_block("league", "select_league", "select_leagueI");
        $t->set_block("select_league", "select_league_option", "select_league_optionI");
        $t->set_block("league", "leagueinseason", "leagueinseasonI");
        $t->set_block("league", "teamsinleagueinseason", "teamsinleagueinseasonI");

        $t->set_block("league", "dayofmatch", "dayofmatchI");
        $t->set_block("dayofmatch", "select_dom", "select_domI");
        $t->set_block("select_dom", "select_dom_option", "select_dom_optionI");

        $t->set_block("league", "match", "matchI");


/*
        $t->set_block("league", "league_select_sport", "league_select_sport");
        $t->set_block("league", "league_select_league", "league_select_league");
        $t->set_block("league", "league_leagueinseason", "league_leagueinseason");
        $t->set_block("league", "league_teamsinleagueinseason", "league_teamsinleagueinseason");
        $t->set_block("league", "league_dayofmatch", "league_dayofmatch");
        $t->set_block("league", "league_matchofleague_date", "league_matchofleague_date");
        $t->set_block("league", "league_matchofleague_result", "league_matchofleague_result");
*/
        // register variables
        session_register("lmSeason");
        session_register("lmSport");
        session_register("lmLeague");
        session_register("lmLeagueInSeason");
        session_register("lmTeamInLeagueInSeasonList");
        session_register("lmOwnTeamInLeagueInSeasonList");
        session_register("lmDayOfMatchList");
        session_register("lmDayOfMatch");

        // preset variables
        if(!isset($lmSeason))
                $lmSeason = new Season($db);
        if(!isset($lmSport))
                $lmSport = new Sport($db);
        if(!isset($lmLeague))
                $lmLeague = new League($db);
        if(!isset($lmLeagueInSeason))
                $lmLeagueInSeason = new LeagueInSeason($db);
        if(!isset($lmTeamInLeagueInSeasonList))
                $lmTeamInLeagueInSeasonList = new TeamInLeagueInSeasonList($db);
        if(!isset($lmOwnTeamInLeagueInSeasonList))
                $lmOwnTeamInLeagueInSeasonList = new OwnTeamInLeagueInSeasonList($db);
        if(!isset($lmDayOfMatchList))
                $lmDayOfMatchList = new DayOfMatchList($db);
        if(!isset($lmDayOfMatch))
                $lmDayOfMatch = new DayOfMatch($db);

        // test if season has changed
        if( isset($seasonID) &&  ($lmSeason->getID() != $seasonID) )
        {
                if(empty($seasonID))
                        $lmSeason->reset();
                else
                {
                        $lmSeason->setDB($db);
                        if(!$lmSeason->load($seasonID))
                                $error->printErrorPage("Saison ".$seasonID." nicht gefunden.");
                }
                $sportID = "";
        }

        // test if sport has changed
        if( isset($sportID) && ($lmSport->getID() != $sportID) )
        {
                if(empty($sportID))
                        $lmSport->reset();
                else
                {
                        $lmSport->setDB($db);
                        if(!$lmSport->load($sportID))
                                $error->printErrorPage("Sport ".$sportID." nicht gefunden.");
                }
                $leagueID = "";
        }

        // test if league has changed
        if( isset($leagueID) && ($lmLeague->getID() != $leagueID) )
        {
                if(empty($leagueID))
                        $lmLeague->reset();
                else
                {
                        $lmLeague->setDB($db);
                        if(!$lmLeague->load($leagueID))
                                $error->printErrorPage("Liga ".$leagueID." nicht gefunden.");
                }
                $domNumber = "";
        }

        // test if dayOfMatch has changed
        if( isset($domNumber) && ($lmDayOfMatch->getNumber() != $domNumber) )
        {
                if(empty($domNumber))
                        $lmDayOfMatch->reset();
                else
                {
                        $lmDayOfMatch->setDB($db);
                        $lmDayOfMatch->load($seasonID, $sportID, $leagueID, $domNumber);
                        // if(!$lmDayOfMatch->load($seasonID, $sportID, $leagueID, $domNumber))
                        //         $error->printErrorPage("Spieltag ".$dayOfMatchNumber." nicht gefunden.");
                }
        }

        // select season
        $seasonIterator = new SeasonIterator($db);
        $seasonIterator->createIterator();
        while($seasonIterator->hasNext())
        {
                $season = $seasonIterator->next();
                $t->set_var(array("SELECT_SEASON_VALUE" => $season->getID(),
                                  "SELECT_SEASON_NAME" => $season->getName(),
                                  "SELECT_SEASON_SELECTED" => $lmSeason->getID()==$season->getID()?"selected":"" ) );
                $t->parse("select_season_optionI", "select_season_option", true);
        }
        $t->parse("select_seasonI", "select_season", true);

        // select sport
        if($lmSeason->getID() != 0)
        {
                $sportIterator = new SportIterator($db);
                $sportIterator->createIterator();
                while($sportIterator->hasNext())
                {
                        $sport = $sportIterator->next();
                        $t->set_var(array("SELECT_SPORT_VALUE" => $sport->getID(),
                                          "SELECT_SPORT_NAME" => $sport->getName(),
                                          "SELECT_SPORT_SELECTED" => $lmSport->getID()==$sport->getID()?"selected":"" ) );
                        $t->parse("select_sport_optionI", "select_sport_option", true);
                }
                $t->parse("select_sportI", "select_sport", true);
        }

        // select league
        if($lmSport->getID() != 0)
        {
                $leagueIterator = new LeagueIterator($db);
                $leagueIterator->createSportIterator($lmSport);
                while($leagueIterator->hasNext())
                {
                        $league = $leagueIterator->next();
                        $t->set_var(array("SELECT_LEAGUE_VALUE" => $league->getID(),
                                          "SELECT_LEAGUE_NAME" => $league->getName(),
                                          "SELECT_LEAGUE_SELECTED" => $lmLeague->getID()==$league->getID()?"selected":"" ) );
                        $t->parse("select_league_optionI", "select_league_option", true);
                }
                $t->parse("select_leagueI", "select_league", true);
        }

        // leageue in season
        if($lmLeague->getID() != 0)
        {
                $lmLeagueInSeason->setDB($db);
                if($lmLeagueInSeason->load($lmSeason, $lmLeague))
                {
                        $t->set_var(array("LIS_TEXT" => sprintf("Die %s im Bereich %s der Saison %s ist eingerichtet.", $lmLeague->getName(), $lmSport->getName(), $lmSeason->getName()),
                                          "LIS_BUTTON_VALUE" => "ändern" ) );
                }
                else
                {
                        $t->set_var(array("LIS_TEXT" => sprintf("In der Saison %s ist im Bereich %s noch keine %s eingerichtet.", $lmSeason->getName(), $lmSport->getName(), $lmLeague->getName()),
                                          "LIS_BUTTON_VALUE" => "einrichten" ) );
                        $lmLeagueInSeason->reset();
                }
                $t->parse("leagueinseasonI", "leagueinseason", true);
        }
        else
        {
                $lmLeagueInSeason->reset();
        }

        // teams in league in season
        if( ($lmLeagueInSeason->getSeasonID() != 0) && ($lmLeagueInSeason->getLeagueID() != 0) && ($lmLeagueInSeason->getSportID() != 0) )
        {
                $lmTeamInLeagueInSeasonList->setDB($db);
                $lmTeamInLeagueInSeasonList->init($lmLeagueInSeason);

                if($lmTeamInLeagueInSeasonList->getNumberOfTeamsInLeagueInSeason() == 0)
                {
                        $t->set_var(array("TILIS_TEXT" => "Die Mannschaften sind noch nicht erfasst",
                                          "TILIS_BUTTON_VALUE" => "Mannschaften erfassen" ) );
                }
                else if($lmTeamInLeagueInSeasonList->getNumberOfTeamsInLeagueInSeason() == $lmLeagueInSeason->getNumberOfTeams())
                {
                        $t->set_var(array("TILIS_TEXT" => "Die Mannschaften sind erfasst",
                                          "TILIS_BUTTON_VALUE" => "ändern" ) );
                }
                else
                {
                        $t->set_var(array("TILIS_TEXT" => "Von ".$lmLeagueInSeason->getNumberOfTeams()." Mannschaften sind ".$lmTeamInLeagueInSeasonList->getNumberOfTeamsInLeagueInSeason()." erfasst.",
                                          "TILIS_BUTTON_VALUE" => "Mannschaften erfassen" ) );
                }
                $t->set_var("OTILIS_BUTTON_VALUE", "Eigene Mannschaften erfassen");
                $t->parse("teamsinleagueinseasonI", "teamsinleagueinseason", true);

        }
        else
        {
                $lmTeamInLeagueInSeasonList = new TeamInLeagueInSeasonList($db);
        }

        // day of match
        if( ($lmLeagueInSeason->getSeasonID() != 0) && ($lmLeagueInSeason->getLeagueID() != 0) && ($lmLeagueInSeason->getSportID() != 0) && ($lmTeamInLeagueInSeasonList->getNumberOfTeamsInLeagueInSeason() == $lmLeagueInSeason->getNumberOfTeams()) )
        {
                if($lmLeagueInSeason->getHasDaysOfMatch() == "true")
                {
                        // day of match
                        $lmDayOfMatchList->setDB($db);
                        $lmDayOfMatchList->init($lmLeagueInSeason);

                        if($lmLeagueInSeason->getIsDoubleSeason() == "true")
                                $double = 2;
                        else
                                $double = 1;

                        if($lmDayOfMatchList->getNumberOfRegisteredDays() == 0)
                        {
                                $t->set_var(array("DOM_TEXT" => "Die Spieltage sind noch nicht erfasst",
                                                  "DOM_BUTTON_VALUE" => "Spieltage erfassen" ) );
                        }
                        else if($lmDayOfMatchList->getNumberOfRegisteredDays() == ( ($lmLeagueInSeason->getNumberOfTeams() - 1 + ($lmLeagueInSeason->getNumberOfTeams() % 2)) * 2 * $double ) )
                        {
                                $t->set_var(array("DOM_TEXT" => "Die Spieltage sind erfasst",
                                                  "DOM_BUTTON_VALUE" => "ändern" ) );
                        }
                        else
                        {
                                $t->set_var(array("DOM_TEXT" => "Von ".( ($lmLeagueInSeason->getNumberOfTeams() - 1 + ($lmLeagueInSeason->getNumberOfTeams() % 2)) * 2 * $double)." Spieltagen sind ".$lmDayOfMatchList->getNumberOfRegisteredDays()." erfasst.",
                                                  "DOM_BUTTON_VALUE" => "Spieltage erfassen" ) );
                        }
                        $t->parse("dayofmatchI", "dayofmatch", true);

                        // select day of match
                        if($lmDayOfMatchList->getNumberOfRegisteredDays() > 0)
                        {
                                $lmDayOfMatchList->loadList();
                                while($lmDayOfMatchList->hasNext())
                                {
                                        $dom = $lmDayOfMatchList->next();
                                        $t->set_var(array("SELECT_DOM_VALUE" => $dom->getNumber(),
                                                          "SELECT_DOM_NAME" => $dom->getNumber().". Spieltag (".$dom->getDate().")",
                                                          "SELECT_DOM_SELECTED" => $lmDayOfMatch->getNumber()==$dom->getNumber()?"selected":"" ) );
                                        $t->parse("select_dom_optionI", "select_dom_option", true);
                                }
                                $t->parse("select_domI", "select_dom", true);
                        }

                        if( $lmDayOfMatch->getNumber() > 0 )
                        {
                                $matchList = new MatchOfLeagueList($db);
                                $matchList->init($lmLeagueInSeason, $lmDayOfMatch);
                                $matchList->loadEditList();
                                $matchList->loadList();

                                $t->set_var(array("REGISTER_ACT" => $matchList->getNumberOfRegisteredMatches(),
                                                  "REGISTER_TARGET" => $matchList->getNumber(),
                                                  "RESULT_ACT" => $matchList->getNumberOfRegisteredResults(),
                                                  "RESULT_TARGET" => $matchList->getNumber(),
                                                    ));

                                $t->parse("matchI", "match", true);
                        }
                }
                else
                {
                        // day of match
                        $lmDayOfMatchList->setDB($db);
                        $lmDayOfMatchList->init($lmLeagueInSeason);

                        $t->parse("dayofmatchI", "dayofmatch", true);

                        // select day of match
                        $lmDayOfMatchList->loadList();
                        while($lmDayOfMatchList->hasNext())
                        {
                                $dom = $lmDayOfMatchList->next();
                                $t->set_var(array("SELECT_DOM_VALUE" => $dom->getNumber(),
                                                          "SELECT_DOM_NAME" => "Spielwoche ".$dom->getDate(),
                                                          "SELECT_DOM_SELECTED" => $domNumber==$dom->getNumber()?"selected":"" ) );
                                $t->parse("select_dom_optionI", "select_dom_option", true);

                        }
                        // new week
                        if(is_object($dom))
                        {
                                $t->set_var(array("SELECT_DOM_VALUE" => $dom->getNumber()+1,
                                                  "SELECT_DOM_NAME" => "neue Spielwoche",
                                                  "SELECT_DOM_SELECTED" => $domNumber==$dom->getNumber()+1?"selected":"" ) );
                                $t->parse("select_dom_optionI", "select_dom_option", true);
                        }
                        else
                        {
                                $t->set_var(array("SELECT_DOM_VALUE" => "1",
                                                  "SELECT_DOM_NAME" => "neue Spielwoche",
                                                  "SELECT_DOM_SELECTED" => "" ) );
                                $t->parse("select_dom_optionI", "select_dom_option", true);
                        }

                        $t->parse("select_domI", "select_dom", true);

                        if( $domNumber > 0 )
                        {
                                $matchList = new MatchOfLeagueList($db);
                                $matchList->init($lmLeagueInSeason, $lmDayOfMatch);
                                $matchList->loadEditList();
                                $matchList->loadList();

                                $t->set_var(array("REGISTER_ACT" => $matchList->getNumberOfRegisteredMatches(),
                                                  "REGISTER_TARGET" => $matchList->getNumber(),
                                                  "RESULT_ACT" => $matchList->getNumberOfRegisteredResults(),
                                                  "RESULT_TARGET" => $matchList->getNumber(),
                                                    ));

                                $t->parse("matchI", "match", true);
                        }
                }
        }
        else
        {
                $lmDayOfMatchList = new DayOfMatchList($db);
        }














/*



        // test if season has changed
        if( isset($seasonID) &&  ($lmSeason->getID() != $seasonID) )
        {
                if(empty($seasonID))
                        $lmSeason->reset();
                else
                {
                        $lmSeason->setDB($db);
                        if(!$lmSeason->load($seasonID))
                                $error->printErrorText("Saison ".$seasonID." nicht gefunden.");
                }
                $sportID = "";
        }

        // test if sport has changed
        if( isset($sportID) && ($lmSport->getID() != $sportID) )
        {
                if(empty($sportID))
                        $lmSport->reset();
                else
                {
                        $lmSport->setDB($db);
                        if(!$lmSport->load($sportID))
                                $error->printErrorText("Sport ".$sportID." nicht gefunden.");
                }
                $leagueID = "";
        }

        // test if league has changed
        if( isset($leagueID) && ($lmLeague->getID() != $leagueID) )
        {
                if(empty($leagueID))
                        $lmLeague->reset();
                else
                {
                        $lmLeague->setDB($db);
                        if(!$lmLeague->load($leagueID))
                                $error->printErrorText("Liga ".$leagueID." nicht gefunden.");
                }
                $dayOfMatchNumber = "";
        }

        // test if dayOfMatch has changed
        if( isset($dayOfMatchNumber) && ($lmDayOfMatch->getNumber() != $dayOfMatchNumber) )
        {
                if(empty($dayOfMatchNumber))
                        $lmDayOfMatch->reset();
                else
                {
                        $lmDayOfMatch->setDB($db);
                        if(!$lmDayOfMatch->load($seasonID, $leagueID, $sportID, $dayOfMatchNumber))
                                $error->printErrorText("Spieltag ".$dayOfMatchNumber." nicht gefunden.");
                }
        }




        // select season
        $seasonList = new SeasonList($db);
        $seasonList->createList();
        $gui->list2select("Saison", "season", $seasonList, $lmSeason);


        // select sport
        if($lmSeason->getID() != 0)
        {
                $sportList = new SportList($db);
                $sportList->createList();
                $gui->list2select("Sportart", "sport", $sportList, $lmSport);
        }

        // select league
        if($lmSport->getID() != 0)
        {
                $leagueList = new LeagueList($db, $lmSport);
                $leagueList->createList();
                $gui->list2select("Liga", "league", $leagueList, $lmLeague);
        }

        // leageue in season
        if($lmLeague->getID() != 0)
        {
                $lmLeagueInSeason->setDB($db);

                if($lmLeagueInSeason->load($lmSeason, $lmLeague))
                {
                        $gui->printTextBox(sprintf("Die %s im Bereich %s der Saison %s ist eingerichtet.", $lmLeague->getName(), $lmSport->getName(), $lmSeason->getName()));
                        $gui->printButtonEdit("ändern", "leagueInSeason");

                }
                else
                {
                        $gui->printTextBox(sprintf("In der Saison %s ist im Bereich %s noch keine %s eingerichtet.", $lmSeason->getName(), $lmSport->getName(), $lmLeague->getName()));
                        $gui->printButtonNew("einrichten", "leagueInSeason");
                }
                $gui->printSpacer();
        }
        else
        {
                $lmLeagueInSeason->reset();
        }

        // teams in league in season
        if( ($lmLeagueInSeason->getSeasonID() != 0) && ($lmLeagueInSeason->getLeagueID() != 0) && ($lmLeagueInSeason->getSportID() != 0) )
        {
                $lmTeamsInLeagueInSeason->setDB($db);
                $lmTeamsInLeagueInSeason->load($lmLeagueInSeason);

                if($lmTeamsInLeagueInSeason->getNumber() == 0)
                {
                        $gui->printTextBox("Die Mannschaften sind noch nicht erfasst");
                        $gui->printButtonNew("Mannschaften erfassen", "teamsInLeagueInSeason");
                }
                else if($lmTeamsInLeagueInSeason->getNumber() == $lmLeagueInSeason->getNumberOfTeams())
                {
                        $gui->printTextBox("Die Mannschaften sind erfasst");
                        $gui->printButtonNew("ändern", "teamsInLeagueInSeason");
                }
                else
                {
                        $gui->printTextBox("Von ".$lmLeagueInSeason->getNumberOfTeams()." Mannschaften sind ".$lmTeamsInLeagueInSeason->getNumber()."erfasst.");
                        $gui->printButtonNew("Mannschaften erfassen", "teamsInLeagueInSeason");
                }
                $gui->printSpacer();
        }
        else
        {
                $lmTeamsInLeagueInSeason->reset();
        }

echo $lmLeagueInSeason->getHasDaysOfMatch();

        // with days of match
        if($lmLeagueInSeason->getHasDaysOfMatch() == "true")
        {
                // days of match
                if( ($lmLeagueInSeason->getSeasonID() != 0) && ($lmLeagueInSeason->getLeagueID() != 0) && ($lmLeagueInSeason->getSportID() != 0) )
                {
                        $dayOfMatchList = new DayOfMatchList($db);
                        $dayOfMatchList->createList($lmSeason, $lmLeague, $lmSport);

                        if($lmLeagueInSeason->getIsDoubleSeason() == "true")
                                $double = 2;
                        else
                                $double = 1;

                        if($dayOfMatchList->getNumberOfRegisteredDays() == 0)
                        {
                                $gui->printTextBox("Die Spieltage sind noch nicht erfasst");
                                $gui->printButtonNew("Spieltage erfassen", "dayOfMatchList");
                        }
                        else if($dayOfMatchList->getNumberOfRegisteredDays() == ( ($lmLeagueInSeason->getNumberOfTeams() - 1) * 2 * $double ) )
                        {
                                $gui->printTextBox("Die Spieltage sind erfasst");
                                $gui->printButtonNew("ändern", "dayOfMatchList");
                        }
                        else
                        {
                                $gui->printTextBox("Von ".( ($lmLeagueInSeason->getNumberOfTeams() - 1) * 2 * $double)." Spieltagen sind ".$dayOfMatchList->getNumberOfRegisteredDays()." erfasst.");
                                $gui->printButtonNew("Spieltage erfassen", "dayOfMatchList");
                        }
                }

                $gui->printSpacer();

                // matches
                if( ($dayOfMatchList->getNumberOfRegisteredDays() > 0) && ($lmTeamsInLeagueInSeason->getNumber() == $lmLeagueInSeason->getNumberOfTeams()) )
                {
                        $gui->list2domselect("Für", "dayOfMatch", $dayOfMatchList, $lmDayOfMatch);
                        $gui->printButtonNew("Spiel erfassen/ändern", "dayOfMatch");
                        $gui->printButtonNew("Ergebnisse erfassen/ändern", "dayOfMatchResults");


                }
                else
                {
                }


        }
        // withOUT days of match
        else
        {
                if( ($lmLeagueInSeason->getSeasonID() != 0) && ($lmLeagueInSeason->getLeagueID() != 0) && ($lmLeagueInSeason->getSportID() != 0) && ($lmTeamsInLeagueInSeason->getNumber() == $lmLeagueInSeason->getNumberOfTeams()) )
                {
                        $gui->printButtonNew("Spiele erfassen/ändern", "matches");
                        $gui->printButtonNew("Ergebnisse erfassen/ändern", "results");
                }
        }
*/

        $t->parse("leagueI", "league", true);
        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");

?>
