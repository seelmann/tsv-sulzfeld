<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        if(!isset($pos))
                $pos=0;
        $count=10;

        xmlStart($data);



        if(isset($go) && ($go==1))
        {
                if ($name=="" || $comment=="")
                {
                        xmlError($data);
                        $errors++;
                }

                if ($email!=="" && !validate_email($email))
                {
                        xmlEmailError($data);
                        $errors++;
                }

                if(get_magic_quotes_gpc())
                {
                        $name = stripslashes($name);
                        $city = stripslashes($city);
                        $email = stripslashes($email);
                        $comment = stripslashes($comment);
                }

                $name = strip_tags($name);
                $city = strip_tags($city);
                $email = strip_tags($email);
                $comment = strip_tags($comment);

                $name2 = htmlentities(htmlentities($name));
                $city2 = htmlentities(htmlentities($city));
                $email2 = htmlentities(htmlentities($email));
                $comment2 = htmlentities(htmlentities($comment));

                if($errors == 0)
                {
                        $name = addslashes($name);
                        $city = addslashes($city);
                        $email = addslashes($email);
                        $comment = addslashes($comment);

                        // new entry into database
                        $date = date("Y-m-d");
                        $time = date("H:i:s");

                        $query = "INSERT INTO moduleGuestbook (date,time,name,email,city,comment)
                                                        VALUES ('$date','$time','$name','$email','$city','$comment')";
                        $db->executeQuery($query);

                        $name2 = "";
                        $city2 = "";
                        $email2 = "";
                        $comment2 = "";
                }
        }

        xmlForm($data, $name2, $city2, $email2, $comment2);

        xmlShowEntries($data, $pos, $count, $db);

        xmlStop($data);


        // $trans = get_html_translation_table (HTML_ENTITIES);
        // $trans = array_flip ($trans);
        // $data = strtr ($data, $trans);

        // echo nl2br(htmlentities($data));






        function validate_email ($address)
        {
                return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_A-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_A-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_A-z{|}~]+$',$address));
        }

        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= "<header align=\"center\" size=\"1\">Gästebuch</header>\n";
        }

        function xmlStop(&$data)
        {
                $data .= "</page>\n";
        }

        function xmlEmailError(&$data)
        {
                $data .= "<text align=\"center\" style=\"strong\">Falsches Format der E-Mail Adresse.</text>\n";
        }

        function xmlError(&$data)
        {
                $data .= "<text align=\"center\" style=\"strong\">Bitte mindestens die Felder \"Name\" und \"Beitrag\" ausfüllen.</text>\n";
        }

        function xmlForm(&$data, $name2, $city2, $email2, $comment2)
        {
                $data .= "<form receiver=\"self\" buttontext=\"eintragen\">\n";
                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"name\" value=\"%s\"></input>\n", "Name", $name2);
                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"city\" value=\"%s\"></input>\n", "Ort", $city2);
                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"email\" value=\"%s\"></input>\n", "E-Mail Adresse", $email2);
                $data .= sprintf("<input type=\"textarea\" text=\"%s\" name=\"comment\" value=\"%s\"></input>\n", "Beitrag", $comment2);
                $data .= "</form>\n";
        }

        function xmlShowEntries(&$data, $pos, $count, $db)
        {
                global $ID;
                $query = "SELECT   date_format(date,'%d.%m.%Y') as date_g,
                                   name,
                                   email,
                                   city,
                                   comment
                          FROM     moduleGuestbook
                          ORDER BY date DESC,
                                   time DESC
                          LIMIT    $pos, $count";
                $db->executeQuery($query);

                $data .= "<table align=\"center\">\n";

                while($db->nextRow())
                {
                     $data .= "<row>\n";
                     $data .= "<cell>\n";
                     $data .= sprintf("<text align=\"flowing\">Am %s schrieb %s</text>", $db->getValue("date_g"), htmlentities(htmlentities($db->getValue("name"))));
                     if($db->getValue("email") != "")
                        $data .= sprintf("<link align=\"flowing\" type=\"email\" email=\"%s\">(%s)</link>", htmlentities(htmlentities($db->getValue("email"))), htmlentities(htmlentities($db->getValue("email"))));
                     if($db->getValue("city") != "")
                        $data .= sprintf("<text align=\"flowing\">&amp;nbsp;aus %s</text>\n", htmlentities(htmlentities($db->getValue("city"))));
                     $data .= "</cell>\n";
                     $data .= "</row>\n";

                     $data .= "<row toggle=\"true\">\n";
                     $data .= "<cell>\n";
                     $data .= htmlentities(htmlentities($db->getValue("comment")));
                     $data .= "</cell>\n";
                     $data .= "</row>\n";

                     $data .= "<row toggle=\"false\"><cell><text align=\"center\">&amp;nbsp;</text></cell></row>";
                }

                $data .= "<row>\n";
                $data .= "<cell>\n";
                $data .= "<text align=\"center\">\n";

                $query = "SELECT count(id) number from moduleGuestbook";
                $db->executeQuery($query);
                $db->nextRow();
                $number = $db->getValue("number");

                $maxpos = intval($number / $count) * $count;
                $minpos = 0;

                // echo $maxpos;

                if ($pos > $minpos)
                {
                        $p = $pos - $count;
                        $data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\"> &lt;&lt; </link>", "pos", $p);
                }

                if($pos > 3 * $count)
                        $data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\">[%s] ... </link>", "pos", "0", "1");
                if($pos == 3 * $count)
                        $data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\">[%s]</link>", "pos", "0", "1");


                for($p = $pos - 2 * $count; $p <= $pos + 2 * $count; $p+=$count)
                {
                        if( ($p >= 0) && ($p < $number) )
                        {
                                $n = ($p / $count ) + 1;
				if($p == $pos)
					$data .= sprintf("<text align=\"flowing\"><b>[%s]</b></text>", $n);
				else
                                	$data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\">[%s]</link>", "pos", $p, $n);
                        }
                }


                if($pos == $maxpos - 3 * $count)
                        $data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\">[%s]</link>", "pos", $maxpos, ($maxpos/$count) + 1);
                if($pos < $maxpos - 3 * $count)
                        $data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\"> ... [%s]</link>", "pos", $maxpos, ($maxpos/$count) + 1);

                if($pos < $maxpos)
                {
                        $p = $pos + $count;
                        $data .= sprintf("<link align=\"flowing\" type=\"self\" paramkey=\"%s\" paramval=\"%s\"> &gt;&gt; </link>", "pos", $p);
                }



                $data .= "</text>";
                $data .= "</cell>\n";
                $data .= "</row>\n";
                $data .= "</table>\n";
        }



?>
