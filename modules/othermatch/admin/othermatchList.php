<?php
        ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/othermatch/classes");
        include("classOthermatch.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "othermatchList.ihtml"));
        $t->set_block("page", "othermatch", "othermatchI");
        $t->set_block("page", "result", "resultI");

        $othermatchIterator = new OthermatchIterator($db);
        if(!empty($othermatch))
        {
            $othermatchIterator->createIterator($othermatch);
            while($othermatchIterator->hasNext())
            {
                $othermatch = $othermatchIterator->next();
                $t->set_var(array("OM_OMID" => $othermatch->getID(),
                                  "OM_NAME" => $othermatch->getDate() ." ". $othermatch->getHome() ."-". $othermatch->getGuest()) );
                $t->parse("othermatchI", "othermatch", true);
            }
        }
        else if(!empty($result))
        {
            $othermatchIterator->createResultIterator($result);
            while($othermatchIterator->hasNext())
            {
                $othermatch = $othermatchIterator->next();
                $t->set_var(array("RESULT_OMID" => $othermatch->getID(),
                                  "RESULT_NAME" => $othermatch->getDate() ." ". $othermatch->getHome() ."-". $othermatch->getGuest()) );
                $t->parse("resultI", "result", true);
            }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
