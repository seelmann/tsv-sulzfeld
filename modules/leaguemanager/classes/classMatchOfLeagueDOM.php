<?php
        class MatchOfLeagueDOM
        {
                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;
                var $dayOfMatchNumber = 0;

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

                function MatchOfLeagueDOM($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }
                function reset()
                {
                        $this->seasonID = 0;
                        $this->leagueID = 0;
                        $this->sportID = 0;
                        $this->dayOfMatchNumber = 0;

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

                function create($season, $league, $sport, $dayOfMatch, $number, $date, $time, $homeID, $guestID)
                {
                        if( ! (is_object($season) && (get_class($season)=="season") )  )
                                return false;
                        if( ! (is_object($league) && (get_class($league)=="league") )  )
                                return false;
                        if( ! (is_object($sport) && (get_class($sport)=="sport") )  )
                                return false;
                        if( ! (is_object($dayOfMatch) && (get_class($dayOfMatch)=="dayofmatch") )  )
                                return false;

                        $this->seasonID = $season->getID();
                        $this->leagueID = $league->getID();
                        $this->sportID = $sport->getID();
                        $this->dayOfMatchNumber = $dayOfMatch->getNumber();

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

                        $query = sprintf("  update lmMatchOfLeagueDOM
                                            set    homeResult = '%s',
                                                   guestResult = '%s'
                                            where  seasonID='%s' and
                                                   leagueID='%s' and
                                                   sportID='%s' and
                                                   dayOfMatchNumber='%s' and
                                                   number='%s'
                                        ",
                                        $this->homeResult,
                                        $this->guestResult,
                                        $this->seasonID,
                                        $this->leagueID,
                                        $this->sportID,
                                        $this->dayOfMatchNumber,
                                        $this->number
                                        );
echo $query;
                        return $this->db->executeQuery($query);
                }

                function save()
                {
                        // check for update: If the seasonID, leagueID and sportID already exists in database then do an update
                        $query = sprintf("select * from lmMatchOfLeagueDOM where seasonID='%s' and leagueID='%s' and sportID='%s' and dayOfMatchNumber='%s' and number='%s'", $this->seasonID, $this->leagueID, $this->sportID, $this->dayOfMatchNumber, $this->number);

echo $query;
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                $query = sprintf("  update lmMatchOfLeagueDOM
                                                    set    date = '%s',
                                                           time = '%s',
                                                           homeTeamID = '%s',
                                                           guestTeamID = '%s'
                                                    where  seasonID='%s' and
                                                           leagueID='%s' and
                                                           sportID='%s' and
                                                           dayOfMatchNumber='%s' and
                                                           number='%s'
                                                 ",
                                                    $this->date,
                                                    $this->time,
                                                    $this->homeTeamID,
                                                    $this->guestTeamID,
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->dayOfMatchNumber,
                                                    $this->number
                                                );
echo $query;
                                $this->db->executeQuery($query);
                                return true;
                        }

                        $query = sprintf("  insert into lmMatchOfLeagueDOM
                                                   ( seasonID, leagueID, sportID, dayOfMatchNumber, date, time, homeTeamID, guestTeamID, number )
                                            values (   '%s'  ,   '%s'  ,  '%s'  ,       '%s'      , '%s', '%s',    '%s'   ,     '%s'   ,  '%s'  )
                                         ",
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->dayOfMatchNumber,
                                                    $this->date,
                                                    $this->time,
                                                    $this->homeTeamID,
                                                    $this->guestTeamID,
                                                    $this->number
                                        );
echo $query;
                        return $this->db->executeQuery($query);
                }

                function load($seasonID, $leagueID, $sportID, $dayOfMatchNumber, $number)
                {
                        $this->reset();

                        $this->seasonID = $seasonID;
                        $this->leagueID = $leagueID;
                        $this->sportID = $sportID;
                        $this->dayOfMatchNumber = $dayOfMatchNumber;
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
                                            from   lmMatchOfLeagueDOM as mat,
                                                   lmTeam as home,
                                                   lmTeam as guest
                                            where  mat.seasonID='%s' and
                                                   mat.leagueID='%s' and
                                                   mat.sportID='%s' and
                                                   mat.dayOfMatchNumber='%s' and
                                                   mat.number='%s' and
                                                   mat.homeTeamID=home.ID and
                                                   mat.guestTeamID=guest.ID
                                         ", $seasonID,
                                            $leagueID,
                                            $sportID,
                                            $dayOfMatchNumber,
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
                function getDayOfMatchNumber()
                {
                        return $this->dayOfMatchNumber;
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


        class MatchOfLeagueDOMList
        {
                var $list = array();

                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;
                var $dayOfMatchNumber = 0;
                var $numberOfRegisteredMatches = 0;

                var $db;

                function MatchOfLeagueDOMList($db)
                {
                        $this->db = $db;
                }


                function reset()
                {
                        $this->list = array();
                        $this->seasonID = 0;
                        $this->leagueID = 0;
                        $this->sportID = 0;
                        $this->dayOfMatchNumber = 0;
                        $this->numberOfRegisteredMatches = 0;
                }

                function createList($dayOfMatch)
                {
                        $this->reset();

                        $this->seasonID = $dayOfMatch->getSeasonID();
                        $this->leagueID = $dayOfMatch->getLeagueID();
                        $this->sportID = $dayOfMatch->getSportID();
                        $this->dayOfMatchNumber = $dayOfMatch->getNumber();

                        $query = sprintf( " select   number
                                            from     lmMatchOfLeagueDOM
                                            where    seasonID='%s' and
                                                     leagueID='%s' and
                                                     sportID='%s' and
                                                     dayOfMatchNumber='%s'
                                            order by number
                                          ", $dayOfMatch->getSeasonID(),
                                             $dayOfMatch->getLeagueID(),
                                             $dayOfMatch->getSportID(),
                                             $dayOfMatch->getNumber()
                                       );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("number");
                        $this->numberOfRegisteredMatches = sizeof($this->list);
                }
                function createEditList($dayOfMatch, $leagueInSeason)
                {
                        $this->createList($dayOfMatch);

                        $this->list = array();

                        for( $i = 1; $i <= $leagueInSeason->getNumberOfTeams() / 2 ; $i++)
                                $this->list[] = $i;
                }

                function hasNext()
                {
                        return sizeof($this->list) > 0;
                }
                function next()
                {
                        $o = new MatchOfLeagueDOM($this->db);
                        if(!$o->load($this->seasonID, $this->leagueID, $this->sportID, $this->dayOfMatchNumber, $this->list[0]))
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
?>
