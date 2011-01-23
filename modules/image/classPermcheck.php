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

        function hasUserImageUploadPermission()
        {
            global $sUser;

            if(!isset($sUser))
                return false;

            $query = sprintf("select * from authUserImage where username='%s' and uploadPerm='yes'", $sUser["username"]);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        } // funtion hasUserImageUploadPermission
} // class Permcheck
?>