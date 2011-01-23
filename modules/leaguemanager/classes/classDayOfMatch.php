<?php
/*
        class DayOfMatch
        {
                var $number = 0;
                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;

                var $date = "0000-00-00";
                var $time = "00:00:00";

                var $numberOfRegisteredMatches = 0;

                // static attributes
                var $db;
                var $validate;

                function DayOfMatch($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->seasonID = 0;
                        $this->leagueID = 0;
                        $this->sportID = 0;
                        $this->number = 0;
                        $this->date = "0000-00-00";
                        $this->time = "00:00:00";
                        $this->numberOfRegisteredMatches = 0;
                }


                function create($season, $league, $sport, $number, $date, $time)
                {
                        if( ! (is_object($season) && (get_class($season)=="season") )  )
                                return false;
                        if( ! (is_object($league) && (get_class($league)=="league") )  )
                                return false;
                        if( ! (is_object($sport) && (get_class($sport)=="sport") )  )
                                return false;

                        $this->seasonID = $season->getID();
                        $this->leagueID = $league->getID();
                        $this->sportID = $sport->getID();

                        if( !($this->validate->checkDate($date)))
                                return false;
                        if( !($this->validate->checkTime($time)))
                                return false;
                        if( !($this->validate->isNumber($number)))
                                return false;

                        $this->number = $number;
                        $this->date = $date;
                        $this->time = $time;

                        return $this->save();
                }

                function modify($date, $time)
                {
                        if( !($this->validate->checkDate($date)))
                                return false;
                        if( !($this->validate->checkTime($time)))
                                return false;

                        $this->date = $date;
                        $this->time = $time;

                        return $this->save();
                }

                function save()
                {
                        // check for update: If the seasonID, leagueID and sportID already exists in database then do an update
                        $query = sprintf("select * from lmDayOfMatch where seasonID='%s' and leagueID='%s' and sportID='%s' and number='%s'", $this->seasonID, $this->leagueID, $this->sportID, $this->number);
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                $query = sprintf("  update lmDayOfMatch
                                                    set    date = '%s',
                                                           time = '%s'
                                                    where  seasonID='%s' and
                                                           leagueID='%s' and
                                                           sportID='%s' and
                                                           number='%s'
                                                 ",
                                                    $this->date,
                                                    $this->time,
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->number
                                                );
echo $query;
                                $this->db->executeQuery($query);
                                return true;
                        }

                        $query = sprintf("  insert into lmDayOfMatch
                                                   ( seasonID, leagueID, sportID, date, time, number )
                                            values (   '%s'  ,    %s   ,    %s  , '%s', '%s',  '%s'  )
                                         ",
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->date,
                                                    $this->time,
                                                    $this->number
                                        );
echo $query;
                        return $this->db->executeQuery($query);
                }

                function load($seasonID, $leagueID, $sportID, $number)
                {
echo $number;
                        $this->seasonID = $seasonID;
                        $this->leagueID = $leagueID;
                        $this->sportID = $sportID;
                        $this->number = $number;

                        $query = sprintf("select date, time from lmDayOfMatch where seasonID='%s' and leagueID='%s' and sportID='%s' and number='%s'", $seasonID, $leagueID, $sportID, $number);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->date = $this->db->getValue("date");
                                $this->time = $this->db->getValue("time");
                        }
                        else
                        {
                                return false;
                        }

                        $query = sprintf("select count(*) as n from lmMatchOfLeagueDOM where seasonID='%s' and leagueID='%s' and sportID='%s' and dayOfMatchNumber='%s'", $seasonID, $leagueID, $sportID, $number);
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->numberOfRegisteredMatches = $this->db->getValue("n");
                        }
                        else
                        {
                                return false;
                        }

                        return true;
                }

                function setDB($db)
                {
                        $this->db = $db;
                }
                function setEmpty($number)
                {
                        $this->date = "";
                        $this->time = "";
                }

                function getSeasonID()
                {
                        return $this->seasonID;
                }
                function getLeagueID()
                {
                        return $this->leagueID;
                }
                function getSportID()
                {
                        return $this->sportID;
                }
                function getDate()
                {
                        return $this->date;
                }
                function getTime()
                {
                        return $this->time;
                }
                function getNumber()
                {
                        return $this->number;
                }
                function getNumberOfRegisteredMatches()
                {
                        return $this->numberOfRegisteredMatches;
                }
        }

        class DayOfMatchList
        {
                var $list = array();

                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;

                var $numberOfRegisteredDays = 0;

                // static attributes
                var $db;
                var $validate;


                function DayOfMatchList($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->seasonID = 0;
                        $this->leagueID = 0;
                        $this->sportID = 0;
                        $this->list = array();
                        $this->numberOfRegistredDays = 0;
                }

                function createList($season, $league, $sport)
                {
                        $this->reset();

                        $this->seasonID = $season->getID();
                        $this->leagueID = $league->getID();
                        $this->sportID = $sport->getID();

                        $query = sprintf("select number from lmDayOfMatch where seasonID='%s' and leagueID='%s' and sportID='%s' order by number", $this->seasonID, $this->leagueID, $this->sportID);
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("number");
                        $this->numberOfRegisteredDays = sizeof($this->list);
                }

                function createEditList($season, $league, $sport, $leagueInSeason)
                {
                        $this->createList($season, $league, $sport);

                        if($leagueInSeason->getIsDoubleSeason() == "true") $double = 2;
                        else $double = 1;

                        $this->list = array();

                        for( $i = 1; ($i <= ($leagueInSeason->getNumberOfTeams() - 1) * 2 * $double) ; $i++)
                                $this->list[] = $i;
                }

                function hasNext()
                {
                        return sizeof($this->list) > 0;
                }

                function next()
                {
                        $o = new DayOfMatch($this->db);
                        if(!$o->load($this->seasonID, $this->leagueID, $this->sportID, $this->list[0]))
                                $o->setEmpty($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }

                function getNumberOfRegisteredDays()
                {
                        return $this->numberOfRegisteredDays;
                }
                function setDB($db)
                {
                        $this->db = $db;
                }
        }
*/

        class DayOfMatch
        {
                var $number = 0;
                var $date = "0000-00-00";
                var $time = "00:00:00";
                var $isodate = "0000-00-00";

                var $week = "0000-00-00";

                var $db;

                function DayOfMatch($db)
                {
                        $this->db = $db;
                }

                function reset()
                {
                        $this->number = 0;
                        $this->date = "0000-00-00";
                        $this->week = "0000-00-00";
                        $this->time = "00:00:00";
                        $this->isodate = "0000-00-00";
                }

                function load($seasonID, $sportID, $leagueID, $domNumber)
                {
                        $lis = new LeagueInSeason($this->db);
                        $lis->loadID($seasonID, $leagueID);

                        if($lis->getHasDaysOfMatch() == "true")
                        {
                                $query = sprintf("select   number,
                                                           date as isodate,
                                                           time as isotime,
                                                           date_format(date, '%%d.%%m.%%Y') as date,
                                                           time_format(time, '%%H:%%i') as time
                                                  from     lmDayOfMatch
                                                  where    seasonID = '%s'
                                                  and      sportID = '%s'
                                                  and      leagueID = '%s'
                                                  and      number = '%s' ",
                                                           $seasonID,
                                                           $sportID,
                                                           $leagueID,
                                                           $domNumber );
                                $this->db->executeQuery($query);
                                if($this->db->nextRow())
                                {
                                        $this->number = $this->db->getValue("number");
                                        $this->date = $this->db->getValue("date");
                                        $this->time = $this->db->getValue("time");
                                        $this->isodate = $this->db->getValue("isodate");
                                        return true;
                                }
                                else
                                        return false;
                        }
                        else
                        {
                                $query = sprintf("select date as isodate,
                                                         time as isotime,
                                                         date_format(date, '%%d.%%m.%%Y') as date,
                                                         time_format(time, '%%H:%%i') as time
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    sportID='%s'
                                                  and    leagueID='%s'
                                                  and    ( number='%s' or number='%s' )",
                                                   $seasonID,
                                                   $sportID,
                                                   $leagueID,
                                                   $domNumber,
                                                   $domNumber-1 );
                                $this->db->executeQuery($query);
                                if($this->db->nextRow())
                                {
                                        $this->number = $domNumber;
                                        // $this->number = 0;
                                        $this->date = $this->db->getValue("date");
                                        $this->time = $this->db->getValue("time");
                                        $this->isodate = $this->db->getValue("isodate");

                                        $d = strtotime($this->db->getValue("isodate"));
                                        $s = $d + ( (7-date("w", $d)) * 24*60*60);
                                        $this->week = date("d.m.Y", $s);

                                        return true;
                                }
                                else
                                        return false;

                        }
                }

                function setDB($db)
                {
                        $this->db = $db;
                }

                function getNumber()
                {
                        return $this->number;
                }
                function getDate()
                {
                        return $this->date;
                }
                function getWeek()
                {
                        return $this->week;
                }
                function getTime()
                {
                        return $this->time;
                }
                function getIsoDate()
                {
                        return $this->isodate;
                }
        }


        class DayOfMatchList
        {
                var $list;
                var $number;
                var $index;
                var $regNumber;

                var $day;
                var $lis;
                var $season;
                var $sport;
                var $league;

                // static attributes
                var $db;
                var $validate;

                function DayOfMatchList($db, $lis=null)
                {
                        $this->db = $db;
                        $this->day = new DayOfMatch($db);

                        if(is_object($lis))
                                $this->init($lis);
                }

                function init($lis)
                {
                        $this->list = array();

                        $this->lis = $lis;
                        $this->lis->setDB($this->db);

                        $this->season = $lis->getSeason();
                        $this->sport = $lis->getSport();
                        $this->league = $lis->getLeague();

                        $query = sprintf("select count(number) as number
                                          from   lmDayOfMatch
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        $this->regNumber = $this->db->getValue("number");

                        $this->reset();
                }

                function reset()
                {
                        $this->index = 0;
                }

                function modifyList($isodates, $isotimes)
                {
                        // delete all entries
                        $query = sprintf("delete
                                          from   lmDayOfMatch
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        if(!$this->db->executeQuery($query))
                                return false;

                        // add new entries from list
                        if(is_array($isodates) && is_array($isotimes))
                        {
                                $this->calcNumberOfDays();

                                for( $i = 1; $i <= $this->number ; $i++)
                                {

                                        if(!empty($isodates[$i]) && !empty($isotimes[$i]))
                                        {
                                                $query = sprintf("insert
                                                                  into   lmDayOfMatch
                                                                         (number,
                                                                          date,
                                                                          time,
                                                                          seasonID,
                                                                          sportID,
                                                                          leagueID )
                                                                  values ( '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s' ) ",
                                                                           $i,
                                                                           $isodates[$i],
                                                                           $isotimes[$i],
                                                                           $this->season->getID(),
                                                                           $this->sport->getID(),
                                                                           $this->league->getID() );
                                                $this->db->executeQuery($query);
                                        }
                                        else if(!empty($isodates[$i]) && empty($isotimes[$i]))
                                        {
                                                $query = sprintf("insert
                                                                  into   lmDayOfMatch
                                                                         (number,
                                                                          date,
                                                                          seasonID,
                                                                          sportID,
                                                                          leagueID )
                                                                  values ( '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s' ) ",
                                                                           $i,
                                                                           $isodates[$i],
                                                                           $this->season->getID(),
                                                                           $this->sport->getID(),
                                                                           $this->league->getID() );
                                                $this->db->executeQuery($query);
                                        }
                                        else if(empty($isodates[$i]) && !empty($isotimes[$i]))
                                        {
                                                $query = sprintf("insert
                                                                  into   lmDayOfMatch
                                                                         (number,
                                                                          time,
                                                                          seasonID,
                                                                          sportID,
                                                                          leagueID )
                                                                  values ( '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s',
                                                                          '%s' ) ",
                                                                           $i,
                                                                           $isotimes[$i],
                                                                           $this->season->getID(),
                                                                           $this->sport->getID(),
                                                                           $this->league->getID() );
                                                $this->db->executeQuery($query);
                                        }
                                }

                                return true;
                        }
                        else
                                return false;
                }

                function loadEditList()
                {
                        $this->calcNumberOfDays();
                        $this->list = array();

                        $query = sprintf("select   number,
                                                   date as isodate,
                                                   time as isotime,
                                                   date_format(date, '%%d.%%m.%%Y') as date,
                                                   time_format(time, '%%H:%%i') as time
                                          from     lmDayOfMatch
                                          where    seasonID = '%s'
                                          and      sportID = '%s'
                                          and      leagueID = '%s'
                                          order by number ",
                                                   $this->season->getID(),
                                                   $this->sport->getID(),
                                                   $this->league->getID());
                        $this->db->executeQuery($query);

                        $this->db->nextRow();
                        for( $i = 1; $i <= $this->number; $i++)
                        {
                                if($i == $this->db->getValue("number"))
                                {
                                        $this->list[] = array( $i, $this->db->getValue("date"), $this->db->getValue("time"));
                                        $this->db->nextRow();
                                }
                                else
                                        $this->list[] = array($i, "", "");
                        }
                        $this->number = sizeof($this->list);
                }

                function loadList()
                {
                    if($this->lis->getHasDaysOfMatch() == "true")
                    {
                        $query = sprintf("select   number,
                                                   date as isodate,
                                                   time as isotime,
                                                   date_format(date, '%%d.%%m.%%Y') as date,
                                                   time_format(time, '%%H:%%i') as time
                                          from     lmDayOfMatch
                                          where    seasonID = '%s'
                                          and      sportID = '%s'
                                          and      leagueID = '%s'
                                          order by number ",
                                                   $this->season->getID(),
                                                   $this->sport->getID(),
                                                   $this->league->getID());
                        $this->db->executeQuery($query);
                        $this->list = array();
                        while($this->db->nextRow())
                                $this->list[] = array( $this->db->getValue("number"), $this->db->getValue("date"), $this->db->getValue("time"));
                    }
                    else
                    {
                                $query = sprintf("select date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    sportID='%s'
                                                  and    leagueID='%s'
                                                  and    number='1' ",
                                                   $this->season->getID(),
                                                   $this->sport->getID(),
                                                   $this->league->getID());
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $startdate = strtotime($this->db->getValue("isodate"));

                                $query = sprintf("select date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    sportID='%s'
                                                  and    leagueID='%s'
                                                  order  by date desc
                                                  limit  0,1 ",
                                                   $this->season->getID(),
                                                   $this->sport->getID(),
                                                   $this->league->getID());
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $enddate = strtotime($this->db->getValue("isodate"));

                                $lastSunday = $startdate;
                                $nextSunday = $lastSunday + ( (7-date("w", $lastSunday)) * 24*60*60);

                                $this->list = array();
                                while($lastSunday < $enddate)
                                {
                                        $query = sprintf("select number,
                                                                 number,
                                                                 date as isodate,
                                                                 time as isotime,
                                                                 date_format(date, '%%d.%%m.%%Y') as date,
                                                                 time_format(time, '%%H:%%i') as time
                                                          from   lmMatchOfLeague
                                                          where  seasonID='%s'
                                                          and    sportID='%s'
                                                          and    leagueID='%s'
                                                          and    date between '%s' and '%s'
                                                          group by number ",
                                                        $this->season->getID(),
                                                        $this->sport->getID(),
                                                        $this->league->getID(),
                                                        date("Y-m-d", $lastSunday),
                                                        date("Y-m-d", $nextSunday)
                                                          );
                                        $this->db->executeQuery($query);
                                        if(($this->db->getNumRows() > 2) || ($nextSunday > $enddate))
                                        {
                                                $lastSunday = $nextSunday;
                                                $nextSunday = $lastSunday + ( (7-date("w", $lastSunday)) * 24*60*60);
                                                while($this->db->nextRow())
                                                {
                                                }
                                                // $this->list[] = array( $this->db->getValue("number"), $this->db->getValue("date"), $this->db->getValue("time"));
                                                $this->list[] = array( $this->db->getValue("number"), date("d.m.Y", $lastSunday), $this->db->getValue("time"));
                                        }
                                        else
                                        {
                                                $nextSunday = $nextSunday + ( (7-date("w", $nextSunday)) * 24*60*60);
                                        }
                                }
                        }

                       $this->number = sizeof($this->list);
                }

                function hasNext()
                {
                        return ($this->index < $this->number);
                }
                function next()
                {
                        $this->day->reset();
                        $this->day->number = $this->list[$this->index][0];
                        $this->day->date = $this->list[$this->index][1];
                        $this->day->time = $this->list[$this->index][2];
                        $this->index++;
                        return $this->day;
                }

                function calcNumberOfDays()
                {
                        if($this->lis->getIsDoubleSeason() == "true")
                                $double = 2;
                        else
                                $double = 1;

                        if($this->lis->getNumberOfTeams() % 2 == 0)
                                $evenodd = 0;
                        else
                                $evenodd = 1;

                        $this->number = ($this->lis->getNumberOfTeams() - 1 + $evenodd) * 2 * $double;
                }

                function setDB($db)
                {
                        $this->db = $db;
                }

                function getNumber()
                {
                        return $this->number;
                }
                function getNumberOfRegisteredDays()
                {
                        return $this->regNumber;
                }
        }





?>
