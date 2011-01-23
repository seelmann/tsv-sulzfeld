<?php
        class Table
        {
                var $seasonID;
                var $leagueID;
                var $domNumber;

                var $leagueName;
                var $seasonName;
                var $sportName;
                var $maxDate;
                var $maxIsoDate;

                var $a;
                var $db;

                function Table($db)
                {
                        $this->db = $db;
                }

                function create($seasonID, $leagueID, $number=0)
                {
                        $this->seasonID = $seasonID;
                        $this->leagueID = $leagueID;
                        $this->domNumber = $number;

                        $this->initLeagueInSeason();
                        $this->initTeams();
                        $this->calculate();

                        $this->sort();
                }

                function initLeagueInSeason()
                {
/*
                        $query = sprintf( "     select  league.name as leagueName,
                                                        season.name as seasonName,
                                                        sport.name as sportName
                                                from    lmLeague as league,
                                                        lmSeason as season,
                                                        lmSport as sport
                                                where   league.ID='%s' and
                                                        season.ID='%s' and
                                                        sport.ID=league.sportID
                                          ",    $this->leagueID,
                                                $this->seasonID
                                        );
*/
                        $query = sprintf( "     select    lis.hasDaysOfMatch,
                                                          league.name as leagueName,
                                                          season.name as seasonName,
                                                          sport.name as sportName
                                                from      lmLeagueInSeason as lis
                                                left join lmLeague as league
                                                on        lis.leagueID=league.ID
                                                left join lmSeason as season
                                                on        lis.seasonID=season.ID
                                                left join lmSport as sport
                                                on        lis.sportID=sport.ID
                                                where     lis.leagueID='%s' and
                                                          lis.seasonID='%s'
                                          ",    $this->leagueID,
                                                $this->seasonID
                                        );
                        $this->db->executeQuery($query);
                        $this->db->nextRow();
                        $this->leagueName = $this->db->getValue("leagueName");
                        $this->seasonName = $this->db->getValue("seasonName");
                        $this->sportName = $this->db->getValue("sportName");
                        $this->hasDaysOfMatch = $this->db->getValue("hasDaysOfMatch")=="true"?true:false;
                }

                function initTeams()
                {
                        $query = sprintf( "     select    team.name as name,
                                                          otilis.ownName as ownName
                                                from      lmTeam as team,
                                                          lmTeamInLeagueInSeason as tilis
                                                left join lmOwnTeamInLeagueInSeason as otilis
                                                using    (seasonID, leagueID, teamID)
                                                where     tilis.seasonID='%s' and
                                                          tilis.leagueID='%s' and
                                                          tilis.teamID=team.ID
                                          ",    $this->seasonID,
                                                $this->leagueID
                                        );
                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                        {
                                $ownName = $this->db->getValue("ownName");
                                $own = empty($ownName)?"false":"true";
                                $this->a[$this->db->getValue("name")] = array("nom" => 0, "won" => 0, "remis" => 0, "lost" => 0, "resultPos" => 0, "resultNeg" => 0, "own" => $own );
                        }
                }

                function calculate()
                {
                        if($this->domNumber == 0)
                        {
                                $query = sprintf( "     select          mat.date as isodate,
                                                                        date_format(mat.date, '%%d.%%m.%%Y') as date,
                                                                        mat.homeTeamID,
                                                                        home.name as home,
                                                                        mat.guestTeamID,
                                                                        guest.name as guest,
                                                                        mat.homeResult as homeResult,
                                                                        mat.guestResult as guestResult
                                                        from            lmMatchOfLeague as mat,
                                                                        lmTeam as home,
                                                                        lmTeam as guest
                                                        where           mat.seasonID='%s' and
                                                                        mat.leagueID='%s' and
                                                                        mat.homeResult != '-1' and
                                                                        mat.guestResult != '-1' and
                                                                        mat.homeTeamID=home.ID and
                                                                        mat.guestTeamID=guest.ID
                                                  ",    $this->seasonID,
                                                        $this->leagueID
                                                );
                        }
                        else
                        {
                                if($this->hasDaysOfMatch)
                                {
                                        $query = sprintf( "     select          mat.date as isodate,
                                                                                date_format(mat.date, '%%d.%%m.%%Y') as date,
                                                                                mat.homeTeamID,
                                                                                home.name as home,
                                                                                mat.guestTeamID,
                                                                                guest.name as guest,
                                                                                mat.homeResult as homeResult,
                                                                                mat.guestResult as guestResult
                                                                from            lmMatchOfLeague as mat,
                                                                                lmTeam as home,
                                                                                lmTeam as guest
                                                                where           mat.seasonID='%s' and
                                                                                mat.leagueID='%s' and
                                                                                mat.homeResult != '-1' and
                                                                                mat.guestResult != '-1' and
                                                                                mat.dayOfMatchNumber <= '%s' and
                                                                                mat.homeTeamID=home.ID and
                                                                                mat.guestTeamID=guest.ID
                                                                ",    $this->seasonID,
                                                                      $this->leagueID,
                                                                      $this->domNumber
                                                        );
                                }
                                else
                                {
                                        $query = sprintf( "     select          mat.date as isodate,
                                                                                date_format(mat.date, '%%d.%%m.%%Y') as date,
                                                                                mat.homeTeamID,
                                                                                home.name as home,
                                                                                mat.guestTeamID,
                                                                                guest.name as guest,
                                                                                mat.homeResult as homeResult,
                                                                                mat.guestResult as guestResult
                                                                from            lmMatchOfLeague as mat,
                                                                                lmTeam as home,
                                                                                lmTeam as guest
                                                                where           mat.seasonID='%s' and
                                                                                mat.leagueID='%s' and
                                                                                mat.homeResult != '-1' and
                                                                                mat.guestResult != '-1' and
                                                                                mat.number <= '%s' and
                                                                                mat.homeTeamID=home.ID and
                                                                                mat.guestTeamID=guest.ID
                                                                ",    $this->seasonID,
                                                                      $this->leagueID,
                                                                      $this->domNumber
                                                        );

                                }

                        }
                        $this->db->executeQuery($query);

                        while($this->db->nextRow())
                        {
                                if($this->db->getValue("isodate") > $this->maxIsoDate)
                                {
                                        $this->maxIsoDate = $this->db->getValue("isodate");
                                        $this->maxDate = $this->db->getValue("date");
                                }

                                // increment number of matches
                                $this->a[$this->db->getValue("home")] ["nom"] ++ ;
                                $this->a[$this->db->getValue("guest")] ["nom"] ++ ;

                                // increment result
                                $this->a[$this->db->getValue("home")] ["resultPos"] += $this->db->getValue("homeResult") ;
                                $this->a[$this->db->getValue("home")] ["resultNeg"] += $this->db->getValue("guestResult") ;
                                $this->a[$this->db->getValue("guest")] ["resultPos"] += $this->db->getValue("guestResult") ;
                                $this->a[$this->db->getValue("guest")] ["resultNeg"] += $this->db->getValue("homeResult") ;

                                // increment points, won, remis, lost
                                if( $this->db->getValue("homeResult") > $this->db->getValue("guestResult") )
                                {
                                        $this->a[$this->db->getValue("home")] ["won"] ++ ;
                                        // $this->a[$this->db->getValue("home")] ["points"] += 3 ;
                                        $this->a[$this->db->getValue("guest")] ["lost"] ++;
                                }
                                else if( $this->db->getValue("homeResult") < $this->db->getValue("guestResult") )
                                {
                                        $this->a[$this->db->getValue("guest")] ["won"] ++ ;
                                        // $this->a[$this->db->getValue("guest")] ["points"] += 3 ;
                                        $this->a[$this->db->getValue("home")] ["lost"] ++;
                                }
                                else
                                {
                                        $this->a[$this->db->getValue("guest")] ["remis"] ++ ;
                                        // $this->a[$this->db->getValue("guest")] ["points"] ++ ;
                                        $this->a[$this->db->getValue("home")] ["remis"] ++;
                                        // $this->a[$this->db->getValue("home")] ["points"] ++ ;
                                }
                        }
                }

                function cmp ($a, $b)
                {
                        if($this->sportName == "Fuﬂball")
                        {
                                if ( ($a["won"]*3 + $a["remis"]) < ($b["won"]*3 + $b["remis"]) )
                                        return 1;
                                else if( ($a["won"]*3 + $a["remis"]) > ($b["won"]*3 + $b["remis"]) )
                                        return -1;
                                else
                                {
                                        if ( ($a["resultPos"]-$a["resultNeg"]) < ($b["resultPos"]-$b["resultNeg"]) )
                                                return 1;
                                        else if ( ($a["resultPos"]-$a["resultNeg"]) > ($b["resultPos"]-$b["resultNeg"]) )
                                                return -1;
                                        else
                                        {
                                                if ( $a["resultPos"] < $b["resultPos"] )
                                                        return 1;
                                                else if ( $a["resultPos"] > $b["resultPos"] )
                                                        return -1;
                                        }
                                }
                                return 0;
                        }

                        if($this->sportName == "Tischtennis")
                        {
                                // Punktedifferenz
                                if ( ($a["won"] - $a["lost"]) < ($b["won"] - $b["lost"]) )
                                        return 1;
                                else if( ($a["won"] + $a["lost"]) > ($b["won"] + $b["lost"]) )
                                        return -1;

                                // Mehr Positivpunkte
                                else if ( ($a["won"]*2 + $a["remis"]) < ($b["won"]*2 + $b["remis"]) )
                                        return 1;
                                else if( ($a["won"]*2 + $a["remis"]) > ($b["won"]*2 + $b["remis"]) )
                                        return -1;

                                // gleiche Punktedifferenz und gleiche Positivpunkte, Spiele ausz‰hlen
                                else
                                {
                                        if ( ($a["resultPos"]-$a["resultNeg"]) < ($b["resultPos"]-$b["resultNeg"]) )
                                                return 1;
                                        else if ( ($a["resultPos"]-$a["resultNeg"]) > ($b["resultPos"]-$b["resultNeg"]) )
                                                return -1;
                                        else
                                        {
                                                if ( $a["resultPos"] < $b["resultPos"] )
                                                        return 1;
                                                else if ( $a["resultPos"] > $b["resultPos"] )
                                                        return -1;
                                        }
                                }
                                return 0;
                        }




/*
                        if ( $a["points"] < $b["points"] )
                                return 1;
                        else if ( $a["points"] > $b["points"] )
                                return -1;
                        else
                        {
                                if ( ($a["resultPos"]-$a["resultNeg"]) < ($b["resultPos"]-$b["resultNeg"]) )
                                        return 1;
                                else if ( ($a["resultPos"]-$a["resultNeg"]) > ($b["resultPos"]-$b["resultNeg"]) )
                                        return -1;
                                else
                                {
                                        if ( $a["resultPos"] < $b["resultPos"] )
                                                return 1;
                                        else if ( $a["resultPos"] > $b["resultPos"] )
                                                return -1;
                                }
                        }
                        return 0;
*/
                }


                function sort()
                {
                        uasort($this->a, array(&$this,'cmp'));
                }

                function nextRow()
                {
                        if (list ($key, $value) = each ($this->a))
                        {
                                $this->teamName = $key;
                                $this->numberOfMatches = $value["nom"];
                                $this->won = $value["won"];
                                $this->remis = $value["remis"];
                                $this->lost = $value["lost"];
                                $this->diff = $value["resultPos"]." : ".$value["resultNeg"];
                                $this->diffPos = $value["resultPos"];
                                $this->diffNeg = $value["resultNeg"];
                                $this->own = $value["own"]=="true"?true:false;

                                if($this->sportName == "Fuﬂball")
                                {
                                        $this->points = $value["won"]*3 + $value["remis"];
                                }
                                if($this->sportName == "Tischtennis")
                                {
                                        $this->points = ($value["won"]*2 + $value["remis"]) . " : " . ($value["lost"]*2 + $value["remis"]);
                                }

                                return true;
                        }
                        else
                                return false;
                }

                function getTeamName()
                {
                        return $this->teamName;
                }
                function getNumberOfMatches()
                {
                        return $this->numberOfMatches;
                }
                function getWon()
                {
                        return $this->won;
                }
                function getRemis()
                {
                        return $this->remis;
                }
                function getLost()
                {
                        return $this->lost;
                }
                function getDiff()
                {
                        return $this->diff;
                }

                function getDiffPos()
                {
                        return $this->diffPos;
                }
                function getDiffNeg()
                {
                        return $this->diffNeg;
                }

                function getPoints()
                {
                        return $this->points;
                }
                function getOwn()
                {
                        return $this->own;
                }

                function getLeagueName()
                {
                        return $this->leagueName;
                }
                function getSeasonName()
                {
                        return $this->seasonName;
                }
                function getSportName()
                {
                        return $this->sportName;
                }
                function getMaxDate()
                {
                        return $this->maxDate;
                }
        }
?>