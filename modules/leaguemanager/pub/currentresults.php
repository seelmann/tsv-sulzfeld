<?php
        include_once("classError.php");
        include_once("classDBmysql.php");

        include("classResult.php");

        $data = "";
        $errors = 0;
        $error = new Error();
        $db = new DBmysql($error);

        $weeks = 4;
        $limit = 0;

        xmlStart($data);

        $result = new Result($db);

        $result->createSportResults($weeks);
        while($result->nextSport($weeks, $limit))
        {
                $data .= "<header size=\"3\" align=\"left\">".$result->getSportName().":</header>";
                include("modules/leaguemanager/pub/incLeagueResults.php");
        }

        xmlStop($data);

        function xmlStart(&$data)
        {
                $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
                $data .= "<page>\n";
                $data .= sprintf("<header align=\"center\" size=\"1\">Ergebnisse</header>\n");
        }

        function xmlStop(&$data)
        {

                $data .= "</page>\n";
        }
?>
