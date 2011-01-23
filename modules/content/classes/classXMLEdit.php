<?php
    include("classXML.php");
    include("classImageInfo.php");

    class XMLEdit extends XML
    {


        var $headerSize = array("1" => "1 - sehr groß", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6 - sehr klein");
        var $align = array("left" => "linksbündig", "center" => "zentriert", "right" => "rechtsbündig", "justify" => "Blocksatz", "flowing" => "fließend (ohne Umbruch)");
        var $listType = array("." => "ohne Nummerierung", "1" => "numerisch arabisch", "I" => "römisch groß", "i" => "römisch klein", "A" => "alphabetisch groß", "a" => "alphabetisch klein");
        var $insert = array("0" => "einfügen von", "HEADER" => "Überschrift", "TEXT" => "Text", "IMAGE" => "Bild", "LINK" => "Link", "LIST" => "Liste", "COLS" => "Spalten", "SIMPLETABLE" => "einfache Tabelle", "TABLE" => "Tabelle");

        var $error;
        var $db;
        var $type;
        var $id;
        var $tag;
        var $indent=0;
        var $lang;

        function XMLEdit($error, $db, $type, $id)
        {
                include("german.lang");
                $this->lang = $lang;

                $this->error = $error;
                $this->db = $db;
                $this->type = $type;
                $this->id = $id;
                $this->XML();
                // $this->init();
                $this->indent = 1;
        }

        function printStatus($title, $imageID1, $imageID2)
        {
                $this->printSectionPageStatus($title, $imageID1, $imageID2);
        }






/*****************************************************************************
 *                                                                           *
 *                             SELECTION                                     *
 *                                                                           *
 *****************************************************************************/

        function tagOpen($tag)
        {
            $this->tag = $tag;

            print ("\n");
            printf("<!-- START %s -->\n", $tag->getName());
            $this->indent(0);
            printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>\n", $tag->getKey(), $tag->getName());

            switch($tag->getName())
            {
                case "LIST":
                    $this->printSectionListStart();
                    break;
                case "COLS":
                    $this->printSectionColsStart();
                    break;
                case "COL":
                    $this->printSectionColStart();
                    break;
                case "SIMPLETABLE":
                    $this->printSectionSimpleTableStart();
                    break;
                case "SIMPLEROW":
                    if(is_object($this->tag->getSuperTag()))
                    {
                        $o = $this->tag->getSuperTag();
                        $this->indent(0);
                        printf("<script language=\"javascript\">document.save.%s.value++;</script>", $o->getKey()."_R");
                    }

                    $this->printSectionSimpleRowStart();
                    break;
                case "TABLE":
                    $this->printSectionTableStart();
                    break;
                case "ROW":
                    if(is_object($this->tag->getSuperTag()))
                    {
                        $o = $this->tag->getSuperTag();
                        $this->indent(0);
                        printf("<script language=\"javascript\">document.save.%s.value++;</script>", $o->getKey()."_R");
                    }
                    $this->printSectionRowStart();
                    break;
                case "CELL":
                    $this->printSectionCellStart();
                    break;
            }
        }

        function tagData($tag)
        {
            $this->tag = $tag;
            switch($tag->getName())
            {
                case "HEADER":
                    $this->printSectionHeader();
                    break;
                case "TEXT":
                    $this->printSectionText();
                    break;
                case "IMAGE":
                    $this->printSectionImage();
                    break;
                case "LINK":
                    $this->printSectionLink();
                    break;
                case "LISTITEM":
                    $this->printSectionListItem();
                    break;
                case "SIMPLECELL":
                    $this->printSectionSimpleCell();
                    break;
            }
        }

        function tagClose($tag)
        {
            $this->tag = $tag;
            switch($tag->getName())
            {
                case "LIST":
                    $this->printSectionListStop();
                    break;
                case "COLS":
                    $this->printSectionColsStop();
                    break;
                case "COL":
                    $this->printSectionColStop();
                    break;
                case "SIMPLETABLE":
                    $this->printSectionSimpleTableStop();
                    break;
                case "SIMPLEROW":
                    $this->printSectionSimpleRowStop();
                    break;
                case "TABLE":
                    $this->printSectionTableStop();
                    break;
                case "ROW":
                    $this->printSectionRowStop();
                    break;
                case "CELL":
                    $this->printSectionCellStop();
                    break;
            }
        }



/*****************************************************************************
 *                                                                           *
 *                             SECTIONS                                      *
 *                                                                           *
 *****************************************************************************/

        /**
         * PAGE functions
         */
         function printSectionPageStatus($title, $imageID1, $imageID2)
         {
                $this->printTableStart();
                $this->printSpacerFirst();

                $this->printElementInputField($this->lang["title"], "e_001_TITLE", $title);

                $this->printSpacer();
                $this->printElementPageImages($imageID1, $imageID2);
                $this->printSpacerLast();
                $this->printTableStop();
                $this->printSpacerWhite();
                $this->printInsertElement("e_001_000_INSERT");
         }


        /**
         * HEADER functions
         */
        function printSectionHeader()
        {
                $this->printElementStart($this->lang["header"]);
                $this->printElementRow(array("SIZE", "ALIGN"));
                $this->printSpacer();
                $this->printElementInputField($this->lang["text"], $this->tag->getKey()."_TEXT", $this->tag->getData());
                $this->printElementStop();
        }


        /**
         * TEXT functions
         */
        function printSectionText()
        {
                $this->printElementStart($this->lang["text"]);
                $this->printElementRow(array("ALIGN"));
                $this->printSpacer();
                $this->printElementRow(array("TEXTAREA"));
                $this->printElementTextArea($this->lang["text"], $this->tag->getKey()."_TEXT", $this->tag->getData());
                $this->printElementStop();
        }


        /**
         * IMAGE functions
         */
        function printSectionImage()
        {
                $this->printElementStart($this->lang["image"]);
                $this->printElementRow(array("ALIGN"));

                if(($this->tag->getAttribute("ID") == 0) || ($this->tag->getAttribute("ID") == ""))
                    $this->printElementSelectImage();
                else
                    $this->printElementShowImage();

                $this->printElementStop();
        }

        /**
         * LINK functions
         */
        function printSectionLink()
        {
                $this->printElementStart($this->lang["link"]);
                $this->printElementRow(array("ALIGN"));
                $this->printSpacer();
                $this->printElementInputField($this->lang["text2show"], $this->tag->getKey()."_TEXT", $this->tag->getData());
                $this->printSpacer();
                $this->printElementLinkFree();
                $this->printSpacer();
                $this->printElementLinkEmail();
                $this->printSpacer();
                $this->printElementLinkInternpage();
                $this->printSpacer();
                $this->printElementLinkInterncat();
                $this->printElementStop();
        }


        /**
         * LIST functions
         */
        function printSectionListStart()
        {
                $this->printElementStart($this->lang["list"]);
                $this->printElementRow(array("LISTTYPE", "ALIGN"));
                $this->printSpacer();
                $this->printInsertListItem($this->tag->getKey() . "_000_INSERT");
        }

        function printSectionListStop()
        {
                $this->printElementStop();
        }

        function printSectionListItem()
        {
                $this->printElementListItem();
                $this->printSpacer();
                $this->printInsertListItem($this->tag->getKey() . "_INSERT");
        }


        /**
         * COLS functions
         */
        function printSectionColsStart()
        {
                $this->indent(0);
                printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>\n", $this->tag->getKey()."_C", $this->tag->getAttribute("C"));

                $this->printElementStart($this->lang["cols"]);

                if( $this->tag->getAttribute("C") == 0 )
                {
                        $this->printElementColsInit();
                }
                else
                {
                        $this->printElementRow(array("ALIGN"));
                        $this->printSpacer();

                        $this->printRowStart();
                        $this->printWrappingStart();
                        $this->printCorner();
                }
        }

        function printSectionColsStop()
        {
                if( $this->tag->getAttribute("C") != 0 )
                {
                        $this->printCorner();
                        $this->printWrappingStop();
                        $this->printRowStop();
                }
                $this->printElementStop();
        }

        function printSectionColStart()
        {
                $this->printCornerThin();
                $this->printCornerWhiteThin();
                $this->indent(2);
                print("<td width=\"40%\">");
                $this->indent++;
                $this->printInsertElement($this->tag->getKey() . "_000_INSERT");
        }

        function printSectionColStop()
        {
                $this->indent--;
                $this->indent(2);
                print("</td>\n");
                $this->printCornerWhiteThin();
                $this->printCornerThin();
        }


        /**
         * SIMPLETABLE functions
         */
        function printSectionSimpleTableStart()
        {
                $this->indent(0);
                printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>\n", $this->tag->getKey()."_R", $this->tag->getAttribute("R"));
                $this->indent(0);
                printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>\n", $this->tag->getKey()."_C", $this->tag->getAttribute("C"));

                $this->printElementStart($this->lang["simpletable"]);

                if( ($this->tag->getAttribute("R") == 0) && ($this->tag->getAttribute("C") == 0) )
                {
                        $this->printElementSimpleTableInit();
                }
                else
                {
                        $this->indent(0);
                        printf("<script language=\"javascript\">document.save.%s.value=0;</script>\n", $this->tag->getKey()."_R");
                        $this->printElementRow(array("ALIGN"));
                        $this->printSpacer();
                        $this->printInsertElementSimpleRow($this->tag->getKey() . "_000_INSERT");
                        $this->printSpacer();
                }
        }

        function printSectionSimpleTableStop()
        {
                $this->printElementStop();
        }

        function printSectionSimpleRowStart()
        {
                $this->printRowStart();
                $this->printWrappingStart();
                $this->printCorner();
        }

        function printSectionSimpleRowStop()
        {
                $this->printCorner();

                $this->indent(2);
                printf("<input type=\"hidden\" name=\"%s\" value=\"no\"></input>\n", "".$this->tag->getKey()."_DELETE");
                $this->indent(2);
                printf("<td class=\"regCard\"><a href=\"javascript:deleteElement(document.save.%s)\"><img src=\"/images/admin/delete.gif\" width=\"16\" height=\"16\" border=\"0\"></a></td>\n", "".$this->tag->getKey()."_DELETE");
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();

                $this->printSpacer();
                $this->printInsertElementSimpleRow($this->tag->getKey() . "_INSERT");
                $this->printSpacer();
        }

        function printSectionSimpleCell()
        {
                $this->printCornerThin();
                $this->printTextArea($this->tag->getKey()."_TEXT", $this->tag->getData());
                $this->printCornerThin();
        }


        /**
         * TABLE functions
         */
        function printSectionTableStart()
        {
                $this->indent(0);
                printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>\n", $this->tag->getKey()."_R", $this->tag->getAttribute("R"));
                $this->indent(0);
                printf("<input type=\"hidden\" name=\"%s\" value=\"%s\"></input>\n", $this->tag->getKey()."_C", $this->tag->getAttribute("C"));

                $this->printElementStart($this->lang["table"]);

                if( ($this->tag->getAttribute("R") == 0) && ($this->tag->getAttribute("C") == 0) )
                {
                        $this->printElementTableInit();
                }
                else
                {
                        $this->indent(0);
                        printf("<script language=\"javascript\">document.save.%s.value=0;</script>\n", $this->tag->getKey()."_R");
                        $this->printElementRow(array("ALIGN"));
                        $this->printSpacer();
                        $this->printInsertElementRow($this->tag->getKey() . "_000_INSERT");
                        $this->printSpacer();
                }
        }

        function printSectionTableStop()
        {
                $this->printElementStop();
        }

        function printSectionRowStart()
        {
                $this->printRowStart();
                $this->printWrappingStart();
                $this->printCorner();
        }

        function printSectionRowStop()
        {
                $this->printCorner();

                $this->indent(2);
                printf("<input type=\"hidden\" name=\"%s\" value=\"no\"></input>\n", "".$this->tag->getKey()."_DELETE");
                $this->indent(2);
                print ("<td class=\"regCard\">");
                printf("<a href=\"javascript:deleteElement(document.save.%s)\"><img src=\"/images/admin/delete.gif\" width=\"16\" height=\"16\" border=\"0\"></a>", "".$this->tag->getKey()."_DELETE");
                print ("</td>\n");
                $this->printCheckbox($this->tag->getKey()."_TOGGLE", "true", $this->tag->getAttribute("TOGGLE"));
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();

                $this->printSpacer();
                $this->printInsertElementRow($this->tag->getKey() . "_INSERT");
                $this->printSpacer();
        }

        function printSectionCellStart()
        {
                $this->printCornerThin();
                $this->printCornerWhiteThin();
                $this->indent(2);
                print("<td width=\"40%\">");
                $this->indent++;
                $this->printInsertElement($this->tag->getKey() . "_000_INSERT");
        }

        function printSectionCellStop()
        {
                $this->indent--;
                $this->indent(2);
                print("</td>\n");
                $this->printCornerWhiteThin();
                $this->printCornerThin();
        }


/*****************************************************************************
 *                                                                           *
 *                             ELEMENTS                                      *
 *                                                                           *
 *****************************************************************************/

         /**
         * COMMON functions
         */

        function printElementStart($name)
        {
                $this->printTableStart();
                $this->printRowStart();
                $this->printWrappingStart();
/*
                $this->printCornerLO();
                $this->printCorner(3);
                $this->printCornerRO();
                $this->printCornerWhite();
                $this->printRowStop();
                $this->printRowStart();
*/
                $this->printCorner();
                $this->indent(2);
                printf("<td class=\"regCard\" nowrap>&nbsp;&nbsp;<b>%s</b>&nbsp;&nbsp;</td>\n", $name);
                $this->printCorner();
                $this->indent(2);
                printf("<td class=\"regCard\"><a href=\"javascript:deleteElement(document.save.%s)\"><img src=\"/images/admin/delete.gif\" width=\"16\" height=\"16\" border=\"0\"></a></td>\n", "".$this->tag->getKey()."_DELETE");
                $this->indent(2);
                printf("<input type=\"hidden\" name=\"%s\" value=\"no\"></input>\n", "".$this->tag->getKey()."_DELETE");
                $this->printCorner();
                $this->printCornerWhite();
/*
                $this->printRowStop();
                $this->printRowStart();
                $this->printCorner(5);
                $this->printCornerWhite();
*/
                $this->printWrappingStop();
                $this->printRowStop();
                $this->printSpacerRegister();
        }

        function printElementStop()
        {
                // $this->printSpacerLast();
                $this->printTableStop();
                $this->printSpacerWhite();
                $this->printInsertElement($this->tag->getKey() . "_INSERT");
        }




        function printElementRow($elements)
        {
                // get number of elements
                $number = sizeof($elements);

                // start the row
                $this->printRowStart();

                // wrap into a new table
                $this->printWrappingStart();


                $this->printCorner();

                // traverse elements
                for($i=0; $i<$number; $i++)
                {
                        switch($elements[$i])
                        {
                                case "SIZE":
                                        $this->printElementSelectSize();
                                        break;
                                case "ALIGN":
                                        $this->printElementSelectAlign();
                                        break;
                                case "LISTTYPE":
                                        $this->printElementSelectListType();
                                        break;
                        }
                }

                // wrap it out
                $this->printWrappingStop();

                // end of row
                $this->printRowStop();
        }

        function printElementSelectSize()
        {
                $this->printText($this->lang["size"]);
                $this->printCorner();
                $this->printSelect($this->headerSize, $this->tag->getKey()."_SIZE", $this->tag->getAttribute("SIZE"));
                $this->printCorner();
        }

        function printElementSelectAlign()
        {
                $this->printText($this->lang["alignment"]);
                $this->printCorner();
                $this->printSelect($this->align, $this->tag->getKey()."_ALIGN", $this->tag->getAttribute("ALIGN"));
                $this->printCorner();
        }

        function printElementSelectListType()
        {
                $this->printText($this->lang["numbering"]);
                $this->printCorner();
                $this->printSelect($this->listType, $this->tag->getKey()."_TYPE", $this->tag->getAttribute("TYPE"));
                $this->printCorner();
        }

        function printElementInputField($desc, $key, $data)
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printText($desc);
                $this->printCorner();
                $this->printInputField($key, $data);
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printElementTextArea($desc, $key, $data)
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printText($desc);
                $this->printCorner();
                $this->printTextArea($key, $data);
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }


        /**
         * PAGE functions
         */
        function printElementPageImages($imageID1, $imageID2)
        {

                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printInputFieldHidden("e_001_IMAGEID1", $imageID1);
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"right\"><a href=\"javascript:selectImage('imageSelect.php','imageselect', document.save.%s);\">Bild 1: </a></td>\n", "e_001_IMAGEID1");
                if( !($imageID1 == 0) && !($imageID1 == ""))
                {
                        $this->printCorner();
                        $this->printImageFromID($imageID1);
                }
                $this->printCorner();
                $this->printInputFieldHidden("e_001_IMAGEID2", $imageID2);
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"right\"><a href=\"javascript:selectImage('imageSelect.php','imageselect', document.save.%s);\">Bild 2: </a></td>\n", "e_001_IMAGEID2");
                if( !($imageID2 == 0) && !($imageID2 == ""))
                {
                        $this->printCorner();
                        $this->printImageFromID($imageID2);
                }
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }


        /**
         * IMAGE functions
         */
        function printElementSelectImage()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->indent(2);
                printf("<td class=\"regCard\"><a href=\"javascript:selectImage('/modules/image/admin/imageSelect.php','imageselect', document.save.%s);\">Bild auswählen</a></td>\n", $this->tag->getKey()."_ID");
                $this->printInputFieldHidden($this->tag->getKey()."_ID", $this->tag->getAttribute("ID"));
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printElementShowImage()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printImageFromID($this->tag->getAttribute("ID"));
                $this->printInputFieldHidden($this->tag->getKey()."_ID", $this->tag->getAttribute("ID"));
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }


        /**
         * LINK functions
         */
        function printElementLinkFree()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printRadio($this->tag->getKey()."_TYPE", "free", $this->tag->getAttribute("TYPE"));
                $this->printCorner();
                $this->printText($this->lang["freeinput"]);
                $this->printCorner();
                $this->printInputField($this->tag->getKey()."_HREF", $this->tag->getAttribute("HREF"));
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printElementLinkEmail()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printRadio($this->tag->getKey()."_TYPE", "email", $this->tag->getAttribute("TYPE"));
                $this->printCorner();
                $this->printText($this->lang["emailaddress"]);
                $this->printCorner();
                $this->printInputField($this->tag->getKey()."_EMAIL", $this->tag->getAttribute("EMAIL"));
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printElementLinkInternpage()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printRadio($this->tag->getKey()."_TYPE", "internpage", $this->tag->getAttribute("TYPE"));
                $this->printCorner();
                $this->printText($this->lang["internpage"]);
                $this->printCorner();
                $tempArray = array();

                $query = " select    p.ID as ID,
                                     concat(
                                              if ( c2.title='root', '', concat(
                                                                                 if( c3.title='root', '', concat(
                                                                                                                  c3.title,
                                                                                                                  ' -> ' ) ),
                                                                                 c2.title,
                                                                                 ' -> ' ) ),
                                              c1.title,
                                              ' -> ',
                                              p.title ) as title
                           from      contentPage p
                           left join contentCat c1
                           on        p.catID=c1.ID
                           left join contentCat c2
                           on        c1.superID=c2.ID
                           left join contentCat c3
                           on        c2.superID=c3.ID
                           order by  c1.superID,
                                     c1.ord,
                                     p.ord ";

                $this->db->executeQuery($query);
                while($this->db->nextRow())
                        $tempArray[$this->db->getValue("ID")] = $this->db->getValue("title");
                $this->printSelect($tempArray, $this->tag->getKey()."_PAGEID", $this->tag->getAttribute("PAGEID") );

                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printElementLinkInterncat()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printRadio($this->tag->getKey()."_TYPE", "interncat", $this->tag->getAttribute("TYPE"));
                $this->printCorner();
                $this->printText($this->lang["interncat"]);
                $this->printCorner();
                $tempArray = array();

                $query = " select    c1.ID as ID,
                                     concat(
                                              if ( c2.superID='0', '', concat(
                                                                                 if( c3.superID='0', '', concat(
                                                                                                                  c3.title,
                                                                                                                  ' -> ' ) ),
                                                                                 c2.title,
                                                                                 ' -> ' ) ),
                                              c1.title ) as title
                           from      contentCat c1
                           left join contentCat c2
                           on        c1.superID=c2.ID
                           left join contentCat c3
                           on        c2.superID=c3.ID
                           order by  c1.superID,
                                     c1.ord ";

