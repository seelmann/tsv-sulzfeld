<?php
        class MatchPostpone
        {
                // attributes
                var $reason = "";
                var $newdate = "0000-00-00";
                var $newtime = "00:00:00";
                var $newisodate = "0000-00-00";

                var $olddate = "0000-00-00";
                var $oldtime = "00:00:00";

                var $number;
                var $domNumber;
                var $seasonID;
                var $sportID;
                var $leagueID;

                // static attributes
                var $db;

                function MatchPostpone($db)
                {
                        $this->db = $db;
                }

                function init($lis, $dom)
                {
                        $lis->setDB($this->db);

                        $s = $lis->getSeason();
                        $this->seasonID = $s->getID();

                        $s = $lis->getSport();
                        $this->sportID = $s->getID();

                        $s = $lis->getLeague();
                        $this->leagueID = $s->getID();

                        if($lis->getHasDaysOfMatch() == "true")
                                $this->domNumber = $dom->getNumber();
                        else
                                $this->domNumber = 0;
                }

                function create($reason, $newdate, $newtime)
                {
                        $this->reason = $reason;
                        $this->newdate = $newdate;
                        $this->newtime = $newtime;

                        $query = sprintf(" insert
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
                                                    $reason,
                                                    empty($newdate)?"NULL":"'".$newdate."'",
                                                    empty($newtime)?"NULL":"'".$newtime."'",
                                                    $this->seasonID,
                                                    $this->sportID,
                                                    $this->leagueID,
                                                    $this->domNumber,
                                                    $this->number );
                        return $this->db->executeQuery($query);
                }
                function modify($reason, $newdate, $newtime)
                {
                        $this->reason = $reason;
                        $this->newdate = $newdate;
                        $this->newtime = $newtime;

                        $query = sprintf(" update   lmMatchPostpone
                                           set      reason = '%s',
                                                    newdate = %s,
                                                    newtime = %s
                                           where    seasonID = '%s'
                                           and      sportID = '%s'
                                           and      leagueID = '%s'
                                           and      dayOfMatchNumber = '%s'
                                           and      number = '%s' ",
                                                    $reason,
                                                    empty($newdate)?"NULL":"'".$newdate."'",
                                                    empty($newtime)?"NULL":"'".$newtime."'",
                                                    $this->seasonID,
                                                    $this->sportID,
                                                    $this->leagueID,
                                                    $this->domNumber,
                                                    $this->number );
                        return $this->db->executeQuery($query);
                }
                function delete()
                {
                        $query = sprintf(" delete
                                           from     lmMatchPostpone
                                           where    seasonID = '%s'
                                           and      sportID = '%s'
                                           and      leagueID = '%s'
                                           and      dayOfMatchNumber = '%s'
                                           and      number = '%s' ",
                                                    $this->seasonID,
                                                    $this->sportID,
                                                    $this->leagueID,
                                                    $this->domNumber,
                                                    $this->number );
                        return $this->db->executeQuery($query);
                }

                function load($number)
                {
                        $this->number = $number;
                        $query = sprintf("select reason,
                                                 newdate,
                                                 newtime
                                          from   lmMatchPostpone
                                          where  seasonID = '%s'
                                          and    sportID = '%s'
                                          and    leagueID = '%s'
                                          and    dayOfMatchNumber = '%s'
                                          and    number = '%s' ",
                                                 $this->seasonID,
                                                 $this->sportID,
                                                 $this->leagueID,
                                                 $this->domNumber,
                                                 $number );
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->reason = $this->db->getValue("reason");
                                $this->newdate = $this->db->getValue("newdate");
                                $this->newtime = $this->db->getValue("newtime");
                                return true;
                        }
                        else
                        {
                                return false;
                        }
                }

                function loadFromID($id)
                {
                        $query = sprintf("select    postpone.reason,
                                                    date_format(postpone.newdate, '%%d.%%m.%%Y') as newdate,
                                                    time_format(postpone.newtime, '%%H:%%i') as newtime,
                                                    date_format(mat.date, '%%d.%%m.%%Y') as olddate,
                                                    time_format(mat.time, '%%H:%%i') as oldtime,
                                                    postpone.newdate as newisodate

                                          from      lmMatchPostpone as postpone
                                          left join lmMatchOfLeague as mat
                                          using     (seasonID, sportID, leagueID, dayOfMatchNumber, number)

                                          where     postpone.ID = '%s' ",
                                                    $id );

                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->reason = $this->db->getValue("reason");
                                $this->newdate = $this->db->getValue("newdate");
                                $this->newtime = $this->db->getValue("newtime");
                                $this->olddate = $this->db->getValue("olddate");
                                $this->oldtime = $this->db->getValue("oldtime");
                                $this->newisodate = $this->db->getValue("newisodate");
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

                function getReason()
                {
                        return $this->reason;
                }
                function getNewdate()
                {
                        return $this->newdate;
                }
                function getNewisodate()
                {
                        return $this->newisodate;
                }
                function getNewtime()
                {
                        return $this->newtime;
                }
                function getOlddate()
                {
                        return $this->olddate;
                }
                function getOldtime()
                {
                        return $this->oldtime;
                }
        }
?>
