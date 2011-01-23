<?php

    class ImageInfo
    {
        var $error;
        var $db;
        var $imagePath;

        var $a = array();
        var $index = 0;
        var $number = 0;

        var $ID;


        function ImageInfo($error, $db)
        {
            $this->error = $error;
            $this->db = $db;
            $this->imagePath = "/images";

        }

        function getNumberOfImages()
        {
            $query = "select count(*) as number from imageImage";
            $this->db->executeQuery($query);
            $this->db->nextRow();
            return $this->db->getValue("number");
        }

        function getUsedMemory()
        {
            $query = "select ( sum(imageSize) + sum(thumbSize) ) as size from imageImage";
            $this->db->executeQuery($query);
            $this->db->nextRow();
            return $this->db->getValue("size");
        }

        function createList()
        {
            $query = sprintf(" select    c.ID,
                                         DATE_FORMAT(c.createDate, '%%d.%%m.%%Y %%H:%%i:%%s') as createDate,
                                         c.description,
                                         c.imageSize,
                                         c.imageWidth,
                                         c.imageHeight,
                                         c.imageFilename,
                                         c.imageType,
                                         c.thumbSize,
                                         c.thumbWidth,
                                         c.thumbHeight,
                                         c.thumbFilename,
                                         c.thumbType,
                                         c.lockDate,
                                         a.realname
                               from      imageImage c
                               left join authUser a
                               on        c.lockUsername=a.username
                               order by  c.ID
                             ", $ID);
            $this->db->executeQuery($query);
            while($this->db->nextRow())
                $this->a[] = array( $this->db->getValue("ID"),
                                    $this->db->getValue("createDate"),
                                    $this->db->getValue("description"),
                                    $this->db->getValue("imageSize"),
                                    $this->db->getValue("imageWidth"),
                                    $this->db->getValue("imageHeight"),
                                    $this->db->getValue("imageFilename"),
                                    $this->db->getValue("imageType"),
                                    $this->db->getValue("thumbSize"),
                                    $this->db->getValue("thumbWidth"),
                                    $this->db->getValue("thumbHeight"),
                                    $this->db->getValue("thumbFilename"),
                                    $this->db->getValue("thumbType"),
                                    $this->db->getValue("lockDate"),
                                    $this->db->getValue("realname"),
                                  );
            $this->number = sizeof($this->a);
        }

        function createDetails($ID)
        {
            $query = sprintf(" select   c.ID,
                                        DATE_FORMAT(c.createDate, '%%d.%%m.%%Y %%H:%%i:%%s') as createDate,
                                        c.description,
                                        c.imageSize,
                                        c.imageWidth,
                                        c.imageHeight,
                                        c.imageFilename,
                                        c.imageType,
                                        c.thumbSize,
                                        c.thumbWidth,
                                        c.thumbHeight,
                                        c.thumbFilename,
                                        c.thumbType,
                                        c.lockDate,
                                        a.realname
                               from     imageImage c
                               left join authUser a
                               on       c.lockUsername=a.username
                               where    c.ID=%s
                             ", $ID);
            $this->db->executeQuery($query);
            $this->db->nextRow();
            $this->a[] = array( $this->db->getValue("ID"),
                                $this->db->getValue("createDate"),
                                $this->db->getValue("description"),
                                $this->db->getValue("imageSize"),
                                $this->db->getValue("imageWidth"),
                                $this->db->getValue("imageHeight"),
                                $this->db->getValue("imageFilename"),
                                $this->db->getValue("imageType"),
                                $this->db->getValue("thumbSize"),
                                $this->db->getValue("thumbWidth"),
                                $this->db->getValue("thumbHeight"),
                                $this->db->getValue("thumbFilename"),
                                $this->db->getValue("thumbType"),
                                $this->db->getValue("lockDate"),
                                $this->db->getValue("realname"),
                              );
            $this->number = sizeof($this->a);
            $this->nextImage();
        }


        function nextImage()
        {
                if($this->index < $this->number)
                {
                        $this->ID = $this->a[$this->index][0];
                        $this->createDate = $this->a[$this->index][1];
                        $this->description = $this->a[$this->index][2];
                        $this->imageSize = $this->a[$this->index][3];
                        $this->imageWidth = $this->a[$this->index][4];
                        $this->imageHeight = $this->a[$this->index][5];
                        $this->imageFilename = $this->a[$this->index][6];
                        $this->imageType = $this->a[$this->index][7];
                        $this->thumbSize = $this->a[$this->index][8];
                        $this->thumbWidth = $this->a[$this->index][9];
                        $this->thumbHeight = $this->a[$this->index][10];
                        $this->thumbFilename = $this->a[$this->index][11];
                        $this->thumbType = $this->a[$this->index][12];
                        $this->lockDate = $this->a[$this->index][13];
                        $this->realname = $this->a[$this->index][14];
                        $this->index++;
                        return true;
                }
                else
                        return false;
        }

        function getID()
        {
            return $this->ID;
        }
        function getCreateDate()
        {
            return $this->createDate;
        }
        function getDescription()
        {
            return $this->description;
        }

        function getImageSize()
        {
            return $this->imageSize;
        }
        function getImageWidth()
        {
            return $this->imageWidth;
        }
        function getImageHeight()
        {
            return $this->imageHeight;
        }
        function getImageFilename()
        {
            return $this->imageFilename;
        }
        function getImageType()
        {
            return $this->imageType;
        }

        function getThumbSize()
        {
            return $this->thumbSize;
        }
        function getThumbWidth()
        {
            return $this->thumbWidth;
        }
        function getThumbHeight()
        {
            return $this->thumbHeight;
        }
        function getThumbFilename()
        {
            return $this->thumbFilename;
        }
        function getThumbType()
        {
            return $this->thumbType;
        }

        function getLockDate()
        {
            return $this->lockDate;
        }

        function getLockName()
        {
            return $this->realname;
        }

    } // class ImageInfo

?>