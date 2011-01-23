<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        include("classResult.php");
        include("classTable.php");
        include("classOwnTeamsInSeason.php");
        include("classLeagueInSeason.php");
        include("classDayOfMatch.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        xmlStart($data);


        if(empty($leagueID))
        {
                $ownteams = new OwnTeamsInSeason($db);
                $now = time();
                $ownteams->load();
                $data .= "<text align=\"center\">Bitte die Liga auswählen</text>";
                $data .= "<list type=\".\">\n";
                $sport = "";
                while($ownteams->nextRow())
                {
                        if($sport != $ownteams->getSportName())
                        {
                                $sport = $ownteams->getSportName();
                                $data .= "</list>\n";
                                $data .= "<header size=\"3\" align=\"left\">".$sport."</header>";
                                $data .= "<list type=\".\">\n";

                        }
                        // $data .= "<listitem><link type=\"self\" paramkey=\"leagueID\" paramval=\"".$ownteams->getLeagueID()."\" paramkey2=\"seasonID\" paramval2=\"".$ownteams->getSeasonID()."\">".$ownteams->getOwnName()." (".$ownteams->getLeagueName().")</link></listitem>";
                        $data .= "<listitem><link type=\"self\" paramkey=\"leagueID\" paramval=\"".$ownteams->getLeagueID()."\" paramkey2=\"seasonID\" paramval2=\"".$ownteams->getSeasonID()."\">".$ownteams->getLeagueName()."</link></listitem>";
                }
                $data .= "</list>\n";
        }
        else
        {
                // show results of DOM
                $lis = new LeagueInSeason($db);
                $lis->loadID($seasonID, $leagueID);
                $dom = new DayOfMatch($db);
                $dom->load($seasonID, $sportID, $leagueID, $number);

                $result = new Result($db);
                $result->createLeagueResults($seasonID, $leagueID, $number);

                $table = new Table($db);
                $table->create($seasonID, $leagueID, $number);

                $data .= "<header size=\"2\" align=\"center\">Saison ".$lis->getSeasonName().", ".$lis->getLeagueName()."</header>";
                include("modules/leaguemanager/pub/incLeagueResults.php");
                $data .= "<spacer />";
                $data .= sprintf("<header align=\"center\" size=\"2\">Tabelle (Stand: ".$table->getMaxDate().")</header>\n");
                include("modules/leaguemanager/pub/incTable.php");
        }


        xmlStop($data);



        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= sprintf("<header align=\"center\" size=\"1\">letzter Spieltag</header>\n");
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }
?>
