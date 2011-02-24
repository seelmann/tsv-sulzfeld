<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if($permcheck->hasUserCatPermission($cat))
    {
        $query = sprintf("select * from contentCat where superID=%s", $cat);
        $db->executeQuery($query);
        if($db->getNumRows() > 0)
        {
            $error->printErrorPage("In dieser Kategorie befindes sich noch Unterkategorien!");
        }

        $query = sprintf("select * from contentPage where catID=%s", $cat);
        $db->executeQuery($query);
        if($db->getNumRows() > 0)
        {
            $error->printErrorPage("In dieser Kategorie befindes sich noch Seiten!");
        }

        if($cat > 1)
        {
            $query = sprintf("delete from contentCat where ID=%s", $cat);
            $db->executeQuery($query);

            echo "<br><br>";
            echo $query;
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