echo $query;
                $this->db->executeQuery($query);
                while($this->db->nextRow())
                        $tempArray[$this->db->getValue("ID")] = "" . $this->db->getValue("title");
                $this->printSelect($tempArray, $this->tag->getKey()."_CATID", $this->tag->getAttribute("CATID") );

                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }


        /**
         * LIST functions
         */
        function printElementListItem()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printText("Text");
                $this->printCorner();
                $this->printTextArea($this->tag->getKey()."_TEXT", $this->tag->getData());
                $this->printCorner();
                $this->indent(2);
                printf("<input type=\"hidden\" name=\"%s\" value=\"no\"></input>\n", "".$this->tag->getKey()."_DELETE");
                $this->indent(2);
                printf("<td class=\"regCard\"><a href=\"javascript:deleteElement(document.save.%s)\"><img src=\"/images/admin/delete.gif\" width=\"16\" height=\"16\" border=\"0\"></a></td>\n", "".$this->tag->getKey()."_DELETE");
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }


        /**
         * COLS functions
         */
        function printElementColsInit()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printText($this->lang["numofcols"]);
                $this->printCorner();
                $this->printTextArea($this->tag->getKey()."_NOC", $this->tag->getData());

                $this->printWrappingStop();
                $this->printRowStop();
        }


        /**
         * SIMPLETABLE functions
         */
        function printElementSimpleTableInit()
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printText($this->lang["numofrows"]);
                $this->printCorner();
                $this->printTextArea($this->tag->getKey()."_NOR", $this->tag->getData());

                $this->printCorner();
                $this->printText($this->lang["numofcols"]);
                $this->printCorner();
                $this->printTextArea($this->tag->getKey()."_NOC", $this->tag->getData());

                $this->printWrappingStop();
                $this->printRowStop();
        }

        /**
         * TABLE functions
         */
        function printElementTableInit()
        {
                $this->printElementSimpleTableInit();
        }


