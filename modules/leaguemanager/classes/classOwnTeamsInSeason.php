<?php
        class OwnTeamsInSeason
        {
                var $list = array();
                var $index = 0;

                var $ownTeamID = 0;
                var $ownName = "";
                var $leagueName = "";
                var $sportName = "";

                var $leagueID = 0;
                var $seasonID = 0;

                var $number = 0;

                // static attributes
                var $db;


                function OwnTeamsInSeason($db)
                {
                        $this->db = $db;
                }

                function reset()
                {
                        $this->list = array();
                        $this->index = 0;

                        $this->ownTeamID = 0;
                        $this->ownName = "";
                        $this->leagueName = "";
                        $this->sportName = "";

                        $this->leagueID = 0;
                        $this->seasonID = 0;

                        $this->number = 0;
                }

                function load()
                {
                                $this->reset();
/*
                                $query = sprintf(" select    tilis.ownName,
                                                             tilis.teamID,
                                                             l.name as leagueName,
                                                             sp.name as sportName,
                                                             tilis.leagueID as leagueID,
                                                             tilis.seasonID as seasonID
                                                   from      lmTeamInLeagueInSeason tilis
                                                   left join lmSeason s on s.ID=tilis.seasonID
                                                   left join lmLeague l on l.ID=tilis.leagueID
                                                   left join lmSport sp on sp.ID=tilis.sportID
                                                   where     now() between s.dateFrom and s.dateTo and
                                                             tilis.own='true'
                                                   order by  sp.name
                                                 "
                                                );
*/
                                $query = sprintf(" select    otilis.ownName,
                                                             otilis.teamID,
                                                             l.name as leagueName,
                                                             sp.name as sportName,
                                                             otilis.leagueID as leagueID,
                                                             otilis.seasonID as seasonID
                                                   from      lmOwnTeamInLeagueInSeason otilis,
                                                             lmSeason s,
                                                             lmLeague l,
                                                             lmSport sp
                                                   where     s.ID=otilis.seasonID
                                                   and       l.ID=otilis.leagueID
                                                   and       sp.ID=otilis.sportID
                                                   and       now() between s.dateFrom and s.dateTo
                                                   order by  sp.name,
                                                             l.activeYouth,
                                                             otilis.ownName ");

                                $this->db->executeQuery($query);
                                while($this->db->nextRow())
                                        $this->list[] = array( $this->db->getValue("teamID"), $this->db->getValue("ownName"), $this->db->getValue("leagueName"), $this->db->getValue("sportName"), $this->db->getValue("leagueID"), $this->db->getValue("seasonID") );
                                $this->number = sizeof($this->list);
                }

                function nextRow()
                {
                        if($this->index < $this->number)
                        {
                                $this->ownTeamID = $this->list[$this->index][0];
                                $this->ownName = $this->list[$this->index][1];
                                $this->leagueName = $this->list[$this->index][2];
                                $this->sportName = $this->list[$this->index][3];
                                $this->leagueID = $this->list[$this->index][4];
                                $this->seasonID = $this->list[$this->index][5];
                                $this->index++;
                                return true;
                        }
                        else
                                return false;

                }
                function resetIndex()
                {
                        $this->index = 0;
                }

                function setDB($db)
                {
                        $this->db = $db;
                }

                function getOwnName()
                {
                        return $this->ownName;
                }
                function getOwnTeamID()
                {
                        return $this->ownTeamID;
                }
                function getLeagueName()
                {
                        return $this->leagueName;
                }
                function getSportName()
                {
                        return $this->sportName;
                }

                function getLeagueID()
                {
                        return $this->leagueID;
                }
                function getSeasonID()
                {
                        return $this->seasonID;
                }

                function getNumber()
                {
                        return $this->number;
                }
        }
?>