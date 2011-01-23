<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classLeague.php");
        include("classSport.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");

        $t->set_file(array("page" => "leagueList.ihtml"));
        $t->set_block("page", "sport", "sportI");
        $t->set_block("sport", "activeyouth", "activeyouthI");
        $t->set_block("activeyouth", "league", "leagueI");


        // Ligaliste erstellen
        $leagueIterator = new LeagueIterator($db);
        $leagueIterator->createIterator();

        $sn = "";
        $ay = "";
        $snChanged == false;
        if($leagueIterator->hasNext())
        {
                $league = $leagueIterator->next();
                $sport = $league->getSport();
                $sn = $sport->getName();
                $ay = $league->getActiveYouth();
                $t->set_var("LEAGUE_SPORT_NAME", $sn);
                $t->set_var("LEAGUE_ACTIVEYOUTH_NAME", $ay=="active"?"Aktive":"Jugend");
                $t->set_var(array("LEAGUE_LEAGUEID" => $league->getID(),
                                  "LEAGUE_LEAGUENAME" => $league->getName() ) );
                $t->parse("leagueI", "league", true);
        }
        while($leagueIterator->hasNext())
        {
                $league = $leagueIterator->next();
                $sport = $league->getSport();

                if( ($league->getActiveYouth() != $ay) || ($sport->getName() != $sn) )
                {
                        if($sport->getName() != $sn)
                        {
                                $snChanged = true;
                                $t->parse("activeyouthI", "activeyouth", true);
                                $ay = $league->getActiveYouth();
                                $t->set_var("LEAGUE_ACTIVEYOUTH_NAME", $ay=="active"?"Aktive":"Jugend");

                                $t->parse("sportI", "sport", true);
                                $sn = $sport->getName();
                                $t->set_var("LEAGUE_SPORT_NAME", $sn);


                                $t->set_var(array("LEAGUE_LEAGUEID" => $league->getID(),
                                                  "LEAGUE_LEAGUENAME" => $league->getName() ) );
                                $t->parse("leagueI", "league", false);
                        }
                        else
                        {
                                if($snChanged)
                                        $t->parse("activeyouthI", "activeyouth", false);
                                else
                                        $t->parse("activeyouthI", "activeyouth", true);
                                $snChanged = false;

                                $ay = $league->getActiveYouth();
                                $t->set_var("LEAGUE_ACTIVEYOUTH_NAME", $ay=="active"?"Aktive":"Jugend");

                                $t->set_var(array("LEAGUE_LEAGUEID" => $league->getID(),
                                                  "LEAGUE_LEAGUENAME" => $league->getName() ) );
                                $t->parse("leagueI", "league", false);
                        }
                }
                else
                {
                        $t->set_var(array("LEAGUE_LEAGUEID" => $league->getID(),
                                          "LEAGUE_LEAGUENAME" => $league->getName() ) );
                        $t->parse("leagueI", "league", true);
                }

/*
                if($sport->getName() != $sn)
                {
                        $t->parse("sportI", "sport", true);
                        $sn = $sport->getName();
                        $t->set_var("LEAGUE_SPORT_NAME", $sn);

                        $t->set_var(array("LEAGUE_LEAGUEID" => $league->getID(),
                                          "LEAGUE_LEAGUENAME" => $league->getName() ) );
                        $t->parse("leagueI", "league", false);
                }
                else
                {
                        $t->set_var(array("LEAGUE_LEAGUEID" => $league->getID(),
                                          "LEAGUE_LEAGUENAME" => $league->getName() ) );
                        $t->parse("leagueI", "league", true);
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
