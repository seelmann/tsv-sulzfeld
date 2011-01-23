<?php
        include_once("classValidation.php");

        class League
        {
                // attributes
                var $id = 0;
                var $name = "";
                var $activeYouth = "active";
                var $sport = null;

                // static attributes
                var $db;
                var $validate;

                function League($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->name = "";
                        $this->activeYouth = "active";
                        $this->sport = null;
                }

                function create($name, $activeYouth, $sport)
                {
                        if( !($this->validate->countString($name)>3) )
                                return false;
                        if( ! ( ($activeYouth == "active") || ($activeYouth == "youth") ) )
                                return false;
                        if( ! (is_object($sport) && (get_class($sport)=="sport") )  )
                                return false;

                        $this->name = $name;
                        $this->activeYouth = $activeYouth;
                        $this->sport = $sport;

                        return $this->save();
                }
                function modify($name, $activeYouth, $sport)
                {
                        if( !($this->validate->countString($name)>3) )
                                return false;
                        if( ! ( ($activeYouth == "active") || ($activeYouth == "youth") ) )
                                return false;
                        if( ! (is_object($sport) && (get_class($sport)=="sport") )  )
                                return false;

                        $this->name = $name;
                        $this->activeYouth = $activeYouth;
                        $this->sport = $sport;

                        return $this->save();
                }
                function delete()
                {
                        // Abhängigkeiten überprüfen: leagueInSeason
                        $query = sprintf("select count(leagueID) as number from lmLeagueInSeason where leagueID='%s'", $this->id);
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        if($this->db->getValue("number") == 0)
                        {
                                $query = sprintf("delete from lmLeague where ID='%s'", $this->id);
                                return $this->db->executeQuery($query);
                        }
                        else
                                return false;
                }

                function save()
                {
                        // check for update: If the ID is set and already exists in database then do an update
                        if($this->id > 0)
                        {
                                $query = sprintf("select * from lmLeague where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update lmLeague set name='%s', sportID='%s', activeYouth='%s' where ID='%s'", $this->name, $this->sport->getID(), $this->activeYouth, $this->id);
                                        $this->db->executeQuery($query);
                                        return true;
                                }
                        }

                        // check for insert: If the name and sportID and activeYouth already exists in database (are unique) do nothing else insert the league
                        $query = sprintf("select * from lmLeague where name='%s' and sportID='%s' and activeYouth='%s'", $this->name, $this->sport->getID(), $this->activeYouth);
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                return false;
                        }
                        else
                        {
                                $query = sprintf("insert into lmLeague (name, sportID, activeYouth) values ('%s', '%s', '%s')", $this->name, $this->sport->getID(), $this->activeYouth);
                                $this->db->executeQuery($query);
                                $this->id = $this->db->getInsertID();
                                return $this->id;
                        }
                }

                function load($id)
                {
                        $query = sprintf("select * from lmLeague where ID='%s'", $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->name = $this->db->getValue("name");
                                $sid = $this->db->getValue("sportID");
                                $this->activeYouth = $this->db->getValue("activeYouth");

                                $this->sport = new Sport($this->db);
                                $this->sport->load($sid);
                                return true;
                        }
                        else
                        {
                                return false;
                        }
                }

                function setDB($db)
                {
                        $this->db = $db;
                }
                function setSport($sport)
                {
                        $this->sport = $sport;
                }

                function getID()
                {
                        return $this->id;
                }
                function getName()
                {
                        return $this->name;
                }
                function getSport()
                {
                        return $this->sport;
                }
                function getSportID()
                {
                        return $this->sport->getID();
                }
                function getActiveYouth()
                {
                        return $this->activeYouth;
                }
        }

        class LeagueIterator
        {
                var $list = array();
                var $db;

                function LeagueIterator($db)
                {
                        $this->db = $db;
                }

                function createSportIterator($sport)
                {
                        $query = sprintf("select   ID
                                          from     lmLeague
                                          where    sportID='%s'
                                          order by activeYouth,
                                                   name"
                                         , $sport->getID()) ;
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");
                }

                function createActiveYouthIterator($activeYouth)
                {
                }

                function createSportActiveYouthIterator($sport, $activeYouth)
                {
                }

                function createIterator()
                {
                        $query = sprintf("select    l.ID
                                          from      lmLeague l
                                          left join lmSport s
                                          on        s.ID = l.sportID
                                          order by  s.name,
                                                    l.activeYouth,
                                                    l.name ");
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");
                }
                function hasNext()
                {
                        return sizeof($this->list) > 0;
                }
                function next()
                {
                        $o = new League($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>
