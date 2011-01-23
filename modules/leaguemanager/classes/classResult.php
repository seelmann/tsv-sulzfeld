<?php
        include_once("classDateTimeFormat.php");

        class Result
        {
                var $homeTeamName = "";
                var $guestTeamName = "";
                var $homeResult = 0;
                var $guestResult = 0;
                var $date = "";
                var $time = "";
                //var $isoDate = "";
                //var $isoTime = "";

                //var $reportHead = "";
                //var $reportText = "";
                var $reportID = 0;

                var $postponeID = 0;
                var $postponeNewDate = "";
                var $postponeNewTime = "";
                var $postponeNewIsoDate = "";
                var $postponeNewIsoTime = "";
                //var $postponeReason = "";

                var $othermatchReportID = 0;
                var $canceled = 'no';

                var $sportName = "";
                var $sportID = "";

                var $sportArray = array();
                var $sportIndex = 0;
                var $sportNumber = 0;

                var $resultArray = array();
                var $resultIndex = 0;
                var $resultNumber = 0;

                var $maxIsoDate = "0000-00-00";

                var $domNumber = 0;
                var $db;
                var $dateTimeFormat;

                function Result($db)
                {
                        $this->db = $db;
                        $this->dateTimeFormat = new DateTimeFormat();
                }

                function createSportResults($weeks=2)
                {

                        $db2 = new DBmysql($this->db->getError());

                        // temporary table
                        $tablename = "temp_results_".time();
                        $query = "create temporary table $tablename ( ID int(11) not null,
                                                                      name varchar(255) not null,
                                                                      num int(11) not null )";
                        $db2->executeQuery($query);
                        $query = "insert into $tablename select ID, name, 0 from lmSport";
                        $db2->executeQuery($query);

                        // get results from matches
                        $query = sprintf( "select     mat.sportID as ID,
                                                      count(*) as num
                                           from       lmOwnTeamInLeagueInSeason as otilis
                                           inner join lmMatchOfLeague as mat
                                           on         mat.seasonID = otilis.seasonID
                                           and        mat.sportID = otilis.sportID
                                           and        mat.leagueID = otilis.leagueID
                                           and        ( mat.homeTeamID = otilis.teamID or
                                                        mat.guestTeamID = otilis.teamID )

                                           left join  lmMatchPostpone as pp
                                           on         mat.seasonID=pp.seasonID
                                           and        mat.sportID=pp.sportID
                                           and        mat.leagueID=pp.leagueID
                                           and        mat.dayOfMatchNumber=pp.dayOfMatchNumber
                                           and        mat.number=pp.number

                                           where      (     mat.date >= '%s'
                                                        and mat.date <= curdate()
                                                        and mat.homeResult != -1
                                                        and mat.guestResult != -1
                                                      )
                                           or         (     pp.newdate >= '%s'
                                                        and pp.newdate <= curdate()
                                                        and mat.date < '%s'
                                                        and mat.homeResult != -1
                                                        and mat.guestResult != -1
                                                      )
                                           or         (     mat.date >= '%s'
                                                        and mat.date <= curdate()
                                                        and mat.homeResult = -1
                                                        and mat.guestResult = -1
                                                        and pp.ID is not NULL
                                                        and (pp.newdate is NULL or pp.newdate > curdate())
                                                      )
                                           group by   mat.sportID
                                        ", date("Ymd", time()-86400*(7*$weeks+1))
                                         , date("Ymd", time()-86400*(7*$weeks+1))
                                         , date("Ymd", time()-86400*(7*$weeks+1))
                                         , date("Ymd", time()-86400*(7*$weeks+1)) );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                        {
                            $query = sprintf("update  %s
                                              set     num=num+%s
                                              where   ID='%s'
                                             ", $tablename, $this->db->getValue("num"), $this->db->getValue("ID"));
                            $db2->executeQuery($query);
                        }

                        // get results of othermatches
                        $query = sprintf( "select     sportID as ID,
                                                      count(*) as num
                                           from       moduleOthermatch
                                           where      date >= '%s'
                                           and        date <= curdate()
                                           and        ( ( canceled = 'yes' )
                                                        or
                                                        ( homeResult != -1 and guestResult != -1 )
                                                      )
                                           group by   sportID
                                        ", date("Ymd", time()-86400*(7*$weeks+1)) );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                        {
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
                                $this->sportArray[] = array($db2->getValue("ID"), $db2->getValue("name"), $db2->getValue("num"));
                        }
                        $this->sportNumber = sizeof($this->sportArray);

                }



                function createLeagueResults($seasonID, $leagueID, $number=0)
                {
                        // with or without day of match?
                        $query = sprintf("select hasDaysOfMatch from lmLeagueInSeason where seasonID='%s' and leagueID='%s'", $seasonID, $leagueID);
                        $this->db->executeQuery($query);
                        $this->db->nextRow();

                        if($this->db->getValue("hasDaysOfMatch") == "true")  // Spieltage
                        {
                                if($number==0)
                                {
                                        // get number of last day of match
                                        $query = sprintf("select   number
                                                          from     lmDayOfMatch
                                                          where    seasonID='%s'
                                                          and      leagueID='%s'
                                                          and      ( (date < curdate()) or
                                                                     (date = curdate() and time <= curtime()) )
                                                          order by date desc
                                                          limit    0,1
                                                         ",
                                                                   $seasonID,
                                                                   $leagueID
                                                        );
                                        $this->db->executeQuery($query);
                                        $this->db->nextRow();
                                        $this->domNumber = $this->db->getValue("number");
                                }
                                else
                                        $this->domNumber = $number;

                                // get all matches from this day
/*
                                $query = sprintf("select home.name as home,
                                                         guest.name as guest,
                                                         mat.homeResult,
                                                         mat.guestResult,
                                                         mat.date as isodate,
                                                         mat.time as isotime,
                                                         date_format(mat.date, '%%d.%%m.%%Y') as date,
                                                         time_format(mat.time, '%%H:%%i') as time,
                                                         rep.ID as reportID,
                                                         rep.head,
                                                         rep.text,
                                                         pp.ID as postponeID,
                                                         pp.newdate as isonewdate,
                                                         pp.newtime as isonewtime,
                                                         date_format(pp.newdate, '%%d.%%m.%%Y') as newdate,
                                                         time_format(pp.newtime, '%%H:%%i') as newtime,
                                                         pp.reason
                                                  from   lmMatchOfLeague as mat
                                                  left join lmMatchReport rep
                                                  using     (seasonID, sportID, leagueID, dayOfMatchNumber, number)
                                                  left join lmMatchPostpone pp
                                                  using     (seasonID, sportID, leagueID, dayOfMatchNumber, number),
                                                         lmTeam as home,
                                                         lmTeam as guest
                                                  where  mat.seasonID='%s' and
                                                         mat.leagueID='%s' and
                                                         mat.dayOfMatchNumber='%s' and
                                                         mat.homeTeamID=home.ID and
                                                         mat.guestTeamID=guest.ID
                                                  order by mat.date,
                                                           mat.time
                                                 ",
                                                         $seasonID,
                                                         $leagueID,
                                                         $this->domNumber
                                                );
*/
                                $query = sprintf("select home.name as home,
                                                         guest.name as guest,
                                                         mat.homeResult,
                                                         mat.guestResult,
                                                         mat.date as isodate,
                                                         mat.time as isotime,
                                                         date_format(mat.date, '%%d.%%m.%%Y') as date,
                                                         time_format(mat.time, '%%H:%%i') as time,
                                                         pp.ID as postponeID,
                                                         pp.newdate as isonewdate,
                                                         pp.newtime as isonewtime,
                                                         date_format(pp.newdate, '%%d.%%m.%%Y') as newdate,
                                                         time_format(pp.newtime, '%%H:%%i') as newtime,
                                                         pp.reason,
                                                         rep.ID as reportID,
                                                         rep.head,
                                                         rep.text
                                                  from   lmMatchOfLeague as mat
                                                  left join lmMatchPostpone pp
                                                  on     pp.seasonID=mat.seasonID
                                                  and    pp.sportID=mat.sportID
                                                  and    pp.leagueID=mat.leagueID
                                                  and    pp.dayOfMatchNumber=mat.dayOfMatchNumber
                                                  and    pp.number=mat.number
                                                  left join lmMatchReport rep
                                                  on     rep.seasonID=mat.seasonID
                                                  and    rep.sportID=mat.sportID
                                                  and    rep.leagueID=mat.leagueID
                                                  and    rep.dayOfMatchNumber=mat.dayOfMatchNumber
                                                  and    rep.number=mat.number,
                                                         lmTeam as home,
                                                         lmTeam as guest
                                                  where  mat.seasonID='%s' and
                                                         mat.leagueID='%s' and
                                                         mat.dayOfMatchNumber='%s' and
                                                         mat.homeTeamID=home.ID and
                                                         mat.guestTeamID=guest.ID
                                                  order by mat.date,
                                                           mat.time
                                                 ",
                                                         $seasonID,
                                                         $leagueID,
                                                         $this->domNumber
                                                );
                        }
                        else
                        {
                                $query = sprintf("select date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    leagueID='%s'
                                                  and    number='1' ",
                                                   $seasonID,
                                                   $leagueID );
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $startdate = strtotime($this->db->getValue("isodate"));

                                // get last match
                                if($number==0)
                                {
                                        $query = sprintf("select   date as isodate,
                                                                   number
                                                          from     lmMatchOfLeague
                                                          where    seasonID='%s' and
                                                                   leagueID='%s' and
                                                                   date <= now()
                                                          order by date desc,
                                                                   time desc,
                                                                   number desc
                                                          limit    0,1
                                                         ",
                                                                   $seasonID,
                                                                   $leagueID
                                                        );
                                        $this->db->executeQuery($query);
                                        $this->db->nextRow();
                                        $enddate = strtotime($this->db->getValue("isodate"));
                                        $endnumber = $this->db->getValue("number");

                                }
                                else
                                {
                                        $query = sprintf("select date as isodate
                                                          from   lmMatchOfLeague
                                                          where  seasonID='%s'
                                                          and    leagueID='%s'
                                                          and    number='%s' ",
                                                           $seasonID,
                                                           $leagueID,
                                                           $number );
                                        $this->db->executeQuery($query);
                                        $this->db->nextRow();
                                        $enddate = strtotime($this->db->getValue("isodate"));
                                        $endnumber = $number;
                                }

                                $this->maxIsoDate = date("Y-m-d", $enddate);
                                $lastSunday = $startdate;
                                $nextSunday = $lastSunday + ( (7-date("w", $lastSunday)) * 24*60*60);

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
                                                          and    leagueID='%s'
                                                          and    date between '%s' and '%s'
                                                          group by number ",
                                                           $seasonID,
                                                           $leagueID,
                                                           date("Y-m-d", $lastSunday),
                                                           date("Y-m-d", $nextSunday)
                                                          );
                                        $this->db->executeQuery($query);

                                        if(($this->db->getNumRows() > 2) || ($nextSunday > $enddate))
                                        {
                                                $lastSunday = $nextSunday;
                                                $nextSunday = $lastSunday + ( (7-date("w", $lastSunday)) * 24*60*60);
                                                if($this->db->nextRow())
                                                        $startnumber = $this->db->getValue("number");
                                        }
                                        else
                                        {
                                                $nextSunday = $nextSunday + ( (7-date("w", $nextSunday)) * 24*60*60);
                                        }
                                }

                                $query = sprintf("select home.name as home,
                                                         guest.name as guest,
                                                         mat.homeResult,
                                                         mat.guestResult,
                                                         mat.date as isodate,
                                                         mat.time as isotime,
                                                         date_format(mat.date, '%%d.%%m.%%Y') as date,
                                                         time_format(mat.time, '%%H:%%i') as time,
                                                         rep.ID as reportID,
                                                         rep.head,
                                                         rep.text,
                                                         pp.ID as postponeID,
                                                         pp.newdate as isonewdate,
                                                         pp.newtime as isonewtime,
                                                         date_format(pp.newdate, '%%d.%%m.%%Y') as newdate,
                                                         time_format(pp.newtime, '%%H:%%i') as newtime,
                                                         pp.reason
                                                  from   lmMatchOfLeague as mat
                                                  left join lmMatchPostpone pp
                                                  on     pp.seasonID=mat.seasonID
                                                  and    pp.sportID=mat.sportID
                                                  and    pp.leagueID=mat.leagueID
                                                  and    pp.dayOfMatchNumber=mat.dayOfMatchNumber
                                                  and    pp.number=mat.number
                                                  left join lmMatchReport rep
                                                  on     rep.seasonID=mat.seasonID
                                                  and    rep.sportID=mat.sportID
                                                  and    rep.leagueID=mat.leagueID
                                                  and    rep.dayOfMatchNumber=mat.dayOfMatchNumber
                                                  and    rep.number=mat.number,
                                                         lmTeam as home,
                                                         lmTeam as guest
                                                  where  mat.seasonID='%s' and
                                                         mat.leagueID='%s' and
                                                         mat.number between '%s' and '%s' and
                                                         mat.homeTeamID=home.ID and
                                                         mat.guestTeamID=guest.ID
                                                  order by mat.date,
                                                           mat.time
                                                 ",
                                                         $seasonID,
                                                         $leagueID,
                                                         $startnumber,
                                                         $endnumber
                                                );
                        }
                        $this->db->executeQuery($query);
                        $this->resultArray = array();
                        while($this->db->nextRow())


                                                $this->resultArray[] = array( $this->db->getValue("date"),
                                                                            $this->db->getValue("time"),
                                                                            $this->db->getValue("home"),
                                                                            $this->db->getValue("guest"),
                                                                            $this->db->getValue("homeResult"),
                                                                            $this->db->getValue("guestResult"),
                                                                            $this->db->getValue("reportID"),
                                                                            $this->db->getValue("newdate"),
                                                                            $this->db->getValue("newtime"),
                                                                            $this->db->getValue("postponeID"),
                                                                            $this->db->getValue("othermatchReportID"),
                                                                            $this->db->getValue("isonewdate"),
                                                                            $this->db->getValue("isonewtime") );


