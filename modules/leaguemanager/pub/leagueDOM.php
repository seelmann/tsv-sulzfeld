<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        include("classResult.php");
        include("classTable.php");
        include("classLeagueInSeason.php");
        include("classDayOfMatch.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        xmlStart($data);

        if(empty($leagueID) || empty($seasonID) || empty($sportID))
        {
                $data .= "<text align=\"center\">Bitte die Liga auswählen</text>";
                $lis = new LeagueInSeasonIterator($db);
                $lis->createIterator();
                $data .= "<list type=\".\">\n";
                $sportname = "";
                while($lis->hasNext())
                {
                        $lis->next();
                        if($sportname != $lis->getSportName())
                        {
                                $sportname = $lis->getSportName();
                                $data .= "</list>\n";
                                $data .= "<header size=\"3\" align=\"left\">".$sportname."</header>";
                                $data .= "<list type=\".\">\n";

                        }
                        $data .= "<listitem><link type=\"self\"
                                                  paramkey=\"leagueID\"
                                                  paramval=\"".$lis->getLeagueID()."\"
                                                  paramkey2=\"seasonID\"
                                                  paramval2=\"".$lis->getSeasonID()."\"
                                                  paramkey3=\"sportID\"
                                                  paramval3=\"".$lis->getSportID()."\"

                                                  >".$lis->getLeagueName()."</link></listitem>";
                }
                $data .= "</list>\n";
        }
        else if(!empty($leagueID) && !empty($seasonID) && !empty($sportID) && empty($number))
        {

                $lis = new LeagueInSeason($db);
                $lis->loadID($seasonID, $leagueID);

                $domList = new DayOfMatchList($db,$lis);
                $domList->loadList();

                $data .= "<header size=\"2\" align=\"center\">Saison ".$lis->getSeasonName().", ".$lis->getLeagueName()."</header>";

                if($lis->getHasDaysOfMatch() == "true")
                {
                        $data .= "<text align=\"center\">Bitte den Spieltag auswählen</text>";
                        $data .= "<list type=\".\">\n";
                        while($domList->hasNext())
                        {
                                $dom = $domList->next();
                                if($dom->getDate() == "")
                                {
                                        $data .= "<listitem>".$dom->getNumber().". Spieltag </listitem>";
                                }
                                else
                                {
                                        $data .= "<listitem><link type=\"self\"
                                                                  paramkey=\"leagueID\"
                                                                  paramval=\"".$lis->getLeagueID()."\"
                                                                  paramkey2=\"seasonID\"
                                                                  paramval2=\"".$lis->getSeasonID()."\"
                                                                  paramkey3=\"sportID\"
                                                                  paramval3=\"".$lis->getSportID()."\"
                                                                  paramkey4=\"number\"
                                                                  paramval4=\"".$dom->getNumber()."\"

                                                                  >".$dom->getNumber().". Spieltag ( ".$dom->getDate()." )</link></listitem>";
                                }
                        }
                        $data .= "</list>\n";
                }
                else
                {
                        $data .= "<text align=\"center\">Bitte die Spielwoche auswählen</text>";
                        $data .= "<list type=\".\">\n";
                        while($domList->hasNext())
                        {
                                $dom = $domList->next();
                                $data .= "<listitem><link type=\"self\"
                                                          paramkey=\"leagueID\"
                                                          paramval=\"".$lis->getLeagueID()."\"
                                                          paramkey2=\"seasonID\"
                                                          paramval2=\"".$lis->getSeasonID()."\"
                                                          paramkey3=\"sportID\"
                                                          paramval3=\"".$lis->getSportID()."\"
                                                          paramkey4=\"number\"
                                                          paramval4=\"".$dom->getNumber()."\"

                                                          >Spielwoche ".$dom->getDate()." </link></listitem>";
                        }
                        $data .= "</list>\n";
                }

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
                if($lis->getHasDaysOfMatch() == "true")
                {
                        $data .= "<header size=\"2\" align=\"center\">".$result->getDOMNumber()." . Spieltag (".$dom->getDate().")</header>";
                        include("modules/leaguemanager/pub/incLeagueResults.php");
                        if($dom->getIsoDate() < date("Y-m-d"))
                        {
                                $data .= "<spacer />";
                                $data .= sprintf("<header align=\"center\" size=\"2\">Tabelle (Stand: ".$table->getMaxDate().")</header>\n");
                                include("modules/leaguemanager/pub/incTable.php");
                        }
                }
                else
                {
                        $data .= "<header size=\"2\" align=\"center\">Spielwoche ".$dom->getWeek()." </header>";
                        include("modules/leaguemanager/pub/incLeagueResults.php");

                        if($result->getMaxIsoDate() < date("Y-m-d"))
                        {
                                $data .= "<spacer />";
                                $data .= sprintf("<header align=\"center\" size=\"2\">Tabelle (Stand: ".$table->getMaxDate().")</header>\n");
                                include("modules/leaguemanager/pub/incTable.php");
                        }
                }
        }

        xmlStop($data);



        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= sprintf("<header align=\"center\" size=\"1\">Spieltage</header>\n");
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }
?>