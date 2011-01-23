<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
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
        $t = new Template("./templates/", "keep");
        $t->set_file(array("page" => "matchReportList.ihtml"));
        $t->set_block("page", "matchreport", "matchreportI");

        $matchList = new MatchOfLeagueList($db);
        $matchList->init($lmLeagueInSeason, $lmDayOfMatch);
        $matchList->loadList();

        while($matchList->hasNext())
        {
                $match = $matchList->next();
                $t->set_var(array("NUMBER" => $match->getNumber(),
                                  "NAME" => $match->getDate()." ".$match->getTime()." ".$match->getHomeTeamName()." ".$match->getGuestTeamName() ) );
                $t->parse("matchreportI", "matchreport", true);
        }


        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