/*
                                $this->resultArray[] = array($this->db->getValue("home"),#
                                                             $this->db->getValue("guest"),#
                                                             $this->db->getValue("homeResult"),#
                                                             $this->db->getValue("guestResult"),#
                                                             $this->db->getValue("isodate"),
                                                             $this->db->getValue("isotime"),
                                                             $this->db->getValue("date"),#
                                                             $this->db->getValue("time"),#
                                                             $this->db->getValue("head"),
                                                             $this->db->getValue("text"),
                                                             $this->db->getValue("reportID"),#
                                                             $this->db->getValue("postponeID"),#
                                                             $this->db->getValue("reason"),
                                                             $this->db->getValue("isonewdate"),
                                                             $this->db->getValue("isonewtime"),
                                                             $this->db->getValue("newdate"),#
                                                             $this->db->getValue("newtime"),#
                                                             );
*/
                        $this->number = sizeof($this->resultArray);

                        $this->resultNumber = sizeof($this->resultArray);
                        $this->resultIndex = 0;

                                // usort($this->resultArray, array(&$this,'cmp'));

                        return true;
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
                                        $this->resultArray = array();

                                        // temporary table
                                        $tablename = "temp_result_".$this->sportID."_".time();
                                        $query = "create temporary table $tablename ( home varchar(255) not null,
                                                                                      guest varchar(255) not null,
                                                                                      date date not null,
                                                                                      time time not null,
                                                                                      homeResult int(4) not null,
                                                                                      guestResult int(4) not null,
                                                                                      reportID int(11) not null,
                                                                                      newdate date not null,
                                                                                      newtime time not null,
                                                                                      postponeID int(11) not null,
                                                                                      canceled enum('yes', 'no') not null,
                                                                                      othermatchReportID int(11) not null ) ";
                                        $db2->executeQuery($query);

                                        // get results from matches
                                        $query = sprintf( "select     home.name as home,
                                                                      guest.name as guest,
                                                                      mat.date as isodate,
                                                                      mat.time as isotime,
                                                                      mat.homeResult,
                                                                      mat.guestResult,
                                                                      rep.ID as reportID,
                                                                      pp.newdate as newisodate,
                                                                      pp.newtime as newisotime,
                                                                      pp.ID as postponeID,
                                                                      mat.sportID

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
                                                           left join  lmMatchPostpone as pp
                                                           on         mat.seasonID=pp.seasonID
                                                           and        mat.sportID=pp.sportID
                                                           and        mat.leagueID=pp.leagueID
                                                           and        mat.dayOfMatchNumber=pp.dayOfMatchNumber
                                                           and        mat.number=pp.number
                                                           left join lmMatchReport rep
                                                           on        rep.seasonID=mat.seasonID
                                                           and       rep.sportID=mat.sportID
                                                           and       rep.leagueID=mat.leagueID
                                                           and       rep.dayOfMatchNumber=mat.dayOfMatchNumber
                                                           and       rep.number=mat.number

                                                           where      ( (     mat.date >= '%s'
                                                                          and mat.date <= curdate()
                                                                          and mat.homeResult != -1
                                                                          and mat.guestResult != -1
                                                                        )
                                                                      or
                                                                        (     pp.newdate >= '%s'
                                                                          and pp.newdate <= curdate()
                                                                          and mat.date < '%s'
                                                                          and mat.homeResult != -1
                                                                          and mat.guestResult != -1
                                                                        )
                                                                      or
                                                                        (     mat.date >= '%s'
                                                                          and mat.date <= curdate()
                                                                          and mat.homeResult = -1
                                                                          and mat.guestResult = -1
                                                                          and pp.ID is not NULL
                                                                          and (pp.newdate is NULL or pp.newdate > curdate())
                                                                        )
                                                                      )
                                                           and       mat.sportID = %s
                                                        ", date("Ymd", time()-86400*(7*$weeks+1))
                                                         , date("Ymd", time()-86400*(7*$weeks+1))
                                                         , date("Ymd", time()-86400*(7*$weeks+1))
                                                         , date("Ymd", time()-86400*(7*$weeks+1))
                                                         , $this->sportID );
                                        $this->db->executeQuery($query);
                                        while($this->db->nextRow())
                                        {
                                            $reportID = $this->db->getValue("reportID");
                                            $reportID = isset($reportID)?$reportID:0;

                                            $postponeID = $this->db->getValue("postponeID");
                                            $postponeID = isset($postponeID)?$postponeID:0;

                                            $query = sprintf("insert into %s
                                                              values ('%s', '%s', '%s', '%s', %s, %s, %s, '%s', '%s', %s, 'no', 0)
                                                             ", $tablename,
                                                                $this->db->getValue("home"),
                                                                $this->db->getValue("guest"),
                                                                $this->db->getValue("isodate"),
                                                                $this->db->getValue("isotime"),
                                                                $this->db->getValue("homeResult"),
                                                                $this->db->getValue("guestResult"),
                                                                $reportID,
                                                                $this->db->getValue("newisodate"),
                                                                $this->db->getValue("newisotime"),
                                                                $postponeID );
                                            $db2->executeQuery($query);
                                        }

                                        // get results from othermatches
                                        $query = sprintf( "select     home,
                                                                      guest,
                                                                      mat.date as isodate,
                                                                      mat.time as isotime,
                                                                      mat.homeResult,
                                                                      mat.guestResult,
                                                                      mat.canceled,
                                                                      rep.ID as othermatchReportID

                                                           from       moduleOthermatch as mat
                                                           left join  moduleOthermatchReport rep
                                                           on         rep.othermatchID=mat.ID

                                                           where      mat.date >= '%s'
                                                           and        mat.date <= curdate()
                                                           and        ( ( mat.canceled = 'yes' )
                                                                        or
                                                                        ( mat.homeResult != -1 and mat.guestResult != -1 )
                                                                      )
                                                           and       mat.sportID = '%s'
                                                        ", date("Ymd", time()-86400*(7*$weeks+1))
                                                         , $this->sportID );
                                        $this->db->executeQuery($query);

                                        while($this->db->nextRow())
                                        {
                                            $othermatchReportID = $this->db->getValue("othermatchReportID");
                                            $othermatchReportID = isset($othermatchReportID)?$othermatchReportID:0;

                                            $query = sprintf("insert into %s
                                                              values ('%s', '%s', '%s', '%s', %s, %s, 0, '', '', 0, '%s', %s)
                                                             ", $tablename,
                                                                $this->db->getValue("home"),
                                                                $this->db->getValue("guest"),
                                                                $this->db->getValue("isodate"),
                                                                $this->db->getValue("isotime"),
                                                                $this->db->getValue("homeResult"),
                                                                $this->db->getValue("guestResult"),
                                                                $this->db->getValue("canceled"),
                                                                $othermatchReportID );
                                            $db2->executeQuery($query);
                                        }

                                        // Fill data into array
                                        $query = sprintf(" select    home,
                                                                     guest,
                                                                     date_format(date, '%%d.%%m.%%Y') as date,
                                                                     time_format(time, '%%H:%%i') as time,
                                                                     homeResult,
                                                                     guestResult,
                                                                     reportID,
                                                                     date_format(newdate, '%%d.%%m.%%Y') as newdate,
                                                                     time_format(newtime, '%%H:%%i') as newtime,
                                                                     postponeID,
                                                                     othermatchReportID,
                                                                     date as isodate,
                                                                     time as isotime,
                                                                     newdate as newisodate,
                                                                     newtime as newisotime,
                                                                     canceled
                                                           from      %s
                                                           order by  isodate desc, isotime desc
                                                           limit     0,%s
                                                          ", $tablename, $limit );
                                        $db2->executeQuery($query);
                                        while($db2->nextRow())
                                        {
                                                $this->resultArray[] = array( $db2->getValue("date"),
                                                                            $db2->getValue("time"),
                                                                            $db2->getValue("home"),
                                                                            $db2->getValue("guest"),
                                                                            $db2->getValue("homeResult"),
                                                                            $db2->getValue("guestResult"),
                                                                            $db2->getValue("reportID"),
                                                                            $db2->getValue("newdate"),
                                                                            $db2->getValue("newtime"),
                                                                            $db2->getValue("postponeID"),
                                                                            $db2->getValue("othermatchReportID"),
                                                                            $db2->getValue("newisodate"),
                                                                            $db2->getValue("newisotime"),
                                                                            $db2->getValue("canceled") );
                                        }

                                        $this->resultNumber = sizeof($this->resultArray);
                                        $this->resultIndex = 0;
                                        $this->sportIndex++;
                                        return true;
                                }
                                else
                                        $this->sportIndex++;
                        }
                        return false;
                }

                function nextResult()
                {
                        if($this->resultIndex < $this->resultNumber)
                        {
                                $this->date = $this->resultArray[$this->resultIndex][0];
                                $this->time = $this->resultArray[$this->resultIndex][1];
                                $this->homeTeamName = $this->resultArray[$this->resultIndex][2];
                                $this->guestTeamName = $this->resultArray[$this->resultIndex][3];
                                $this->homeResult = $this->resultArray[$this->resultIndex][4];
                                $this->guestResult = $this->resultArray[$this->resultIndex][5];
                                $this->reportID = $this->resultArray[$this->resultIndex][6];
                                $this->postponeNewDate = $this->resultArray[$this->resultIndex][7];
                                $this->postponeNewTime = $this->resultArray[$this->resultIndex][8];
                                $this->postponeID = $this->resultArray[$this->resultIndex][9];
                                $this->othermatchReportID = $this->resultArray[$this->resultIndex][10];
                                $this->postponeNewIsoDate = $this->resultArray[$this->resultIndex][11];
                                $this->postponeNewIsoTime = $this->resultArray[$this->resultIndex][12];
                                $this->canceled = $this->resultArray[$this->resultIndex][13];
                                $this->resultIndex++;
                                return true;
                        }
                        else
                                return false;
                }
