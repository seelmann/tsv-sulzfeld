<?php
        include_once("classValidation.php");
        include_once("classDateTimeFormat.php");

        class Date
        {
                // attributes
                var $id = 0;
                var $fromdate = "";
                var $fromtime = "";
                var $todate = "";
                var $totime = "";
                var $event = "";
                var $head = "";
                var $text = "";

                // static attributes
                var $db;
                var $validate;
                var $dateTimeFormat;
 
                function Date($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                        $this->dateTimeFormat = new DateTimeFormat();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->fromdate = "";
                        $this->fromtime = "";
                        $this->todate = "";
                        $this->totime = "";
                        $this->event = "";
                        $this->head = "";
                        $this->text = "";
                }

                function create($fromdate, $fromtime, $todate, $totime, $event, $head, $text)
                {
                        if( !$this->validate->checkDate($fromdate) )
                                return false;
                        if( !empty($fromtime) && !$this->validate->checkTime($fromtime) )
                                return false;
                        if( !empty($todate) && !$this->validate->checkDate($todate) )
                                return false;
                        if( !empty($totime) && !$this->validate->checkTime($totime) )
                                return false;
                        if( !($this->validate->countString($event)>3) )
                                return false;
                        if( !empty($head) && !($this->validate->countString($head)>3) )
                                return false;
                        if( !empty($text) && !($this->validate->countString($text)>3) )
                                return false;

                        $this->fromdate = $fromdate;
                        $this->fromtime = $fromtime;
                        $this->todate = $todate;
                        $this->totime = $totime;
                        $this->event = $event;
                        $this->head = $head;
                        $this->text = $text;

                        $query = sprintf("insert into moduleDate
                                                      ( fromdate, fromtime, todate, totime, event )
                                                      values ('%s', '%s', '%s', '%s', '%s')",
                                         $this->fromdate, $this->fromtime, $this->todate, $this->totime, $this->event);
                        $this->db->executeQuery($query);
                        $this->id = $this->db->getInsertID();

                        // infos
                        if( !empty($this->head) || !empty($this->text))
                        {
                                $query = sprintf("insert into moduleDateInfo
                                                              ( dateID, head, text )
                                                              values ('%s', '%s', '%s')",
                                                  $this->id, $this->head, $this->text);
                                $this->db->executeQuery($query);
                        }
                        return $this->id;
                }

                function modify($fromdate, $fromtime, $todate, $totime, $event, $head, $text)
                {
                         if( !$this->validate->checkDate($fromdate) )
                                return false;
                        if( !empty($fromtime) && !$this->validate->checkTime($fromtime) )
                                return false;
                        if( !empty($todate) && !$this->validate->checkDate($todate) )
                                return false;
                        if( !empty($totime) && !$this->validate->checkTime($totime) )
                                return false;
                        if( !($this->validate->countString($event)>3) )
                                return false;
                        if( !empty($head) && !($this->validate->countString($head)>3) )
                                return false;
                        if( !empty($text) && !($this->validate->countString($text)>3) )
                                return false;

                        $this->fromdate = $fromdate;
                        $this->fromtime = $fromtime;
                        $this->todate = $todate;
                        $this->totime = $totime;
                        $this->event = $event;
                        $this->head = $head;
                        $this->text = $text;

                        if($this->id > 0)
                        {
                                $query = sprintf("select * from moduleDate where ID='%s'", $this->id);
                                $this->db->executeQuery($query);
                                if($this->db->getNumRows() > 0)
                                {
                                        $query = sprintf("update moduleDate
                                                          set    fromdate='%s',
                                                                 fromtime='%s',
                                                                 todate='%s',
                                                                 totime='%s',
                                                                 event='%s'
                                                          where  ID='%s'",
                                                                 $this->fromdate,
                                                                 $this->fromtime,
                                                                 $this->todate,
                                                                 $this->totime,
                                                                 $this->event,
                                                                 $this->id );
                                        $this->db->executeQuery($query);

                                        // infos
                                        $query = sprintf("select * from moduleDateInfo where dateID='%s'", $this->id);
                                        $this->db->executeQuery($query);
                                        if($this->db->getNumRows() > 0)
                                        {
                                                if(empty($this->head) && empty($this->text))
                                                {
                                                        $query = sprintf("delete from moduleDateInfo where dateID='%s'", $this->id);
                                                }
                                                else // if(!emtpy($this->head) || !empty($this->text))
                                                {
                                                        $query = sprintf("update moduleDateInfo
                                                                          set    head='%s',
                                                                                 text='%s'
                                                                          where  dateID='%s' ",
                                                                          $this->head, $this->text, $this->id);
                                                }
                                        }
                                        else
                                        {
                                                if(!empty($this->head) || !empty($this->text))
                                                {
                                                        $query = sprintf("insert into moduleDateInfo
                                                                                      ( dateID, head, text )
                                                                                        values ('%s', '%s', '%s')",
                                                                          $this->id, $this->head, $this->text);
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
                        $query = sprintf("delete from moduleDateInfo where dateID='%s'", $this->id);
                        $this->db->executeQuery($query);

                        $query = sprintf("delete from moduleDate where ID='%s'", $this->id);
                        $this->db->executeQuery($query);

                        return true;
                }

                function load($id)
                {
                        $query = sprintf("select    ID,
                                                    date_format(fromdate, '%%d.%%m.%%Y') as fromdate,
                                                    time_format(fromtime, '%%H:%%i') as fromtime,
                                                    date_format(todate, '%%d.%%m.%%Y') as todate,
                                                    time_format(totime, '%%H:%%i') as totime,
                                                    event,
                                                    head,
                                                    text
                                          from      moduleDate
                                          left join moduleDateInfo
                                          on        moduleDateInfo.dateID=moduleDate.ID
                                          where     moduleDate.ID='%s'",
                                                    $id);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->fromdate = $this->db->getValue("fromdate")=="00.00.0000"?"":$this->db->getValue("fromdate");
                                $this->fromtime = $this->db->getValue("fromtime")=="00:00"?"":$this->db->getValue("fromtime");
                                $this->todate = $this->db->getValue("todate")=="00.00.0000"?"":$this->db->getValue("todate");
                                $this->totime = $this->db->getValue("totime")=="00:00"?"":$this->db->getValue("totime");
                                $this->event = $this->db->getValue("event");
                                $this->head = $this->db->getValue("head");
                                $this->text = $this->db->getValue("text");
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
                function getFromDate()
                {
                        return $this->dateTimeFormat->addDay($this->fromdate);
                }
                function getToDate()
                {
                        return $this->dateTimeFormat->addDay($this->todate);
                }
                function getFromDateEdit()
                {
                        return $this->fromdate;
                }
                function getToDateEdit()
                {
                        return $this->todate;
                }
                function getFromTime()
                {
                        return $this->fromtime;
                }
                function getToTime()
                {
                        return $this->totime;
                }
                function getEvent()
                {
                        return $this->event;
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

        class DateIterator
        {
                var $list = array();
                var $db;
                var $number;

                function DateIterator($db)
                {
                        $this->db = $db;
                }

                function createIterator($weeks=4, $limit=0)
                {
                        if($limit == 0)
                                $limit = 1000000;

                        $this->list = array();
                        if($weeks == 0)
                                $query = sprintf(" select    ID
                                                   from      moduleDate
                                                   where     fromdate >= curdate() or todate >= curdate()
                                                   order by  fromdate, fromtime, todate, totime
                                                   limit     0,%s ", $limit);
                        else
                                $query = sprintf(" select    ID
                                                   from      moduleDate
                                                   where     fromdate <= '%s'
                                                   and       ( fromdate >= curdate()
                                                               or todate >= curdate() )
                                                   order by  fromdate, fromtime, todate, totime
                                                   limit     0,%s
                                                ", date("Ymd", time()+86400*7*$weeks),
                                                   $limit );

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
                        $o = new Date($this->db);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
        }
?>
