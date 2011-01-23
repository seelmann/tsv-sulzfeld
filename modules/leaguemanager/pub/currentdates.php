<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        include("classMatchDate.php");
        include("classDate.php");
        include("classOwnTeamsInSeason.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        $weeks = 4;
        $limit = 0;

        xmlStart($data);

        // Termine
        $data .= "<header size=\"3\" align=\"left\">Termine:</header>\n";
        $data .= "<table align=\"center\">\n";
        $dateIterator = new DateIterator($db);
        $dateIterator->createIterator($weeks);

        if($dateIterator->hasNext())
        {
                $data .= "<row toggle=\"reset\">\n";
                $data .= "<cellh>Datum</cellh>\n";
                $data .= "<cellh>Veranstaltung</cellh>\n";
                $data .= "</row>\n";

                while($dateIterator->hasNext())
                {
                        $date = $dateIterator->next();
                        $data .= "<row>\n";
                        $data .= "<cell>";
                        $data .= $date->getFromDate()." ".$date->getFromTime()." ";
                        if( $date->getToDateEdit() != "" )
                            $data .= "- ".$date->getToDate()." ".$date->getToTime()." ";
                        $data .= "</cell>\n";
                        $data .= "<cell>".$date->getEvent()."</cell>\n";
                        if( ($date->getHead() != "") || ($date->getText() != "") )
                                $data .= "<cell><infopopup paramkey=\"dateID\" paramval=\"".$date->getID()."\"></infopopup></cell>\n";
                        $data .= "</row>\n";
                }
        }
        $data .= "</table>\n";

        // Spiele
        $matchdate = new MatchDate($db);
        $matchdate->createSportDates($weeks);
        while($matchdate->nextSport($weeks, $limit))
        {
                $data .= "<header size=\"3\" align=\"left\">".$matchdate->getSportName()."spiele:</header>\n";
                $data .= "<table align=\"center\">\n";
                $data .= "<row toggle=\"reset\">\n";
                $data .= "<cellh>Datum</cellh>\n";
                $data .= "<cellh>Uhrzeit</cellh>\n";
                $data .= "<cellh>Heim</cellh>\n";
                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                $data .= "<cellh>Gast</cellh>\n";
                $data .= "</row>\n";

                while($matchdate->nextDate())
                {
                        if($matchdate->getCanceled() == 'yes')
                            $data .= "<row strike=\"true\">\n";
                        else
                            $data .= "<row>\n";

                        $data .= "<cell>".$matchdate->getDate()."</cell>\n";
                        $data .= "<cell>".$matchdate->getTime()."</cell>\n";
                        $data .= "<cell>".$matchdate->getHomeTeamName()."</cell>\n";
                        $data .= "<cell> - </cell>\n";
                        $data .= "<cell>".$matchdate->getGuestTeamName()."</cell>\n";
                        if( ($matchdate->getInfoID() != 0) )
                                $data .= "<cell><infopopup paramkey=\"matchInfoID\" paramval=\"".$matchdate->getInfoID()."\"></infopopup></cell>\n";
                        if( ($matchdate->getPostponeID() != 0) && ($matchdate->getCanceled() == 'no'))
                                $data .= "<cell><infopopup paramkey=\"matchPostponeID2\" paramval=\"".$matchdate->getPostponeID()."\"></infopopup></cell>\n";
                        if( ($matchdate->getPostponeID() != 0) && ($matchdate->getCanceled() == 'yes'))
                                $data .= "<cell><infopopup paramkey=\"matchCanceled\" paramval=\"".$matchdate->getPostponeID()."\"></infopopup></cell>\n";
                        if( ($matchdate->getOthermatchInfoID() != 0) )
                                $data .= "<cell><infopopup paramkey=\"othermatchInfoID\" paramval=\"".$matchdate->getOthermatchInfoID()."\"></infopopup></cell>\n";

                        $data .= "</row>\n";
                }
        $data .= "</table>\n";
        }

        xmlStop($data);



        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                // $data = "<!DOCTYPE page [ <!ENTITY nbsp \"&amp;nbsp;\"> ]>";
                $data .= "<page>\n";
                $data .= sprintf("<header align=\"center\" size=\"1\">Termine</header>\n");
                        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }
?>
