<?php
/*
        class MatchOfLeague
        {
                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;

                var $number = 0;
                var $date = "0000-00-00";
                var $time = "00:00:00";

                var $homeTeamID = 0;
                var $homeTeamName = "";
                var $homeTeamShortname = "";

                var $guestTeamID = 0;
                var $guestTeamName = "";
                var $guestTeamShortname = "";

                var $finished = false;
                var $homeResult = 0;
                var $guestResult = 0;

                var $postpone = null;
                var $report = null;

                // static attributes
                var $db;
                var $validate;

                function MatchOfLeague($db)
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


                        $this->homeTeamID = 0;
                        $this->homeTeamName = "";
                        $this->homeTeamShortname = "";

                        $this->guestTeamID = 0;
                        $this->guestTeamName = "";
                        $this->guestTeamShortname = "";

                        $this->finished = false;
                        $this->homeResult = 0;
                        $this->guestResult = 0;

                        $this->postpone = null;
                        $this->report = null;
                }

                function create($season, $league, $sport, $number, $date, $time, $homeID, $guestID)
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
                        if( !($this->validate->isNumber($homeID)))
                                return false;
                        if( !($this->validate->isNumber($guestID)))
                                return false;

                        $this->number = $number;
                        $this->date = $date;
                        $this->time = $time;
                        $this->homeTeamID = $homeID;
                        $this->guestTeamID = $guestID;

                        return $this->save();
                }

                function result($homeResult, $guestResult)
                {
                        if( !($this->validate->isNumber($homeResult)))
                                return false;
                        if( !($this->validate->isNumber($guestResult)))
                                return false;
                        $this->homeResult = $homeResult;
                        $this->guestResult = $guestResult;

                        $query = sprintf("  update lmMatchOfLeague
                                            set    homeResult = '%s',
                                                   guestResult = '%s'
                                            where  seasonID='%s' and
                                                   leagueID='%s' and
                                                   sportID='%s' and
                                                   number='%s'
                                        ",
                                        $this->homeResult,
                                        $this->guestResult,
                                        $this->seasonID,
                                        $this->leagueID,
                                        $this->sportID,
                                        $this->number
                                        );
                        return $this->db->executeQuery($query);
                }

                function save()
                {
                        // check for update: If the seasonID, leagueID and sportID already exists in database then do an update
                        $query = sprintf("select * from lmMatchOfLeague where seasonID='%s' and leagueID='%s' and sportID='%s' and number='%s'", $this->seasonID, $this->leagueID, $this->sportID, $this->number);

                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                $query = sprintf("  update lmMatchOfLeague
                                                    set    date = '%s',
                                                           time = '%s',
                                                           homeTeamID = '%s',
                                                           guestTeamID = '%s'
                                                    where  seasonID='%s' and
                                                           leagueID='%s' and
                                                           sportID='%s' and
                                                           number='%s'
                                                 ",
                                                    $this->date,
                                                    $this->time,
                                                    $this->homeTeamID,
                                                    $this->guestTeamID,
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->number
                                                );
                                $this->db->executeQuery($query);
                                return true;
                        }

                        $query = sprintf("  insert into lmMatchOfLeague
                                                   ( seasonID, leagueID, sportID, date, time, homeTeamID, guestTeamID, number )
                                            values (   '%s'  ,   '%s'  ,  '%s'  , '%s', '%s',    '%s'   ,     '%s'   ,  '%s'  )
                                         ",
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->date,
                                                    $this->time,
                                                    $this->homeTeamID,
                                                    $this->guestTeamID,
                                                    $this->number
                                        );
                        return $this->db->executeQuery($query);
                }

                function load($seasonID, $leagueID, $sportID, $number)
                {
                        $this->reset();

                        $this->seasonID = $seasonID;
                        $this->leagueID = $leagueID;
                        $this->sportID = $sportID;
                        $this->number = $number;

                        $query = sprintf("  select mat.date,
                                                   mat.time,
                                                   mat.homeTeamID,
                                                   mat.guestTeamID,
                                                   mat.homeResult,
                                                   mat.guestResult,
                                                   mat.postponeID,
                                                   mat.reportID,
                                                   home.name as homeName,
                                                   home.shortname as homeShortname,
                                                   guest.name as guestName,
                                                   guest.shortname as guestShortname
                                            from   lmMatchOfLeague as mat,
                                                   lmTeam as home,
                                                   lmTeam as guest
                                            where  mat.seasonID='%s' and
                                                   mat.leagueID='%s' and
                                                   mat.sportID='%s' and
                                                   mat.number='%s' and
                                                   mat.homeTeamID=home.ID and
                                                   mat.guestTeamID=guest.ID
                                         ", $seasonID,
                                            $leagueID,
                                            $sportID,
                                            $number
                                        );
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->date = $this->db->getValue("date");
                                $this->time = $this->db->getValue("time");

                                $this->homeTeamID = $this->db->getValue("homeTeamID");
                                $this->homeTeamName = $this->db->getValue("homeName");
                                $this->homeTeamShortname = $this->db->getValue("homeShortname");

                                $this->guestTeamID = $this->db->getValue("guestTeamID");
                                $this->guestTeamName = $this->db->getValue("guestName");
                                $this->guestTeamShortname = $this->db->getValue("guestShortname");

                                $this->homeResult = $this->db->getValue("homeResult");
                                $this->guestResult = $this->db->getValue("guestResult");

                                $this->reportID = $this->db->getValue("reportID");
                                $this->postponeID = $this->db->getValue("postponeID");
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
                        $this->reset();
                        $this->date = "";
                        $this->time = "";
                        $this->number = $number;
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

                function getNumber()
                {
                        return $this->number;
                }
                function getDate()
                {
                        return $this->date;
                }
                function getTime()
                {
                        return $this->time;
                }

                function getHomeTeamID()
                {
                        return $this->homeTeamID;
                }
                function getHomeTeamName()
                {
                        return $this->homeTeamName;
                }
                function getHomeTeamShortname()
                {
                        return $this->homeTeamShortname;
                }

                function getGuestTeamID()
                {
                        return $this->guestTeamID;
                }
                function getGuestTeamName()
                {
                        return $this->guestTeamName;
                }
                function getGuestTeamShortname()
                {
                        return $this->guestTeamShortname;
                }

                function getHomeResult()
                {
                        return $this->homeResult;
                }
                function getGuestResult()
                {
                        return $this->guestResult;
                }


                function getIsFinished()
                {
                        return $this->finished;
                }

        }


        class MatchOfLeagueList
        {
                var $list = array();

                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;
                var $numberOfRegisteredMatches = 0;

                var $db;

                function MatchOfLeagueList($db)
                {
                        $this->db = $db;
                }


                function reset()
                {
                        $this->list = array();
                        $this->seasonID = 0;
                        $this->leagueID = 0;
                        $this->sportID = 0;
                        $this->numberOfRegisteredMatches = 0;
                }

                function createList($leagueInSeason)
                {
                        $this->reset();

                        $this->seasonID = $leagueInSeason->getSeasonID();
                        $this->leagueID = $leagueInSeason->getLeagueID();
                        $this->sportID = $leagueInSeason->getSportID();

                        $query = sprintf( " select   number
                                            from     lmMatchOfLeague
                                            where    seasonID='%s' and
                                                     leagueID='%s' and
                                                     sportID='%s'
                                            order by number
                                          ", $leagueInSeason->getSeasonID(),
                                             $leagueInSeason->getLeagueID(),
                                             $leagueInSeason->getSportID()
                                       );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("number");
                        $this->numberOfRegisteredMatches = sizeof($this->list);
                }
                function createEditList($leagueInSeason)
                {
                        $this->createList($leagueInSeason);

                        $this->list = array();

                        if($leagueInSeason->getIsDoubleSeason() == "true")
                                $t = $leagueInSeason->getNumberOfTeams() * ($leagueInSeason->getNumberOfTeams() - 1) * 2;
                        else
                                $t = $leagueInSeason->getNumberOfTeams() * ($leagueInSeason->getNumberOfTeams() - 1);

                        for( $i = 1; $i <= $t ; $i++)
                                $this->list[] = $i;
                }

                function hasNext()
                {
                        return sizeof($this->list) > 0;
                }
                function next()
                {
                        $o = new MatchOfLeague($this->db);
                        if(!$o->load($this->seasonID, $this->leagueID, $this->sportID, $this->list[0]))
                                $o->setEmpty($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }

                function getNumberOfRegisteredMatches()
                {
                        return $this->numberOfRegistereMatches;
                }
                function setDB($db)
                {
                        $this->db = $db;
                }
        }

*/

        class MatchOfLeague
        {
                var $number = 0;
                var $date = "0000-00-00";
                var $time = "00:00:00";

                var $homeID;
                var $guestID;
                var $homeTeamName;
                var $guestTeamName;
                var $homeResult;
                var $guestResult;
                var $isRegistered;
                var $isPostponed;

                var $db;

                function MatchOfLeague($db)
                {
                        $this->db = $db;
                }

                function reset()
                {
                        $this->number = 0;
                        $this->date = "0000-00-00";
                        $this->time = "00:00:00";
                        $this->homeID = 0;
                        $this->guestID = 0;
                        $this->homeTeamName = "";
                        $this->guestTeamName = "";
                        $this->homeResult = "";
                        $this->guestResult = "";
                        $this->isRegistered = false;
                        $this->isPostponed = false;
                }


                function getNumber()
                {
                        return $this->number;
                }
                function getDate()
                {
                        return $this->date;
                }
                function getTime()
                {
                        return $this->time;
                }
                function getHomeTeamID()
                {
                        return $this->homeID;
                }
                function getGuestTeamID()
                {
                        return $this->guestID;
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
                function getIsRegistered()
                {
                        return $this->isRegistered;
                }
                function getIsPostponed()
                {
                        return $this->isPostponed;
                }
        }

        class MatchOfLeagueList
        {
                var $list;
                var $number;
                var $index;
                var $numberOfRegisteredMatches;
                var $numberOfRegisteredResults;

                var $match;
                var $lis;
                var $dom;
                var $season;
                var $sport;
                var $league;

                // static attributes
                var $db;
                var $validate;


                function MatchOfLeagueList($db)
                {
                        $this->db = $db;
                        $this->match = new MatchOfLeague($db);
                }

                function init($lis, $dom)
                {
                        $this->list = array();

                        $this->dom = $dom;

                        $this->lis = $lis;
                        $this->lis->setDB($this->db);

                        $this->season = $lis->getSeason();
                        $this->sport = $lis->getSport();
                        $this->league = $lis->getLeague();

                        $this->reset();
                }

                function reset()
                {
                        $this->index = 0;
                }

                function modifyList($isodates, $isotimes, $hometeams, $guestteams)
                {
                        if($this->lis->getHasDaysOfMatch() == "true")
                                $domNumber = $this->dom->getNumber();
                        else
                                $domNumber = 0;

                        $this->calcNumberOfMatches();
                        foreach($isodates as $i => $x)
                        {
                                if( (empty($isodates[$i]) && empty($isotimes[$i]) ) ||
                                    empty($hometeams[$i]) || ($hometeams[$i] == 0) ||
                                    empty($guestteams[$i]) || ($guestteams[$i] == 0 ) )
                                {
                                        // delete match
                                        $query = sprintf("delete
                                                          from   lmMatchOfLeague
                                                          where  seasonID = '%s'
                                                          and    sportID = '%s'
                                                          and    leagueID = '%s'
                                                          and    dayOfMatchNumber = '%s'
                                                          and    number = '%s'
                                                         ", $this->season->getID(),
                                                            $this->sport->getID(),
                                                            $this->league->getID(),
                                                            $domNumber,
                                                            $i );
                                        $this->db->executeQuery($query);
                                }
                                else
                                {
                                        $query = sprintf("select number
                                                          from   lmMatchOfLeague
                                                          where  seasonID = '%s'
                                                          and    sportID = '%s'
                                                          and    leagueID = '%s'
                                                          and    dayOfMatchNumber = '%s'
                                                          and    number = '%s'
                                                         ", $this->season->getID(),
                                                            $this->sport->getID(),
                                                            $this->league->getID(),
                                                            $domNumber,
                                                            $i );
                                        $this->db->executeQuery($query);

                                        if($this->db->getNumRows() == 0)
                                        {
                                                // insert match
                                                $query = sprintf("insert
                                                                  into   lmMatchOfLeague
                                                                         (number,
                                                                          date,
                                                                          time,
                                                                          seasonID,
                                                                          sportID,
                                                                          leagueID,
                                                                          dayOfMatchNumber,
                                                                          homeTeamID,
                                                                          guestTeamID )
                                                                  values ( '%s',
                                                                           %s,
                                                                           %s,
                                                                           '%s',
                                                                           '%s',
                                                                           '%s',
                                                                           '%s',
                                                                           '%s',
                                                                           '%s' ) ",
                                                                           $i,
                                                                           empty($isodates[$i])?"NULL":"'".$isodates[$i]."'",
                                                                           empty($isotimes[$i])?"NULL":"'".$isotimes[$i]."'",
                                                                           $this->season->getID(),
                                                                           $this->sport->getID(),
                                                                           $this->league->getID(),
                                                                           $domNumber,
                                                                           $hometeams[$i],
                                                                           $guestteams[$i] );
                                                $this->db->executeQuery($query);
                                        }
                                        else
                                        {
                                                // update
                                                 $query = sprintf("update lmMatchOfLeague
                                                                   set    date = %s,
                                                                          time = %s,
                                                                          homeTeamID = '%s',
                                                                          guestTeamID = '%s'
                                                                   where  seasonID = '%s'
                                                                   and    sportID = '%s'
                                                                   and    leagueID = '%s'
                                                                   and    dayOfMatchNumber = '%s'
                                                                   and    number = '%s'
                                                                  ",      empty($isodates[$i])?"NULL":"'".$isodates[$i]."'",
                                                                          empty($isotimes[$i])?"NULL":"'".$isotimes[$i]."'",
                                                                          $hometeams[$i],
                                                                          $guestteams[$i],
                                                                          $this->season->getID(),
                                                                          $this->sport->getID(),
                                                                          $this->league->getID(),
                                                                          $domNumber,
                                                                          $i );
                                                $this->db->executeQuery($query);
                                        }
                                }
                        }
                        return true;
                }

                function modifyResults($homeresults, $guestresults, $canceled)
                {
                        if($this->lis->getHasDaysOfMatch() == "true")
                                $domNumber = $this->dom->getNumber();
                        else
                                $domNumber = 0;
echo "domNumber: ".$domNumber."<br>";
var_dump($homeresults);
echo "<br>";
var_dump($guestresults);
echo "<br>";
var_dump($canceled);
echo "<br>";

                        if(is_array($homeresults) && is_array($guestresults) && is_array($canceled))
                        {
                                foreach($homeresults as $key => $val)
                                {
                                        $query = sprintf(" update lmMatchOfLeague
                                                           set    homeResult = '%s',
                                                                  guestResult = '%s'
                                                           where  seasonID = '%s'
                                                           and    sportID = '%s'
                                                           and    leagueID = '%s'
                                                           and    dayOfMatchNumber = '%s'
                                                           and    number = '%s' ",
                                                                  ($homeresults[$key]=="")?"-1":$homeresults[$key],
                                                                  ($guestresults[$key]=="")?"-1":$guestresults[$key],
                                                                  $this->season->getID(),
                                                                  $this->sport->getID(),
                                                                  $this->league->getID(),
                                                                  $domNumber,
                                                                  $key );
                                        $this->db->executeQuery($query);


                                        if( $canceled[$key] == "true")
                                        {
                                                $query = sprintf(" select *
                                                                   from   lmMatchPostpone
                                                                   where  seasonID = '%s'
                                                                   and    sportID = '%s'
                                                                   and    leagueID = '%s'
                                                                   and    dayOfMatchNumber = '%s'
                                                                   and    number = '%s' ",
                                                                   $this->season->getID(),
                                                                   $this->sport->getID(),
                                                                   $this->league->getID(),
                                                                   $domNumber,
                                                                   $key );
                                                $this->db->executeQuery($query);

                                                if($this->db->getNumRows() == 0)
                                                {
                                                        $query = sprintf("insert
                                                                          into     lmMatchPostpone
                                                                          (        reason,
                                                                                   newdate,
                                                                                   newtime,
                                                                                   seasonID,
                                                                                   sportID,
                                                                                   leagueID,
                                                                                   dayOfMatchNumber,
                                                                                   number )
                                                                           values ( '%s',
                                                                                     %s,
                                                                                     %s,
                                                                                    '%s',
                                                                                    '%s',
                                                                                    '%s',
                                                                                    '%s',
                                                                                    '%s' ) ",
                                                                            "",
                                                                            "NULL",
                                                                            "NULL",
                                                                            $this->season->getID(),
                                                                            $this->sport->getID(),
                                                                            $this->league->getID(),
                                                                            $domNumber,
                                                                            $key );
                                                        $this->db->executeQuery($query);
                                                }
                                        }

                                        if( !isset($canceled[$key]) )
                                        {
                                                $query = sprintf(" select *
                                                                   from   lmMatchPostpone
                                                                   where  seasonID = '%s'
                                                                   and    sportID = '%s'
                                                                   and    leagueID = '%s'
                                                                   and    dayOfMatchNumber = '%s'
                                                                   and    number = '%s' ",
                                                                   $this->season->getID(),
                                                                   $this->sport->getID(),
                                                                   $this->league->getID(),
                                                                   $domNumber,
                                                                   $key );
                                                $this->db->executeQuery($query);

                                                if($this->db->getNumRows() == 1)
                                                {
                                                        $query = sprintf("delete
                                                                          from     lmMatchPostpone
                                                                          where    seasonID = %s
                                                                          and      sportID = %s
                                                                          and      leagueID = %s
                                                                          and      dayOfMatchNumber = %s
                                                                          and      number = %s",
                                                                          $this->season->getID(),
                                                                          $this->sport->getID(),
                                                                          $this->league->getID(),
                                                                          $domNumber,
                                                                          $key );
                                                         $this->db->executeQuery($query);
                                                }
                                        }

                                 }

                                 return true;
                        }
                        else
                                return false;

                }

                function loadEditList()
                {
                        if($this->lis->getHasDaysOfMatch() == "true")
                        {
                                $query = sprintf("select   mol.number,
                                                           mol.date as isodate,
                                                           mol.time as isotime,
                                                           date_format(mol.date, '%%d.%%m.%%Y') as date,
                                                           time_format(mol.time, '%%H:%%i') as time,
                                                           mol.homeTeamID,
                                                           mol.guestTeamID,
                                                           mol.homeResult,
                                                           mol.guestResult,
                                                           home.name as homeTeamName,
                                                           guest.name as guestTeamName,
                                                           mpp.ID
                                                  from     lmMatchOfLeague mol
                                                  left join lmMatchPostpone mpp
                                                  using    (seasonID, sportID, leagueID, dayOfMatchNumber, number)
                                                  left join lmTeam home
                                                  on        home.ID = mol.homeTeamID
                                                  left join lmTeam guest
                                                  on        guest.ID = mol.guestTeamID
                                                  where    mol.seasonID = '%s'
                                                  and      mol.sportID = '%s'
                                                  and      mol.leagueID = '%s'
                                                  and      mol.dayOfMatchNumber = '%s'
                                                  order by mol.number ",
                                                           $this->season->getID(),
                                                           $this->sport->getID(),
                                                           $this->league->getID(),
                                                           $this->dom->getNumber() );

                                $this->db->executeQuery($query);

                                $this->numberOfRegisteredMatches = $this->db->getNumRows();

                                $this->list = array();

                                $this->calcNumberOfMatches();
                                $this->db->nextRow();
                                for( $i = 1; $i <= $this->number ; $i++)
                                {
                                        if($i == $this->db->getValue("number"))
                                        {
                                                $this->list[] = array( $i,
                                                                       $this->db->getValue("date"),
                                                                       $this->db->getValue("time"),
                                                                       $this->db->getValue("homeTeamID"),
                                                                       $this->db->getValue("guestTeamID"),
                                                                       $this->db->getValue("homeResult")=="-1"?"":$this->db->getValue("homeResult"),
                                                                       $this->db->getValue("guestResult")=="-1"?"":$this->db->getValue("guestResult"),
                                                                       $this->db->getValue("homeTeamName"),
                                                                       $this->db->getValue("guestTeamName"),
                                                                       true,
                                                                       $this->db->getValue("ID")
                                                                       );
                                                $this->db->nextRow();
                                        }
                                        else
                                                $this->list[] = array( $i, "", "", "", "", "", "", "", "", false, "");
                                }
                        }
                        else
                        {
                                $query = sprintf("select date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    leagueID='%s'
                                                  and    number='1' ",
                                                   $this->season->getID(),
                                                   $this->league->getID());
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $startdate = strtotime($this->db->getValue("isodate"));
                                $startnumber = 1;
                                $query = sprintf("select number,
                                                         date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    leagueID='%s'
                                                  and    ( number='%s' or number='%s' )
                                                  order by number desc",
                                                         $this->season->getID(),
                                                         $this->league->getID(),
                                                         $this->dom->getNumber(),
                                                         $this->dom->getNumber()-1 );
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $enddate = strtotime($this->db->getValue("isodate"));
                                $endnumber = $this->db->getValue("number");


                                if( ($endnumber < $this->dom->getNumber()) || ($endnumber == ""))
                                {
                                        // new DOM-week
					if($this->dom->getNumber() == 0) 
						$n = 1;
					else
						$n = $this->dom->getNumber();

                                        for( $i = $n; $i <= ($n+($this->lis->getNumberOfTeams()/2)) ; $i++)
                                        {
                                                        $this->list[] = array( $i, "", "", "", "", "", "", "", "", false);
                                        }
                                }
                                else
                                {
                                        $lastSunday = $startdate;
                                        $nextSunday = $lastSunday + ( (7-date("w", $lastSunday)) * 24*60*60);

                                        while($lastSunday < $enddate)
                                        {
                                                $query = sprintf("select number,
                                                                         date as isodate,
                                                                         time as isotime,
                                                                         date_format(date, '%%d.%%m.%%Y') as date,
                                                                         time_format(time, '%%H:%%i') as time
                                                                  from   lmMatchOfLeague
                                                                  where  seasonID='%s'
                                                                  and    leagueID='%s'
                                                                  and    date between '%s' and '%s'
                                                                  group by number ",
                                                                  $this->season->getID(),
                                                                  $this->league->getID(),
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

                                        $query = sprintf("select   mol.number,
                                                                   mol.date as isodate,
                                                                   mol.time as isotime,
                                                                   date_format(mol.date, '%%d.%%m.%%Y') as date,
                                                                   time_format(mol.time, '%%H:%%i') as time,
                                                                   mol.homeTeamID,
                                                                   mol.guestTeamID,
                                                                   mol.homeResult,
                                                                   mol.guestResult,
                                                                   home.name as homeTeamName,
                                                                   guest.name as guestTeamName,
                                                                   mpp.ID
                                                          from     lmMatchOfLeague mol
                                                          left join lmMatchPostpone mpp
                                                          using    (seasonID, sportID, leagueID, dayOfMatchNumber, number)
                                                          left join lmTeam home
                                                          on        home.ID = mol.homeTeamID
                                                          left join lmTeam guest
                                                          on        guest.ID = mol.guestTeamID
                                                          where    mol.seasonID = '%s'
                                                          and      mol.sportID = '%s'
                                                          and      mol.leagueID = '%s'
                                                          and      mol.number between '%s' and '%s'
                                                          order by mol.number ",
                                                                   $this->season->getID(),
                                                                   $this->sport->getID(),
                                                                   $this->league->getID(),
                                                                   $startnumber,
                                                                   $endnumber );
                                        $this->db->executeQuery($query);

                                        $this->numberOfRegisteredMatches = $this->db->getNumRows();

                                        $this->list = array();

                                        $this->calcNumberOfMatches();
                                        $this->db->nextRow();
                                        for( $i = $startnumber; $i <= $endnumber ; $i++)
                                        {
                                                if($i == $this->db->getValue("number"))
                                                {
                                                        $this->list[] = array( $i,
                                                                               $this->db->getValue("date"),
                                                                               $this->db->getValue("time"),
                                                                               $this->db->getValue("homeTeamID"),
                                                                               $this->db->getValue("guestTeamID"),
                                                                               $this->db->getValue("homeResult")=="-1"?"":$this->db->getValue("homeResult"),
                                                                               $this->db->getValue("guestResult")=="-1"?"":$this->db->getValue("guestResult"),
                                                                               $this->db->getValue("homeTeamName"),
                                                                               $this->db->getValue("guestTeamName"),
                                                                               true,
                                                                               $this->db->getValue("ID")
                                                                               );
                                                        $this->db->nextRow();
                                                }
                                                else
                                                        $this->list[] = array( $i, "", "", "", "", "", "", "", "", false, "");
                                        }
                                }
                        }
                        $this->number = sizeof($this->list);
                }


                function loadList()
                {
                        if($this->lis->getHasDaysOfMatch() == "true")
                        {
                                $query = sprintf("select   mol.number,
                                                           mol.date as isodate,
                                                           mol.time as isotime,
                                                           date_format(mol.date, '%%d.%%m.%%Y') as date,
                                                           time_format(mol.time, '%%H:%%i') as time,
                                                           mol.homeTeamID,
                                                           mol.guestTeamID,
                                                           mol.homeResult,
                                                           mol.guestResult,
                                                           home.name as homeTeamName,
                                                           guest.name as guestTeamName,
                                                           mpp.ID
                                                  from     lmMatchOfLeague mol
                                                  left join lmMatchPostpone mpp
                                                  using    (seasonID, sportID, leagueID, dayOfMatchNumber, number)
                                                  left join lmTeam home
                                                  on        home.ID = mol.homeTeamID
                                                  left join lmTeam guest
                                                  on        guest.ID = mol.guestTeamID
                                                  where    mol.seasonID = '%s'
                                                  and      mol.sportID = '%s'
                                                  and      mol.leagueID = '%s'
                                                  and      mol.dayOfMatchNumber = '%s'
                                                  order by mol.number ",
                                                           $this->season->getID(),
                                                           $this->sport->getID(),
                                                           $this->league->getID(),
                                                           $this->dom->getNumber() );
                        }
                        else
                        {
                                $query = sprintf("select date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    leagueID='%s'
                                                  and    number='1' ",
                                                   $this->season->getID(),
                                                   $this->league->getID());
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $startdate = strtotime($this->db->getValue("isodate"));

                                $query = sprintf("select date as isodate
                                                  from   lmMatchOfLeague
                                                  where  seasonID='%s'
                                                  and    leagueID='%s'
                                                  and    number='%s' ",
                                                         $this->season->getID(),
                                                         $this->league->getID(),
                                                         $this->dom->getNumber() );
                                $this->db->executeQuery($query);
                                $this->db->nextRow();
                                $enddate = strtotime($this->db->getValue("isodate"));
                                $endnumber = $this->dom->getNumber();

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
                                                          $this->season->getID(),
                                                          $this->league->getID(),
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

                                $query = sprintf("select   mol.number,
                                                           mol.date as isodate,
                                                           mol.time as isotime,
                                                           date_format(mol.date, '%%d.%%m.%%Y') as date,
                                                           time_format(mol.time, '%%H:%%i') as time,
                                                           mol.homeTeamID,
                                                           mol.guestTeamID,
                                                           mol.homeResult,
                                                           mol.guestResult,
                                                           home.name as homeTeamName,
                                                           guest.name as guestTeamName,
                                                           mpp.ID
                                                  from     lmMatchOfLeague mol
                                                  left join lmMatchPostpone mpp
                                                  using    (seasonID, sportID, leagueID, dayOfMatchNumber, number)
                                                  left join lmTeam home
                                                  on        home.ID = mol.homeTeamID
                                                  left join lmTeam guest
                                                  on        guest.ID = mol.guestTeamID
                                                  where    mol.seasonID = '%s'
                                                  and      mol.sportID = '%s'
                                                  and      mol.leagueID = '%s'
                                                  and      mol.number between '%s' and '%s'
                                                  order by mol.number ",
                                                           $this->season->getID(),
                                                           $this->sport->getID(),
                                                           $this->league->getID(),
                                                           $startnumber,
                                                           $endnumber );
                        }

                        $this->db->executeQuery($query);
                        $this->list = array();
                        $this->numberOfRegisteredResults = 0;
                        while($this->db->nextRow())
                        {
                                if($this->db->getValue("homeResult") != "-1")
                                        $this->numberOfRegisteredResults++;

                                $this->list[] = array($this->db->getValue("number"),
                                                      $this->db->getValue("date"),
                                                      $this->db->getValue("time"),
                                                      $this->db->getValue("homeTeamID"),
                                                      $this->db->getValue("guestTeamID"),
                                                      $this->db->getValue("homeResult")=="-1"?"":$this->db->getValue("homeResult"),
                                                      $this->db->getValue("guestResult")=="-1"?"":$this->db->getValue("guestResult"),
                                                      $this->db->getValue("homeTeamName"),
                                                      $this->db->getValue("guestTeamName"),
                                                      true,
                                                      $this->db->getValue("ID") );
                        }
                        $this->number = sizeof($this->list);
                }


                function hasNext()
                {
                        return ($this->index < $this->number);
                }
                function next()
                {
                        $this->match->reset();
                        $this->match->number = $this->list[$this->index][0];
                        $this->match->date = $this->list[$this->index][1];
                        $this->match->time = $this->list[$this->index][2];
                        $this->match->homeID = $this->list[$this->index][3];
                        $this->match->guestID = $this->list[$this->index][4];
                        $this->match->homeResult = $this->list[$this->index][5];
                        $this->match->guestResult = $this->list[$this->index][6];
                        $this->match->homeTeamName = $this->list[$this->index][7];
                        $this->match->guestTeamName = $this->list[$this->index][8];
                        $this->match->isRegistered = $this->list[$this->index][9];
                        $this->match->isPostponed = $this->list[$this->index][10];
                        $this->index++;
                        return $this->match;
                }

                function calcNumberOfMatches()
                {
                        if($this->lis->getHasDaysOfMatch() == "true")
                        {
                                if(($this->lis->getNumberOfTeams() % 2) == 0)
                                        $this->number = $this->lis->getNumberOfTeams() / 2;
                                else
                                        $this->number = ($this->lis->getNumberOfTeams()-1) / 2;
                        }
                        else
                        {
                                if($this->lis->getIsDoubleSeason() == "true")
                                        $double = 2;
                                else
                                        $double = 1;
                                $this->number = ($this->lis->getNumberOfTeams() - 1) * $this->lis->getNumberOfTeams() * $double;
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

                function getNumberOfRegisteredMatches()
                {
                        return $this->numberOfRegisteredMatches;
                }

                function getNumberOfRegisteredResults()
                {
                        return $this->numberOfRegisteredResults;
                }







        }





?>
