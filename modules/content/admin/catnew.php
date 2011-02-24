<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if($permcheck->hasUserCatPermission($cat))
    {
        $query = sprintf("select max(ord) as maxOrd from contentCat where superID=%s", $cat);
        $db->executeQuery($query);
        $db->nextRow();
        $maxOrd = $db->getValue("maxOrd");

        echo $maxOrd;
        echo "<br><br>";
        echo $query;

        $query = sprintf("insert into contentCat
                      (superID, title, content, lastmodifyUsername, lastmodifyDate, createUsername, createDate, ord)
                      values (%s, '%s', '%s', '%s', now(), '%s', now(), %s)",
                      $cat, "new category", "<?xml version=\"1.0\"?>\n<page>\n</page>\n", $sUser["username"], $sUser["username"], $maxOrd+1);
        $db->executeQuery($query);

        echo "<br><br>";
        echo $query;

        // header("Location: menu.php?cat=$cat");
    }
    else
    {
        $error->printAccessDenied();
    }
?>
