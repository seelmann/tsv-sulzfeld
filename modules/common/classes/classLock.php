<?php
    class Lock
    {
        var $error;
        var $db;

        var $timeout;

        var $table;
        var $username;
        var $sessionID;

        function Lock($error, $db, $table)
        {
            global $sUser;
            $this->error = $error;
            $this->db = $db;
            $this->timeout = 3600;
            $this->ID = 0;
            $this->table = $table;
            $this->username = $sUser["username"];
            $this->sessionID = session_id();
            $this->freeLocks();
        }

        function checkID($ID)
        {
            if($ID > 0)
                return true;
            else
                return false;
        }

        function setLock($ID)
        {
            $this->freeLocks();

            if($this->checkID($ID))
            {
                if($this->isLocked($ID) && !$this->hasLock($ID))
                {
                    $this->error->printErrorPage("locked");
                }
                else // set Lock
                {
                    if($this->hasLock($ID))
                        $ret = false;
                    else
                        $ret = true;

                    $query = sprintf(" update %s
                                       set    lockUsername='%s',
                                              lockDate=now(),
                                              lockSessionID='%s'
                                       where  ID=%s
                                     ", $this->table, $this->username , $this->sessionID, $ID);
                    $this->db->executeQuery($query);

                    return $ret;
                }
            }
            else
            {
                $this->error->printErrorPage("Keine ID spezifiziert.");
            }
        }

        function hasLock($ID)
        {
            $this->freeLocks();

            $query = sprintf(" select  *
                               from    %s
                               where   lockUsername='%s'
                               and     lockSessionID='%s'
                               and     ID=%s
                             ", $this->table, $this->username, $this->sessionID, $ID);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 1)
            {
                // echo "hasLock = true <br>";
                return true;
            }
            else
            {
                // echo "hasLock = false <br>";
                return false;
            }
        }

        function freeLocks()
        {
            $query = sprintf(" update  %s
                               set     lockUsername='',
                                       lockDate='0000-00-00 00:00:00',
                                       lockSessionID=''
                               where   lockDate < DATE_SUB(now(), INTERVAL %s SECOND)
                             ", $this->table, $this->timeout);
            $this->db->executeQuery($query);
        }

        function isLocked($ID)
        {
            $this->freeLocks();

            $query = sprintf(" select  *
                               from    %s
                               where   ID=%s
                               and     lockUsername=''
                               and     lockSessionID=''
                             ", $this->table, $ID);
            $this->db->executeQuery($query);
            if($this->db->getNumRows() == 1)
            {
                // echo "isLocked = false <br>";
                return false;
            }
            else
            {
                // echo "isLocked = true <br>";
                return true;
            }
        }

        function removeLock($ID)
        {
            if($this->hasLock($ID))
            {
                $query = sprintf(" update  %s
                                   set     lockUsername='',
                                           lockDate='0000-00-00 00:00:00',
                                           lockSessionID=''
                                   where   ID=%s
                                 ", $this->table, $ID);
                $this->db->executeQuery($query);
            }
            else
            {
                $this->error->printErrorPage("Lock nicht gesetzt.");
            }
            $this->freeLocks();
        }

    } // class Lock
?>