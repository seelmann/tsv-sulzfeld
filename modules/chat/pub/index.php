<?php
        include_once("classError.php");
        include_once("classDBmysql.php");
        include_once("classDateTimeFormat.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);


        // Logout Prüfen
        $query = "SELECT nickname, (now()-lastaccess) as diff FROM moduleChatUserOnline WHERE endtime=0 AND lastaccess<now()-30";
        $db->executeQuery($query);
        while($db->nextRow()) {
          $db2 = new DBmysql($error);
          $query = sprintf("UPDATE moduleChatUserOnline SET endtime=lastaccess WHERE endtime=0 AND nickname='%s'", addslashes($db->getValue("nickname")));
          $db2->executeQuery($query);
          $query = sprintf("INSERT INTO moduleChat values ('', now(), '', '%s hat den Chat verlassen', '#000080')", addslashes($db->getValue("nickname")));
          $db2->executeQuery($query);
        }

        xmlStart($data);

        if( isset($from) && isset($to) ) {
            xmlArchive($db, $data, $from, $to);

        }
        else {
            xmlLoginform($data);
            xmlWhoIsOnline($db, $data);
            xmlArchiveList($db, $data);

        }

        xmlStop($data);





        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= "<header align=\"center\" size=\"1\">TSV-Chat</header>\n";
        }

        function xmlStop(&$data)
        {
                $data .= "</page>\n";
        }

        function xmlLoginform(&$data)
        {
                $data .= "<text align=\"justify\">\n";
                $data .= "Eine Neuerung auf der TSV-Homepage: Der TSV-Chat.\n\n";
                $data .= "Der Chat sollte mit Mozilla, Netscape ab Version 6, Internet Explorer ab Version 5 und Konqueror ab Version 3 funktionieren. ";
                $data .= "Zur Nutzung des TSV-Chats muss JavaScript aktiviert sein, da ansonsten die Netz- und Webserverbelastung zu stark wäre. ";
                $data .= "Weiterhin ist die Anzahl gleichzeitiger ChatterInnen erstmal auf 10 begrenzt.\n\n";
                $data .= "Chat-Sessions, mit 3 oder mehr Chattern, werden archiviert und können jederzeit nachgelesen werden.\n\n";
                $data .= "Bitte verhaltet Euch einigermaßen gesittet und unterlasst Beleidigungen und Kommentare, die gegen geltendes Recht verstoßen.\n\n";
                $data .= "Sollten bei der Nutzung technische Probleme oder Fehler auftreten, bitte über das Kontaktformular eine Mail mit Fehlerbeschreibung an Stefan Seelmann schicken.\n\n";
                $data .= "Jetzt aber viel Spaß mit dem neuen TSV-Chat.\n\n";
                $data .= "</text>\n\n";

                $data .= "<table align=\"center\">\n";
                $data .= "<row><cell></cell></row>\n";
                $data .= "<row><cell><link align=\"center\" type=\"freepopup\" width=\"700\" height=\"500\" href=\"/modules/chat/pub/chatstart.php\">C h a t &#160;&#160;&#160; s t a r t e n</link></cell></row>\n";
                $data .= "<row><cell></cell></row>\n";
                $data .= "</table>\n\n";

        }

        function xmlWhoIsOnline(&$db, &$data)
        {
                $data .= "<header align=\"left\" size=\"3\">Wer ist gerade im Chat?</header>\n";
                $data .= "<list type=\".\">";

                $query = "SELECT nickname FROM moduleChatUserOnline WHERE endtime=0";
                $db->executeQuery($query);

                if($db->getNumRows() == 0 ) {
                    $data .= "<listitem>zur Zeit niemand</listitem>";
                }

                while($db->nextRow()) {
                    $data .= "<listitem>" . $db->getValue("nickname") . "</listitem>";
                }
                $data .= "</list>";
        }

        function xmlArchiveList(&$db, &$data)
        {
                $fromArray = array();
                $toArray = array();
                $countArray = array();
                $index = 0;

                $query = "SELECT   unix_timestamp(starttime) as starttimestamp
                                 , unix_timestamp(endtime) as endtimestamp
                                 , nickname
                          FROM     moduleChatUserOnline
                          ORDER BY starttime, endtime";
                $db->executeQuery($query);
                $fromArray[$index] = 0;
                $toArray[$index] = 0;
                $countArray[$index] = 0;
                while($db->nextRow()) {
                    if( ($fromArray[$index] < $db->getValue("starttimestamp")) && ($db->getValue("starttimestamp") < $toArray[$index]) ) { // innerhalb des Zeitraums
                        if( $toArray[$index] < $db->getValue("endtimestamp") ) {
                            $toArray[$index] = $db->getValue("endtimestamp");
                        }
                        $countArray[$index]++;
                    }
                    else { // außerhalb des Zeitraums, neuen Anlegen
                        $index++;
                        $fromArray[$index] = $db->getValue("starttimestamp");
                        $toArray[$index] = $db->getValue("endtimestamp");
                        $countArray[$index] = 1;
                    }
                }


                $data .= "<header align=\"left\" size=\"3\">Chatarchiv</header>\n";
                $data .= "<table align=\"center\">\n";
                $data .= "<row>\n";
                $data .= "<cellh>Zeitraum</cellh>\n";
                $data .= "<cellh>Anzahl Chatter</cellh>\n";
                $data .= "<cellh> </cellh>\n";
                $data .= "</row>\n";

                for($i=0; $i<=$index; $i++) {
                    $formatter = new DateTimeFormat();

                    if( $countArray[$i] > 2 ) {
                        $fromdate = $formatter->addDay(date("d.m.Y", $fromArray[$i]));
                        $fromtime = date("H:i", $fromArray[$i]);
                        $todate = $formatter->addDay(date("d.m.Y", $toArray[$i]));
                        $totime = date("H:i", $toArray[$i]);


                        $data .= "<row>\n";
                        $data .= "<cell>\n";
                        $data .= $fromdate." ".$fromtime." - ".$totime;
                        $data .= "</cell>\n";
                        $data .= "<cell align=\"center\">\n";
                        $data .= $countArray[$i];
                        $data .= "</cell>\n";
                        $data .= "<cell>\n";
                        $data .= "<link align=\"flowing\" type=\"self\" paramkey=\"from\" paramval=\"".$fromArray[$i]."\" paramkey2=\"to\" paramval2=\"".$toArray[$i]."\">lesen</link>";
                        $data .= "</cell>\n";
                        $data .= "</row>\n";
                    }
                }
                $data .= "</table>\n";
        } // function xmlArchiveList


        function xmlArchive(&$db, &$data, $from, $to)
        {
                $formatter = new DateTimeFormat();
                $fromdate = $formatter->addDay(date("m.d.Y", $from));
                $fromtime = date("H:i", $from);
                $todate = $formatter->addDay(date("m.d.Y", $to));
                $totime = date("H:i", $to);

                $data .= "<header align=\"center\" size=\"1\">Chatarchiv vom " . $fromdate." ".$fromtime." - ".$totime . "</header>\n";

                $data .= "<text>\n";

                if( !is_numeric($from) || !is_numeric($to) )
                    return false;

                $query = sprintf("SELECT   nickname, text, color
                                  FROM     moduleChat
                                  WHERE    unix_timestamp(timestamp) between %s and %s
                                  ORDER BY timestamp
                                 ", addslashes($from), addslashes($to) );
                $db->executeQuery($query);

                while($db->nextRow()) {
                    $data .= "<color col=\"" . $db->getValue("color") . "\">";

                    if($db->getValue("nickname") == "") {
                    }
                    else {
                        $data .= htmlentities(htmlentities($db->getValue("nickname"))) . ": " . htmlentities(htmlentities($db->getValue("text"))) . "\n";
                    }
                    $data .= "</color>";
                }
                $data .= "</text>\n";

        } // function xmlArchive
?>
