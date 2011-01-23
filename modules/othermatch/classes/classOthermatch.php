<?php
        include_once("classValidation.php");
        include_once("classDateTimeFormat.php");
        include_once("classSport.php");


        class Othermatch
        {
                // attributes
                var $id = 0;
                var $date = "";
                var $time = "";
                var $home = "";
                var $guest = "";
                var $homeresult = -1;
                var $guestresult = -1;
                var $infohead = "";
                var $infotext = "";
                var $reporthead = "";
                var $reporttext = "";
                var $sportid = 0;
                var $canceled = 'no';

                // static attributes
                var $db;
                var $validate;
                var $dateTimeFormat;

                function Othermatch($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                        $this->dateTimeFormat = new DateTimeFormat();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->date = "";
                        $this->time = "";
                        $this->home = "";
                        $this->guest = "";
                        $this->homeresult = -1;
                        $this->guestresult = -1;
                        $this->infohead = "";
                        $this->infotext = "";
                        $this->reporthead = "";
                        $this->reporttext = "";
                        $this->sportid = 0;
                        $this->canceled = 'no';
                }

                function create($date, $time, $home, $guest, $infohead, $infotext, $sportid)
                {
                        if( !$this->validate->checkDate($date) )
                                return false;
                        if( !$this->validate->checkTime($time) )
                                return false;
                        if( !($this->validate->countString($home)>3) )
                                return false;
                        if( !($this->validate->countString($guest)>3) )
                                return false;
                        if( !empty($infohead) && !($this->validate->countString($infohead)>3) )
                                return false;
                        if( !empty($infotext) && !($this->validate->countString($infotext)>3) )
                                return false;
                        $sport = new Sport($this->db);
                        if( !$sport->load($sportid))
                                return false;

                        $this->date = $date;
                        $this->time = $time;
                        $this->home = $home;
                        $this->guest = $guest;
                        $this->infohead = $infohead;
                        $this->infotext = $infotext;
                        $this->sportid = $sportid;

                        $query = sprintf("insert into moduleOthermatch
                                                      ( date, time, home, guest, sportID )
                                                      values ('%s', '%s', '%s', '%s', '%s')",
                                         $this->date, $this->time, $this->home, $this->guest, $this->sportid);
                        $this->db->executeQuery($query);
                        $this->id = $this->db->getInsertID();

                        // infos
                        if( !empty($this->infohead) || !empty($this->infotext))
                        {
                                $query = sprintf("insert into moduleOthermatchInfo
                                                              ( othermatchID, head, text )
                                                              values ('%s', '%s', '%s')",
                                                  $this->id, $this->infohead, $this->infotext);
                                $this->db->executeQuery($query);
                        }
                        return $this->id;
                }

                function modify($date, $time, $home, $guest, $infohead, $infotext, $sportid)
                {
                        if( !$this->validate->checkDate($date) )
                                return false;
                        if( !$this->validate->checkTime($time) )
                                return false;
                        if( !($this->validate->countString($home)>3) )
                                return false;
                        if( !($this->validate->countString($guest)>3) )
                                return false;
                        if( !empty($infohead) && !($this->validate->countString($infohead)>3) )
                                return false;
                        if( !empty($infotext) && !($this->validate->countString($infotext)>3) )
                                return false;
                        $sport = new Sport($this->db);
                        if( !$sport->load($sportid))
                                return false;

                        $this->date = $date;
                        $this->time = $time;
                        $this->home = $home;
                        $this->guest = $guest;
                        $this->infohead = $infohead;
                        $this->infotext = $infotext;
                        $this->sportid = $sportid;

                        if($this->id > 0)
                        {
                                $query = sprintf("select * from moduleOthermatch where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update moduleOthermatch
                                                          set    date='%s',
                                                                 time='%s',
                                                                 home='%s',
                                                                 guest='%s',
                                                                 sportID='%s'
                                                          where  ID='%s'",
                                                                 $this->date,
                                                                 $this->time,
                                                                 $this->home,
                                                                 $this->guest,
                                                                 $this->sportid,
                                                                 $this->id );
                                        $this->db->executeQuery($query);

                                        // infos
                                        $query = sprintf("select * from moduleOthermatchInfo where othermatchID='%s'", $this->id);
                                        $this->db->executeQuery($query);
                                        if($this->db->getNumRows() > 0)
                                        {
                                                if(empty($this->infohead) && empty($this->infotext))
                                                {
                                                        $query = sprintf("delete from moduleOthermatchInfo where othermatchID='%s'", $this->id);
                                                }
                                                else // if(!emtpy($this->infohead) || !empty($this->infotext))
                                                {
                                                        $query = sprintf("update moduleOthermatchInfo
                                                                          set    head='%s',
                                                                                 text='%s'
                                                                          where  othermatchID='%s' ",
                                                                          $this->infohead, $this->infotext, $this->id);
                                                }
                                        }
                                        else
                                        {
                                                if(!empty($this->infohead) || !empty($this->infotext))
                                                {
                                                        $query = sprintf("insert into moduleOthermatchInfo
                                                                                      ( othermatchID, head, text )
                                                                                        values ('%s', '%s', '%s')",
                                                                          $this->id, $this->infohead, $this->infotext);
                                                }
                                        }
                                        $this->db->executeQuery($query);

                                        return true;
                                }
                                else
                                        return false;
                        }
                        else
                                return false;
                }

                function delete()
                {
                        $query = sprintf("delete from moduleOthermatchInfo where othermatchID='%s'", $this->id);
                        $this->db->executeQuery($query);

                        $query = sprintf("delete from moduleOthermatchReport where othermatchID='%s'", $this->id);
                        $this->db->executeQuery($query);

                        $query = sprintf("delete from moduleOthermatch where ID='%s'", $this->id);
                        $this->db->executeQuery($query);

                        return true;
                }

                function result($homeresult, $guestresult, $reporthead, $reporttext, $canceled)
                {
                        if( empty($homeresult) && empty($guestresult) )
                        {
                                $homeresult = -1;
                                $guestresult = -1;
                        }
                        if( !is_numeric($homeresult) )
                                return false;
                        if( !is_numeric($guestresult) )
                                return false;
                        if( !empty($reporthead) && !($this->validate->countString($reporthead)>3) )
                                return false;
                        if( !empty($reporttext) && !($this->validate->countString($reporttext)>3) )
                                return false;
                        if( empty($canceled) || ($canceled != 'yes') )
                                $canceled = 'no';

                        $this->homeresult = $homeresult;
                        $this->guestresult = $guestresult;
                        $this->reporthead = $reporthead;
                        $this->reporttext = $reporttext;
                        $this->canceled = $canceled;

                        if($this->id > 0)
                        {
                                $query = sprintf("select * from moduleOthermatch where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update moduleOthermatch
                                                          set    homeresult='%s',
                                                                 guestresult='%s',
                                                                 canceled='%s'
                                                          where  ID='%s'",
                                                                 $this->homeresult,
                                                                 $this->guestresult,
                                                                 $this->canceled,
                                                                 $this->id );
                                        $this->db->executeQuery($query);

                                        // report
                                        $query = sprintf("select * from moduleOthermatchReport where othermatchID='%s'", $this->id);
                                        $this->db->executeQuery($query);
                                        if($this->db->getNumRows() > 0)
                                        {
                                                if(empty($this->reporthead) && empty($this->reporttext))
                                                {
                                                        $query = sprintf("delete from moduleOthermatchReport where othermatchID='%s'", $this->id);
                                                }
                                                else // if(!emtpy($this->reporthead) || !empty($this->reporttext))
                                                {
                                                        $query = sprintf("update moduleOthermatchReport
                                                                          set    head='%s',
                                                                                 text='%s'
                                                                          where  othermatchID='%s' ",
                                                                          $this->reporthead, $this->reporttext, $this->id);
                                                }
                                        }
                                        else
                                        {
                                                if(!empty($this->reporthead) || !empty($this->reporttext))
                                                {
                                                        $query = sprintf("insert into moduleOthermatchReport
                                                                                      ( othermatchID, head, text )
                                                                                        values ('%s', '%s', '%s')",
                                                                          $this->id, $this->reporthead, $this->reporttext);
                                                }
                                        }
                                        $this->db->executeQuery($query);

                                        return true;
                                }
                                else
                                        return false;
                        }
                        else
                                return false;

                }

                function load($id)
                {
                        $query = sprintf("select    m.ID,
                                                    date_format(date, '%%d.%%m.%%Y') as date,
                                                    time_format(time, '%%H:%%i') as time,
                                                    home,
                                                    guest,
                                                    homeresult,
                                                    guestresult,
                                                    canceled,
                                                    i.head as infohead,
                                                    i.text as infotext,
                                                    r.head as reporthead,
                                                    r.text as reporttext,
                                                    sportID
                                          from      moduleOthermatch m
                                          left join moduleOthermatchInfo i
                                          on        m.ID=i.othermatchID
                                          left join moduleOthermatchReport r
                                          on        m.ID=r.othermatchID
                                          where     m.ID='%s'",
                                                    $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->date = $this->db->getValue("date");
                                $this->time = $this->db->getValue("time");
                                $this->home = $this->db->getValue("home");
                                $this->guest = $this->db->getValue("guest");
                                $this->homeresult = $this->db->getValue("homeresult");
                                $this->guestresult = $this->db->getValue("guestresult");
                                $this->canceled = $this->db->getValue("canceled");
                                $this->infohead = $this->db->getValue("infohead");
                                $this->infotext = $this->db->getValue("infotext");
                                $this->reporthead = $this->db->getValue("reporthead");
                                $this->reporttext = $this->db->getValue("reporttext");
                                $this->sportid = $this->db->getValue("sportID");
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
                function getDate()
                {
                        return $this->dateTimeFormat->addDay($this->date);
                }
                function getDateEdit()
                {
                        return $this->date;
                }
                function getTime()
                {
                        return $this->time;
                }
                function getHome()
                {
                        return $this->home;
                }
                function getGuest()
                {
                        return $this->guest;
                }
                function getHomeResult()
                {
                        if($this->homeresult == -1)
                            return;
                        else
                            return $this->homeresult;
                }
                function getGuestResult()
                {
                        if($this->guestresult == -1)
                            return;
                        else
                            return $this->guestresult;
                }
                function getInfoHead()
                {
                        return $this->infohead;
                }
                function getInfoText()
                {
                        return $this->infotext;
                }
                function getReportHead()
                {
                        return $this->reporthead;
                }
                function getReportText()
                {
                        return $this->reporttext;
                }
                function getSportID()
                {
                        return $this->sportid;
                }
                function getCanceled()
                {
                        return $this->canceled;
                }

        }



        
        class OthermatchIterator
        {
                var $list = array();
                var $db;
                var $number;

                function OthermatchIterator($db)
                {
                        $this->db = $db;
                }

                function createIterator($option)
                {

                        $this->list = array();
                        if($option == "fut2")
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date >= curdate()
                                                   and       date <= %s
                                                   order by  date, time
                                                  ", date("Ymd", time()+86400*7*2));
                        else if ($option == "futall")
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date >= curdate()
                                                   order by  date, time
                                                  ");
                        else if ($option == "all")
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   order by  date, time
                                                  ");
                        else
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date >= curdate()
                                                   and       date <= %s
                                                   order by  date, time
                                                  ", date("Ymd", time()+86400*7*2));


                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");
                        $this->number = sizeof($this->list);
                }

                function createResultIterator($option)
                {

                        $this->list = array();
                        if($option == "mis2")
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date <= curdate()
                                                   and       date >= %s
                                                   and       homeResult=-1
                                                   order by  date desc, time desc
                                                  ", date("Ymd", time()-86400*7*2));
                        else if ($option == "misall")
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date <= curdate()
                                                   and       homeResult=-1
                                                   order by  date desc, time desc
                                                  ");
                        else if ($option == "all")
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date <= curdate()
                                                   order by  date desc, time desc
                                                  ");
                        else
                                $query = sprintf(" select    ID
                                                   from      moduleOthermatch
                                                   where     date <= curdate()
                                                   and       date >= %s
                                                   order by  date desc, time desc
                                                  ", date("Ymd", time()-86400*7*2));


                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");
                        $this->number = sizeof($this->list);
                }


                function getNumber()
                {
                        return $this->number;
                }

                function hasNext()
                {
                        return sizeof($this->list) > 0;
                }

                function next()
                {
                        $o = new Othermatch($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>
