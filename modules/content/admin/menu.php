<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classMenu.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

?>

<html>
  <head>
    <title></title>
    <link rel="STYLESHEET" type="text/css" href="/css/admin.css"></link>
    <script language="JavaScript" src="edit.js" type="text/javascript"></script>
    </script>
  </head>
  <body>
    <form name="save" action="menu.php" method="post" enctype="multipart/form-data">

<?php
    $menu = new Menu($error, $db);
    $db1 = new DBmysql($error);

    $types = array();
    $query = sprintf("select ID,german from contentType");
    $db1->executeQuery($query);
    while($db1->nextRow())
        $types[$db1->getValue("ID")] = $db1->getValue("german");

    if(isset($cat))
    {
        if(isset($contentType))
        {
                $query = sprintf("update contentCat set contentTypeID=%s where ID=%s", $contentType, $cat);
echo "<br><br>";
echo $query;
echo "<br><br>";
                $db->executeQuery($query);
        }
        printf("<input type=\"hidden\" name=\"cat\" value=\"%s\"></input>", $cat);
        $query = sprintf("select title, contentTypeID from contentCat where ID=%s", $cat);
        $db->executeQuery($query);
        $db->nextRow();
        printCatActions($cat, $db->getValue("title"), $types, $db->getValue("contentTypeID"));
        $menu->buildMenu("cat", $cat);
        printMenu($menu);
    }
    else if(isset($page))
    {
        if(isset($contentType))
        {
                $query = sprintf("update contentPage set contentTypeID=%s where ID=%s", $contentType, $page);
                $db->executeQuery($query);
        }
        printf("<input type=\"hidden\" name=\"page\" value=\"%s\"></input>", $page);
        $query = sprintf("select title, contentTypeID from contentPage where ID=%s", $page);
        $db->executeQuery($query);
        $db->nextRow();
        printPageActions($page, $db->getValue("title"), $types, $db->getValue("contentTypeID"));
        $menu->buildMenu("page", $page);
        printMenu($menu);
    }
    else
    {
        printf("<input type=\"hidden\" name=\"cat\" value=\"%s\"></input>", 1);
        // $query = sprintf("select title from contentCat where ID=%s", 1);
        // $db->executeQuery($query);
        // $db->nextRow();
        // printCatActions($cat, $db->getValue("title"));
        $menu->buildMenu("cat", 1);
        printMenu($menu);
    }



    function printMenu($menu)
    {
        $menu->reset();
        print("          <table class=\"menu\" border=\"0\">");

        print ("            <tr class=\"menu\">\n");
        print ("              <td class=\"menu\">\n");
        print ("                &nbsp;\n");
        print ("              </td>\n");
        print ("            </tr>\n");

        print ("            <tr class=\"menu\">\n");
        print ("              <td class=\"menu\">\n");
        print ("                Menübaum:\n");
        print ("              </td>\n");
        print ("            </tr>\n");

        while($menu->nextRow())
        {
            print ("            <tr class=\"menu\">\n");
            print ("              <td class=\"menu\">\n");
            for($i=0; $i<$menu->getDepth(); $i++)
                print("&nbsp;&nbsp;&nbsp;&nbsp;");
            if($menu->getType() == "cat")
                printf("                <a class=\"menu\" href=\"menu.php?%s=%s\"><img src=\"/images/admin/folder.gif\" border=\"0\">&nbsp;&nbsp;%s</a>\n", $menu->getType(), $menu->getID(), $menu->getTitle());
            else if($menu->getType() == "page")
                printf("                <a class=\"menu\" href=\"menu.php?%s=%s\"><img src=\"/images/admin/document.gif\" border=\"0\">&nbsp;&nbsp;%s</a>\n", $menu->getType(), $menu->getID(), $menu->getTitle());
            print ("              </td>\n");
            print ("            </tr>\n");
        } // while

        print ("          </table>\n");
    } // printMenu()


    function printCatActions($cat, $title, $types, $typeID)
    {
        print ("    <table class=\"action\" border=\"0\" align=\"center\">");
        print ("      <tr class=\"action\">\n");
        print ("        <td class=\"action\">\n");
        printf("          <h5 class=\"action\" align=\"center\">\"%s\"</h5>\n", $title);
        print ("        </td>\n");
        print ("      </tr>\n");
        print ("      <tr class=\"action\">\n");
        print ("        <td class=\"action\">\n");
        print ("          <table class=\"action\" border=\"0\">");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        print ("                <select class=\"action\" name=\"contentType\" size=\"1\">\n");
        while (list ($k, $v) = each ($types) )
                printf("                  <option class=\"action\" value=\"%s\" %s>%s</option>\n", $k, ($k==$typeID?"selected":""), $v);
        print ("                </select>\n");
        print ("                <input type=\"image\" src=\"/images/admin/ok.png\"></input>");
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" target=\"edit\" href=\"edit.php?type=cat&id=%s\"><img src=\"/images/admin/edit.gif\" alt=\"Kategorie bearbeiten\" border=\"0\"></img> Kategorie bearbeiten</a>\n", $cat);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" href=\"javascript:deleteCat(%s)\"><img src=\"/images/admin/delete.gif\" alt=\"Kategorie löschen\" border=\"0\"></img> Kategorie löschen</a>\n", $cat);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" href=\"javascript:newCat(%s)\">neue Unterkategorie</a>\n", $cat);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" href=\"javascript:newPage(%s)\">neue Seite</a>\n", $cat);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("          </table>\n");
        print ("        </td>\n");
        print ("      </tr>\n");
        print ("    </table>\n");
    }

    function printPageActions($page, $title, $types, $typeID)
    {
        print ("    <table class=\"action\" border=\"0\" align=\"center\">");
        print ("      <tr class=\"action\">\n");
        print ("        <td class=\"action\">\n");
        printf("          <h5 class=\"action\" align=\"center\">\"%s\"</h5>\n", $title);
        print ("        </td>\n");
        print ("      </tr>\n");
        print ("      <tr class=\"action\">\n");
        print ("        <td class=\"action\">\n");
        print ("          <table class=\"action\" border=\"0\">");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        print ("                <select class=\"action\" name=\"contentType\" size=\"1\">\n");
        while (list ($k, $v) = each ($types) )
                printf("                  <option class=\"action\" value=\"%s\" %s>%s</option>\n", $k, ($k==$typeID?"selected":""), $v);
        print ("                </select>\n");
        print ("                <input type=\"image\" src=\"/images/admin/ok.png\"></input>");
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" target=\"edit\" href=\"edit.php?type=page&id=%s\"><img src=\"/images/admin/edit.gif\" alt=\"Seite bearbeiten\" border=\"0\"></img> Seite bearbeiten</a>\n", $page);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" href=\"javascript:deletePage(%s)\"><img src=\"/images/admin/delete.gif\" alt=\"Seite löschen\" border=\"0\"></img> Seite löschen</a>\n", $page);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("            <tr class=\"action\">\n");
        print ("              <td class=\"action\" align=\"center\">\n");
        printf("                <a class=\"action\" href=\"javascript:changeToCat(%s)\"><img src=\"/images/admin/folder.gif\" alt=\"in Kategorie umwandeln\" border=\"0\"></img> in Kategorie umwandeln</a>\n", $page);
        print ("              </td>\n");
        print ("            </tr>\n");
        print ("          </table>\n");
        print ("        </td>\n");
        print ("      </tr>\n");
        print ("    </table>\n");
    }

?>

    </form>
  </body>
</html>
