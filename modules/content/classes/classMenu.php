<?php

    class Menu
    {
        var $error;
        var $db;

        var $menu;
        var $menu2;
        var $menu3;

        var $depth;
        var $indexCounter;

        function Menu($error, $db)
        {
            $this->error = $error;
            $this->db = $db;
            $this->menu = array();
            $this->menu2 = array();
            $this->menu3 = array();
            $this->depth = 0;
            $this->indexCounter = -1;
        }

        function buildMenu($type, $ID)
        {
            if($type == "page")
                $catID = $this->getCatOfPage($ID);
            else if($type == "cat")
                $catID = $ID;
            else
                $catID = $ID;

            $this->depth++;

            $a = $this->getPagesInCat($catID);
            for($i=0; $i<sizeOf($a); $i++)
                array_push($this->menu, array("page", $this->depth, $a[$i][0], $a[$i][1], $a[$i][2], $a[$i][3], $a[$i][4]) );

            $a = $this->getSubCatsInCat($catID);
            for($i=0; $i<sizeOf($a); $i++)
                array_push($this->menu, array("cat", $this->depth, $a[$i][0], $a[$i][1], $a[$i][2], $a[$i][3], $a[$i][4]) );

            if($catID > 0)
                $this->buildMenuRecursive($catID);


/*
            // test
            for($i=0; $i<sizeOf($this->menu); $i++)
            {
                echo $this->menu[$i][0] . "  -  " . $this->menu[$i][1] . "  -  " . $this->menu[$i][2] . "  -  " . $this->menu[$i][3] . "  -  " . $this->menu[$i][4] . "  -  " . $this->menu[$i][5] ."  -  " . $this->menu[$i][6] . "<br>\n";
            }
*/
        }

        function buildMenuRecursive($recentCatID)
        {
                $this->depth++;
                $catID = $this->getSuperCatOfCat($recentCatID);
                $temp = array();

                $a = $this->getPagesInCat($catID);
                for($i=0; $i<sizeOf($a); $i++)
                    array_push($temp, array("page", $this->depth, $a[$i][0], $a[$i][1], $a[$i][2], $a[$i][3], $a[$i][4]) );

                $a = $this->getSubCatsInCat($catID);
                for($i=0; $i<sizeOf($a); $i++)
                {
                        array_push($temp, array("cat", $this->depth, $a[$i][0], $a[$i][1], $a[$i][2], $a[$i][3], $a[$i][4]) );
                        if($a[$i][0] == $recentCatID)
                        {
                             $temp = array_merge($temp, $this->menu);
                        }
                }

                $this->menu = $temp;

                if($catID > 0)
                    $this->buildMenuRecursive($catID);
        }


        function getMaxDepth()
        {
            return $this->depth;
        }

        function reset()
        {
            $this->indexCouter = -1;
        }

        function nextRow()
        {
            $this->indexCounter++;
            if($this->indexCounter < sizeOf($this->menu))
                return true;
            else
            {
                $indexCounter = 0;
                return false;
            }
        }
        function prevRow()
        {
            $this->indexCounter--;
            if($this->indexCounter > 0)
                return true;
            else
            {
                $indexCounter = sizeOf($this->menu);
                return false;
            }
        }

        function getType()
        {
            return $this->menu[$this->indexCounter][0];
        }
        function getDepth()
        {
            return $this->depth - $this->menu[$this->indexCounter][1];
        }
        function getID()
        {
            return $this->menu[$this->indexCounter][2];
        }
        function getTitle()
        {
            return $this->menu[$this->indexCounter][3];
        }
        function getImage1()
        {
            return $this->menu[$this->indexCounter][4];
        }
        function getImage2()
        {
            return $this->menu[$this->indexCounter][5];
        }
        function getContentType()
        {
            return $this->menu[$this->indexCounter][6];
        }


        function getNeighbourPagesOfPage($pageID)
        {
            $catID = $this->getCatOfPage($pageID);
            return $this->getPagesInCat($catID);
        } // function getNeighbourPages



        function getPagesInCat($catID)
        {
            // zweidimensionales Array, horizontale Werte: [ID der Seite, Titel der Seite, Pfad zum Bild1, Pfad zum Bild2]
            $a = array();
            $query = sprintf("select    page.ID,
                                        page.title,
                                        image1.imageFilename as image1,
                                        image2.imageFilename as image2,
                                        t.name as contentType
                              from      contentType t,
                                        contentPage page
                              left join imageImage image1
                              on        image1.ID=page.image1ID
                              left join imageImage image2
                              on        image2.ID=page.image2ID
                              where     page.catID=%s and
                                        page.contentTypeID=t.ID
                              order by  page.ord"
                             , $catID);
            $this->db->executeQuery($query);
            while($this->db->nextRow())
                array_push ($a, array($this->db->getValue("ID"), $this->db->getValue("title"), $this->db->getValue("image1"), $this->db->getValue("image2"), $this->db->getValue("contentType") ) );
            return $a;
        } // getPagesInCat

        function getSubCatsInCat($catID)
        {
            // zweidimensionales Array, horizontale Werte: [ID der Seite, Titel der Seite, Pfad zum Bild1, Pfad zum Bild2]
            $a = array();
            $query = sprintf("select    cat.ID,
                                        cat.title,
                                        image1.imageFilename as image1,
                                        image2.imageFilename as image2,
                                        t.name as contentType
                              from      contentType t,
                                        contentCat cat
                              left join imageImage image1
                              on        image1.ID=cat.image1ID
                              left join imageImage image2
                              on        image2.ID=cat.image2ID
                              where     cat.superID=%s and
                                        cat.contentTypeID=t.ID
                              order by  cat.ord"
                             , $catID);
            $this->db->executeQuery($query);
            while($this->db->nextRow())
                array_push ($a, array($this->db->getValue("ID"), $this->db->getValue("title"), $this->db->getValue("image1"), $this->db->getValue("image2"), $this->db->getValue("contentType") ) );
            return $a;
        } // getCatsInCat



        function getCatOfPage($pageID)
        {
            $query = sprintf("select    catID
                              from      contentPage
                              where     ID=%s"
                             , $pageID);
            $this->db->executeQuery($query);
            $this->db->nextRow();
            return $this->db->getValue("catID");
        }

        function getSuperCatOfCat($catID)
        {
            $query = sprintf("select    superID
                              from      contentCat
                              where     ID=%s"
                             , $catID);
            $this->db->executeQuery($query);
            $this->db->nextRow();
            return $this->db->getValue("superID");
        }


        function getHierarchyString($type, $ID)
        {
            $stack = array();

            if($type == "page")
            {
                $query = sprintf("select    catID, title
                                  from      contentPage
                                  where     ID=%s"
                                 , $ID);
                $this->db->executeQuery($query);
                $this->db->nextRow();

                array_push($stack, $this->db->getValue("title"));
                $catID = $this->db->getValue("catID");
            }
            else
                $catID = $ID;

            while( $catID > 1 )
            {
                $query = sprintf("select    superID, title
                                  from      contentCat
                                  where     ID=%s"
                                 , $catID);
                $this->db->executeQuery($query);
                $this->db->nextRow();

                array_push($stack, $this->db->getValue("title"));
                $catID = $this->db->getValue("superID");
            }

            $ret = array_pop($stack);
            while(count($stack) > 0)
            {
                $ret .= " -> ";
                $ret .= array_pop($stack);
            }

            return $ret;
        }


    } // class Menu

?>
