<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classSport.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/", "keep");
        $t->set_file(array("page" => "sportList.ihtml"));
        $t->set_block("page", "sport", "sportI");

        // Saisonliste erstellen
        $sportIterator = new SportIterator($db);
        $sportIterator->createIterator();

        while($sportIterator->hasNext())
        {
                $sport = $sportIterator->next();
                $t->set_var(array("SPORT_SPORTID" => $sport->getID(),
                                  "SPORT_SPORTNAME" => $sport->getName() ) );
                $t->parse("sportI", "sport", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
