<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        include("classTable.php");
        include("classOwnTeamsInSeason.php");

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

                $data .= sprintf("<header align=\"center\" size=\"1\">Tabellen</header>\n");
                $data .= sprintf("<text align=\"center\">Bitte die gewünschte TSV-Mannschaft auswählen.</text>\n");
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
                        $data .= "<listitem><link type=\"self\" paramkey=\"leagueID\" paramval=\"".$ownteams->getLeagueID()."\" paramkey2=\"seasonID\" paramval2=\"".$ownteams->getSeasonID()."\">".$ownteams->getOwnName()." (".$ownteams->getLeagueName().")</link></listitem>";
                }
                $data .= "</list>\n";
        }
        else
        {
                $data .= sprintf("<header align=\"center\" size=\"1\">Tabellen</header>\n");

                // calculate table
                $table = new Table($db);
                $table->create($seasonID, $leagueID);

                // $data .= "<text align=\"center\">Saison: ".$table->getSeasonName()."</text>";
                // $data .= "<text align=\"center\">Sportart: ".$table->getSportName()."</text>";
                // $data .= "<text align=\"center\">Liga: ".$table->getLeagueName()."</text>";
                // $data .= "<text align=\"center\">Stand: ".$table->getMaxDate()."</text>";

                $data .= "<header size=\"2\" align=\"center\">Saison ".$table->getSeasonName().", ".$table->getLeagueName()."</header>";

                $data .= "<header align=\"center\" size=\"2\">Stand: ".$table->getMaxDate()."</header>";

                include("modules/leaguemanager/pub/incTable.php");
        }


        xmlStop($data);



        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }
?>