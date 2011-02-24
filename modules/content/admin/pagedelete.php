<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if($permcheck->hasUserPagePermission($page))
    {
        $query = sprintf("select catID from contentPage where ID=%s", $page);
        $db->executeQuery($query);
        $db->nextRow();
        $catID = $db->getValue("catID");

        echo $query;

        if($page > 1)
        {
            $query = sprintf("delete from contentPage where ID=%s", $page);
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
