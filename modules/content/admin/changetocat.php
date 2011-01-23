<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if($permcheck->hasUserPagePermission($page))
    {
        if($page > 1)
        {
            // Seite nach Kategorie kopieren
            echo "Seite $page nach Kategorie kopieren: <br>";
            $query = sprintf("INSERT INTO contentCat ( superID,
                                                       ord,
                                                       createUsername,
                                                       createDate,
                                                       lastmodifyUsername,
                                                       lastmodifyDate,
                                                       title,
                                                       content,
                                                       image1ID,
                                                       image2ID,
                                                       lockUsername,
                                                       lockDate,
                                                       lockSessionID,
                                                       contentTypeID,
                                                       counter
                                                     )
                              SELECT                 catID,
                                                     ord,
                                                     createUsername,
                                                     createDate,
                                                     lastmodifyUsername,
                                                     lastmodifyDate,
                                                     title,
                                                     content,
                                                     image1ID,
                                                     image2ID,
                                                     lockUsername,
                                                     lockDate,
                                                     lockSessionID,
                                                     contentTypeID,
                                                     counter
                              FROM                   contentPage
                              WHERE                  ID=%s
                             ", $page);
            $db->executeQuery($query);
            $catID = $db->getInsertID();

            echo $query;
            echo "<br>";
            echo "catID: $catID <br><br>";

            if($catID > 0)
            {
                // Seite löschen
                echo "Seite $page löschen: <br>";
                $query = sprintf("delete from contentPage where ID=%s", $page);
                $db->executeQuery($query);
                echo $query;
            }
        }
        else
        {
            $error->printErrorPage("Die Wurzel kann nicht gelöscht werden!");
        }

        // header("Location: menu.php?cat=$cat");
    }
    else
    {
        $error->printAccessDenied();
    }
?>