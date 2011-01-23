<?php
        include_once("classValidation.php");

        class Team
        {
                // attributes
                var $id = 0;
                var $name = "";
                var $shortname = "";
                var $activeYouth = "active";
                var $sport = null;

                // static attributes
                var $db;
                var $validate;

                function Team($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->name = "";
                        $this->shortname = "";
                        $this->activeYouth = "active";
                        $this->sport = null;
                }

                function create($name, $shortname, $activeYouth, $sport)
                {
                        if( !($this->validate->countString($name)>3) )
                                return false;
                        if( !($this->validate->countString($shortname)>3) )
                                return false;
                        if( ! ( ($activeYouth == "active") || ($activeYouth == "youth") ) )
                                return false;
                        if( ! (is_object($sport) && (get_class($sport)=="sport") )  )
                                return false;

                        $this->name = $name;
                        $this->shortname = $shortname;
                        $this->activeYouth = $activeYouth;
                        $this->sport = $sport;

                        return $this->save();
                }
                function modify($name, $shortname, $activeYouth, $sport)
                {
                        if( !($this->validate->countString($name)>3) )
                                return false;
                        if( !($this->validate->countString($shortname)>3) )
                                return false;
                        if( ! ( ($activeYouth == "active") || ($activeYouth == "youth") ) )
                                return false;
                        if( ! (is_object($sport) && (get_class($sport)=="sport") )  )
                                return false;

                        $this->name = $name;
                        $this->shortname = $shortname;
                        $this->activeYouth = $activeYouth;
                        $this->sport = $sport;

                        return $this->save();
                }
                function delete()
                {
                        // Abhängigkeiten überprüfen: teamInLeagueInSeason, alle Matches
                        $query = sprintf("select count(teamID) as number from lmTeamInLeagueInSeason where teamID='%s'", $this->id);
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        if($this->db->getValue("number") == 0)
                        {
                                $query = sprintf("delete from lmTeam where ID='%s'", $this->id);
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
                                $query = sprintf("select * from lmTeam where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update lmTeam set name='%s', shortname='%s', activeYouth='%s', sportID='%s' where ID='%s'", $this->name, $this->shortname, $this->activeYouth, $this->sport->getID(), $this->id);
                                        $this->db->executeQuery($query);
                                        return true;
                                }
                        }

                        // check for insert: If the name and sportID and activeyouth already exists in database (are unique) do nothing else insert the team
                        $query = sprintf("select * from lmTeam where name='%s' and sportID='%s' and activeYouth='%s'", $this->name, $this->sport->getID(), $this->activeYouth);
echo $query;
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                return false;
                        }
                        else
                        {
                                $query = sprintf("insert into lmTeam (name, shortname, activeYouth, sportID) values ('%s','%s','%s', '%s')", $this->name, $this->shortname, $this->activeYouth, $this->sport->getID());
echo $query;
                                $this->db->executeQuery($query);
                                $this->id = $this->db->getInsertID();
                                return $this->id;
                        }
                }




                function load($id)
                {
                        $query = sprintf("select * from lmTeam where ID='%s'", $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->name = $this->db->getValue("name");
                                $this->shortname = $this->db->getValue("shortname");
                                $this->activeYouth = $this->db->getValue("activeYouth");

                                $id = $this->db->getValue("sportID");
                                $this->sport = new Sport($this->db);
                                $this->sport->load($id);
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

                function getID()
                {
                        return $this->id;
                }
                function getName()
                {
                        return $this->name;
                }
                function getShortname()
                {
                        return $this->shortname;
                }
                function getActiveYouth()
                {
                        return $this->activeYouth;
                }
                function getSport()
                {
                        return $this->sport;
                }
        }

        class TeamIterator
        {
                var $list = array();
                var $db;

                function TeamIterator($db)
                {
                        $this->db = $db;
                }

                function createSportIterator($sport)
                {
                        // $query = sprintf("select ID from lmTeam where sportID='%s' order by name", $this->sport->getID());
                }

                function createActiveYouthIterator($activeyouth)
                {
                }

                function createSportActiveYouthIterator($sport, $activeyouth)
                {
                }

                function createLeagueIterator($league)
                {
                        $query = sprintf("select    ID
                                          from      lmTeam
                                          where     sportID = '%s'
                                          and       activeYouth = '%s'
                                          order by  s.name "
                                          , $league->getSportID(), $league->getActiveYouth());
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");
                }

                function createIterator()
                {

// Sortieren nach Namen, Vereinskürzel wird entfernt
                        $query = sprintf("select    t.ID
                                          from      lmTeam t
                                          left join lmSport s
                                          on        s.ID = t.sportID
                                          order by  s.name,
                                                    t.activeYouth,
                                                    substring(t.name,(instr(t.name,' '))) ");


/*
                        $query = sprintf("select    t.ID
                                          from      lmTeam t
                                          left join lmSport s
                                          on        s.ID = t.sportID
                                          order by  s.name,
                                                    t.activeYouth,
                                                    t.name ");
*/
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
                        $o = new Team($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>
