<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classSeason.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/", "keep");
        $t->set_file(array("page" => "seasonList.ihtml"));
        $t->set_block("page", "season", "seasonI");

        // Saisonliste erstellen
        $seasonIterator = new SeasonIterator($db);
        $seasonIterator->createIterator();

        while($seasonIterator->hasNext())
        {
                $season = $seasonIterator->next();
                $t->set_var(array("SEASON_SEASONID" => $season->getID(),
                                  "SEASON_SEASONNAME" => $season->getName() ) );
                $t->parse("seasonI", "season", true);
        }


        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