/*
                function cmp ($a, $b)
                {
                                if ( $a[4] < $b[4] )
                                        return 1;
                                else if( $a[4] > $b[4] )
                                        return -1;
                                else
                                {
                                        if ( $a[5] < $b[5] )
                                                return 1;
                                        else if ( $a[5] > $b[5] )
                                                return -1;
                                }
                                return 0;
                }
*/
                function getSportName()
                {
                        return $this->sportName;
                }
                function getDate()
                {
                        if(    ($this->postponeID > 0)
                            && ($this->postponeNewIsoDate." ".$this->postponeNewIsoTime != "0000-00-00 00:00:00")
                            && ($this->postponeNewIsoDate." ".$this->postponeNewIsoTime < date("Y-m-d H:i:s")))
                                return $this->dateTimeFormat->addDay($this->postponeNewDate);
                        else
                                return $this->dateTimeFormat->addDay($this->date);
                }
                function getTime()
                {
                        if(    ($this->postponeID > 0)
                            && ($this->postponeNewIsoDate." ".$this->postponeNewIsoTime != "0000-00-00 00:00:00")
                            && ($this->postponeNewIsoDate." ".$this->postponeNewIsoTime < date("Y-m-d H:i:s")))
                                return $this->postponeNewTime;
                        else
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
                function getHomeResult()
                {
                        return $this->homeResult;
                }
                function getGuestResult()
                {
                        return $this->guestResult;
                }
                function getResult()
                {
                        if(($this->homeResult != "-1") && ($this->guestResult != "-1"))
                                return $this->homeResult . " : " .$this->guestResult;
                        else if( $this->canceled == 'yes' )
                                return "ausgef.";
                        else if( $this->postponeID > 0)
                                return "ausgef.";
                        else // if(($this->homeResult == "-1") || ($this->guestResult == "-1"))
                                return "&amp;nbsp; : &amp;nbsp;";
                }
                function getDOMNumber()
                {
                        return $this->domNumber;
                }
/*
                function getReportHead()
                {
                        return $this->reportHead;
                }
                function getReportText()
                {
                        return $this->reportText;
                }
*/
                function getReportID()
                {
                        return $this->reportID;
                }
                function getOthermatchReportID()
                {
                        return $this->othermatchReportID;
                }
                function getCanceled()
                {
                        return $this->canceled;
                }

                function getPostponeID()
                {
                        return $this->postponeID;
                }
/*
                function getPostponeReason()
                {
                        return $this->postponeReason;
                }
*/
                function getPostponeNewDate()
                {
                        return $this->postponeNewDate;
                }
                function getPostponeNewTime()
                {
                        return $this->postponeNewTime;
                }
                function getMaxIsoDate()
                {
                        return $this->maxIsoDate;
                }
        }
?>
