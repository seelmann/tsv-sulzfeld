<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classMissingResult.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "missingResultList.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "list", "listI");
        $t->set_block("list", "match", "matchI");
        $t->set_block("page", "success", "successI");


        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();


echo sizeof($seasonid)."<br><br>";

                                $matchList = new MissingResultList($db);
                                $matchList->modifyResults( $seasonid,
                                                           $sportid,
                                                           $leagueid,
                                                           $domnumber,
                                                           $matchnumber,
                                                           $homeresults,
                                                           $guestresults,
                                                           $canceled );

        }

        if(!isset($job))
        // print formular
        {
                // Matchliste erstellen
                $matchList = new MissingResultList($db);
                $matchList->loadEditList($type);
                $counter = 0;

                while($matchList->hasNext())
                {
                                $counter++;
                                $match = $matchList->next();
                                $t->set_var(array("COUNTER" => $counter,
                                                  "SEASON_NAME" => $match->getSeasonName(),
                                                  "SEASON_ID" => $match->getSeasonID(),
                                                  "SPORT_NAME" => $match->getSportName(),
                                                  "SPORT_ID" => $match->getSportID(),
                                                  "LEAGUE_NAME" => $match->getLeagueName(),
                                                  "LEAGUE_ID" => $match->getLeagueID(),
                                                  "MATCH_NUMBER" => $match->getMatchNumber(),
                                                  "DOM_NUMBER" => $match->getDOMNumber(),
                                                  "MATCH_DATE" => $match->getDate(),
                                                  "MATCH_TIME" => $match->getTime(),
                                                  "HOME_TEAM_NAME" => $match->getHomeTeamName(),
                                                  "GUEST_TEAM_NAME" => $match->getGuestTeamName(),
                                                  "HOME_RESULT" => "",
                                                  "GUEST_RESULT" => "",
                                                  "CHECKED" => $match->getIsCanceled()?"checked":""
                                                   ) );
                                $t->parse("matchI", "match", true);
                }
                $t->parse("listI", "list");
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");

?>
