<?php
        ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
        include("classError.php");
        include("classDBmysql.php");
        include("classDate.php");
        include("classMatchInfo.php");
        include("classMatchReport.php");
        include("classMatchPostpone.php");
	
	include("classXMLShow.php");

        $error = new Error();
        $db = new DBmysql($error);

        if(isset($matchInfoID))
        {
                $matchinfo = new MatchInfo($db);
                $matchinfo->loadFromID($matchInfoID);
                $head = $matchinfo->getHead();
                $text = $matchinfo->getText();
        }
        else if(isset($dateID))
        {
                $date = new Date($db);
                $date->load($dateID);
                $head = $date->getHead();
                $text = $date->getText();
        }
        else if(isset($matchReportID))
        {
                $matchreport = new MatchReport($db);
                $matchreport->loadFromID($matchReportID);
                $head = $matchreport->getHead();
                $text = $matchreport->getText();
        }
        else if(isset($matchPostponeID))
        {
                $matchpostpone = new MatchPostpone($db);
                $matchpostpone->loadFromID($matchPostponeID);
                $head = "";
                $text = "";
                if($matchpostpone->getReason() != "")
                        $text .= $matchpostpone->getReason()."</p><p>";
                if($matchpostpone->getNewdate() != "") {
                        if($matchpostpone->getNewisodate()." ".$matchpostpone->getNewtime() > date("Y-m-d H:i"))
                                $text .= "Neuer Termin: ".$matchpostpone->getNewdate()." ".$matchpostpone->getNewtime();
                        else
                                $text .= "Nachholspiel vom ".$matchpostpone->getOlddate()." ".$matchpostpone->getOldtime();
                }
                if($text == "")
                    $text = "keine weitere Informationen";
        }
        else if(isset($matchPostponeID2))
        {
                $matchpostpone = new MatchPostpone($db);
                $matchpostpone->loadFromID($matchPostponeID2);
                $head = "";
                $text = "";
                if($matchpostpone->getOlddate() != "")
                        $text .= "Nachholspiel vom ".$matchpostpone->getOlddate()." ".$matchpostpone->getOldtime();
        }
        if(isset($othermatchInfoID))
        {
                $query = "select * from moduleOthermatchInfo where ID=$othermatchInfoID";
                $db->executeQuery($query);
                if($db->nextRow())
                {
                    $head = $db->getValue("head");
                    $text = $db->getValue("text");;
                }
        }
        if(isset($othermatchReportID))
        {
                $query = "select * from moduleOthermatchReport where ID=$othermatchReportID";
                $db->executeQuery($query);
                if($db->nextRow())
                {
                    $head = $db->getValue("head");
                    $text = $db->getValue("text");;
                }
        }
        else if(isset($matchCanceled))
        {
                $matchpostpone = new MatchPostpone($db);
                $matchpostpone->loadFromID($matchCanceled);
                $head = "";
                $text = "";
                if($matchpostpone->getReason() != "")
                        $text .= $matchpostpone->getReason()."</p><p>";
                if($matchpostpone->getNewdate() != "") {
                        if($matchpostpone->getNewisodate()." ".$matchpostpone->getNewtime() > date("Y-m-d H:i"))
                                $text .= "Neuer Termin: ".$matchpostpone->getNewdate()." ".$matchpostpone->getNewtime();
                }
                if($text == "")
                    $text = "keine weitere Informationen";
        }




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
  <head>
    <title>Info</title>
    <link rel="STYLESHEET" type="text/css" href="/css/pub.css"></link>
  </head>
  <body bgcolor="#D0E0E0" text="#000080" link="#000080" vlink="#000080" alink="#000080">
    <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#000080">
      <tr>
        <td>
          <table border="0" cellspacing="2" cellpadding="0" width="100%">
            <tr>
              <td>
                <table border="0" cellspacing="0" cellpadding="5" width="100%" bgcolor="#D0E0E0">
                  <tr>
                    <td>


                      <?php if(!empty($head)) { ?><h1 align="center"><?php echo $head ?></h1><?php } ?>
                      <p align="justify">
		        <?php 
			  if(substr($text, 0, 5) == "<?xml") {
  			    $xml = new XMLShow($error, $db);
                            $xml->parse($text);			  
			  }
			  else {
			    echo nl2br($text);
			  }
			?>
		      </p> 
		      
		      
		      


                    </td>
                  </tr>
                  <tr>
                    <td align="center">&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="center"><form><input type="button" value="schließen" onclick="self.close()"></input></form></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
