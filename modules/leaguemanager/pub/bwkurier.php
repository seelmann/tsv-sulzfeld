<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        xmlStart($data);

        // list directory
        $data .= "<header size=\"3\">Ausgaben:</header>";
        $data .= "<list type=\".\">\n";
        $d = dir("bwkurier");
        while($entry=$d->read())
        {
                if(!is_file($entry) && !is_link($entry) && $entry!="." && $entry!="..")
                {
                        // get the date
                        $date = date("d.m.Y",filemtime("bwkurier/$entry"));
                        $data .= "<listitem><link type=\"self\" paramkey=\"ausgabe\" paramval=\"".$entry."\">".$entry." vom ".$date."</link></listitem>";
                } //if
        } // while
        $data .= "</list>";
        $d->close();

        // list the files
        if(isset($ausgabe))
        {
                $data .= "<header size=\"3\">".$ausgabe."</header>";
                $data .= "<list type=\".\">\n";

                $d = dir("bwkurier/$ausgabe");
                while($entry=$d->read())
                {
                        if(!is_dir($entry) && $entry!="." && $entry!="..")
                        {
                                $pages[$i++] = $entry;
                        } // if
                } // while
                // sort and print the pages
                sort ($pages);
                reset ($pages);
                while (list ($key, $val) = each ($pages))
                {
                        $size = floor(round(filesize("bwkurier/$ausgabe/$val") / 1024));
                        $data .= "<listitem><link type=\"free\" href=\"./bwkurier/".$ausgabe."/".$val."\">".$val."</link></listitem>";
                        // $data .= "<listitem><link type=\"freepopup\" href=\"./bwkurier/".$ausgabe."/".$val."\">".$val."</link></listitem>";
                        // printf("<li><a href=\"../bwkurier/%s/%s\" target=\"bwkurier\" onClick=\"{win=open('bwkurier.php3','bwkurier','width=723,height=550,resizable=yes'); return true;}\" >%s</a> &nbsp;&nbsp;&nbsp; (%s kB)</li>\n",$ausgabe,$val,$val,$size);
                }

                print("</ul>\n");
        } // if
        $d->close();


        xmlStop($data);



        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= "<header align=\"center\" size=\"1\">Blau- &amp; Weiss- Kurier</header>\n";
                $data .= "<text>Wollen Sie unsere Stadionzeitung schon einen Tag vor dem offiziellen Erscheinungstermin, also bereits am Vortag des Heimspiels der ersten Mannschaft, bequem auf Ihrem PC lesen? Oder möchten Sie in älteren Archiv-Ausgaben schmökern?</text>\n";
                $data .= "<text align=\"flowing\">Dann sind Sie hier genau richtig. Sie benötigen nur den </text><link align=\"flowing\" type=\"free\" href=\"http://www.adobe.de/products/acrobat/readstep.html\">Adobe Acrobat Reader</link><text align=\"flowing\">, um sich Seite für Seite unseres Heftes in Ruhe anzusehen.</text>\n";
		$data .= "<text> </text>";
                $data .= "<text align=\"flowing\">Das Redaktionsteam freut sich über jeden Verbesserungsvorschlag. So haben Sie selbst die Möglichkeit, das Erscheinungsbild des Kuriers zu beeinflussen. Die E-Mail-Adresse lautet: </text><link align=\"flowing\" type=\"email\" email=\"BuW-Kurier@gmx.de\">BuW-Kurier@gmx.de</link>\n";
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }

?>