/*****************************************************************************
 *                                                                           *
 *                            INSERTIONS                                     *
 *                                                                           *
 *****************************************************************************/

        function printInsertElement($key)
        {
                print ("\n");
                print("<!-- START INSERT ELEMENT -->\n");
                $this->printTableStart();
                $this->printRowStart();
                $this->printColStart("right");

                $this->indent(3);
                printf("<select name=\"%s\" size=\"1\" onChange=\"doSubmit()\">\n", $key);
                reset($this->insert);
                while (list ($k, $v) = each ($this->insert) )
                {
                        $this->indent(4);
                        printf("<option value=\"%s\">%s</option>\n", $k, $v);
                }
                $this->indent(3);
                print ("</select>\n");

                $this->printColStop();
                $this->printRowStop();
                $this->printTableStop();
        }

        function printInsertListItem($key)
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->indent(2);
                print ("<td align=\"right\" class=\"regCard\">\n");
                $this->indent(3);
                printf("<input type=\"hidden\" name=\"%s\" value=\"\">\n", $key);
                $this->indent(3);
                printf("<a href=\"javascript:insertElement(document.save.%s, 'LISTITEM')\"><img src=\"/images/admin/add.gif\" width=\"16\" height=\"16\" border=\"0\"></a>\n", $key);
                $this->indent(2);
                print ("</td>\n");
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printInsertElementSimpleRow($key)
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->indent(2);
                print ("<td align=\"right\" class=\"regCard\">\n");
                $this->indent(3);
                printf("<input type=\"hidden\" name=\"%s\" value=\"\">\n", $key);
                $this->indent(3);
                printf("<a href=\"javascript:insertElement(document.save.%s, 'SIMPLEROW')\"><img src=\"/images/admin/add.gif\" width=\"16\" height=\"16\" border=\"0\"></a>\n", $key);
                $this->indent(2);
                print ("</td>\n");
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        }

        function printInsertElementRow($key)
        {
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->indent(2);
                print ("<td align=\"right\" class=\"regCard\">\n");
                $this->indent(3);
                printf("<input type=\"hidden\" name=\"%s\" value=\"\">\n", $key);
                $this->indent(3);
                printf("<a href=\"javascript:insertElement(document.save.%s, 'ROW')\"><img src=\"/images/admin/add.gif\" width=\"16\" height=\"16\" border=\"0\">&nbsp;&nbsp;Zeile einfügen</a>\n", $key);
                $this->indent(2);
                print ("</td>\n");
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
        } // function insertElement

        function printSpacerFirst()
        {
/*
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCornerLO();
                $this->printCornerRO();

                $this->printWrappingStop();
                $this->printRowStop();
*/
        }


        function printSpacerRegister()
        {
/*
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printCornerRO();

                $this->printWrappingStop();
                $this->printRowStop();
*/
        }

        function printSpacer()
        {
/*
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCorner();
                $this->printCorner();

                $this->printWrappingStop();
                $this->printRowStop();
*/
        }

        function printSpacerLast()
        {
/*
                $this->printRowStart();
                $this->printWrappingStart();

                $this->printCornerLU();
                $this->printCornerRU();

                $this->printWrappingStop();
                $this->printRowStop();
*/
        }

        function printSpacerWhite()
        {
                print ("<br>");
        }

        function printWrappingStart()
        {
                $this->printColStart();
                $this->indent++;
                $this->printTableStart();
                $this->printRowStart();
        }

        function printWrappingStop()
        {
                $this->printRowStop();
                $this->printTableStop();
                $this->indent--;
                $this->printColStop();
        }












        /**
         *
         * Basic Functions
         *
         *
         */
        function printTableStart()
        {
                $this->indent(0);
                print ("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
        }

        function printTableStop()
        {
                $this->indent(0);
                print ("</table>\n");

                if($this->indent == 1)
                    print("\n\n\n\n");
        }
        function printRowStart()
        {
                $this->indent(1);
                print ("<tr>\n");
        }
        function printRowStop()
        {
                $this->indent(1);
                print ("</tr>\n");
        }
        function printColStart($align="")
        {
                $this->indent(2);
                if($align == "")
                        print ("<td>\n");
                else
                        printf("<td align=\"%s\">\n", $align);
        }
        function printColStop()
        {
                $this->indent(2);
                print ("</td>\n");
        }

        function printCornerLO()
        {
                $this->indent(2);
                print ("<td class=\"regCard\" align=\"left\" valign=\"top\"><img src=\"/images/admin/lo.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n");
        }
        function printCornerRO()
        {
                $this->indent(2);
                print ("<td class=\"regCard\" align=\"right\" valign=\"top\"><img src=\"/images/admin/ro.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n");
        }
        function printCornerLU()
        {
                $this->indent(2);
                print ("<td class=\"regCard\" align=\"left\" valign=\"bottom\"><img src=\"/images/admin/lu.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n");
        }
        function printCornerRU()
        {
                $this->indent(2);
                print ("<td class=\"regCard\" align=\"right\" valign=\"bottom\"><img src=\"/images/admin/ru.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n");
        }
        function printCorner($colspan=1)
        {
                //$this->indent(2);
                //printf("<td class=\"regCard\" colspan=\"%s\"><img src=\"/images/admin/px.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n", $colspan);
        }
        function printCornerThin($colspan=1)
        {
                $this->indent(2);
                printf("<td class=\"regCard\" colspan=\"%s\" width=\"5\"><img src=\"/images/admin/px.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n", $colspan);
        }
        function printCornerWhite()
        {
                $this->indent(2);
                print ("<td width=\"100%\"><img src=\"/images/admin/px.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n");
        }
        function printCornerWhiteThin()
        {
                $this->indent(2);
                printf("<td><img src=\"/images/admin/px.gif\" width=\"5\" height=\"5\" border=\"0\"></td>\n", $colspan);
        }

        function printText($text)
        {
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"right\">%s: </td>\n", $text);
        }
        function printImage($image)
        {
                $this->indent(2);
                printf("<td class=\"regCard\"><img src=\"/images/%s\"></td>\n", $image);
        }
        function printImageFromID($ID)
        {
                $image = new ImageInfo($this->error, $this->db);
                $image->createDetails($ID);
                if ($image->getThumbFilename() != "")
                {
                        $this->indent(2);
                        printf("<td class=\"regCard\"><img src=\"/images/%s\"></td>\n", $image->getThumbFilename());
                }
                else
                {
                        $this->indent(2);
                        printf("<td class=\"regCard\"><img src=\"/images/%s\"></td>\n", $image->getImageFilename());
                }
        }
        function printSelect($array, $key, $data)
        {
                $this->indent(2);
                print ("<td class=\"regCard\" align=\"left\">\n");
                $this->indent(3);
                printf("<select class=\"regCard\" name=\"%s\" size=\"1\">\n", $key);
                while (list ($k, $v) = each ($array) )
                {
                        $this->indent(4);
                        printf("<option class=\"regCard\" value=\"%s\" %s>%s</option>\n", $k, ($k==$data?"selected":""), $v);
                }
                $this->indent(3);
                print ("</select>\n");
                $this->indent(2);
                print ("</td>\n");
        }

        function printInputField($key, $data)
        {
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"left\"><input class=\"regCard\" size=\"40\" type=\"text\" name=\"%s\" value=\"%s\"></input></td>\n", $key, $data);
        }
        function printTextArea($key, $data)
        {
                $length = strlen($data);
                $rows = 5;

                if($length < 20)
                        $this->printInputField($key, $data);
                else
                {
                        $rows = intval($length / 20);
                        if($rows > 5)
                            $rows = 5;

                        $this->indent(2);
                        printf("<td class=\"regCard\" align=\"left\"><textarea class=\"regCard\" rows=\"%s\" cols=\"40\" name=\"%s\">%s</textarea></td>\n", $rows, $key, $data);
                }
        }
        function printInputFieldHidden($key, $data)
        {
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"left\"><input class=\"regCard\" type=\"hidden\" name=\"%s\" value=\"%s\">&nbsp;</input></td>\n", $key, $data);
        }
        function printRadio($key, $value, $data)
        {
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"left\"><input class=\"regCard\" type=\"radio\" name=\"%s\" value=\"%s\" %s>&nbsp;</input></td>\n", $key, $value, $data==$value?"checked":"");
        }
        function printCheckbox($key, $value, $data)
        {
                $this->indent(2);
                printf("<td class=\"regCard\" align=\"left\"><input class=\"regCard\" type=\"checkbox\" name=\"%s\" value=\"%s\" %s>&nbsp;</input></td>\n", $key, $value, $data==$value?"checked":"");
        }


        function indent($offset=0)
        {
/*
                for($ind = 0; $ind < $offset; $ind ++)
                {
                        print("  ");
                }

                for($ind = 0; $ind < $this->indent; $ind ++)
                {
                        print("      ");
                }
*/
        }

    } // class XML

?>