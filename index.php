<?php
        ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");

        include("classError.php");
        include("classDBmysql.php");
        include("classXMLShow.php");
        include("classXMLPrint.php");
        include("classMenu.php");

        // initialize objects
        $error = new Error();
        $db = new DBmysql($error);
        $menu = new Menu($error, $db);
        $menuStarted = false;
	$olddepth = 0;

        // check $type and $ID, default: Homepage (type=page&ID=1)
        if(isset($type) && ($type == "page"))
                $table = "contentPage";
        else if(isset($type) && ($type == "cat"))
                $table = "contentCat";
        else
        {
                $table = "contentPage";
                $type = "page";
        }
        if(!isset($ID) || (!is_numeric($ID)))
        {
                $table = "contentPage";
                $ID = 1;
        }

        // get content and contentType for the current page/category
        // $query = sprintf(" select p.content, t.name as contentType from %s p, contentType t where p.ID=%s and p.contentTypeID=t.ID", $table, $ID);
        $query = sprintf(" select   p.content,
                                    p.title,
                                    date_format(p.lastmodifyDate, '%%d.%%m.%%Y') as lastupdate,
                                    p.counter,
                                    t.name as contentType,
                                    t.file
                           from     %s p
                                    inner join contentType t on p.contentTypeID=t.ID
                           where    p.ID='%s' ",
                                    $table,
                                    $ID );

        $db->executeQuery($query);

        if($db->nextRow())
        {
                // get information from result
                $content = $db->getValue("content");
                $title = $db->getValue("title");
                $file = $db->getValue("file");
                $contentType = $db->getValue("contentType");
                $lastupdate = $db->getValue("lastupdate");
                $counter = $db->getValue("counter");

                // update counter
                $query = sprintf(" update  %s
                                   set     counter=counter+1
                                   where   ID='%s' ",
                                           $table,
                                           $ID );
                $db->executeQuery($query);

                // find first page of an empty category
                if($contentType == "emptyCat")
                {
                        $table = "contentPage";
                        $query = sprintf(" select   p.content,
                                                p.title,
                                                date_format(p.lastmodifyDate, '%%d.%%m.%%Y') as lastupdate,
                                                t.name as contentType,
                                                t.file
                                        from     %s p,
                                                contentType t
                                        where    p.catID='%s'
                                        and      p.contentTypeID=t.ID
                                        order by ord
                                        limit    0,1 ",
                                                $table,
                                                $ID );
                        $db->executeQuery($query);
                        if($db->getNumRows() == 1)
                        {
                                $db->nextRow();
                                $content = $db->getValue("content");
                                $title = $db->getValue("title");
                                $file = $db->getValue("file");
                                $contentType = $db->getValue("contentType");
                                $lastupdate = $db->getValue("lastupdate");
                        }
                        else
                        {
                                // nächste Sub-Kategorie finden
                                $contentType = "normal";
                        }
                }
        }
        else
        {
                // header ("Location: index.php");
		echo "FEHLER";
        }

        $menu->buildMenu($type, $ID);
        $menu->reset();
        $menu->nextRow(); // root

        f1("TSV Sulzfeld - ".$title);

        while($menu->nextRow())
        {
                if($menu->getContentType() == "menu")
                {
                        if($menuStarted)
                        {
                                menuStop();
                                $olddepth = 0;
                        }


                        menuStart($menu->getTitle());
                        $menuStarted = true;

                        if(!$menu->nextRow() || ($menu->getContentType() == "menu"))
                        {
                                $menu->prevRow();
                                $menu1 = new Menu($error, $db);
                                $menu1->buildMenu($menu->getType(), $menu->getID());
                                $menu1->nextRow(); // root
                                while($menu1->nextRow())
                                        menuRow($menu1);
                        }
                        else
                                $menu->prevRow();
                }
                menuRow($menu);
        } // while
        menuStop();

        f2();



        if($contentType == "normal")
                $data = $content;
        else
        {
                include($file);
                $lastupdate = "";
        }
        $xml = new XMLShow($error, $db);
        $xml->parse($data);

        f3();






    function f1($title)
    {
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title><?php echo $title ?></title>
    <link rel="STYLESHEET" type="text/css" href="/css/pub.css"></link>
  </head>
  <body bgcolor="#D0E0E0" text="#000080" link="#000080" vlink="#000080" alink="#000080" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <a name="top"></a>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td valign="top" width="100" rowspan="2"> <!-- Spalte für Wappen und Menü-->
          <table bgcolor="#C0D0D0" border="0" cellspacing="0" cellpadding="0" width="100" > <!-- Tabelle für Zwischenlinie -->
            <tr>
              <td>
                <table border="0" cellspacing="15" cellpadding="0" width="100%"> <!-- Tabelle für Menüabstand -->
                  <tr>
                    <td align="center">
                      <a href="index.php" onmouseover="status='zur Startseite';return true;" onmouseout="status='';return true;"><img src="/images/pub/wappen.gif" width="143" height="180" alt="Wappen des TSV Sulzfeld" border="0"></img></a>
                    </td>
                  </tr>
<?php
    }

        function menuStart($title)
        {
        ?>
                  <tr>
                    <td>
                      <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                          <td align="left" valign="top"><img src="/images/pub/lo2.gif" width="5" height="5" border="0"></td>
                          <td bgcolor="#000080" align="center"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                          <td align="right" valign="top"><img src="/images/pub/ro2.gif" width="5" height="5" border="0"></td>
                        </tr>
                        <tr>
                          <td bgcolor="#000080"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                          <td bgcolor="#000080" align="center"><font color="#FFFFFF"><b><?php echo $title ?></b></font></td>
                          <td bgcolor="#000080"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                        </tr>
                        <tr>
                          <td bgcolor="#000080"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                          <td class bgcolor="#000080" align="center"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                          <td bgcolor="#000080"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                        </tr>


<?php
        }
        function menuRow($menu)
        {
                global $PHP_SELF, $olddepth;
                if(($olddepth > $menu->getDepth()) && ($menu->getContentType()!="menu"))
		{
                        //print ("                          <tr class=\"menu\">\n");
                        //print ("                            <td class=\"menu\" bgcolor=\"#000080\"><img src=\"/images/pub/px.gif\" width=\"5\" height=\"1\" border=\"0\"></td>");
                        //print ("                            <td class=\"menu\" bgcolor=\"#000080\" width=\"100%%\" nowrap>&nbsp;</td>\n");
                        //print ("                            <td class=\"menu\" bgcolor=\"#000080\"><img src=\"/images/pub/px.gif\" width=\"5\" height=\"1\" border=\"0\"></td>");
                        //print ("                          </tr>\n");
		}
                if($menu->getContentType() != "menu")
                {
                        print ("                          <tr class=\"menu\" >\n");
                        print ("                            <td  class=\"menu\" bgcolor=\"#000080\"><img src=\"/images/pub/px.gif\" width=\"5\" height=\"5\" border=\"0\"></td>");
                        print ("                            <td class=\"menu\" bgcolor=\"#000080\" width=\"100%\" nowrap>\n");
                        //print ("                              ");
                        for($i=2; $i<$menu->getDepth(); $i++)
                                print ("&nbsp;&nbsp;&nbsp;&nbsp;");
                        if(($menu->getType() == "cat") && ($menu->getID() > 1))
                                printf("<a class=\"menu\" href=\"%s?type=%s&ID=%s\" onmouseover=\"status='%s';return true;\" onmouseout=\"status='';return true;\">&nbsp;&nbsp;<font color=\"#D0E0E0\"><b>%s</b></font>&nbsp;&nbsp;</a>\n", $PHP_SELF, $menu->getType(), $menu->getID(), $menu->getTitle(), $menu->getTitle());
                        else if($menu->getType() == "page")
                                printf("<a class=\"menu\" href=\"%s?type=%s&ID=%s\" onmouseover=\"status='%s';return true;\" onmouseout=\"status='';return true;\">&nbsp;&nbsp;<font color=\"#D0E0E0\"><b>%s</b></font>&nbsp;&nbsp;</a>\n", $PHP_SELF, $menu->getType(), $menu->getID(), $menu->getTitle(), $menu->getTitle());
                        print ("                            </td>\n");
                        print ("                            <td  class=\"menu\" bgcolor=\"#000080\"><img src=\"/images/pub/px.gif\" width=\"5\" height=\"5\" border=\"0\"></td>");
                        print ("                          </tr>\n");

                }
                $olddepth = $menu->getDepth();
        }
        function menuStop()
        {
        ?>
                        <tr>
                          <td align="left" valign="bottom"><img src="/images/pub/lu2.gif" width="5" height="5" border="0"></td>
                          <td bgcolor="#000080"><img src="/images/pub/px.gif" width="5" height="5" border="0"></td>
                          <td align="right" valign="bottom"><img src="/images/pub/ru2.gif" width="5" height="5" border="0"></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
<?php
        }




    function f2()
    {
        ?>
                </table>
              </td>
              <td width="4"><img src="/images/pub/px.gif" width="4" height="4" border="0"></td>
              <td bgcolor="#000080" valign="top"> <!-- Line zwischen Menü und Content -->
                <table border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="66" bgcolor="#C0D0D0"><img src="/images/pub/px.gif" width="2" height="2" border="0"></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr> <!-- Linie unter Menü -->
              <td height="4"><img src="/images/pub/px.gif" width="4" height="4" border="0"></td>
              <td rowspan="2" colspan="2"><img src="/images/pub/border_ru2.gif" width="6" height="6" border="0"></td>
            </tr>
            <tr> <!-- Linie unter Menü -->
              <td bgcolor="#000080"><img src="/images/pub/px.gif" width="2" height="2" border="0"></td>
            </tr>
          </table>
        </td>
        <td valign="top" width="99%"> <!-- Spalte für Kopfzeile und Content -->
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr> <!-- Kopfzeile -->
              <td bgcolor="#CoD0D0">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                  <tr>
                    <td background="/images/pub/header.jpg" height="66" widht="100%">&nbsp;</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr> <!-- Linie unter Kopfzeile -->
              <td bgcolor="#000080"><img src="/images/pub/px.gif" width="2" height="2" border="0"></td>
            </tr>
            <tr> <!-- Content -->
              <td bgcolor="#D0E0E0" align="center" height="400" valign="top">
                <table border="0" cellspacing="15" cellpadding="0" width="90%">
                  <tr>
                    <td>
<?php
    }

    function f3()
    {
        global $lastupdate;
        global $counter;
        ?>
                    </td>
                   </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Footer -->
      <tr>
        <td align="right" valign="bottom">

                <table bgcolor="#C0D0D0" cellspacing="0" cellpadding="0" border="0" width="90%" align="right">
                  <tr>
                    <td height="1" width="2"><img src="/images/pub/px.gif" width="1" height="1" border="0"></td>
                    <td height="1" width="4"><img src="/images/pub/px.gif" width="1" height="1" border="0"></td>
                    <td height="1" width="1200"><img src="/images/pub/px.gif" width="1" height="1" border="0"></td>
                  </tr>
                  <tr>
                    <td height="2" colspan="2" rowspan="2"><img src="/images/pub/border_lo2.gif" width="6" height="6" border="0"></td>
                    <!-- colspan -->
                    <td height="2" bgcolor="#000080"><img src="/images/pub/px.gif" width="2" height="2" border="0"></td>
                  </tr>
                  <tr>
                    <!-- rowspan -->
                    <!-- colspan -->
                    <td height="4"><img src="/images/pub/px.gif" width="4" height="4" border="0"></td>
                  </tr>
                  <tr>
                    <td width="2" bgcolor="#000080"><img src="/images/pub/px.gif" width="2" height="2" border="0"></td>
                    <td width="4" bgcolor="#C0D0D0"><img src="/images/pub/px.gif" width="4" height="4" border="0"></td>
                      <td>
                        <table border=0 width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                            <td align="left" width="20%">&nbsp;<a href="#top" onmouseover="status='nach oben';return true;" onmouseout="status='';return true;"><img src="/images/pub/2uparrow.gif" alt="nach oben" border="0"></a></td>
                            <td align="left" width="20%"><?php echo $counter ?> Aufrufe</td>
                            <td align="left" width="20%">
<?php
                global $HTTP_GET_VARS, $HTTP_POST_VARS, $PHP_SELF;
                $href = "print.php?style=print";
                while(list($k, $v) = each($HTTP_GET_VARS))
                {
                        if(!is_array($v))
                                $href .= "&".$k."=".$v;
                        else
                        {
                                while(list($k2, $v2) = each($v))
                                        $href .= "&".$k."[]=".$v2;

                        }
                }
                while(list($k, $v) = each($HTTP_POST_VARS))
                {
                        if(!is_array($v))
                                $href .= "&".$k."=".$v;
                        else
                        {
                                while(list($k2, $v2) = each($v))
                                        $href .= "&".$k."[]=".$v2;

                        }
                }
                // printf("<a href=\"%s\"><img src=\"/images/pub/druckversion.gif\" border=\"0\"></img> Druckversion</a>", $href );
                printf("<a href=\"%s\" target=\"_blank\" onmouseover=\"status='Druckversion dieser Seite';return true;\" onmouseout=\"status='';return true;\"><img src=\"/images/pub/druckversion.gif\" border=\"0\" alt=\"Druckversion dieser Seite\"></img> Druckversion</a>", $href );
        ?>
                          </td>
                          <td align="center" width="25%">
                            <a href="<?php $PHP_SELF ?>?type=page&ID=47&subject=Fehler%20gefunden&receiver=1" onmouseover="status='Fehler melden';return true;" onmouseout="status='';return true;"><img src="/images/pub/error.gif" border="0" alt="Fehler melden"> Fehler melden</a>
                          </td>
        <?php
                          if(!empty($lastupdate))
                              printf("                          <td align=\"right\" width=\"25%%\"> %s &nbsp;&nbsp;</td>", $lastupdate);
                          // else
                              // print ("                          <td align=\"right\" width=\"25%%\">&nbsp;</td>");
        ?>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td height="4" colspan="2" rowspan="2"><img src="/images/pub/border_lu2.gif" width="6" height="6" border="0"></td>
                    <!-- colspan -->
                    <td height="4"><img src="/images/pub/px.gif" width="4" height="4" border="0"></td>
                  </tr>
                  <tr>
                    <!-- rowspan -->
                    <!-- colspan -->
                    <td height="2" bgcolor="#000080"><img src="/images/pub/px.gif" width="2" height="2" border="0"></td>
                  </tr>
                </table>


        </td>
      </tr>
    </table>
  </body>
</html>
<?php
    }






?>
