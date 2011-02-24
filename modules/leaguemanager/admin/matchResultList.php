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
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "matchResultList.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "list", "listI");
        $t->set_block("list", "match", "matchI");
        $t->set_block("match", "match_registered", "match_registeredI");
        $t->set_block("match", "match_not_registered", "match_not_registeredI");
        $t->set_block("page", "success", "successI");


        if(isset($job))
        // perform action
        {
                $errors = 0;


var_dump($homeresults);
echo "<br>";
var_dump($guestresults);
echo "<br>";
var_dump($postpone);
echo "<br>";


                // validate fields
                $validate = new Validation();

/*
                for($i=1; $i<=sizeof($homeresults); $i++)
                {
                        if(!empty($homeresults[$i]))
                        {
                                if( !$validate->isNumber($homeresults[$i]) )
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültige Zahl in Spiel ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                        if(!empty($guestresults[$i]))
                        {
                                if( !$validate->isNumber($guestresults[$i]) )
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültige Zahl in Spiel ".$i);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }
                        }
                }
*/
                if(is_array($homeresults) && is_array($guestresults))
                {
                        foreach($homeresults as $key => $val)
                        {
                                if(($homeresults[$key]!="") && ($guestresults[$key]!=""))
                                {
                                        if( !$validate->isNumber($homeresults[$key]) )
                                        {
                                                $t->set_var("ERROR_TEXT", "Ungültiges Heimergebnis in Spiel ".$key);
                                                $t->parse("errorI", "error", true);
                                                unset($job);
                                                $errors++;
                                        }
                                        if( !$validate->isNumber($guestresults[$key]) )
                                        {
                                                $t->set_var("ERROR_TEXT", "Ungültiges Gastergebnis in Spiel ".$key);
                                                $t->parse("errorI", "error", true);
                                                unset($job);
                                                $errors++;
                                        }
                                }

                                if( (($homeresults[$key]!="") && ($guestresults[$key]=="")) ||
                                    (($homeresults[$key]=="") && ($guestresults[$key]!="")) )
                                {
                                        $t->set_var("ERROR_TEXT", "Ungültiges Ergebnis in Spiel ".$key);
                                        $t->parse("errorI", "error", true);
                                        unset($job);
                                        $errors++;
                                }

                        }

                        if( $errors==0 )
                        {
                                $matchList = new MatchOfLeagueList($db);
                                $matchList->init($lmLeagueInSeason, $lmDayOfMatch);

                                if(!$matchList->modifyResults($homeresults, $guestresults, $postpone))
                                        $error->printErrorPage("Fehler bei der Zuordnung aufgetreten");
                                $t->set_var("SUCCESS_TEXT", "Ergebnisses wurden erfolgreich eingetragen");
                                $t->parse("successI", "success", true);
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                // Teamliste erstellen
                $matchList = new MatchOfLeagueList($db);
                $matchList->init($lmLeagueInSeason, $lmDayOfMatch);
                $matchList->loadEditList();

                while($matchList->hasNext())
                {
                        $match = $matchList->next();

                        if($match->getIsRegistered())
                        {
                                $t->set_var(array("MATCH_NUMBER" => $match->getNumber(),
                                                  "MATCH_DATE" => $match->getDate(),
                                                  "MATCH_TIME" => $match->getTime(),
                                                  "HOME_TEAM_NAME" => $match->getHomeTeamName(),
                                                  "GUEST_TEAM_NAME" => $match->getGuestTeamName(),
                                                  "HOME_RESULT" => isset($homeresult)?$homeresult[$match->getNumber()]:$match->getHomeResult(),
                                                  "GUEST_RESULT" => isset($guestresult)?$guestresult[$match->getNumber()]:$match->getGuestResult(),
                                                  "READONLY" => "",
                                                  "CHECKED" => isset($postpone)?(($postpone[$match->getNumber()]=="true")?"checked":""):(is_numeric($match->getIsPostponed())?"checked":"")
                                                   ) );
                        }
                        else
                        {
                                $t->set_var(array("MATCH_NUMBER" => $match->getNumber(),
                                                  "MATCH_DATE" => "",
                                                  "MATCH_TIME" => "",
                                                  "HOME_TEAM_NAME" => "",
                                                  "GUEST_TEAM_NAME" => "",
                                                  "HOME_RESULT" => "",
                                                  "GUEST_RESULT" => "",
                                                  "READONLY" => "readonly",
                                                  "CHECKED" => ""
                                                   ) );
                        }
                        $t->parse("matchI", "match", true);

                }
                $t->parse("listI", "list");
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");

?>
