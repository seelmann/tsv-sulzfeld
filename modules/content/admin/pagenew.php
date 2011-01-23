<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    if($permcheck->hasUserCatPermission($cat))
    {
        $query = sprintf("select max(ord) as maxOrd from contentPage where catID=%s", $cat);
        $db->executeQuery($query);
        $db->nextRow();
        $maxOrd = $db->getValue("maxOrd");

        // echo $maxOrd;
        // echo "<br><br>";
        // echo $query;

        $query = sprintf("insert into contentPage
                      (catID, title, content, lastmodifyUsername, lastmodifyDate, createUsername, createDate, ord)
                      values (%s, '%s', '%s', '%s', now(), '%s', now(), %s)",
                      $cat, "new page", "<?xml version=\"1.0\"?>\n<page>\n</page>\n", $sUser["username"], $sUser["username"], $maxOrd+1);
        $db->executeQuery($query);

        // echo "<br><br>";
        // echo $query;

        // header("Location: menu.php?cat=$cat");
    }
    else
    {
        $error->printAccessDenied();
    }
?>
