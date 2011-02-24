<?php
        ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classDate.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "dateList.ihtml"));
        $t->set_block("page", "date", "dateI");

        // Vorinitialisierung
        if(!isset($week) || !is_numeric($week))
                $week = 4;

        // Saisonliste erstellen
        $dateIterator = new DateIterator($db);
        $dateIterator->createIterator($week);

        while($dateIterator->hasNext())
        {
                $date = $dateIterator->next();
                $t->set_var(array("DATE_DATEID" => $date->getID(),
                                  "DATE_DATENAME" => $date->getFromDate()."  ".$date->getFromTime()."  ".$date->getEvent() ) );
                $t->parse("dateI", "date", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
