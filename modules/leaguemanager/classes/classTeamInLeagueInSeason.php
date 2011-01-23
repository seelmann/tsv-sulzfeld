<?php
/*
        class TeamInLeagueInSeason
        {
                var $id = 0;
                var $name = "";
                var $isInLeagueInSeason = false;
                var $isOwn = false;
                var $ownName = "";

                // static attributes
                var $db;
                var $validate;

                var $lis;
                var $season;
                var $sport;
                var $league;

                function TeamInLeagueInSeason($db, $lis)
                {
                        $this->db = $db;
                        $this->validate = new Validation();

                        $this->lis = $lis;
                        $this->lis->setDB($db);

                        $this->season = $lis->getSeason();
                        $this->sport = $lis->getSport();
                        $this->league = $lis->getLeague();
                }

                function reset()
                {
                        $this->id = 0;
                        $this->name = "";
                        $this->isInLeagueInSeason = false;
                        $this->isOwn = false;
                        $this->ownName = "";
                }

                function load($id)
                {
                        $query = sprintf(" select    t.ID,
                                                     t.name,
                                                     tilis.teamID,
                                                     otilis.ownName
                                           from      lmTeam t
                                           left join lmTeamInLeagueInSeason tilis
                                           on        t.ID = tilis.teamID
                                           and       tilis.seasonID = '%s'
                                           and       tilis.sportID = '%s'
                                           and       tilis.leagueID = '%s'
                                           left join lmOwnTeamInLeagueInSeason otilis
                                           on        t.ID = otilis.teamID
                                           and       otilis.seasonID = '%s'
                                           and       otilis.sportID = '%s'
                                           and       otilis.leagueID = '%s'
                                           where     t.ID = '%s'
                                          ", $this->season->getID(),
                                             $this->sport->getID(),
                                             $this->league->getID(),
                                             $this->season->getID(),
                                             $this->sport->getID(),
                                             $this->league->getID(),
                                             $id );
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->id = $this->db->getValue("ID");
                                $this->name = $this->db->getValue("name");

                                if($this->db->getValue("teamID") == $this->db->getValue("ID"))
                                        $this->isInLeagueInSeason = true;
                                else
                                        $this->isInLeagueInSeason = false;

                                if($this->db->getValue("ownName") == "")
                                {
                                        $this->isOwn = false;
                                        $this->ownName = "";
                                }
                                else
                                {
                                        $this->isOwn = true;
                                        $this->ownName = $this->db->getValue("ownName");
                                }

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
                function getIsOwn()
                {
                        return $this->isOwn;
                }
                function getOwnName()
                {
                        return $this->ownName;
                }
                function getIsInLeagueInSeason()
                {
                        return $this->isInLeagueInSeason;
                }
        }

        class TeamInLeagueInSeasonIterator
        {
                var $list = array();
                var $db;

                var $lis;
                var $season;
                var $sport;
                var $league;

                var $number = 0;

                function TeamInLeagueInSeasonIterator($db)
                {
                        $this->db = $db;
                }

                function createEditIterator($lis)
                {
                        $this->lis = $lis;
                        $this->lis->setDB($this->db);

                        $this->season = $lis->getSeason();
                        $this->sport = $lis->getSport();
                        $this->league = $lis->getLeague();

                        $this->list = array();
                        $query = sprintf("select    ID
                                          from      lmTeam
                                          where     sportID = '%s'
                                          and       activeYouth = '%s'
                                          order by  name "
                                          , $this->sport->getID(), $this->league->getActiveYouth());
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");

                        $query = sprintf("select count(teamID) as number
                                          from   lmTeamInLeagueInSeason
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        $this->number = $this->db->getValue("number");
                }

                function createIterator($lis)
                {
                        $this->lis = $lis;
                        $this->lis->setDB($this->db);

                        $this->season = $lis->getSeason();
                        $this->sport = $lis->getSport();
                        $this->league = $lis->getLeague();

                        $this->list = array();
                        $query = sprintf("select    ID
                                          from      lmTeam
                                          where     sportID = '%s'
                                          and       activeYouth = '%s'
                                          order by  name "
                                          , $this->sport->getID(), $this->league->getActiveYouth());
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = $this->db->getValue("ID");

                        $query = sprintf("select count(teamID) as number
                                          from   lmTeamInLeagueInSeason
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        $this->number = $this->db->getValue("number");
                }

                function modify($lis, $teams)
                {
                        $this->lis = $lis;
                        $this->lis->setDB($this->db);

                        $this->season = $lis->getSeason();
                        $this->sport = $lis->getSport();
                        $this->league = $lis->getLeague();

                        // delete all entries
                        $query = sprintf("delete
                                          from   lmTeamInLeagueInSeason
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        if(!$this->db->executeQuery($query))
                                return false;

                        // add new entries from list
                        while(list($k, $v) = each($teams))
                        {
                                $query = sprintf("insert
                                                  into   lmTeamInLeagueInSeason
                                                         ( teamID,
                                                           seasonID,
                                                           sportID,
                                                           leagueID )
                                                  values ( '%s',
                                                           '%s',
                                                           '%s',
                                                           '%s' )
                                                 ", $k, $this->season->getID(), $this->sport->getID(), $this->league->getID() );
                                $this->db->executeQuery($query);
                        }

                        return true;
                }

                function hasNext()
                {
                        return sizeof($this->list) > 0;
                }
                function next()
                {
                        $o = new TeamInLeagueInSeason($this->db, $this->lis);
                        $o->load($this->list[0]);
                        array_shift($this->list);
                        return $o;
                }
                function setDB($db)
                {
                        $this->db = $db;
                }
                function getNumber()
                {
                        return $this->number;
                }
        }
*/

        class TeamInLeagueInSeason
        {
                var $id = 0;
                var $name = "";
                var $isInLeagueInSeason = false;
                // var $isOwn = false;
                // var $ownName = "";

                function TeamInLeagueInSeason()
                {
                }

                function reset()
                {
                        $this->id = 0;
                        $this->name = "";
                        $this->isInLeagueInSeason = false;
                }

                function getID()
                {
                        return $this->id;
                }
                function getName()
                {
                        return $this->name;
                }
                function getIsInLeagueInSeason()
                {
                        return $this->isInLeagueInSeason;
                }

        }


        class TeamInLeagueInSeasonList
        {
                var $list;
                var $number;
                var $index;
                var $tilisNumber;

                var $team;
                var $lis;
                var $season;
                var $sport;
                var $league;

                // static attributes
                var $db;
                var $validate;

                function TeamInLeagueInSeasonList($db, $lis=null)
                {
                        $this->db = $db;
                        $this->team = new TeamInLeagueInSeason();

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

                        $query = sprintf("select count(teamID) as number
                                          from   lmTeamInLeagueInSeason
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        $this->tilisNumber = $this->db->getValue("number");

                        $this->reset();
                }

                function reset()
                {
                        $this->index = 0;
                }

                function loadList()
                {
                        $query = sprintf("select    t.ID,
                                                    substring(t.name,(instr(t.name,' '))) as name,
                                                    tilis.teamID
                                          from      lmTeamInLeagueInSeason tilis
                                          left join lmTeam t
                                          on        t.ID=tilis.teamID
                                          where     tilis.seasonID = '%s'
                                          and       tilis.sportID = '%s'
                                          and       tilis.leagueID = '%s'
                                          order by  substring(t.name,(instr(t.name,' '))) "
                                          , $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
/*
                        $query = sprintf("select    t.ID,
                                                    t.name,
                                                    tilis.teamID
                                          from      lmTeamInLeagueInSeason tilis
                                          left join lmTeam t
                                          on        t.ID=tilis.teamID
                                          where     tilis.seasonID = '%s'
                                          and       tilis.sportID = '%s'
                                          and       tilis.leagueID = '%s'
                                          order by  t.name "
                                          , $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
*/
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = array( $this->db->getValue("ID"),
                                                       $this->db->getValue("name"),
                                                       $this->db->getValue("teamID")==$this->db->getValue("ID")?true:false );
                        $this->number = sizeof($this->list);  
                }

                function loadEditList()
                {
                        $query = sprintf("select    t.ID,
                                                    t.name,
                                                    tilis.teamID
                                          from      lmTeam t
                                          left join lmTeamInLeagueInSeason tilis
                                          on        t.ID=tilis.teamID
                                          and       tilis.seasonID = '%s'
                                          and       tilis.sportID = '%s'
                                          and       tilis.leagueID = '%s'
                                          where     t.sportID = '%s'
                                          and       t.activeYouth = '%s'
                                          order by  substring(t.name,(instr(t.name,' '))) "
                                          , $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID(),
                                            $this->sport->getID(),
                                            $this->league->getActiveYouth());

/*                        
			$query = sprintf("select    t.ID,
                                                    t.name,
                                                    tilis.teamID
                                          from      lmTeam t
                                          left join lmTeamInLeagueInSeason tilis
                                          on        t.ID=tilis.teamID
                                          and       tilis.seasonID = '%s'
                                          and       tilis.sportID = '%s'
                                          and       tilis.leagueID = '%s'
                                          where     t.sportID = '%s'
                                          and       t.activeYouth = '%s'
                                          order by  t.name "
                                          , $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID(),
                                            $this->sport->getID(),
                                            $this->league->getActiveYouth());
*/
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->list[] = array( $this->db->getValue("ID"),
                                                       $this->db->getValue("name"),
                                                       $this->db->getValue("teamID")==$this->db->getValue("ID")?true:false );
                        $this->number = sizeof($this->list);
                }

                function modifyList($teams)
                {
                        // delete all entries
                        $query = sprintf("delete
                                          from   lmTeamInLeagueInSeason
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                         ", $this->season->getID(),
                                            $this->sport->getID(),
                                            $this->league->getID() );
                        if(!$this->db->executeQuery($query))
                                return false;

                        // add new entries from list
                        if(is_array($teams))
                        {
                                while(list($k, $v) = each($teams))
                                {
                                        $query = sprintf("insert
                                                          into   lmTeamInLeagueInSeason
                                                                 ( teamID,
                                                                   seasonID,
                                                                   sportID,
                                                                   leagueID )
                                                          values ( '%s',
                                                                   '%s',
                                                                   '%s',
                                                                   '%s' )
                                                         ", $k, $this->season->getID(), $this->sport->getID(), $this->league->getID() );
                                        $this->db->executeQuery($query);
                                }
                        }

                        return true;
                }

                function hasNext()
                {
                        return ($this->index < $this->number);
                }
                function next()
                {
                        $this->team->reset();
                        $this->team->id = $this->list[$this->index][0];
                        $this->team->name = $this->list[$this->index][1];
                        $this->team->isInLeagueInSeason = $this->list[$this->index][2];
                        $this->index++;
                        return $this->team;
                }

                function setDB($db)
                {
                        $this->db = $db;
                }

                function isInList($id)
                {
                        for($i=0; $i<sizeof($this->list); $i++)
                        {
                                if($this->list[$i][0] == $id)
                                        return true;
                        }
                        return false;
                }

                function getNumber()
                {
                        return $this->number;
                }
                function getNumberOfTeamsInLeagueInSeason()
                {
                        return $this->tilisNumber;
                }

        }

?>
