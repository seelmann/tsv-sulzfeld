<?php
        include_once("classValidation.php");

        class Season
        {
                // attributes
                var $id = 0;
                var $name = "";
                var $from = "0000-00-00";
                var $to = "0000-00-00";

                // static attributes
                var $db;
                var $validate;

                function Season($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->name = "";
                        $this->from = "0000-00-00";
                        $this->to = "0000-00-00";
                }

                function create($name, $from, $to)
                {
                        if( !($this->validate->checkDate($from) && $this->validate->checkDate($to) && $this->validate->countString($name)>3) )
                                return false;

                        $this->name = $name;
                        $this->from = $from;
                        $this->to = $to;

                        return $this->save();
                }
                function modify($name, $from, $to)
                {
                        if( !($this->validate->checkDate($from) && $this->validate->checkDate($from) && $this->validate->countString($name)>3) )
                                return false;

                        $this->name = $name;
                        $this->from = $from;
                        $this->to = $to;

                        return $this->save();
                }
                function delete()
                {
                        // Abhängigkeiten überprüfen: leagueInSeason
                        $query = sprintf("select count(seasonID) as number from lmLeagueInSeason where seasonID='%s'", $this->id);
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        if($this->db->getValue("number") == 0)
                        {
                                $query = sprintf("delete from lmSeason where ID='%s'", $this->id);
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
                                $query = sprintf("select * from lmSeason where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update lmSeason set name='%s', dateFrom='%s', dateTo='%s' where ID='%s'", $this->name, $this->from, $this->to, $this->id);
                                        $this->db->executeQuery($query);
                                        return true;
                                }
                        }

                        // check for insert: If the name already exists in database (its unique) do nothing else insert the season
                        $query = sprintf("select * from lmSeason where name='%s'", $this->name);
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                return false;
                        }
                        else
                        {
                                $query = sprintf("insert into lmSeason (name, dateFrom, dateTo) values ('%s', '%s', '%s')", $this->name, $this->from, $this->to);
                                $this->db->executeQuery($query);
                                $this->id = $this->db->getInsertID();
                                return $this->id;
                        }
                }
                function load($id)
                {
                        $query = sprintf("select * from lmSeason where ID='%s'", $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->name = $this->db->getValue("name");
                                $this->from = $this->db->getValue("dateFrom");
                                $this->to = $this->db->getValue("dateTo");
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
                function getFrom()
                {
                        return $this->from;
                }
                function getTo()
                {
                        return $this->to;
                }

        }

        class SeasonIterator
        {
                var $list = array();
                var $db;

                function SeasonIterator($db)
                {
                        $this->db = $db;
                }

                function createIterator()
                {
                        $query = "select ID from lmSeason order by dateFrom desc, dateTo asc";
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");
                }
                function createDateIterator($date)
                {
                        $query = sprintf("select ID from lmSeason where '%s' between dateFrom and dateTo order by dateFrom desc, dateTo asc", $date);
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
                        $o = new Season($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>