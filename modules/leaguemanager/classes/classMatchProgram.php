<?php
        include_once("classDateTimeFormat.php");

        class MatchProgram
        {
                var $homeTeamName = "";
                var $guestTeamName = "";
                var $date = "";
                var $time = "";
                var $result = "";
                var $sportName = "";

                var $a = array();
                var $number = 0;
                var $index = 0;

                var $db;
                var $dateTimeFormat;

                function MatchProgram($db)
                {
                        $this->db = $db;
                        $this->dateTimeFormat = new DateTimeFormat();
                }

                function create($period, $from, $to, $teams, $showdate, $showtime, $showresult, $homeaway)
                {
                        $query = "select ";

                        // which information?
                        if($showdate == "yes")
                                $query .= "date_format(mat.date, '%d.%m.%Y') as date, ";
                        if($showtime == "yes")
                                $query .= "time_format(mat.time, '%H:%i') as time, ";
                        if($showresult == "yes")
                                $query .= "mat.homeResult, mat.guestResult, ";
                                // $query .= "concat(mat.homeResult, ' : ', mat.guestResult) as result, ";
                        $query .= "home.name as home, guest.name as guest, mat.date as isodate, mat.time as isotime, sp.name as sport ";

                        $query .= "from lmMatchOfLeague as mat ";
                        $query .= "left join lmTeam as home on mat.homeTeamID=home.ID ";
                        $query .= "left join lmTeam as guest on mat.guestTeamID=guest.ID ";
                        $query .= "left join lmLeagueInSeason as lis on mat.seasonID=lis.seasonID and mat.leagueID=lis.leagueID and mat.sportID=lis.sportID ";
                        $query .= "left join lmSeason as s on now() between s.dateFrom and s.dateTo and s.ID=mat.seasonID ";
                        $query .= "left join lmSport as sp on sp.ID=mat.sportID ";

                        $query .= "where ";

                        // which teams and home and/or away?
                        $query .= " ( ";
                        for($i=0; $i<sizeof($teams); $i++)
                        {
                                if(is_numeric($teams[$i]))
                                {
                                        if($homeaway == "home")
                                                $query .= "mat.homeTeamID=".$teams[$i]." or ";
                                        else if($homeaway == "away")
                                                $query .= "mat.guestTeamID=".$teams[$i]." or ";
                                        else
                                                $query .= "mat.homeTeamID=".$teams[$i]." or mat.guestTeamID=".$teams[$i]." or ";
                                }
                        }
                        $query = substr($query, 0, -3);
                        $query .= " ) ";

                        // which period?
                        switch($period)
                        {
                                case "vr":
                                        $query .= "and ( (mat.number <= (((lis.numberOfTeams * (lis.numberOfTeams - 1)) / 2) * if(lis.isDoubleSeason='true',2,1)) and (lis.hasDaysOfMatch='false')) or ((mat.dayOfMatchNumber <= (lis.numberOfTeams - 1 + (lis.numberOfTeams % 2) )) and (lis.hasDaysOfMatch='true')) ) ";
                                        break;
                                case "rr":
                                        $query .= "and ( (mat.number > (((lis.numberOfTeams * (lis.numberOfTeams - 1)) / 2) * if(lis.isDoubleSeason='true',2,1)) and (lis.hasDaysOfMatch='false')) or ((mat.dayOfMatchNumber > (lis.numberOfTeams - 1 + (lis.numberOfTeams % 2) )) and (lis.hasDaysOfMatch='true')) ) ";
                                        break;
                                case "sa":
                                        break;
                                case "zr":
                                        $query .= "and mat.date between '".$from."' and '".$to."' ";
                                        break;
                        }

			$query .= "and mat.date between s.dateFrom and s.dateTo ";

                        $this->db->executeQuery($query);
                        while($this->db->nextRow())
                                $this->a[] = array($this->db->getValue("date"),
                                                   $this->db->getValue("time"),
                                                   $this->db->getValue("home"),
                                                   $this->db->getValue("guest"),
                                                   $this->db->getValue("homeResult"),
                                                   $this->db->getValue("guestResult"),
                                                   $this->db->getValue("sport"),
                                                   $this->db->getValue("isodate"),
                                                   $this->db->getValue("isotime") );

                        $this->number = sizeof($this->a);
                        usort($this->a, array(&$this,'cmp'));
                        return true;

                }

                function cmp ($a, $b)
                {
                                // sport
                                if ( $a[6] < $b[6] )
                                        return -1;
                                else if( $a[6] > $b[6] )
                                        return 1;
                                // date
                                else if ( $a[7] < $b[7] )
                                        return -1;
                                else if ( $a[7] > $b[7] )
                                        return 1;
                                // time
                                else if ( $a[8] < $b[8] )
                                        return -1;
                                else if ( $a[8] > $b[8] )
                                        return 1;
                                else
                                        return 0;
                }


                function nextRow()
                {
                        if($this->index < $this->number)
                        {
                                $this->date = $this->a[$this->index][0];
                                $this->time = $this->a[$this->index][1];
                                $this->homeTeamName = $this->a[$this->index][2];
                                $this->guestTeamName = $this->a[$this->index][3];
                                $this->result = $this->a[$this->index][4]=="-1"?" : ":$this->a[$this->index][4]." : ".$this->a[$this->index][5];
                                $this->sportName = $this->a[$this->index][6];
                                $this->index++;
                                return true;
                        }
                        else
                                return false;
                }

                function getDate()
                {
                        return $this->dateTimeFormat->addDay($this->date);
                }
                function getTime()
                {
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
                function getResult()
                {
                        return $this->result;
                }
                function getSportName()
                {
                        return $this->sportName;
                }

        }
?>
