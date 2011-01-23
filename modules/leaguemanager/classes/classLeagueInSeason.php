<?php
        include_once("classSeason.php");
        include_once("classSport.php");
        include_once("classLeague.php");

        /* abstract */ class LeagueInSeason
        {
                // attributes
                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;
                var $seasonName = "";
                var $leagueName = "";
                var $sportName = "";
                var $isDoubleSeason = "false";
                var $hasDaysOfMatch = "false";
                var $numberOfTeams = 0;
                var $maleFemale = "male";


                // static attributes
                var $db;
                var $validate;


                function LeagueInSeason($db)
                {
                        $this->db = $db;
                        $this->validate = new Validation();
                }

                function reset()
                {
                        $this->seasonID = 0;
                        $this->leagueID = 0;
                        $this->sportID = 0;
                        $this->isDoubleSeason = "false";
                        $this->hasDaysOfMatch = "false";
                        $this->numberOfTeams = 0;
                        $this->maleFemale = "male";
                }

                function create($season, $league, $sport, $isDoubleSeason, $hasDaysOfMatch, $numberOfTeams, $maleFemale)
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

                        $trueFalseArray = array("true"=>"ja", "false"=>"nein");
                        $maleFemaleArray = array("male"=>"Herren/Jungen", "female"=>"Damen/Mädchen", "mixed"=>"Mixed");

                        if( !$this->validate->isInArray($isDoubleSeason, $trueFalseArray) )
                                return false;
                        if( !$this->validate->isInArray($hasDaysOfMatch, $trueFalseArray) )
                                return false;
                        if( !$this->validate->isNumber($numberOfTeams) )
                                return false;
                        if( !$this->validate->isInArray($maleFemale, $maleFemaleArray) )
                                return false;

                        $this->isDoubleSeason = $isDoubleSeason;
                        $this->hasDaysOfMatch = $hasDaysOfMatch;
                        $this->numberOfTeams = $numberOfTeams;
                        $this->maleFemale = $maleFemale;

                        return $this->save();
                }
                function modify($isDoubleSeason, $hasDaysOfMatch, $numberOfTeams, $maleFemale)
                {
                        $trueFalseArray = array("true"=>"ja", "false"=>"nein");
                        $maleFemaleArray = array("male"=>"Herren/Jungen", "female"=>"Damen/Mädchen", "mixed"=>"Mixed");

                        if( !$this->validate->isInArray($isDoubleSeason, $trueFalseArray) )
                                return false;
                        if( !$this->validate->isInArray($hasDaysOfMatch, $trueFalseArray) )
                                return false;
                        if( !$this->validate->isNumber($numberOfTeams) )
                                return false;
                        if( !$this->validate->isInArray($maleFemale, $maleFemaleArray) )
                                return false;

                        $this->isDoubleSeason = $isDoubleSeason;
                        $this->hasDaysOfMatch = $hasDaysOfMatch;
                        $this->numberOfTeams = $numberOfTeams;
                        $this->maleFemale = $maleFemale;

                        return $this->save();
                }
                function delete()
                {
                        // Abhängigkeiten überprüfen: teamsInLeagueInSeason, dayOfMatch, leagueMatch
                        $query = sprintf("select count(teamID) as number
                                          from   lmTeamInLeagueInSeason
                                          where  leagueID='%s'
                                          and    sportID='%s'
                                          and    seasonID='%s'
                                          ", $this->leagueID, $this->sportID, $this->seasonID);
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        if($this->db->getValue("number") == 0)
                        {
                                $query = sprintf("delete from lmLeagueInSeason where seasonID='%s' and leagueID='%s' and sportID='%s'", $this->seasonID, $this->leagueID, $this->sportID);
                                return $this->db->executeQuery($query);
                        }
                        else
                                return false;
                }

                function save()
                {
                        // check for update: If the seasonID, leagueID and sportID already exists in database then do an update
                        $query = sprintf("select * from lmLeagueInSeason where seasonID='%s' and leagueID='%s' and sportID='%s'", $this->seasonID, $this->leagueID, $this->sportID);
                        $this->db->executeQuery($query);
                        if($this->db->getNumRows() > 0)
                        {
                                $query = sprintf("  update lmLeagueInSeason
                                                    set    isDoubleSeason = '%s',
                                                           hasDaysOfMatch = '%s',
                                                           numberOfTeams = '%s',
                                                           maleFemale = '%s'
                                                    where  seasonID='%s' and
                                                           leagueID='%s' and
                                                           sportID='%s'
                                                 ",
                                                    $this->isDoubleSeason,
                                                    $this->hasDaysOfMatch,
                                                    $this->numberOfTeams,
                                                    $this->maleFemale,
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID
                                                );
                                $this->db->executeQuery($query);
                                return true;
                        }

                        $query = sprintf("  insert into lmLeagueInSeason
                                                   ( seasonID, leagueID, sportID, isDoubleSeason, hasDaysOfMatch, numberOfTeams, maleFemale )
                                            values (   '%s'  ,    %s   ,    %s  ,       '%s'    ,      '%s'     ,     '%s'     ,    '%s'    )
                                         ",
                                                    $this->seasonID,
                                                    $this->leagueID,
                                                    $this->sportID,
                                                    $this->isDoubleSeason,
                                                    $this->hasDaysOfMatch,
                                                    $this->numberOfTeams,
                                                    $this->maleFemale
                                        );
                        return $this->db->executeQuery($query);
                }

                function load($season, $league)
                {
                        return $this->loadID($season->getID(), $league->getID());
                }

                function loadID($seasonID, $leagueID)
                {

                        $query = sprintf("select lis.*,
                                                 l.name as leagueName,
                                                 sp.name as sportName,
                                                 s.name as seasonName
                                          from   lmLeagueInSeason lis,
                                                 lmLeague l,
                                                 lmSport sp,
                                                 lmSeason s
                                          where  s.ID=lis.seasonID
                                          and    l.ID=lis.leagueID
                                          and    sp.ID=lis.sportID
                                          and    lis.seasonID='%s'
                                          and    lis.leagueID='%s'",
                                          $seasonID,
                                          $leagueID );

                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->seasonID = $seasonID;
                                $this->leagueID = $leagueID;
                                $this->sportID = $this->db->getValue("sportID");

                                $this->seasonName = $this->db->getValue("seasonName");
                                $this->leagueName = $this->db->getValue("leagueName");
                                $this->sportName = $this->db->getValue("sportName");

                                $this->isDoubleSeason = $this->db->getValue("isDoubleSeason");
                                $this->hasDaysOfMatch = $this->db->getValue("hasDaysOfMatch");
                                $this->numberOfTeams = $this->db->getValue("numberOfTeams");
                                $this->maleFemale = $this->db->getValue("maleFemale");

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

                function getSeasonID()
                {
                        return $this->seasonID;
                }
                function getSeason()
                {
                        $season = new Season($this->db);
                        $season->load($this->seasonID);
                        return $season;
                }
                function getLeagueID()
                {
                        return $this->leagueID;
                }
                function getLeague()
                {
                        $league = new League($this->db);
                        $league->load($this->leagueID);
                        return $league;
                }
                function getSportID()
                {
                        return $this->sportID;
                }
                function getSport()
                {
                        $sport = new Sport($this->db);
                        $sport->load($this->sportID);
                        return $sport;
                }
                function getLeagueName()
                {
                        return $this->leagueName;
                }
                function getSportName()
                {
                        return $this->sportName;
                }
                function getSeasonName()
                {
                        return $this->seasonName;
                }
                function getIsDoubleSeason()
                {
                        return $this->isDoubleSeason;
                }
                function getHasDaysOfMatch()
                {
                        return $this->hasDaysOfMatch;
                }
                function getNumberOfTeams()
                {
                        return $this->numberOfTeams;
                }
                function getMaleFemale()
                {
                        return $this->maleFemale;
                }
        }

        class LeagueInSeasonIterator
        {
                var $seasonID = 0;
                var $leagueID = 0;
                var $sportID = 0;
                var $seasonName = "";
                var $leagueName = "";
                var $sportName = "";

                var $list;
                var $number;
                var $index;

                var $db;

                function LeagueInSeasonIterator($db)
                {
                        $this->db = $db;
                        $this->reset();
                }

                function reset()
                {
                        $this->index = 0;
                }

                function createIterator($seasonID=0)
                {
                        if($seasonID==0)
                        {
                                $query = sprintf(" select    l.name as leagueName,
                                                             sp.name as sportName,
                                                             s.name as seasonName,
                                                             lis.leagueID as leagueID,
                                                             lis.sportID as sportID,
                                                             lis.seasonID as seasonID
                                                   from      lmLeagueInSeason lis,
                                                             lmSeason s,
                                                             lmLeague l,
                                                             lmSport sp
                                                   where     s.ID=lis.seasonID
                                                   and       l.ID=lis.leagueID
                                                   and       sp.ID=lis.sportID
                                                   and       now() between s.dateFrom and s.dateTo
                                                   order by  sp.name,
                                                             l.activeYouth,
                                                             l.name ");
                        }
                        else
                        {
                                $query = sprintf(" select    l.name as leagueName,
                                                             sp.name as sportName,
                                                             s.name as seasonName,
                                                             lis.leagueID as leagueID,
                                                             lis.sportID as sportID,
                                                             lis.seasonID as seasonID
                                                   from      lmLeagueInSeason lis,
                                                             lmSeason s,
                                                             lmLeague l,
                                                             lmSport sp
                                                   where     s.ID=lis.seasonID
                                                   and       l.ID=lis.leagueID
                                                   and       sp.ID=lis.sportID
                                                   and       lis.seasonID='%s'
                                                   order by  sp.name,
                                                             l.activeYouth,
                                                             l.name ", $seasonID);

                        }

                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = array( $this->db->getValue("leagueName"),
                                                       $this->db->getValue("sportName"),
                                                       $this->db->getValue("seasonName"),
                                                       $this->db->getValue("leagueID"),
                                                       $this->db->getValue("sportID"),
                                                       $this->db->getValue("seasonID") );
                        $this->number = sizeof($this->list);
                }

                function hasNext()
                {
                        return ($this->index < $this->number);
                }
                function next()
                {
                        $this->leagueName = $this->list[$this->index][0];
                        $this->sportName = $this->list[$this->index][1];
                        $this->seasonName = $this->list[$this->index][2];
                        $this->leagueID = $this->list[$this->index][3];
                        $this->sportID = $this->list[$this->index][4];
                        $this->seasonID = $this->list[$this->index][5];
                        $this->index++;
                        return $this;
                }

                function getLeagueID()
                {
                        return $this->leagueID;
                }
                function getSportID()
                {
                        return $this->sportID;
                }
                function getSeasonID()
                {
                        return $this->seasonID;
                }
                function getLeagueName()
                {
                        return $this->leagueName;
                }
                function getSportName()
                {
                        return $this->sportName;
                }
                function getSeasonName()
                {
                        return $this->seasonName;
                }
                function getNumber()
                {
                        return $this->number;
                }
        }
?>
