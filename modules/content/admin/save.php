<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

    $edit = new Edit($error, $db, $type, $id);

    if(isset($type) && ($type == "page"))
    {
        if($permcheck->hasUserPagePermission($id))
        {
            $edit->save($HTTP_POST_VARS);
        }
        else
        {
            $error->printAccessDenied();
        }
    }
    else if(isset($type) && ($type == "cat"))
    {
        if($permcheck->hasUserCatPermission($id))
        {
            $edit->save($HTTP_POST_VARS);
        }
        else
        {
            $error->printAccessDenied();
        }
    }
    else
    {
        $error->printAccessDenied();
    }

    if( !isset($go) || ( isset($go) && ($go == "refresh") ) )
    {
        header("Location: edit.php?type=$type&id=$id");
    }
?>

