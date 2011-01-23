<?php
    include("classLock.php");
    include("classXMLEdit.php");

    class Edit
    {
        var $error;
        var $db;
        var $type;
        var $ID;
        var $table;
        var $tableHistory;
        var $lock;

        var $xml;

        var $tempC; // temp variable for numbers of columns of a table, needed to insert a row into a table.

        function Edit($error, $db, $type, $ID)
        {
            $this->error = $error;
            $this->db = $db;
            $this->ID = $ID;

            $this->type = $type;

            if($type == "page")
            {
                $this->table = "contentPage";
                $this->tableHistory = "contentPageHistory";
                $this->lock = new Lock($error, $db, "contentPage");
            }
            else if($type == "cat")
            {
                $this->table = "contentCat";
                $this->tableHistory = "contentCatHistory";
                $this->lock = new Lock($error, $db, "contentCat");
            }
            $this->init();
        }

        function init()
        {
            if($this->lock->setLock($this->ID))
            {
                $query = sprintf(" select  title,
                                           content,
                                           image1ID,
                                           image2ID
                                   from    %s
                                   where   ID=%s
                                 ", $this->table, $this->ID);
                $this->db->executeQuery($query);
                $this->db->nextRow();
                $this->insertHistory($this->db->getValue("title"), $this->db->getValue("content"), $this->db->getValue("image1ID"), $this->db->getValue("image2ID"));
            }
        }

        function start()
        {
            global $sUser;
            if($this->lock->hasLock($this->ID))
            {
/*
                $query = sprintf(" select    p.title,
                                             p.content,
                                             p.image1ID,
                                             p.image2ID,
                                             i1.imageFilename as image1,
                                             i2.imageFilename as image2,
                                             max(p.date)
                                   from      %s p
                                   left join imageImage i1
                                   on        p.image1ID=i1.ID
                                   left join imageImage i2
                                   on        p.image2ID=i2.ID
                                   where     p.sessionID='%s'
                                   and       p.username='%s'
                                   and       p.ID=%s
                                   group by  p.sessionID, p.username, p.ID
                                 ", $this->tableHistory, session_id(), $sUser["username"], $this->ID);
*/

                $query = sprintf(" select    p.title,
                                             p.content,
                                             p.image1ID,
                                             p.image2ID,
                                             i1.imageFilename as image1,
                                             i2.imageFilename as image2
                                   from      %s p
                                   left join imageImage i1
                                   on        p.image1ID=i1.ID
                                   left join imageImage i2
                                   on        p.image2ID=i2.ID
                                   where     p.sessionID='%s'
                                   and       p.username='%s'
                                   and       p.ID=%s
                                   order by  p.date desc
                                 ", $this->tableHistory, session_id(), $sUser["username"], $this->ID);

                $this->db->executeQuery($query);
                $this->db->nextRow();
                $this->xml = new XMLEdit($this->error, $this->db, $this->type, $this->ID);

                $this->xml->printStatus($this->db->getValue("title"), $this->db->getValue("image1ID"), $this->db->getValue("image2ID"));

                $this->xml->parse($this->db->getValue("content"));
            }
        }

        function insertHistory($title="", $content="", $imageID1=0, $imageID2=0)
        {
            global $sUser;
            $query = sprintf(" insert into %s
                               (           sessionID,
                                           date,
                                           username,
                                           ID,
                                           title,
                                           content,
                                           image1ID,
                                           image2ID )
                               values (    '%s',
                                           now(),
                                           '%s',
                                           %s,
                                           '%s',
                                           '%s',
                                           '%s',
                                           '%s' )
                             ", $this->tableHistory, session_id(), $sUser["username"], $this->ID, $title, $content, $imageID1, $imageID2);
            $this->db->executeQuery($query);

            // echo "<br><br><br><br>";
            // echo nl2br(htmlentities($query));

        }

        function updateOriginal($title="", $content="", $imageID1=0, $imageID2=0)
        {
            global $sUser;
            $query = sprintf(" update %s
                               set    lastmodifyDate = now(),
                                      lastmodifyUsername = '%s',
                                      title = '%s',
                                      content = '%s',
                                      image1ID = '%s',
                                      image2ID = '%s'
                               where  ID = %s
                             ", $this->table, $sUser["username"], $title, $content, $imageID1, $imageID2, $this->ID);
            $this->db->executeQuery($query);

            // echo nl2br(htmlentities($content));
            // echo "<br><br><br><br>";
            // echo nl2br(htmlentities($query));
        }

        function save($v)
        {
            ksort ($v);
            reset ($v);
            // var_dump($v);

            $elementCounter = array();
            $endTag = array();
            $insert = array();
            $depth = 1;

            $elementCounter[0] = 1;

            // build the xml
            $data = "<?xml version=\"1.0\"?>\n<page>\n";

            $data .= $this->insertElement($v["e_001_000_INSERT"]);

            do
            {
                $elementCounter[$depth]++;
                $elementName = "e_";
                for($i=0; $i<=$depth; $i++)
                {
                    $elementName .= sprintf("%03d_", $elementCounter[$i]);
                }
                $elementName = substr ($elementName, 0, -1);

                if($v["".$elementName."_DELETE"] == yes)
                    continue;

                if($v[$elementName])
                {
                    switch($v[$elementName])
                    {
                        case "HEADER":
                            $align = $v[$elementName."_ALIGN"];
                            $size = $v[$elementName."_SIZE"];
                            $text = $v[$elementName."_TEXT"];
                            $data .= sprintf("<header align=\"%s\" size=\"%s\">%s</header>\n", htmlspecialchars($align), htmlspecialchars($size), htmlspecialchars($text) );
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "TEXT":
                            $align = $v[$elementName."_ALIGN"];
                            $text = $v[$elementName."_TEXT"];
                            $data .= sprintf("<text align=\"%s\">%s</text>\n", htmlspecialchars($align), htmlspecialchars($text) );
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "IMAGE":
                            $align = $v[$elementName."_ALIGN"];
                            $id = $v[$elementName."_ID"];
                            $data .= sprintf("<image align=\"%s\" id=\"%s\"></image>\n", htmlspecialchars($align), htmlspecialchars($id) );
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "LINK":
                            $align = $v[$elementName."_ALIGN"];
                            $text = $v[$elementName."_TEXT"];
                            $type = $v[$elementName."_TYPE"];
                            $href = $v[$elementName."_HREF"];
                            $email = $v[$elementName."_EMAIL"];
                            $pageid = $v[$elementName."_PAGEID"];
                            $catid = $v[$elementName."_CATID"];
                            $data .= sprintf("<link align=\"%s\" type=\"%s\" href=\"%s\" email=\"%s\" pageid=\"%s\" catid=\"%s\">%s</link>\n", htmlspecialchars($align), htmlspecialchars($type), htmlspecialchars($href), htmlspecialchars($email), htmlspecialchars($pageid), htmlspecialchars($catid), htmlspecialchars($text) );
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "LIST":
                            $align = $v[$elementName."_ALIGN"];
                            $type = $v[$elementName."_TYPE"];
                            $data .= sprintf("<list align=\"%s\" type=\"%s\">\n", htmlspecialchars($align), htmlspecialchars($type) );
                            $endTag[$depth] = "LIST";
                            $depth++;
                            $elementCounter[$depth] = 0;
                            $data .= $this->insertElement($v[$elementName."_000_INSERT"]);
                            break;
                        case "LISTITEM":
                            $text = $v[$elementName."_TEXT"];
                            $data .= sprintf("<listitem>%s</listitem>\n", htmlspecialchars($text) );
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "COLS":
                            if( isset($v[$elementName."_NOC"]) )
                            {
                                $c = $v[$elementName."_NOC"];
                                $this->tempC = $c;
                                $data .= sprintf("<cols c=\"%s\">\n", $c);
                                for($j=0; $j<$c; $j++)
                                    $data .= "<col></col>\n";
                                $data .= "</cols>\n";
                            }
                            else
                            {
                                $c = $v[$elementName."_C"];
                                $this->tempC = $c;
                                $align = $v[$elementName."_ALIGN"];
                                $data .= sprintf("<cols c=\"%s\" align=\"%s\">\n", $c, $align);
                                $endTag[$depth] = "COLS";
                                $depth++;
                                $elementCounter[$depth] = 0;
                                $data .= $this->insertElement($v[$elementName."_000_INSERT"]);
                            }
                            break;
                        case "COL":
                            $data .= sprintf("<col>\n");
                            $endTag[$depth] = "COL";
                            $depth++;
                            $elementCounter[$depth] = 0;
                            $data .= $this->insertElement($v[$elementName."_000_INSERT"]);
                            break;
                        case "SIMPLETABLE":
                            if(isset($v[$elementName."_NOR"]) && isset($v[$elementName."_NOC"]))
                            {
                                $r = $v[$elementName."_NOR"];
                                $c = $v[$elementName."_NOC"];
                                $this->tempC = $c;
                                $data .= sprintf("<simpletable r=\"%s\" c=\"%s\">\n", $r, $c);

                                for($i=0; $i<$r; $i++)
                                {
                                    $data .= "<simplerow>\n";
                                    for($j=0; $j<$c; $j++)
                                        $data .= "<simplecell></simplecell>\n";
                                    $data .= "</simplerow>\n";
                                }
                                $data .= "</simpletable>\n";
                            }
                            else
                            {
                                $r = $v[$elementName."_R"];
                                $c = $v[$elementName."_C"];
                                $this->tempC = $c;
                                $align = $v[$elementName."_ALIGN"];
                                $data .= sprintf("<simpletable r=\"%s\" c=\"%s\" align=\"%s\">\n", $r, $c, $align);
                                $endTag[$depth] = "SIMPLETABLE";
                                $depth++;
                                $elementCounter[$depth] = 0;
                                $data .= $this->insertElement($v[$elementName."_000_INSERT"]);
                            }
                            break;
                        case "SIMPLEROW":
                            $data .= "<simplerow>\n";
                            $endTag[$depth] = "SIMPLEROW";
                            $depth++;
                            $elementCounter[$depth] = 0;
                            break;
                        case "SIMPLECELL":
                            $text = $v[$elementName."_TEXT"];
                            $data .= sprintf("<simplecell>%s</simplecell>\n", $text);
                            break;






                        case "TABLE":
                            if(isset($v[$elementName."_NOR"]) && isset($v[$elementName."_NOC"]))
                            {
                                $r = $v[$elementName."_NOR"];
                                $c = $v[$elementName."_NOC"];
                                $this->tempC = $c;
                                $data .= sprintf("<table r=\"%s\" c=\"%s\">\n", $r, $c);

                                for($i=0; $i<$r; $i++)
                                {
                                    $data .= "<row>\n";
                                    for($j=0; $j<$c; $j++)
                                        $data .= "<cell></cell>\n";
                                    $data .= "</row>\n";
                                }
                                $data .= "</table>\n";
                            }
                            else
                            {
                                $r = $v[$elementName."_R"];
                                $c = $v[$elementName."_C"];
                                $this->tempC = $c;
                                $align = $v[$elementName."_ALIGN"];
                                $data .= sprintf("<table r=\"%s\" c=\"%s\" align=\"%s\">\n", $r, $c, $align);
                                $endTag[$depth] = "TABLE";
                                $depth++;
                                $elementCounter[$depth] = 0;
                                $data .= $this->insertElement($v[$elementName."_000_INSERT"]);
                            }
                            break;
                        case "ROW":
                            $toggle = ($v[$elementName."_TOGGLE"]=="true")?"true":"false";
                            $data .= sprintf("<row toggle=\"%s\">\n", $toggle);
                            $endTag[$depth] = "ROW";
                            $depth++;
                            $elementCounter[$depth] = 0;
                            break;
                        case "CELL":
                            $data .= "<cell>\n";
                            $endTag[$depth] = "CELL";
                            $depth++;
                            $elementCounter[$depth] = 0;
                            $data .= $this->insertElement($v[$elementName."_000_INSERT"]);
                            break;

                     } // switch
                } // if
                else
                {
                    $elementCounter[$depth] = 0;
                    $depth--;

                    $elementName = "e_";
                    for($i=0; $i<=$depth; $i++)
                    {
                        $elementName .= sprintf("%03d_", $elementCounter[$i]);
                    }
                    $elementName = substr ($elementName, 0, -1);

                    switch ($endTag[$depth])
                    {
                        case "LIST":
                            $data .= "</list>\n";
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "COLS":
                            $data .= "</cols>\n";
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "COL":
                            $data .= "</col>\n";
                            break;
                        case "SIMPLETABLE":
                            $data .= "</simpletable>\n";
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "SIMPLEROW":
                            $data .= "</simplerow>\n";
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "TABLE":
                            $data .= "</table>\n";
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "ROW":
                            $data .= "</row>\n";
                            $data .= $this->insertElement($v[$elementName."_INSERT"]);
                            break;
                        case "CELL":
                            $data .= "</cell>\n";
                            break;
                    } // switch
                } // else


            }
            while($depth > 0);

            $data .= "</page>";

            $title = $v["e_001_TITLE"];
            $imageID1 = $v["e_001_IMAGEID1"];
            $imageID2 = $v["e_001_IMAGEID2"];

            // echo "<br><br><br><br>";
            // echo "<h1>$title</h1>";
            // echo "<br><br><br><br>";
            // echo nl2br(htmlentities($data));

            $this->insertHistory($title, $data, $imageID1, $imageID2);

            if($v["go"] == "save")
            {
                $this->updateOriginal($title, $data, $imageID1, $imageID2);
                $this->lock->removeLock($this->ID);
            }

            if($v["go"] == "cancel")
            {
                $this->lock->removeLock($this->ID);
            }
        }

        function insertElement($tag)
        {
            switch($tag)
            {
                case "HEADER":
                    return "<header align=\"\" size=\"\"></header>\n";
                    break;
                case "TEXT":
                    return "<text align=\"\"></text>\n";
                    break;
                case "IMAGE":
                    return "<image id=\"0\" align=\"\"></image>\n";
                    break;
                case "LINK":
                    return "<link align=\"\" text=\"\" type=\"\" href=\"\" email=\"\" pageid=\"0\" catid=\"0\"></link>\n";
                    break;
                case "LIST":
                    return "<list align=\"\" type=\"\">\n<listitem></listitem>\n</list>\n";
                    break;
                case "LISTITEM":
                    return "<listitem></listitem>\n";
                    break;
                case "COLS":
                    return "<cols c=\"0\"></cols>\n";
                    break;
                case "SIMPLETABLE":
                    return "<simpletable r=\"0\" c=\"0\"></simpletable>\n";
                    break;
                case "SIMPLEROW":
                    $s = "<simplerow>";
                    for($i=0; $i<$this->tempC; $i++)
                        $s .= "<simplecell></simplecell>\n";
                    $s .= "</simplerow>\n";
                    return $s;
                case "TABLE":
                    return "<table r=\"0\" c=\"0\"></table>\n";
                    break;
                case "ROW":
                    $s = "<row>";
                    for($i=0; $i<$this->tempC; $i++)
                        $s .= "<cell></cell>\n";
                    $s .= "</row>\n";
                    return $s;
                default:
                    break;
            }
        }

    }

?>