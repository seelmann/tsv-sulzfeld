<?php
        include_once("classError.php");
        include_once("classDBmysql.php");
        include_once("classValidation.php");

        include("classOwnTeamsInSeason.php");
        include("classMatchProgram.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        xmlStart($data);

        if(isset($go) && ($go==1))
        {
                if(get_magic_quotes_gpc())
                {
                        $from = stripslashes($from);
                        $to = stripslashes($to);
                }

                $from = strip_tags($from);
                $to = strip_tags($to);

                $fromF = htmlentities(htmlentities($from));
                $toF = htmlentities(htmlentities($to));

                // validation
                $validate = new Validation();

                if(empty($period))
                {
                        $data .= "<text align=\"center\">Bitte Zeitraum auswählen.</text>";
                        $errors++;
                }
                else
                {
                        if($period == "zr")
                        {
                                $from2 = $validate->transformDate($from);
                                if( !$validate->checkDate($from2) )
                                {
                                        $data .= "<text align=\"center\">Ungültiges Datum \"von\".</text>";
                                        $errors++;
                                }

                                $to2 = $validate->transformDate($to);
                                if( !$validate->checkDate($to2) )
                                {
                                        $data .= "<text align=\"center\">Ungültiges Datum \"bis\".</text>";
                                        $errors++;
                                }
                        }
                }

                if(!is_array($teams))
                {
                        $data .= "<text align=\"center\">Bitte mindestens eine Mannschaft auswählen.</text>";
                        $errors++;

                }

                if($errors == 0)
                {
                        $program = new MatchProgram($db);
                        $program->create($period, $from2, $to2, $teams, $showdate, $showtime, $showresult, $homeaway);

                        if($program->nextRow())
                        {
                                $sportName = $program->getSportName();
                                $data .= "<header size=\"3\" align=\"left\">".$program->getSportName().":</header>";

                                // start new table
                                $data .= "<table align=\"center\">\n";

                                // print header
                                $data .= "<row>\n";
                                if(!empty($showdate) && ($showdate=="yes"))
                                        $data .= "<cellh>Datum</cellh>\n";
                                if(!empty($showtime) && ($showtime=="yes"))
                                        $data .= "<cellh>Uhrzeit</cellh>\n";
                                $data .= "<cellh>Heim</cellh>\n";
                                $data .= "<cellh>Gast</cellh>\n";
                                if(!empty($showresult) && ($showresult=="yes"))
                                        $data .= "<cellh>Ergebnis</cellh>\n";
                                $data .= "</row>\n";

                                // print this row
                                $data .= "<row>\n";
                                if(!empty($showdate) && ($showdate=="yes"))
                                        $data .= "<cell>".$program->getDate()."</cell>\n";
                                if(!empty($showtime) && ($showtime=="yes"))
                                        $data .= "<cell>".$program->getTime()."</cell>\n";
                                $data .= "<cell>".$program->getHomeTeamName()."</cell>\n";
                                $data .= "<cell>".$program->getGuestTeamName()."</cell>\n";
                                if(!empty($showresult) && ($showresult=="yes"))
                                        $data .= "<cell align=\"center\">".$program->getResult()."</cell>\n";
                                $data .= "</row>\n";

                                while($program->nextRow())
                                {
                                        if($sportName != $program->getSportName())
                                        {
                                                $data .= "</table>\n";

                                                $sportName = $program->getSportName();
                                                $data .= "<header size=\"3\" align=\"left\">".$program->getSportName().":</header>";

                                                // start new table
                                                $data .= "<table align=\"center\">\n";

                                                // print header
                                                $data .= "<row>\n";
                                                if(!empty($showdate) && ($showdate=="yes"))
                                                        $data .= "<cellh>Datum</cellh>\n";
                                                if(!empty($showtime) && ($showtime=="yes"))
                                                        $data .= "<cellh>Uhrzeit</cellh>\n";
                                                $data .= "<cellh>Heim</cellh>\n";
                                                $data .= "<cellh>Gast</cellh>\n";
                                                if(!empty($showresult) && ($showresult=="yes"))
                                                        $data .= "<cellh>Ergebnis</cellh>\n";
                                                $data .= "</row>\n";
                                        }

                                        // print this row
                                        $data .= "<row>\n";
                                        if(!empty($showdate) && ($showdate=="yes"))
                                                $data .= "<cell>".$program->getDate()."</cell>\n";
                                        if(!empty($showtime) && ($showtime=="yes"))
                                                $data .= "<cell>".$program->getTime()."</cell>\n";
                                        $data .= "<cell>".$program->getHomeTeamName()."</cell>\n";
                                        $data .= "<cell>".$program->getGuestTeamName()."</cell>\n";
                                        if(!empty($showresult) && ($showresult=="yes"))
                                                $data .= "<cell align=\"center\">".$program->getResult()."</cell>\n";
                                        $data .= "</row>\n";
                                }
                                $data .= "</table>\n";
                        }
                        else
                        {
                                $data ."<text align=\"center\">Keine Daten gefunden.</text>";
                        }
                }
                else
                {
                        $data .= "<text align=\"center\">Auf dieser Seite können Sie sich die Spielpläne der TSV-Mannschaften zusammenstellen.</text>";
                        startForm($data);
                        selectPeriodOfTime($data, $period, $fromF, $toF);
                        selectTeams($data, $db);
                        selectInformations($data, $homeaway, $showdate, $showtime, $showresult);
                        stopForm($data);
                }
        }
        else
        {
                $data .= "<text align=\"center\">Auf dieser Seite können Sie sich die Spielpläne der TSV-Mannschaften zusammenstellen.</text>";
                startForm($data);
                selectPeriodOfTime($data, $period, $fromF, $toF);
                selectTeams($data, $db);
                selectInformations($data, $homeaway, $showdate, $showtime, $showresult);
                stopForm($data);
        }

        xmlStop($data);





        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= sprintf("<header align=\"center\" size=\"1\">Spielpläne</header>\n");
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }

        function startForm(&$data)
        {
                $data .= "<simpleform receiver=\"self\" buttontext=\"Spielplan anzeigen\">\n";
        }
        function stopForm(&$data)
        {
                $data .= "</simpleform>\n";
        }

        function selectPeriodOfTime(&$data, $period, $from, $to)
        {
                $data .= sprintf("<header size=\"3\">1. Zeitraum auswählen:</header>\n");

                $data .= "<table align=\"center\">";
                $data .= sprintf("<input type=\"radio\" text=\"%s\" name=\"period\" value=\"vr\" default=\"%s\"></input>\n", "Vorrunde", $period);
                $data .= sprintf("<input type=\"radio\" text=\"%s\" name=\"period\" value=\"rr\" default=\"%s\"></input>\n", "Rückrunde", $period);
                $data .= sprintf("<input type=\"radio\" text=\"%s\" name=\"period\" value=\"sa\" default=\"%s\"></input>\n", "gesamte Saison", (!isset($period))?"sa":$period);
                $data .= sprintf("<input type=\"radio\" text=\"%s\" name=\"period\" value=\"zr\" default=\"%s\">", "Zeitraum", $period);
                $data .= "<text align=\"flowing\"> vom &amp;nbsp;</text>";
                $data .= sprintf("<input type=\"flowingtextfield\" name=\"from\" value=\"%s\"></input>", $from);
                $data .= "<text align=\"flowing\"> bis zum &amp;nbsp;</text>";
                $data .= sprintf("<input type=\"flowingtextfield\" name=\"to\" value=\"%s\"></input>", $to);
                $data .= "</input>\n";

                $data .= "</table>";

        }

        function selectTeams(&$data, $db)
        {
                $data .= sprintf("<header size=\"3\">2. Mannschaften auswählen:</header>\n");

                $ownteams = new OwnTeamsInSeason($db);
                $ownteams->load();
                $ownteams->nextRow();
                $sport = $ownteams->getSportName();
                $data .= "<table align=\"center\">";
                $data .= "<row toggle=\"false\"><cell><header size=\"4\" align=\"left\">".$sport.": </header><table>";
                $data .= sprintf("<input type=\"checkbox\" name=\"%s\" value=\"%s\" text=\"%s\"></input>", "teams[]", $ownteams->getOwnTeamID(), $ownteams->getOwnName());

                while($ownteams->nextRow())
                {
                        if($sport != $ownteams->getSportName())
                        {
                                $sport = $ownteams->getSportName();
                                $data .= "</table></cell><cell>";
                                $data .= "<header size=\"4\" align=\"left\">".$sport.": </header>";
                                $data .= "<table>";
                        }
                        $data .= sprintf("<input type=\"checkbox\" name=\"%s\" value=\"%s\" text=\"%s\"></input>", "teams[]", $ownteams->getOwnTeamID(), $ownteams->getOwnName());
                }
                $data .= "</table></cell></row></table>";
        }

        function selectInformations(&$data, $homeaway, $showdate, $showtime, $showresult)
        {
                $data .= sprintf("<header size=\"3\">3. Welche Informationen sollen angezeigt werden?</header>\n");
                $data .= "<table align=\"center\"><row toggle=\"false\">";
                $data .= "<cell><table>";

                $data .= sprintf("<input type=\"radio\" name=\"homeaway\" value=\"homeaway\" text=\"%s\" default=\"%s\"></input>", "Heim- und Auswärtsspiele", (!isset($homeaway))?"homeaway":$homeaway);
                $data .= sprintf("<input type=\"radio\" name=\"homeaway\" value=\"home\" text=\"%s\" default=\"%s\"></input>", "nur Heimspiele", $homeaway);
                $data .= sprintf("<input type=\"radio\" name=\"homeaway\" value=\"away\" text=\"%s\" default=\"%s\"></input>", "nur Auswärtsspiele", $homeaway);

                $data .= "</table></cell>";
                $data .= "<cell><table>";

                $data .= sprintf("<input type=\"checkbox\" name=\"%s\" value=\"%s\" text=\"%s\" default=\"%s\"></input>", "showdate", "yes", "Datum", (!isset($showdate))?"yes":$showdate);
                $data .= sprintf("<input type=\"checkbox\" name=\"%s\" value=\"%s\" text=\"%s\" default=\"%s\"></input>", "showtime", "yes", "Uhrzeit", (!isset($showtime))?"yes":$showtime);
                $data .= sprintf("<input type=\"checkbox\" name=\"%s\" value=\"%s\" text=\"%s\" default=\"%s\"></input>", "showresult", "yes", "Ergebnis", $showresult);

                $data .= "</table></cell>";
                $data .= "</row></table>";
        }
?>
