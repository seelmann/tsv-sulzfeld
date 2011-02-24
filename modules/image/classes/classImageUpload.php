<?php
    include("classLock.php");

    class ImageUpload
    {
        var $error;
        var $db;
        var $lock;
        var $imagePath;

        var $ID;

        var $imageType;
        var $imageWidth;
        var $imageHeight;
        var $imageSize;

        var $thumbType;
        var $thumbWidth;
        var $thumbHeight;
        var $thumbSize;


        function ImageUpload($error, $db)
        {
            $this->error = $error;
            $this->db = $db;
            $this->lock = new Lock($error, $db, "imageImage");
            // $this->imagePath = "/www/htdocs/tsvsulz/images/";
            $this->imagePath = "/homepages/25/d358692369/htdocs/tsv-sulzfeld/images/";
            $this->ID = 0;

        }

        function create($description)
        {
            // neuer Eintrag und ID bestimmen
            $query = sprintf("insert into imageImage (description, createDate) values ('%s', now())", $description);
            $this->db->executeQuery($query);
            $this->ID = $this->db->getInsertID();
            $this->lock->setLock($this->ID);
        }

        function uploadImage($image, $imageSize)
        {
            if($this->lock->hasLock($this->ID))
            {
                $info = getImageSize($image);
                $this->imageWidth = $info[0];
                $this->imageHeight = $info[1];
                $this->imageType = $info[2];
                $this->imageSize = $imageSize;

                // Dateinamen generieren
                switch($this->imageType)
                {
                    case "1":
                        $filename = sprintf("img%05d.gif", $this->ID);
                        break;
                    case "2":
                        $filename = sprintf("img%05d.jpg", $this->ID);
                        break;
                    case "3":
                        $filename = sprintf("img%05d.png", $this->ID);
                        break;
                    case "4":
                        $filename = sprintf("img%05d.swf", $this->ID);
                        break;
                }

                // Bild speichern
                copy($image, $this->imagePath.$filename);

                // Datenbankeintrag updaten
                $query = sprintf("update imageImage
                                  set    imageSize=%s,
                                         imageWidth=%s,
                                         imageHeight=%s,
                                         imageType=%s,
                                         imageFilename='%s'
                                  where  ID=%s"
                                 , $this->imageSize, $this->imageWidth, $this->imageHeight, $this->imageType, $filename, $this->ID);
                $this->db->executeQuery($query);
            }
            else
            {
                // $this->error->printErrorPage("Keine Berechtigung das Bild ".$this->ID." zu bearbeiten");
            }
        }

        function uploadThumb($thumb, $thumbSize, $ID)
        {
            $this->ID = $ID;
            if($this->lock->hasLock($this->ID))
            {
                $info = getImageSize($thumb);
                $this->thumbWidth = $info[0];
                $this->thumbHeight = $info[1];
                $this->thumbType = $info[2];
                $this->thumbSize = $thumbSize;

                // Dateinamen generieren
                switch($this->thumbType)
                {
                    case "1":
                        $filename = sprintf("thumb%05d.gif", $this->ID);
                        break;
                    case "2":
                        $filename = sprintf("thumb%05d.jpg", $this->ID);
                        break;
                    case "3":
                        $filename = sprintf("thumb%05d.png", $this->ID);
                        break;
                    case "4":
                        $filename = sprintf("thumb%05d.swf", $this->ID);
                        break;
                }

                // Bild speichern
                copy($thumb, $this->imagePath.$filename);

                // Datenbankeintrag updaten
                $query = sprintf("update imageImage
                                  set    thumbSize=%s,
                                         thumbWidth=%s,
                                         thumbHeight=%s,
                                         thumbType=%s,
                                         thumbFilename='%s'
                                  where  ID=%s"
                                 , $this->thumbSize, $this->thumbWidth, $this->thumbHeight, $this->thumbType, $filename, $this->ID);
                $this->db->executeQuery($query);
            }
            else
            {
                // $this->error->printErrorPage("Keine Berechtigung das Bild ".$this->ID." zu bearbeiten");
            }
        }

        function createThumb($ID)
        {
            $this->ID = $ID;
            if($this->lock->hasLock($this->ID))
            {
                // Originalbild bestimmen
                $query = sprintf(" select imageFilename,
                                          imageWidth,
                                          imageHeight
                                   from   imageImage
                                   where  ID=%s
                                 ", $this->ID);
                $this->db->executeQuery($query);
                $this->db->nextRow();
                $imageFilename = $this->db->getValue("imageFilename");
                $imageWidth = $this->db->getValue("imageWidth");
                $imageHeight = $this->db->getValue("imageHeight");

                // Bild erzeugen und speichern
                $thumbWidth = 200;
                $thumbHeight = $imageHeight/($imageWidth/$thumbWidth);
                $image = ImageCreateFromPng($this->imagePath.$imageFilename);
                $thumb = ImageCreate($thumbWidth, $thumbHeight);
                ImageCopyResized($thumb,$image,0,0,0,0,$thumbWidth,$thumbHeight,$imageWidth,$imageHeight);
                $thumbFilename = sprintf("thumb%05d.png", $this->ID);
                ImagePng($thumb,$this->imagePath.$thumbFilename);

                // Datenbankeintrag updaten
                $query = sprintf("update imageImage
                                  set    thumbSize=%s,
                                         thumbWidth=%s,
                                         thumbHeight=%s,
                                         thumbType=%s,
                                         thumbFilename='%s'
                                  where  ID=%s"
                                 , 0, $thumbWidth, $thumbHeight, 3, $thumbFilename, $this->ID);
                $this->db->executeQuery($query);
            }
        }


        function endOfUpload($ID)
        {
            $this->lock->removeLock($ID);
        }

        function getImageType()
        {
            return $this->imageType;
        }

        function getID()
        {
            return $this->ID;
        }

        function setID($ID)
        {
            $this->ID = $ID;
            $this->lock->setLock($this->ID);
        }

    } // class Image

?>
