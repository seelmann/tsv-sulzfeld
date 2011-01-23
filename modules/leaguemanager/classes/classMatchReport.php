<?php
        class MatchReport
        {
                // attributes
                var $head = "";
                var $text = "";

                var $number;
                var $domNumber;
                var $seasonID;
                var $sportID;
                var $leagueID;

                // static attributes
                var $db;

                function MatchReport($db)
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

                function create($head, $text)
                {
                        $this->head = $head;
                        $this->text = $text;

                        $query = sprintf(" insert
                                           into     lmMatchReport
                                           (        head,
                                                    text,
                                                    seasonID,
                                                    sportID,
                                                    leagueID,
                                                    dayOfMatchNumber,
                                                    number )
                                           values ( '%s',
                                                    '%s',
                                                    '%s',
                                                    '%s',
                                                    '%s',
                                                    '%s',
                                                    '%s' ) ",
                                                    $head,
                                                    $text,
                                                    $this->seasonID,
                                                    $this->sportID,
                                                    $this->leagueID,
                                                    $this->domNumber,
                                                    $this->number );
                        return $this->db->executeQuery($query);
                }
                function modify($head, $text)
                {
                        $this->head = $head;
                        $this->text = $text;
                        $query = sprintf(" update   lmMatchReport
                                           set      head = '%s',
                                                    text = '%s'
                                           where    seasonID = '%s'
                                           and      sportID = '%s'
                                           and      leagueID = '%s'
                                           and      dayOfMatchNumber = '%s'
                                           and      number = '%s' ",
                                                    $head,
                                                    $text,
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
                                           from     lmMatchReport
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
                        $query = sprintf("select head,
                                                 text
                                          from   lmMatchReport
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
                                $this->head = $this->db->getValue("head");
                                $this->text = $this->db->getValue("text");
                                return true;
                        }
                        else
                        {
                                return false;
                        }
                }

                function loadFromID($id)
                {
                        $query = sprintf("select head,
                                                 text
                                          from   lmMatchReport
                                          where  ID = '%s' ",
                                                 $id );
                        $this->db->executeQuery($query);
                        if($this->db->nextRow())
                        {
                                $this->head = $this->db->getValue("head");
                                $this->text = $this->db->getValue("text");
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

                function getHead()
                {
                        return $this->head;
                }
                function getText()
                {
                        return $this->text;
                }
        }
?>
