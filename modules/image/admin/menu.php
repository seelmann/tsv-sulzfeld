<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");

    print ("<html>\n");
    print ("  <head>\n");
    print ("    <title>Menü</title>\n");
    print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
    print ("  </head>\n");
    print ("  <body bgcolor=\"white\">\n");
    print ("    <table class=\"menu\">\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"status.php\" target=\"edit\">allgemeine Infos</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"upload.php\" target=\"edit\">neues Bild hochladen</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"list.php\" target=\"edit\">Katalog als Liste</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"preview.php\" target=\"edit\">Katalog mit Bildvorschau</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("    </table>\n");
    print ("  </body>\n");
    print ("</html>\n");
?>
