<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");

    print ("<html>\n");
    print ("  <head>\n");
    print ("    <title>Menü</title>\n");
    print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
    print ("  </head>\n");
    print ("  <body bgcolor=\"white\">\n");
    print ("    <table class=\"menu\">\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><a class=\"menu\" href=\"password.php\" target=\"edit\">Passwort ändern</a></td>\n");
    print ("      </tr>\n");

    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\">&nbsp;</td>\n");
    print ("      </tr>\n");

    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\">Benutzer:</td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"useradmin.php?do=create\" target=\"edit\">anlegen</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"useradmin.php?do=edit\" target=\"edit\">ändern</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"useradmin.php?do=deactivate\" target=\"edit\">deaktivieren</a></li></ul></td>\n");
    print ("      </tr>\n");
    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\"><ul><li><a class=\"menu\" href=\"useradmin.php?do=activate\" target=\"edit\">aktivieren</a></li></ul></td>\n");
    print ("      </tr>\n");

    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\">&nbsp;</td>\n");
    print ("      </tr>\n");

    print ("      <tr class=\"menu\">\n");
    print ("        <td class=\"menu\">Berechtigungen ändern:</td>\n");
    print ("      </tr>\n");



$d = dir("../../../modules");
while($entry=$d->read())
{
    //if(!is_file($entry) && !is_link($entry) && $entry!="." && $entry!=".." && $entry!="common")
    if(!is_file($entry) && $entry!="." && $entry!=".." && $entry!="common")
    {
        if(file_exists ("../../../modules/$entry/permadmin.php"))
        {
            print ("<tr class=\"menu\">\n");
            printf("<td class=\"menu\"><ul><li><a class=\"menu\" href=\"/modules/%s/permadmin.php\" target=\"edit\">", $entry);
            include("../../../modules/$entry/navigation.html");
            print ("</li></ul></a></td>\n");
            print ("</tr>\n");
        }
    } //if
} // while
$d->close();

    print ("    </table>\n");
    print ("  </body>\n");
    print ("</html>\n");
?>
