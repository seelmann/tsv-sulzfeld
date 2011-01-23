<?php
        include_once("classValidation.php");
        include_once("classDateTimeFormat.php");
        include_once("classGenMod.php");

        class News extends GenMod
        {
                // attributes
                var $id = 0;
                var $head = "";
                var $text = "";

                // static attributes
                var $db;
                var $validate;
                var $dateTimeFormat;

                function News($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                        $this->dateTimeFormat = new DateTimeFormat();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->head = "";
                        $this->text = "";
                }

                function create($head, $text)
                {
                        global $sUser;
                        if( !($this->validate->countString($head)>3) )
                                return false;
                        if( !($this->validate->countString($text)>3) )
                                return false;

                        $this->head = $head;
                        $this->text = $text;

                        $query = sprintf("insert
                                          into    moduleNews
                                                  ( head,
                                                    text,
                                                    moduser,
                                                    modtime,
                                                    genuser,
                                                    gentime )
                                           values ( '%s',
                                                    '%s',
                                                    '%s',
                                                    now(),
                                                    '%s',
                                                    now() )",
                                                   $this->head,
                                                   $this->text,
                                                   $sUser["username"],
                                                   $sUser["username"] );
                        $this->db->executeQuery($query);
                        $this->id = $this->db->getInsertID();
                        return $this->id;
                }

                function modify($head, $text)
                {
                        global $sUser;
                        if( !($this->validate->countString($head)>3) )
                                return false;
                        if( !($this->validate->countString($text)>3) )
                                return false;

                        $this->head = $head;
                        $this->text = $text;

                        if($this->id > 0)
                        {
                                $query = sprintf("select * from moduleNews where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update moduleNews
                                                          set    head='%s',
                                                                 text='%s',
                                                                 moduser='%s',
                                                                 modtime=now()
                                                          where  ID='%s'",
                                                                 $this->head,
                                                                 $this->text,
                                                                 $sUser["username"],
                                                                 $this->id );
                                        return $this->db->executeQuery($query);
                                }
                                else
                                        return false;
                        }
                        else
                                return false;
                }

                function delete()
                {
                        $query = sprintf("delete from moduleNews where ID='%s'", $this->id);
                        return $this->db->executeQuery($query);
                }

                function load($id)
                {
                        $query = sprintf("select    ID,
                                                    head,
                                                    text,
                                                    genuser,
                                                    gentime,
                                                    moduser,
                                                    modtime,
                                                    gen.realname as genrealname,
                                                    mod.realname as modrealname
                                          from      moduleNews,
                                                    authUser gen,
                                                    authUser mod
                                          where     ID='%s'
                                          and       gen.username=genuser
                                          and       mod.username=moduser",
                                                    $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->head = $this->db->getValue("head");
                                $this->text = $this->db->getValue("text");
                                $this->genuser = $this->db->getValue("genuser");
                                $this->gentime = $this->db->getValue("gentime");
                                $this->moduser = $this->db->getValue("moduser");
                                $this->modtime = $this->db->getValue("modtime");
                                $this->genrealname = $this->db->getValue("genrealname");
                                $this->modrealname = $this->db->getValue("modrealname");
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
                function getHead()
                {
                        return $this->head;
                }
                function getText()
                {
                        return $this->text;
                }
        }

        class NewsIterator
        {
                var $list = array();
                var $db;

                function NewsIterator($db)
                {
                        $this->db = $db;
                }

                function createIterator($weeks = 4, $num = 0)
                {
                        if($num == 0)
                            $num = 1000000;

                        $this->list = array();
                        if($weeks == 0)
                                $query = sprintf(" select    ID
                                                   from      moduleNews
                                                   order by  modtime desc ");
                        else
                                $query = sprintf(" select    ID
                                                   from      moduleNews
                                                   where     gentime >= '%s'
                                                   order by  modtime desc
                                                   limit     0,%s
                                                ", date("Ymd", time()-86400*7*$weeks),
                                                   $num );

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
                        $o = new News($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>
