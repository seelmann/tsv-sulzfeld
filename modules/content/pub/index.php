<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        include("classMatchDate.php");
        include("classDate.php");
        include("classOwnTeamsInSeason.php");
        include("classResult.php");
        include("classNews.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        $weeks = 2;
        $limit = 8;

        xmlStart($data);

        //News
        $newsIterator = new NewsIterator($db);
        $newsIterator->createIterator($weeks, $limit);
        if($newsIterator->hasNext())
        {
                $data .= "<header size=\"3\" align=\"left\">News:</header>\n";
                $data .= "<table align=\"center\" >\n";

                while($newsIterator->hasNext())
                {
                        $news = $newsIterator->next();
                        $data .= "<row>\n";
                        $data .= "<cell><b>".$news->getHead()."</b></cell>\n";
                        $data .= "<cell align=\"right\">von ".$news->getModrealname()."</cell>\n";
                        $data .= "</row>\n";
                        $data .= "<row>\n";
                        $data .= "<cell span=\"2\">".$news->getText()."</cell>\n";
                        $data .= "</row>\n";
                        $data .= "<row toggle=\"false\"><cell>&amp;nbsp;</cell></row>\n";
                }

                $data .= "</table>\n";
        }

        // Termine
        $data .= "<header size=\"3\" align=\"left\">Termine:</header>\n";
        $data .= "<table align=\"center\">\n";
        $dateIterator = new DateIterator($db);
        $dateIterator->createIterator($weeks, $limit);

        if($dateIterator->hasNext())
        {
                $data .= "<row toggle=\"reset\">\n";
                $data .= "<cellh>Datum</cellh>\n";
                $data .= "<cellh>Veranstaltung</cellh>\n";
                $data .= "</row>\n";

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


	// TT-Spiele
        $data .= "<header size=\"3\" align=\"left\">Tischtennisspiele:</header>\n";
	$data .= "<link align=\"center\" type=\"free\" href=\"http://0910.tt-liga.de/vereine/708031/gesamtspielplan\" email=\"\" pageid=\"1\" catid=\"1\">Gesamtspielplan Tischtennis 2009/10 (Link zum BTTV)</link>\n";
		

        // Änderungen
        $tablename = "temp".time();

        $query = "create temporary table $tablename ( type varchar(10) not null,
                                                      ID int(11) not null,
                                                      title varchar(255) not null,
                                                      date date not null )";
        $db->executeQuery($query);
        // Änderungen der Seiten
        $query = "insert into $tablename select 'page', ID, title, date_format(lastmodifydate, '%Y-%m-%d') as date from contentPage";
        $db->executeQuery($query);
        // Änderungen der Kathegorien
        $query = "insert into $tablename select 'cat', ID, title, date_format(lastmodifydate, '%Y-%m-%d') as date from contentCat";
        $db->executeQuery($query);
        // Änderungen des Gästebuches
        $query = "insert into $tablename select 'page', 46, 'Gästebuch', date_format(max(date), '%Y-%m-%d') as date from moduleGuestbook";
        $db->executeQuery($query);

        // Ergebnisse, ...



        $query = sprintf("select type, ID, title, date, date_format(date, '%%d.%%m.%%Y') as germandate
                          from $tablename
                          where date >= %s
                          order by date desc
                          limit 0,$limit
                         "
                         , date("Ymd", time()-86400*7*$weeks*2)
                        );
        $db->executeQuery($query);


        if($db->getNumRows() > 0)
        {
            $data .= "<header size=\"3\" align=\"left\">geänderte Seiten:</header>\n";
            $data .= "<table align=\"center\">\n";
            $data .= "<row toggle=\"reset\">\n";
            $data .= "<cellh>Datum</cellh>\n";
            $data .= "<cellh>Seite</cellh>\n";
            $data .= "</row>\n";

            while($db->nextRow())
            {
                $type = $db->getValue("type");
                $ID = $db->getValue("ID");
                $title = $db->getValue("title");
                $time = $db->getValue("germandate");
                $db2 = new DBmysql($error);
                $menu = new Menu($error, $db2);
                $hierarchy = $menu->getHierarchyString($type, $ID);

                $data .= "<row>\n";
                $data .= "<cell>".$time."</cell>\n";
                $data .= "<cell><link align=\"left\" type=\"intern$type\" pageid=\"$ID\" catid=\"$ID\">$hierarchy</link></cell>\n";
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
                $data .= sprintf("<header align=\"center\" size=\"1\">TSV Sulzfeld 1889 e.V.</header>\n");
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }
?>
