<?php

        class DB
        {
                var $db;

                function DB($error)
                {
                        $this->db = new DBmysql($error);
                        return $this->db;
                }
        }

?>
