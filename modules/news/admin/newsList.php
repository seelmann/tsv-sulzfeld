<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classNews.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "newsList.ihtml"));
        $t->set_block("page", "news", "newsI");

        // Vorinitialisierung
        if(!isset($num) || !is_numeric($num))
                $num = 25;

        // Saisonliste erstellen
        $newsIterator = new NewsIterator($db);
        $newsIterator->createIterator(0, $num);

        while($newsIterator->hasNext())
        {
                $news = $newsIterator->next();
                $t->set_var(array("NEWS_NEWSID" => $news->getID(),
                                  "NEWS_NEWSNAME" => $news->getHead() ) );
                $t->parse("newsI", "news", true);
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
