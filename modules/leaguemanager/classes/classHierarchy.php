<?php
    include("classSeason.php");
    include("classSport.php");
    include("classLeague.php");
    include("classDayOfMatch.php");


    class Hierarchy
    {
        var $error;
        var $db;

        var $season = null;
        var $sport = null;
        var $league = null;
        var $dayOfMatch = null;

        var $hierarchy = array();


        function Hierarchy($error, $db)
        {
            $this->error = $error;
            $this->db = $db;
        }

        function setSeason($o)
        {
                $this->season = $o;
        }
        function setSport($o)
        {
                $this->sport = $o;
        }
        function setLeague($o)
        {
                $this->league = $o;
        }
        function setDayOfMatch($o)
        {
                $this->dayOfMatch = $o;
        }


        function buildHierarchy()
        {
                $this->buildSeason();
        }

        function buildSeason()
        {
                $season = new Season($this->error, $this->db);
                $season->createList();
                while($season->hasNext())
                {
                        $o = $season->next();
                        $this->hierarchy[] = $O;

                        // if($this->seasonID == $season->getID())
                        if($this->season == $o)
                                buildSport();
                }
        }

        function buildSport()
        {
                $sport = new Sport($this->error, $this->db);
                $sport->createList();
                while($sport->hasNext())
                {
                        $sport->next();
                        $this->hierarchy[] = array("sport", $sport->getID(), $sport->getName());

                        // if($this->sportID == $sport->getID())
                        if($this->sport == $sport)
                                buildLeague(sport);
                }
        }

        function buildLeague($sport)
        {
                $league = new League($this->error, $this->db);
                $league->createList($sport);
                while($league->hasNext())
                {
                        $league->next();
                        $this->hierarchy[] = array("league", $league->getID(), $league->getName());

                        // if($this->leagueID == $league->getID())
                        if($this->league == $league)
                                buildDayOfMatch();
                }
        }

        function buildDayOfMatch()
        {
        }

        function getHierarchy()
        {
                return $this->hierarchy;
        }





    } // class Hierarchy

?>
