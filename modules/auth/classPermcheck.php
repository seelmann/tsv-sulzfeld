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

        function hasUserAdminPermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserAdmin where username='%s' and perm='%s'", $sUser["username"], "admin");
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
                return false;
            else
                return true;
        } // hasUserAdminPermission
} // class Permcheck
?>