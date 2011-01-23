<?php
        ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classError.php");
        include("classDBmysql.php");
        include("classXMLShow.php");
        include("classXMLPrint.php");
        include("classMenu.php");

        // initialize objects
        $error = new Error();
        $db = new DBmysql($error);
        $menu = new Menu($error, $db);
        $menuStarted = false;

        // check $type and $ID
        if(isset($type) && ($type == "page"))
                $table = "contentPage";
        else if(isset($type) && ($type == "cat"))
                $table = "contentCat";
        else
        {
                $table = "contentPage";
                $type = "page";
        }
        if(!isset($ID))
                $ID = 1;


        // start html output
        $xml = new XMLPrint($error, $db);
        pageStartPrint();
        pageContentStartPrint();


        // get content and contentType for the current page/category
        // $query = sprintf(" select p.content, t.name as contentType from %s p, contentType t where p.ID=%s and p.contentTypeID=t.ID", $table, $ID);
        $query = sprintf(" select   p.content,
                                    date_format(p.lastmodifyDate, '%%d.%%m.%%Y') as lastupdate,
                                    t.name as contentType,
                                    t.file
                           from     %s p,
                                    contentType t
                           where    p.ID=%s
                           and      p.contentTypeID=t.ID",
                                    $table,
                                    $ID );
        $db->executeQuery($query);
        $db->nextRow();
        $content = $db->getValue("content");
        $file = $db->getValue("file");
        $contentType = $db->getValue("contentType");
        $lastupdate = $db->getValue("lastupdate");

        while($contentType == "emptyCat")
        {
                $table = "contentPage";
                $query = sprintf(" select   p.content,
                                            date_format(p.lastmodifyDate, '%%d.%%m.%%Y') as lastupdate,
                                            t.name as contentType,
                                            t.file
                                   from     %s p,
                                            contentType t
                                   where    p.catID=%s
                                   and      p.contentTypeID=t.ID
                                   order by ord
                                   limit    0,1 ",
                                            $table,
                                            $ID );
                $db->executeQuery($query);
                if($db->getNumRows() == 1)
                {
                        $db->nextRow();
                        $content = $db->getValue("content");
                        $file = $db->getValue("file");
                        $contentType = $db->getValue("contentType");
                        $lastupdate = $db->getValue("lastupdate");
                }
                else
                {
                        // nächste Sub-Kategorie finden
                }
        }

        if($contentType == "normal")
                $data = $content;
        else
                include($file);
        $xml = new XMLPrint($error, $db);
        $xml->parse($data);

        pageContentStopPrint();
        pageStopPrint();




    function pageStartPrint()
    {
        ?>
<html>
  <head>
    <title></title>
    <link rel="STYLESHEET" type="text/css" href="/css/print.css"></link>
  </head>
  <body bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#0000FF" alink="#0000FF">
<?php
    }

    function pageStopPrint()
    {
        ?>
  </body>
</html>
<?php
    }

    function pageContentStartPrint()
    {
        ?>
<?php
    }

    function pageContentStopPrint()
    {
        ?>
<?php

    }


?>
