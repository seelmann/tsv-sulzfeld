<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");

    print ("<html>\n");
    print ("  <head>\n");
    print ("    <title>Navigation</title>\n");
    print ("    <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/admin.css\"></link>\n");
    print ("  </head>\n");
    print ("  <body bgcolor=\"white\">\n");
    print ("    <table class=\"menu\" width=\"100%\">\n");
    print ("      <tr class=\"menu\">\n");

$d = dir("../modules");
while($entry=$d->read())
{
    //if(!is_file($entry) && !is_link($entry) && $entry!="." && $entry!=".." && $entry!="common")
    if(!is_file($entry) && $entry!="." && $entry!=".." && $entry!="common")
    {
        if(file_exists ("../modules/$entry/navigation.html"))
        {
            printf("<td class=\"menu\"><a class=\"menu\" href=\"/modules/%s/admin/index.php\" target=\"main\">", $entry);
            include("../modules/$entry/navigation.html");
            print ("</a></td>\n");
        }
    } //if
} // while
$d->close();

    print ("        <td class=\"menu\"><a class=\"menu\" href=\"logout.php\" target=\"_top\">Logout</a></td>\n");
    printf("        <td class=\"menu\" width=\"100%%\" align=\"right\">%s</a></td>\n", $sUser["realname"]);
    print ("      </tr>\n");
    print ("    </table>\n");
    print ("  </body>\n");
    print ("</html>\n");

?>
