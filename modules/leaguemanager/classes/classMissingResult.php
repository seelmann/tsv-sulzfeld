<?php

        class MissingResult
        {

                var $seasonName;
                var $seasonID;
                var $sportName;
                var $sportID;
                var $leagueName;
                var $leagueID;
                var $domNumber;
                var $matchNumber;
                var $date;
                var $time;
                var $homeID;
                var $guestID;
                var $homeTeamName;
                var $guestTeamName;
                var $homeResult;
                var $guestResult;
                var $isCanceled;

                var $db;

                function MissingResult($db)
                {
                        $this->db = $db;
                }

                function reset()
                {
                        $this->seasonName = "";
                        $this->seasonID = 0;
                        $this->sportName = "";
                        $this->sportID = 0;
                        $this->leagueName = "";
                        $this->leagueID = 0;
                        $this->domNumber = 0;
                        $this->matchNumber = 0;
                        $this->date = "0000-00-00";
                        $this->time = "00:00:00";
                        $this->homeID = 0;
                        $this->guestID = 0;
                        $this->homeTeamName = "";
                        $this->guestTeamName = "";
                        $this->homeResult = "";
                        $this->guestResult = "";
                        $this->isCanceled = false;
                }

                function getSeasonID()
                {
                        return $this->seasonID;
                }
                function getSeasonName()
                {
                        return $this->seasonName;
                }
                function getSportID()
                {
                        return $this->sportID;
                }
                function getSportName()
                {
                        return $this->sportName;
                }
                function getLeagueID()
                {
                        return $this->leagueID;
                }
                function getLeagueName()
                {
                        return $this->leagueName;
                }
                function getDOMNumber()
                {
                        return $this->domNumber;
                }
                function getMatchNumber()
                {
                        return $this->matchNumber;
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
                function getIsCanceled()
                {
                        return $this->isCanceled;
                }
        }

        class MissingResultList
        {
                var $list;
                var $number;
                var $index;

                var $match;

                // static attributes
                var $db;
                var $validate;


                function MissingResultList($db)
                {
                        $this->db = $db;
                        $this->match = new MissingResult($db);
                        $this->reset();
                }

                function reset()
                {
                        $this->index = 0;
                }


                function modifyResults( $seasonid,
                                        $sportid,
                                        $leagueid,
                                        $domnumber,
                                        $matchnumber,
                                        $homeresults,
                                        $guestresults,
                                        $canceled )
                {
                        if(is_array($homeresults) && is_array($guestresults))
                        {
                                foreach($homeresults as $key => $val)
                                {
echo $key." -> ";
                                        if( is_numeric($seasonid[$key]) and
                                            is_numeric($sportid[$key]) and
                                            is_numeric($leagueid[$key]) and
                                            is_numeric($domnumber[$key]) and
                                            is_numeric($matchnumber[$key]) )
                                        {
echo "ident -> ";
                                                // prüfen ob dieser Datensatz vorhanden ist und
                                                // noch kein Ergebnis eingetragen ist.
                                                $query = sprintf(" select *
                                                                   from   lmMatchOfLeague
                                                                   where  seasonID=%s
                                                                   and    sportID=%s
                                                                   and    leagueID=%s
                                                                   and    dayOfMatchNumber=%s
                                                                   and    number=%s
                                                                   and    homeResult=-1
                                                                   and    guestResult=-1 ",
                                                                   $seasonid[$key],
                                                                   $sportid[$key],
                                                                   $leagueid[$key],
                                                                   $domnumber[$key],
                                                                   $matchnumber[$key] );
                                                $this->db->executeQuery($query);

                                                if($this->db->getNumRows() == 1)
                                                {
echo "vorhanden -> ";
                                                        // prüfen ob Ergebnisse numberisch
                                                        if( is_numeric($homeresults[$key]) and
                                                            is_numeric($guestresults[$key]) )
                                                        {
echo "Ergebnis OK ";
                                                                $query = sprintf(" update lmMatchOfLeague
                                                                                   set    homeResult = '%s',
                                                                                          guestResult = '%s'
                                                                                   where  seasonID = '%s'
                                                                                   and    sportID = '%s'
                                                                                   and    leagueID = '%s'
                                                                                   and    dayOfMatchNumber = '%s'
                                                                                   and    number = '%s' ",
                                                                                   $homeresults[$key],
                                                                                   $guestresults[$key],
                                                                                   $seasonid[$key],
                                                                                   $sportid[$key],
                                                                                   $leagueid[$key],
                                                                                   $domnumber[$key],
                                                                                   $matchnumber[$key] );

                                                                $this->db->executeQuery($query);
                                                        }
                                                        if( $canceled[$key] == "true")
                                                        {
echo "ausgef. ";
                                                                $query = sprintf(" select *
                                                                                   from   lmMatchPostpone
                                                                                   where  seasonID = '%s'
                                                                                   and    sportID = '%s'
                                                                                   and    leagueID = '%s'
                                                                                   and    dayOfMatchNumber = '%s'
                                                                                   and    number = '%s' ",
                                                                                   $seasonid[$key],
                                                                                   $sportid[$key],
                                                                                   $leagueid[$key],
                                                                                   $domnumber[$key],
                                                                                   $matchnumber[$key] );
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
                                                                                                    $seasonid[$key],
                                                                                                    $sportid[$key],
                                                                                                    $leagueid[$key],
                                                                                                    $domnumber[$key],
                                                                                                    $matchnumber[$key] );
                                                                         $this->db->executeQuery($query);
                                                                }
                                                        }
                                                        if( !isset($canceled[$key]) )
                                                        {
echo "nicht ausgef. ";
                                                                $query = sprintf(" select *
                                                                                   from   lmMatchPostpone
                                                                                   where  seasonID = '%s'
                                                                                   and    sportID = '%s'
                                                                                   and    leagueID = '%s'
                                                                                   and    dayOfMatchNumber = '%s'
                                                                                   and    number = '%s' ",
                                                                                   $seasonid[$key],
                                                                                   $sportid[$key],
                                                                                   $leagueid[$key],
                                                                                   $domnumber[$key],
                                                                                   $matchnumber[$key] );
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
                                                                                          $seasonid[$key],
                                                                                          $sportid[$key],
                                                                                          $leagueid[$key],
                                                                                          $domnumber[$key],
                                                                                          $matchnumber[$key] );
                                                                         $this->db->executeQuery($query);
                                                                }
                                                        }



                                                }
                                                else
                                                {
                                                        // Datensatz nicht vorhanden oder
                                                        // Ergebnis ist schon eingetragen
                                                }


                                        }
                                        else
                                        {
                                                // Fehler in Spielidentifizerung
                                        }
echo "<br>\n";
                                 }

                                 return true;
                        }
                        else
                                return false;
                }


                function loadEditList($type)
                {

			if($type == all) {
			  $where = "";
			}
			else if($type == "fbakt") {
			  $where = "and mol.sportId=1";
			}
			else if($type == "ttakt") {
			  $where = "and mol.sportId=2 and (home.activeYouth='active' or guest.activeYouth='active')";
			}
			else if($type == "ttjug") {
			  $where = "and mol.sportId=2 and (home.activeYouth='youth' or guest.activeYouth='youth')";
			}


                                $query = sprintf("select    mol.number as matchNumber
                                                  ,         mol.dayOfMatchNumber as domNumber
                                                  ,         mol.date as isodate
                                                  ,         mol.time as isotime
                                                  ,         date_format(mol.date, '%%d.%%m.%%Y') as date
                                                  ,         time_format(mol.time, '%%H:%%i') as time
                                                  ,         mol.homeTeamID
                                                  ,         mol.guestTeamID
                                                  ,         mol.homeResult
                                                  ,         mol.guestResult
                                                  ,         home.name as homeTeamName
                                                  ,         guest.name as guestTeamName
                                                  ,         season.name as seasonName
                                                  ,         season.ID as seasonID
                                                  ,         sport.name as sportName
                                                  ,         sport.ID as sportID
                                                  ,         league.name as leagueName
                                                  ,         league.ID as leagueID
                                                  ,         pp.ID as ppID

                                                  from       lmMatchOfLeague mol
                                                  inner join lmTeam home
                                                  on         home.ID = mol.homeTeamID
                                                  inner join lmTeam guest
                                                  on         guest.ID = mol.guestTeamID
                                                  inner join lmSeason season
                                                  on         season.ID = mol.seasonID
                                                  inner join lmSport sport
                                                  on         sport.ID = mol.sportID
                                                  inner join lmLeague league
                                                  on         league.ID = mol.leagueID
                                                  left join  lmMatchPostpone pp
                                                  on         pp.seasonID = mol.seasonID
                                                  and        pp.sportID = mol.sportID
                                                  and        pp.leagueID = mol.leagueID
                                                  and        pp.dayOfMatchNumber = mol.dayOfMatchNumber
                                                  and        pp.number = mol.number

                                                  where     mol.homeResult = -1
                                                  and       mol.date <= now()
						  %s

                                                  order by  isodate,
                                                            isotime ", $where );

                                $this->db->executeQuery($query);

                                $this->list = array();
                                while($this->db->nextRow())
                                {
                                      $this->list[] = array(  $this->db->getValue("matchNumber"),
                                                              $this->db->getValue("domNumber"),
                                                              $this->db->getValue("date"),
                                                              $this->db->getValue("time"),
                                                              $this->db->getValue("homeTeamID"),
                                                              $this->db->getValue("guestTeamID"),
                                                              $this->db->getValue("homeResult")=="-1"?"":$this->db->getValue("homeResult"),
                                                              $this->db->getValue("guestResult")=="-1"?"":$this->db->getValue("guestResult"),
                                                              $this->db->getValue("homeTeamName"),
                                                              $this->db->getValue("guestTeamName"),
                                                              $this->db->getValue("seasonName"),
                                                              $this->db->getValue("seasonID"),
                                                              $this->db->getValue("sportName"),
                                                              $this->db->getValue("sportID"),
                                                              $this->db->getValue("leagueName"),
                                                              $this->db->getValue("leagueID"),
                                                              $this->db->getValue("ppID")==""?false:true
                                                            );

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
                        $this->match->matchNumber = $this->list[$this->index][0];
                        $this->match->domNumber = $this->list[$this->index][1];
                        $this->match->date = $this->list[$this->index][2];
                        $this->match->time = $this->list[$this->index][3];
                        $this->match->homeID = $this->list[$this->index][4];
                        $this->match->guestID = $this->list[$this->index][5];
                        $this->match->homeResult = $this->list[$this->index][6];
                        $this->match->guestResult = $this->list[$this->index][7];
                        $this->match->homeTeamName = $this->list[$this->index][8];
                        $this->match->guestTeamName = $this->list[$this->index][9];
                        $this->match->seasonName = $this->list[$this->index][10];
                        $this->match->seasonID = $this->list[$this->index][11];
                        $this->match->sportName = $this->list[$this->index][12];
                        $this->match->sportID = $this->list[$this->index][13];
                        $this->match->leagueName = $this->list[$this->index][14];
                        $this->match->leagueID = $this->list[$this->index][15];
                        $this->match->isCanceled = $this->list[$this->index][16];
                        $this->index++;
                        return $this->match;
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

?>
