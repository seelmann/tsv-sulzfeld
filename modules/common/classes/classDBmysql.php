<?php

    class DBmysql
    {
        var $host     = "";
        var $user     = "";
        var $password = "";
        var $database = "";

        var $error;

        var $linkID = 0;
        var $result;
        var $row;

        function DBmysql($error)
        {
            $this->error = $error;
            @$this->linkID = mysql_pconnect($this->host, $this->user, $this->password);

            if(!$this->linkID)
                $this->error->printErrorPage("Fehler beim Verbinden mit der Datenbank");

            if(!@mysql_select_db($this->database, $this->linkID))
                $this->error->printErrorPage("Fehler bei der Datenbankauswahl");
        }

        function getLinkID()
        {
            return $this->linkID;
        }

        function executeQuery($query)
        {
            $this->result = mysql_query($query, $this->linkID);
            if(!$this->result)
            {
                $this->error->printErrorPage("Datenbankfehler<br><br>".mysql_error());
            }
            return true;
        }

        function nextRow()
        {
            if($r = mysql_fetch_array($this->result))
            {
                $this->row = $r;
                return true;
            }
            else
                return false;
        }

        function getValue($key)
        {
            return $this->row["$key"];
        }

        function getNumRows()
        {
            return mysql_num_rows($this->result);
        }

        function getResult()
        {
            return $this->result;
        }

        function getInsertID()
        {
            return mysql_insert_id($this->linkID);
        }

        function getError()
        {
            return $this->error;
        }

    } // class DBmysql

?>
