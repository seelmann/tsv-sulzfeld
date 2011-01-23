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

        function hasUserNewsCreatePermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserNews where username='%s' and createPerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }

        function hasUserNewsModifyPermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserNews where username='%s' and modifyPerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }

        function hasUserNewsDeletePermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserNews where username='%s' and deletePerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }
} // class Permcheck
?>