<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classSport.php");
        include("classLeague.php");
        include("classLeagueInSeason.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "leagueInSeason.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "lis", "lisI");
        $t->set_block("lis", "createbuttons", "createbuttonsI");
        $t->set_block("lis", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");


        if(isset($job))
        // perform action
        {
                $errors = 0;
                // validate fields
                $validate = new Validation();
                if( !$validate->isNumber($numberofteams) )
                {
                        $t->set_var("ERROR_TEXT", "'Anzahl der Mannschaften' ungültig");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !( ($double=="true") || ($double=="false") ) )
                {
                        $t->set_var("ERROR_TEXT", "Bitte 'Doppelrunde' auswählen.");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !( ($dom=="true") || ($dom=="false") ) )
                {
                        $t->set_var("ERROR_TEXT", "Bitte 'Spieltage' auswählen.");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !( ($malefemale=="male") || ($malefemale=="female") || ($malefemale=="mixed") ) )
                {
                        $t->set_var("ERROR_TEXT", "Bitte 'Geschlecht' auswählen.");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        switch($job)
                        {
                                case "create":
                                        $leagueInSeason = new LeagueInSeason($db);
                                        if(!$leagueInSeason->create($lmSeason, $lmLeague, $lmSport, $double, $dom, $numberofteams, $malefemale))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Liga in Saison wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $lmLeagueInSeason->setDB($db);
                                        if(!$lmLeagueInSeason->modify($double, $dom, $numberofteams, $malefemale))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Liga in Saison wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $lmLeagueInSeason->setDB($db);
                                        if(!$lmLeagueInSeason->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Liga in Saison wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {

                // if( $lmLeagueInSeason->getID() == 0)
                $lmLeagueInSeason->setDB($db);
                if(!$lmLeagueInSeason->load($lmSeason, $lmLeague))
                // new league in season
                {
                        $t->set_var(array("LIS_HEADER" => "Neue Liga in Saison erstellen",
                                          "LIS_SEASON_NAME" => $lmSeason->getName(),
                                          "LIS_SPORT_NAME" => $lmSport->getName(),
                                          "LIS_LEAGUE_NAME" => $lmLeague->getName(),
                                          "LIS_ACTIVE_YOUTH" => $lmLeague->getActiveYouth()=="active"?"Aktive":"Jugend",
                                          "LIS_NUMBEROFTEAMS" => $numberofteams,
                                          "LIS_DOUBLE_TRUE_SELECTED" => $double=="true"?"checked":"",
                                          "LIS_DOUBLE_FALSE_SELECTED" => $double=="false"?"checked":"",
                                          "LIS_DOM_TRUE_SELECTED" => $dom=="true"?"checked":"",
                                          "LIS_DOM_FALSE_SELECTED" => $dom=="false"?"checked":"",
                                          "LIS_MALEFEMALE_MALE_SELECTED" => $malefemale=="male"?"checked":"",
                                          "LIS_MALEFEMALE_FEMALE_SELECTED" => $malefemale=="female"?"checked":"",
                                          "LIS_MALEFEMALE_MIXED_SELECTED" => $malefemale=="mixed"?"checked":""
                                          ) );
                        $t->parse("lisI", "lis", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit league in season
                {
                        $t->set_var(array("LIS_HEADER" => "Liga in Saison bearbeiten",
                                          "LIS_SEASON_NAME" => $lmSeason->getName(),
                                          "LIS_SPORT_NAME" => $lmSport->getName(),
                                          "LIS_LEAGUE_NAME" => $lmLeague->getName(),
                                          "LIS_ACTIVE_YOUTH" => $lmLeague->getActiveYouth()=="active"?"Aktive":"Jugend",
                                          "LIS_NUMBEROFTEAMS" => isset($numberofteams)?$nuberofteams:$lmLeagueInSeason->getNumberOfTeams(),
                                          "LIS_DOUBLE_TRUE_SELECTED" => isset($double)?($double=="true"?"checked":""):($lmLeagueInSeason->getIsDoubleSeason()=="true"?"checked":""),
                                          "LIS_DOUBLE_FALSE_SELECTED" => isset($double)?($double=="false"?"checked":""):($lmLeagueInSeason->getIsDoubleSeason()=="false"?"checked":""),
                                          "LIS_DOM_TRUE_SELECTED" => isset($dom)?($dom=="true"?"checked":""):($lmLeagueInSeason->getHasDaysOfMatch()=="true"?"checked":""),
                                          "LIS_DOM_FALSE_SELECTED" => isset($dom)?($dom=="false"?"checked":""):($lmLeagueInSeason->getHasDaysOfMatch()=="false"?"checked":""),
                                          "LIS_MALEFEMALE_MALE_SELECTED" => isset($malefemale)?($malefemale=="male"?"checked":""):($lmLeagueInSeason->getMaleFemale()=="male"?"checked":""),
                                          "LIS_MALEFEMALE_FEMALE_SELECTED" => isset($malefemale)?($malefemale=="female"?"checked":""):($lmLeagueInSeason->getMaleFemale()=="female"?"checked":""),
                                          "LIS_MALEFEMALE_MIXED_SELECTED" => isset($malefemale)?($malefemale=="mixed"?"checked":""):($lmLeagueInSeason->getMaleFemale()=="mixed"?"checked":"")
                                          ) );
                        $t->parse("lisI", "lis", false);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
