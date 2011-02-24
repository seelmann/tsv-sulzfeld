<?php
	ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classError.php");
        include_once("classDBmysql.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        if(!isset($pos))
                $pos=0;
        $count=5;

        xmlStart($data);

	$receivers = array();
        $receivers[0] = "";
        $to = "";

        $query = sprintf("select * from moduleContactForm");
        $db->executeQuery($query);

	while($db->nextRow())
        {
	    $receivers[$db->getValue("ID")] = htmlentities(htmlentities($db->getValue("description")));
            if($db->getValue("ID") == $receiver)
                    $to = $db->getValue("email");
        }

        if(isset($go) && ($go==1))
        {
                if(get_magic_quotes_gpc())
                {
                        $name = stripslashes($name);
                        $city = stripslashes($city);
			$email = stripslashes($email);
			$subject = stripslashes($subject);
			$message = stripslashes($message);
                }

                $name = strip_tags($name);
		$city = strip_tags($city);
                $email = strip_tags($email);
                $subject = strip_tags($subject);
		$message = strip_tags($message);

                $name2 = htmlentities(htmlentities($name));
		$city2 = htmlentities(htmlentities($city));
                $email2 = htmlentities(htmlentities($email));
                $subject2 = htmlentities(htmlentities($subject));
		$message2 = htmlentities(htmlentities($message));


                if ($name=="" || $email=="" || $subject=="" || $message=="" || $to=="")
                {
                        xmlError($data);
                        $errors++;
                }

                if(isset($email) && $email!="" && !validate_email($email))
                {
                        xmlEmailError($data);
                        $errors++;
                }

                if($errors == 0)
                {
                        $xtra="From: $email ($name)\n";
                        $message="Von $name aus $city\n\n$message";
                        mail ($to,$subject,$message,$xtra);

                        if($to != "info@tsv-sulzfeld.de")
                                mail ("info@tsv-sulzfeld.de", "Kopie: ".$subject, "Kopie: \n\n".$message, $xtra);

                        xmlSucess($data);
                }
                else
                {
                        xmlForm($data, $name2, $city2, $email2, $subject2, $message2, $receiver, $receivers);
                }
        }
        else
        {
                xmlForm($data, $name, $city, $email, $subject, $message, $receiver, $receivers);
        }

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
                $data .= sprintf("<header align=\"center\" size=\"1\">Kontakt</header>\n");
        }

        function xmlStop(&$data)
        {
                $data .= "</page>\n";
        }

        function xmlSucess(&$data)
        {
                $data .= "<text align=\"center\" style=\"strong\">Ihre Anfrage wurde weitergeleitet.</text>\n";
        }

        function xmlEmailError(&$data)
        {
                $data .= "<text align=\"center\" style=\"strong\">Falsches Format der E-Mail Adresse.</text>\n";
        }

        function xmlError(&$data)
        {
                $data .= "<text align=\"center\" style=\"strong\">Bitte die Felder \"Empfänger\", \"Name\", \"E-Mail Adresse\", \"Betreff\" und \"Nachricht\" ausfüllen.</text>\n";
        }


        function xmlForm(&$data, $name, $city, $email, $subject, $message, $receiver, $receivers)
        {
                $data .= "<form receiver=\"self\" buttontext=\"Formular abschicken\">\n";

                $data .= "<input type=\"select\" text=\"Empfänger\" name=\"receiver\">\n";
                while (list ($k, $v) = each ($receivers) )
                        $data .= sprintf("<input type=\"option\" value=\"%s\" desc=\"%s\" selected=\"%s\"></input>\n", $k, $v, $k==$receiver?"true":"false");
                $data .= "</input>";

                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"name\" value=\"%s\"></input>\n", "Name", $name);
                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"city\" value=\"%s\"></input>\n", "Ort", $city);
                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"email\" value=\"%s\"></input>\n", "E-Mail Adresse", $email);
                $data .= sprintf("<input type=\"textfield\" text=\"%s\" name=\"subject\" value=\"%s\"></input>\n", "Betreff", $subject);
                $data .= sprintf("<input type=\"textarea\" text=\"%s\" name=\"message\" value=\"%s\"></input>\n", "Nachricht", $message);
                $data .= "</form>\n";
        }




?>






