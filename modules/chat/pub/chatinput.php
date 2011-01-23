<?php
ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes");
include_once("classError.php");
include_once("classDBmysql.php");

// Farben
$colors = Array( "#0000FF" // blau
                ,"#008080" // türkis
                ,"#008000" // grün
                ,"#808000" // oliv
                ,"#FF8000" // orange
                ,"#FF0000" // rot
                ,"#FF00FF" // rosa
                ,"#CC6666" //
                ,"#800080" // lila
                ,"#800000" // braun
                ,"#000000" // schwarz
                ,"#707070" // grau
               );

// Farbe festlegen
if(empty($color)) {
  srand ((double)microtime()*1000000);
  $color = $colors[array_rand($colors)];
}

// Eintragen
if( !empty($nickname) && !empty($message) ) {

  $nickname = addslashes($nickname);
  $messsage = addslashes($message);
  $color = addslashes($color);
  $query = "INSERT INTO moduleChat values('', now(), '$nickname', '$message', '$color')";

  $error = new Error();
  $db = new DBmysql($error);
  $db->executeQuery($query);
}

?>
<html>
 <head>
  <link rel="STYLESHEET" type="text/css" href="/css/pub.css"></link>
  <script type="text/javascript">
  <!--

   function init() {
     // Farbe setzen
     changeColor("<?php echo $color ?>");
   }

   function changeColor(toColor) {

     var row = document.getElementById("colorlist");

     // Markierung löschen
     var cells = row.cells;
     for (var i=0; i < cells.length; ++i)
       cells[i].bgColor = "#D0E0E0";

     // Neue Markierung anbringen
     var frame = document.getElementById(toColor);
     frame.bgColor = toColor;

     // Farbe setzen
     document.chatform.color.value = toColor;

     // Focus auf Eingabefeld setzen
     document.chatform.message.focus();
   }

  //-->
  </script>
 </head>
 <body bgcolor="#D0E0E0" text="#000080" onload="init()">
  <form name="chatform" action="chatinput.php" method="get">
   <table border="0">
    <tr>
     <td>
      <input type="hidden" name="nickname" value="<?php echo $nickname ?>"></input>
      <input type="hidden" name="color" value="<?php echo $color ?>" ></input>
      <input type="text"  name="message" size="40" maxlength="255"></input>
      <input type="submit" value="go"></button>
     </td>
     <td>
      <table cellspacing="3" border="0">
       <tr id="colorlist">
<?php
  // Farbenauswahl anzeigen
  for($i=0; $i<count($colors); $i++) {
    echo "<td id=\"" . $colors[$i] . "\" bgcolor=\"#D0E0E0\">";
    echo "<table cellspacing=\"2\">";
    echo "<tr>";
    echo "<td bgcolor=\"" . $colors[$i] . "\">";
    echo "<a href=\"javascript:changeColor('" . $colors[$i] . "')\">";
    echo "&nbsp;&nbsp;&nbsp";
    echo "</a></td></tr></table></td>";
  }
?>
       </tr>
      </table>
     </td>
    </tr>
   </table>
  </form>
 </body>
</html>
