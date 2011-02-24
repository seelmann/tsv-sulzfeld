<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
    include("auth.php");
    include("classEdit.php");
    include("../classPermcheck.php");
    $permcheck = new Permcheck($db, $error);

?>
<html>
  <head>
    <title></title>
    <link rel="STYLESHEET" type="text/css" href="/css/admin.css"></link>
    <script language="JavaScript" src="edit.js" type="text/javascript"></script>
  </head>
  <body>
    <form name="save" action="save.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="type" value="<?php echo $type ?>"></input>
      <input type="hidden" name="id" value="<?php echo $id ?>"></input>

      <input type="hidden" name="go" value="refresh"></input>

      <table align="center" border="0">
        <tr>
          <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
          </td>
          <td align="center" class="regCard">
            <a href="javascript:setValue(document.save.go, 'save')">Änderungen speichern<br>und schliessen</a></td>
          </td>
          <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
          </td>
          <td align="center" class="regCard">
            <a href="javascript:setValue(document.save.go, 'cancel')">Änderungen verwerfen</a></td>
          </td>
          <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
          </td>
          <td align="center" class="regCard">
            <a href="javascript:setValue(document.save.go, 'refresh')">aktualisieren</a></td>
          </td>
          <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
          </td>
          <td align="center" class="regCard">
            <input type="checkbox" name="autosubmit" checked> Autosubmit?</input>
          </td>
        </tr>
      </table>
      <br>

<?php

    $edit = new Edit($error, $db, $type, $id);

    if(isset($type) && ($type == "page"))
    {
        if($permcheck->hasUserPagePermission($id))
        {
            $edit->start();
        }
        else
        {
            $error->printAccessDenied();
        }
    }
    else if(isset($type) && ($type == "cat"))
    {
        if($permcheck->hasUserCatPermission($id))
        {
            $edit->start();
        }
        else
        {
            $error->printAccessDenied();
        }
    }
    else
    {
        $error->printAccessDenied();
    }
?>


    </form>
  </body>
</html>

