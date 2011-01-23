<?php
    class Permcheck
    {
        var $db;
        var $error;

        function Permcheck($db, $error)
        {

            $this->error = $error;
            $this->db = $db;
        }

        function hasUserPagePermission($pageID)
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserPage where username='%s' and pageID=%s", $sUser["username"], $pageID);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
            {
                $query = sprintf("select catID from contentPage where ID=%s", $pageID);
                $this->db->executeQuery($query);
                $this->db->nextRow();
                return $this->hasUserCatPermission($this->db->getValue("catID"));
            }
            else
            {
                return true;
            }
        } // funtion hasUserPagePermission

        function hasUserCatPermission($catID)
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            if($catID == 0)
            {
                return false;
            }

            $query = sprintf("select * from authUserCat where username='%s' and catID=%s", $sUser["username"], $catID);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
            {
                $query = sprintf("select superID from contentCat where ID=%s", $catID);
                $this->db->executeQuery($query);
                $this->db->nextRow();
                return $this->hasUserCatPermission($this->db->getValue("superID"));
            }
            else
            {
                return true;
            }
        } // funtion hasUserCatPermission
} // class Permcheck
?>