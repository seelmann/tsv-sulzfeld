<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classTeam.php");
        include("classSport.php");
        include("classTemplate.php");
        include("auth.php");

        // Template starten
        $t = new Template("./templates/");

        $t->set_file(array("page" => "teamList.ihtml"));
        $t->set_block("page", "sport", "sportI");
        $t->set_block("sport", "activeyouth", "activeyouthI");
        $t->set_block("activeyouth", "team", "teamI");

        // Teamliste erstellen
        $teamIterator = new TeamIterator($db);
        $teamIterator->createIterator();

        $sn = "";
        $ay = "";
        $snChanged == false;
        if($teamIterator->hasNext())
        {
                $team = $teamIterator->next();
                $sport = $team->getSport();
                $sn = $sport->getName();
                $ay = $team->getActiveYouth();
                $t->set_var("TEAM_SPORT_NAME", $sn);
                $t->set_var("TEAM_ACTIVEYOUTH_NAME", $ay=="active"?"Aktive":"Jugend");
                $t->set_var(array("TEAM_TEAMID" => $team->getID(),
                                  "TEAM_TEAMNAME" => $team->getName() ) );
                $t->parse("teamI", "team", true);
        }
        while($teamIterator->hasNext())
        {
                $team = $teamIterator->next();
                $sport = $team->getSport();

                if( ($team->getActiveYouth() != $ay) || ($sport->getName() != $sn) )
                {
                        if($sport->getName() != $sn)
                        {
                                $snChanged = true;
                                $t->parse("activeyouthI", "activeyouth", true);
                                $ay = $team->getActiveYouth();
                                $t->set_var("TEAM_ACTIVEYOUTH_NAME", $ay=="active"?"Aktive":"Jugend");

                                $t->parse("sportI", "sport", true);
                                $sn = $sport->getName();
                                $t->set_var("TEAM_SPORT_NAME", $sn);


                                $t->set_var(array("TEAM_TEAMID" => $team->getID(),
                                                  "TEAM_TEAMNAME" => $team->getName() ) );
                                $t->parse("teamI", "team", false);
                        }
                        else
                        {
                                if($snChanged)
                                        $t->parse("activeyouthI", "activeyouth", false);
                                else
                                        $t->parse("activeyouthI", "activeyouth", true);
                                $snChanged = false;

                                $ay = $team->getActiveYouth();
                                $t->set_var("TEAM_ACTIVEYOUTH_NAME", $ay=="active"?"Aktive":"Jugend");

                                $t->set_var(array("TEAM_TEAMID" => $team->getID(),
                                                  "TEAM_TEAMNAME" => $team->getName() ) );
                                $t->parse("teamI", "team", false);
                        }
                }
                else
                {
                        $t->set_var(array("TEAM_TEAMID" => $team->getID(),
                                          "TEAM_TEAMNAME" => $team->getName() ) );
                        $t->parse("teamI", "team", true);
                }

/*
                if($sport->getName() != $sn)
                {
                        $t->parse("sportI", "sport", true);
                        $sn = $sport->getName();
                        $t->set_var("TEAM_SPORT_NAME", $sn);

                        $t->set_var(array("TEAM_TEAMID" => $team->getID(),
                                          "TEAM_TEAMNAME" => $team->getName() ) );
                        $t->parse("teamI", "team", false);
                }
                else
                {
                        $t->set_var(array("TEAM_TEAMID" => $team->getID(),
                                          "TEAM_TEAMNAME" => $team->getName() ) );
                        $t->parse("teamI", "team", true);
                }
*/
        }
        if($snChanged)
                $t->parse("activeyouthI", "activeyouth", false);
        else
                $t->parse("activeyouthI", "activeyouth", true);
        $t->parse("sportI", "sport", true);

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
