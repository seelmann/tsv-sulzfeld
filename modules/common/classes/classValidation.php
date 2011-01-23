<?php
        class Validation
        {
                function Validation()
                {
                }

                function checkDate($iso)
                {
                        list($year, $month, $day) = explode("-", $iso);
                        return checkdate($month, $day, $year) && ($iso != "0000-00-00");
                }

                function checkTime($time)
                {
                        list($hour, $minute, $second) = explode(":", $time);
                        if( ($hour < 0) || ($hour > 23) || ($hour==""))
                                return false;
                        if( ($minute < 0) || ($minute > 59) || ($minute=="") )
                                return false;
                        if( ($second < 0) || ($second > 59) || ($second=="") )
                                return false;
                        return true;
                }


                function countString($string)
                {
                        return strlen(trim($string));
                }

                function isNumber($number)
                {
                        return is_numeric($number);
                }

                function isInArray($value, $array)
                {
                        return $array[$value];
                }

                function isInIterator($value, $iterator)
                {
                        $bool = false;
                        while($iterator->hasNext())
                        {
                                $o = $iterator->next();
                                if($value == $o->getID())
                                        $bool = true;
                        }

                        return $bool;
                }

                function transformDate($date)
                {
                        $isodate = "";

                        if( is_array($a = explode (".", $date)) && ($a[0]!=$date) )
                        // explode operation was successful
                        {
                                if(sizeof($a) == 3)
                                {
                                        list($d, $m, $y) = $a;
                                        if(strlen($y) == 2)
                                        {
                                                if($y < 80)
                                                        $y += 2000;
                                                else
                                                        $y += 1900;
                                        }
                                        if(strlen($y) == 0)
                                        {
                                                $today = getdate();
                                                $y = $today['year'];
                                        }
                                        $isodate = sprintf ("%04d-%02d-%02d", $y, $m, $d);
                                }
                                else
                                {
                                        list($d, $m) = $a;
                                        $today = getdate();
                                        $y = $today['year'];
                                        $isodate = sprintf ("%04d-%02d-%02d", $y, $m, $d);
                                }
                        }

                        else if(is_array($a = explode ("-", $date)) && ($a[0]!=$date) )
                        // explode operation was successful
                        {
                                $isodate=$date;
                        }

                        else
                        {
                                if(strlen($date) == 8)
                                {
                                        $d = substr($date,0,2);
                                        $m = substr($date,2,2);
                                        $y = substr($date,4,4);
                                        $isodate = sprintf ("%04d-%02d-%02d", $y, $m, $d);
                                }
                                else if(strlen($date) == 6)
                                {
                                        $d = substr($date,0,2);
                                        $m = substr($date,2,2);
                                        $y = substr($date,4,2);
                                        if($y < 80)
                                                $y += 2000;
                                        else
                                                $y += 1900;
                                        $isodate = sprintf ("%04d-%02d-%02d", $y, $m, $d);
                                }
                                else if(strlen($date) == 4)
                                {
                                        $d = substr($date,0,2);
                                        $m = substr($date,2,2);
                                        $today = getdate();
                                        $y = $today['year'];
                                        $isodate = sprintf ("%04d-%02d-%02d", $y, $m, $d);
                                }
                        }

                        return $isodate;
                }

                function transformTime($time)
                {
                        $isotime = "";

                        if( is_array($a = explode (":", $time)) && ($a[0]!=$time) )
                        // explode operation was successful
                        {
                                if(sizeof($a) == 3)
                                {
                                        list($h, $m, $s) = $a;
                                        $isotime = sprintf ("%02d:%02d:%02d", $h, $m, $s);
                                }
                                else
                                {
                                        list($h, $m) = $a;
                                        $s = "00";
                                        $isotime = sprintf ("%02d:%02d:%02d", $h, $m, $s);
                                }
                        }

                        else
                        {
                                if(strlen($time) == 6)
                                {
                                        $h = substr($time,0,2);
                                        $m = substr($time,2,2);
                                        $s = substr($time,4,2);
                                        $isotime = sprintf ("%02d:%02d:%02d", $h, $m, $s);
                                }
                                else if(strlen($time) == 4)
                                {
                                        $h = substr($time,0,2);
                                        $m = substr($time,2,2);
                                        $s = "00";
                                        $isotime = sprintf ("%02d:%02d:%02d", $h, $m, $s);
                                }
                        }

                        return $isotime;
                }


        }
?>
