<?php
class DateTimeFormat
{
        function addDay($date)
        {
                list($tag, $monat, $jahr) = explode(".", $date);
                $wochentag_num=date("w", mktime(0,0,0,$monat,$tag,$jahr));
                switch($wochentag_num)
                {
                        case 0:
                                $wochentag_kurz="So";
                                break;
                        case 1:
                                $wochentag_kurz="Mo";
                                break;
                        case 2:
                                $wochentag_kurz="Di";
                                break;
                        case 3:
                                $wochentag_kurz="Mi";
                                break;
                        case 4:
                                $wochentag_kurz="Do";
                                break;
                        case 5:
                                $wochentag_kurz="Fr";
                                break;
                        case 6:
                                $wochentag_kurz="Sa";
                                break;
                        default:
                                $wochentag_kurz="";
                }
                //return sprintf("%s, %s\n",$wochentag_kurz, $date);
                return sprintf("%s, %s",$wochentag_kurz, $date);
        }
}
?>
