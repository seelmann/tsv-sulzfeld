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

        function hasUserOthermatchCreatePermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserOthermatch where username='%s' and createPerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }

        function hasUserOthermatchModifyPermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserOthermatch where username='%s' and modifyPerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }

        function hasUserOthermatchDeletePermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserOthermatch where username='%s' and deletePerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }

        function hasUserOthermatchResultPermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserOthermatch where username='%s' and resultPerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        }
} // class Permcheck
?>
