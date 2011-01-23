<?php
        include_once("classDateTimeFormat.php");
        include_once("classDBmysql.php");

        class MatchDate
        {
                var $homeTeamName = "";
                var $guestTeamName = "";
                var $date = "";
                var $time = "";
                //var $infoHead = "";
                //var $infoText = "";
                var $infoID = 0;
                var $postponeID = 0;
                var $othermatchInfoID = 0;
                var $canceled = "no";

                var $sportName = "";
                var $sportID = 0;

                var $sportArray = array();
                var $sportIndex = 0;
                var $sportNumber = 0;

                var $dateArray = array();
                var $dateIndex = 0;
                var $dateNumber = 0;

                var $db;
                var $dateTimeFormat;

                function MatchDate($db)
                {
                        $this->db = $db;
                        $this->dateTimeFormat = new DateTimeFormat();
                }

                function createSportDates($weeks=2)
                {
                        $db2 = new DBmysql($this->db->getError());

                        // temporary table
                        $tablename = "temp_matchdate_".time();
                        $query = "create temporary table $tablename ( ID int(11) not null,
                                                                      name varchar(255) not null,
                                                                      num int(11) not null )";
                        $db2->executeQuery($query);
                        $query = "insert into $tablename select ID, name, 0 from lmSport";
                        $db2->executeQuery($query);

                        // get data from lmMatchOfLeague
                        $query = sprintf( "select     mat.sportID as ID,
                                                      count(*) as num
                                           from       lmOwnTeamInLeagueInSeason as otilis
                                           inner join lmMatchOfLeague as mat
                                           on         mat.seasonID = otilis.seasonID
                                           and        mat.sportID = otilis.sportID
                                           and        mat.leagueID = otilis.leagueID
                                           and        ( mat.homeTeamID = otilis.teamID or
                                                        mat.guestTeamID = otilis.teamID )
                                           where     mat.date <= '%s'
                                           and       mat.date >= curdate()
                                           group by  mat.sportID
                                        ", date("Ymd", time()+86400*(7*$weeks+1)) );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                        {
                            $query = sprintf("update  %s
                                              set     num=num+%s
                                              where   ID='%s'
                                             ", $tablename, $this->db->getValue("num"), $this->db->getValue("ID"));
                            $db2->executeQuery($query);
                        }

                        // get data from lmMatchPostpone
                        $query = sprintf( "select     pp.sportID as ID,
                                                      count(*) as num
                                           from       lmOwnTeamInLeagueInSeason as otilis
                                           inner join lmMatchOfLeague as mat
                                           on         mat.seasonID = otilis.seasonID
                                           and        mat.sportID = otilis.sportID
                                           and        mat.leagueID = otilis.leagueID
                                           and        ( mat.homeTeamID = otilis.teamID or
                                                        mat.guestTeamID = otilis.teamID )
                                           inner join lmMatchPostpone as pp
                                           on         mat.seasonID=pp.seasonID
                                           and        mat.sportID=pp.sportID
                                           and        mat.leagueID=pp.leagueID
                                           and        mat.dayOfMatchNumber=pp.dayOfMatchNumber
                                           and        mat.number=pp.number


                                           where     pp.newdate <= '%s'
                                           and       pp.newdate >= curdate()
                                           group by  pp.sportID
                                        ", date("Ymd", time()+86400*(7*$weeks+1)) );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                        {
                            $query = sprintf("update  %s
                                              set     num=num+%s
                                              where   ID='%s'
                                             ", $tablename, $this->db->getValue("num"), $this->db->getValue("ID"));
                            $db2->executeQuery($query);
                        }

                        // get data from moduleOthermatch
                        $query = sprintf( "select     sportID as ID,
                                                      count(*) as num
                                           from       moduleOthermatch
                                           where      date <= '%s'
                                           and        date >= curdate()
                                           group by   sportID
                                        ", date("Ymd", time()+86400*(7*$weeks+1)) );
//echo $query;
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                        {
//echo $this->db->getValue("ID") . " - " . $this->db->getValue("num");
                            $query = sprintf("update  %s
                                              set     num=num+%s
                                              where   ID='%s'
                                             ", $tablename, $this->db->getValue("num"), $this->db->getValue("ID"));
                            $db2->executeQuery($query);
                        }

                        // Fill data into array
                        $query = "select * from $tablename";
                        $db2->executeQuery($query);
                        while($db2->nextRow())
                        {
                                // echo $this->db->getValue("ID") .", ". $this->db->getValue("name") .", ". $this->db->getValue("num"). "<br>";
                                $this->sportArray[] = array($db2->getValue("ID"), $db2->getValue("name"), $db2->getValue("num"));
                        }
                        $this->sportNumber = sizeof($this->sportArray);
                }


                function createLeagueDates()
                {
                }


                function nextSport($weeks=2, $limit=0)
                {
                        $db2 = new DBmysql($this->db->getError());
                        if($limit == 0)
                                $limit = 1000000;
                        while($this->sportIndex < $this->sportNumber)
                        {
                                $this->sportID = $this->sportArray[$this->sportIndex][0];
                                $this->sportName = $this->sportArray[$this->sportIndex][1];
                                if($this->sportArray[$this->sportIndex][2] > 0)
                                {
                                        $this->dateArray = array();


                                        // temporary table
                                        $tablename = "temp_matchdate_".$this->sportID."_".time();
                                        $query = "create temporary table $tablename ( home varchar(255) not null,
                                                                                      guest varchar(255) not null,
                                                                                      date date not null,
                                                                                      time time not null,
                                                                                      infoID int(11) not null,
                                                                                      postponeID int(11) not null,
                                                                                      othermatchInfoID int(11) not null,
                                                                                      canceled enum('yes','no') not null ) ";
                                        $db2->executeQuery($query);

                                        // get dates from lmMatchOfLeague
                                        $query = sprintf( " select     home.name as home,
                                                                       guest.name as guest,
                                                                       mat.date as isodate,
                                                                       mat.time as isotime,
                                                                       info.ID as infoID,
                                                                       pp.ID as postponeID

                                                            from       lmMatchOfLeague as mat
                                                            inner join lmOwnTeamInLeagueInSeason as otilis
                                                            on         mat.seasonID = otilis.seasonID
                                                            and        mat.sportID = otilis.sportID
                                                            and        mat.leagueID = otilis.leagueID
                                                            and        ( mat.homeTeamID = otilis.teamID or
                                                                         mat.guestTeamID = otilis.teamID )
                                                            inner join lmTeam as home
                                                            on         mat.homeTeamID=home.ID
                                                            inner join lmTeam as guest
                                                            on         mat.guestTeamID=guest.ID
                                                            left join  lmMatchInfo as info
                                                            on         mat.seasonID = info.seasonID
                                                            and        mat.sportID = info.sportID
                                                            and        mat.leagueID = info.leagueID
                                                            and        mat.dayOfMatchNumber = info.dayOfMatchNumber
                                                            and        mat.number = info.number
                                                            left join lmMatchPostpone pp
                                                            on         mat.seasonID = pp.seasonID
                                                            and        mat.sportID = pp.sportID
                                                            and        mat.leagueID = pp.leagueID
                                                            and        mat.dayOfMatchNumber = pp.dayOfMatchNumber
                                                            and        mat.number = pp.number

                                                            where      mat.date <= '%s'
                                                            and        mat.date >= curdate()
                                                            and        mat.sportID = '%s'
                                                         ", date("Ymd", time()+86400*(7*$weeks+1)),
                                                            $this->sportID
                                                        );
                                        $this->db->executeQuery($query);
                                        while($this->db->nextRow())
                                        {
                                            $infoID = $this->db->getValue("infoID");
                                            $infoID = isset($infoID)?$infoID:0;

                                            $postponeID = $this->db->getValue("postponeID");
                                            $postponeID = !empty($postponeID)?$postponeID:0;
                                            $canceled = !empty($postponeID)?"yes":"no";



                                            $query = sprintf("insert into %s
                                                              values ('%s', '%s', '%s', '%s', %s, %s, 0, '%s')
                                                             ", $tablename,
                                                                $this->db->getValue("home"),
                                                                $this->db->getValue("guest"),
                                                                $this->db->getValue("isodate"),
                                                                $this->db->getValue("isotime"),
                                                                $infoID,
                                                                $postponeID,
                                                                $canceled );
                                            $db2->executeQuery($query);
                                        }

                                        // get dates from lmMatchPostpone
                                        $query = sprintf( " select     home.name as home,
                                                                       guest.name as guest,
                                                                       postpone.newdate as isodate,
                                                                       postpone.newtime as isotime,
                                                                       postpone.ID as postponeID

                                                            from       lmMatchPostpone as postpone
                                                            inner join lmMatchOfLeague as mat
                                                            on         mat.seasonID = postpone.seasonID
                                                            and        mat.sportID = postpone.sportID
                                                            and        mat.leagueID = postpone.leagueID
                                                            and        mat.dayOfMatchNumber = postpone.dayOfMatchNumber
                                                            and        mat.number = postpone.number
                                                            inner join lmOwnTeamInLeagueInSeason as otilis
                                                            on         mat.seasonID = otilis.seasonID
                                                            and        mat.sportID = otilis.sportID
                                                            and        mat.leagueID = otilis.leagueID
                                                            and        ( mat.homeTeamID = otilis.teamID or
                                                                         mat.guestTeamID = otilis.teamID )
                                                            inner join lmTeam as home
                                                            on         mat.homeTeamID=home.ID
                                                            inner join lmTeam as guest
                                                            on         mat.guestTeamID=guest.ID

                                                            where     postpone.newdate <= '%s'
                                                            and       postpone.newdate >= curdate()
                                                            and       postpone.sportID = '%s'
                                                         ", date("Ymd", time()+86400*(7*$weeks+1)),
                                                            $this->sportID
                                                        );
                                        $this->db->executeQuery($query);
                                        while($this->db->nextRow())
                                        {
                                            $postponeID = $this->db->getValue("postponeID");
                                            $postponeID = isset($postponeID)?$postponeID:0;


                                            $query = sprintf("insert into %s
                                                              values ('%s', '%s', '%s', '%s', 0, %s, 0, 'no')
                                                             ", $tablename,
                                                                $this->db->getValue("home"),
                                                                $this->db->getValue("guest"),
                                                                $this->db->getValue("isodate"),
                                                                $this->db->getValue("isotime"),
                                                                $postponeID );
                                            $db2->executeQuery($query);
                                        }

                                        // get dates from modulsOthermatch
                                        $query = sprintf( " select     home,
                                                                       guest,
                                                                       date,
                                                                       time,
                                                                       info.ID as othermatchInfoID

                                                            from       moduleOthermatch mat
                                                            left join  moduleOthermatchInfo as info
                                                            on         mat.ID = info.othermatchID

                                                            where      mat.date <= '%s'
                                                            and        mat.date >= curdate()
                                                            and        mat.sportID = '%s'
                                                         ", date("Ymd", time()+86400*(7*$weeks+1)),
                                                            $this->sportID
                                                        );
                                        $this->db->executeQuery($query);
                                        while($this->db->nextRow())
                                        {
                                            $othermatchInfoID = $this->db->getValue("othermatchInfoID");
                                            $othermatchInfoID = isset($othermatchInfoID)?$othermatchInfoID:0;

                                            $query = sprintf("insert into %s
                                                              values ('%s', '%s', '%s', '%s', 0, 0, %s, 'no')
                                                             ", $tablename,
                                                                $this->db->getValue("home"),
                                                                $this->db->getValue("guest"),
                                                                $this->db->getValue("date"),
                                                                $this->db->getValue("time"),
                                                                $othermatchInfoID );
                                            $db2->executeQuery($query);
                                        }

                                        // Fill data into array
                                        $query = sprintf(" select    home,
                                                                     guest,
                                                                     date_format(date, '%%d.%%m.%%Y') as date,
                                                                     time_format(time, '%%H:%%i') as time,
                                                                     infoID,
                                                                     postponeID,
                                                                     othermatchInfoID,
                                                                     canceled,
                                                                     date as isodate,
                                                                     time as isotime
                                                           from      %s
                                                           order by  isodate, isotime
                                                           limit     0,%s
                                                          ", $tablename, $limit );
                                        $db2->executeQuery($query);
                                        while($db2->nextRow())
                                        {
                                                $this->dateArray[] = array( $db2->getValue("date"),
                                                                            $db2->getValue("time"),
                                                                            $db2->getValue("home"),
                                                                            $db2->getValue("guest"),
                                                                            $db2->getValue("infoID"),
                                                                            $db2->getValue("postponeID"),
                                                                            $db2->getValue("othermatchInfoID"),
                                                                            $db2->getValue("canceled") );
                                        }

                                        $this->dateNumber = sizeof($this->dateArray);
                                        $this->dateIndex = 0;
                                        $this->sportIndex++;

                                        //usort($this->dateArray, array(&$this,'cmp'));

                                        return true;
                                }
                                else
                                        $this->sportIndex++;
                        }
                        return false;
                }

                function hasDate()
                {
                }

                function nextDate()
                {
                        if($this->dateIndex < $this->dateNumber)
                        {
                                $this->date = $this->dateArray[$this->dateIndex][0];
                                $this->time = $this->dateArray[$this->dateIndex][1];
                                $this->homeTeamName = $this->dateArray[$this->dateIndex][2];
                                $this->guestTeamName = $this->dateArray[$this->dateIndex][3];
                                $this->infoID = $this->dateArray[$this->dateIndex][4];
                                $this->postponeID = $this->dateArray[$this->dateIndex][5];
                                $this->othermatchInfoID = $this->dateArray[$this->dateIndex][6];
                                $this->canceled = $this->dateArray[$this->dateIndex][7];

                                $this->dateIndex++;
                                return true;
                        }
                        else
                                return false;
                }

                function cmp ($a, $b)
                {
                                if ( $a[4] < $b[4] )
                                        return -1;
                                else if( $a[4] > $b[4] )
                                        return 1;
                                else
                                {
                                        if ( $a[5] < $b[5] )
                                                return -1;
                                        else if ( $a[5] > $b[5] )
                                                return 1;
                                }
                                return 0;
                }

                function getSportName()
                {
                        return $this->sportName;
                }
                function getDate()
                {
                        return $this->dateTimeFormat->addDay($this->date);
                }
                function getTime()
                {
                        return $this->time;
                }
                function getHomeTeamName()
                {
                        return $this->homeTeamName;
                }
                function getGuestTeamName()
                {
                        return $this->guestTeamName;
                }
/*
                function getEvent()
                {
                        return $this->event;
                }
                function getInfoHead()
                {
                        return $this->infoHead;
                }
                function getInfoText()
                {
                        return $this->infoText;
                }
*/
                function getInfoID()
                {
                        return $this->infoID;
                }
                function getPostponeID()
                {
                        return $this->postponeID;
                }
                function getOthermatchInfoID()
                {
                        return $this->othermatchInfoID;
                }
                function getCanceled()
                {
                        return $this->canceled;
                }

                function getDateNumber()
                {
                        return $this->dateNumber;
                }
        }
?>
