<?php
        include_once("classValidation.php");

        class Sport
        {
                // attributes
                var $id = 0;
                var $name = "";

                // static attributes
                var $db;
                var $validate;
 
                function Sport($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->name = "";
                }

                function create($name)
                {
                        if( !($this->validate->countString($name)>3) )
                                return false;

                        $this->name = $name;

                        return $this->save();
                }
                function modify($name)
                {
                        if( !($this->validate->countString($name)>3) )
                                return false;

                        $this->name = $name;

                        return $this->save();
                }
                function delete()
                {
                        // Abhängigkeiten überprüfen: league
                        $query = sprintf("select count(ID) as number from lmLeague where sportID='%s'", $this->id);
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        if($this->db->getValue("number") == 0)
                        {
                                $query = sprintf("delete from lmSport where ID='%s'", $this->id);
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
                                $query = sprintf("select * from lmSport where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update lmSport set name='%s' where ID='%s'", $this->name, $this->id);
                                        $this->db->executeQuery($query);
                                        return true;
                                }
                        }

                        // check for insert: If the name already exists in database (its unique) do nothing else insert the sport
                        $query = sprintf("select * from lmSport where name='%s'", $this->name);
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                return false;
                        }
                        else
                        {
                                $query = sprintf("insert into lmSport (name) values ('%s')", $this->name);
                                $this->db->executeQuery($query);
                                $this->id = $this->db->getInsertID();
                                return $this->id;
                        }
                }
                function load($id)
                {
                        $query = sprintf("select * from lmSport where ID='%s'", $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->name = $this->db->getValue("name");
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

        }

        class SportIterator
        {
                var $list = array();
                var $db;

                function SportIterator($db)
                {
                        $this->db = $db;
                }

                function createIterator()
                {
                        $query = "select ID from lmSport order by name asc";
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
                        $o = new Sport($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>
