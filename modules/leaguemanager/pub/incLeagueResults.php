<?php
                $data .= "<table align=\"center\">\n";
                $data .= "<row>\n";
                $data .= "<cellh>Datum</cellh>\n";
                $data .= "<cellh>Uhrzeit</cellh>\n";
                $data .= "<cellh>Heim</cellh>\n";
                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                $data .= "<cellh>Gast</cellh>\n";
                $data .= "<cellh>Ergebnis</cellh>\n";
                $data .= "</row>\n";

                while($result->nextResult())
                {
                        $data .= "<row>\n";
                        $data .= "<cell>".$result->getDate()."</cell>\n";
                        $data .= "<cell>".$result->getTime()."</cell>\n";
                        $data .= "<cell>".$result->getHomeTeamName()."</cell>\n";
                        $data .= "<cell> - </cell>\n";
                        $data .= "<cell>".$result->getGuestTeamName()."</cell>\n";
                        // $data .= "<cell align=\"center\">".$result->getHomeResult()." : ".$result->getGuestResult()."</cell>\n";
                        $data .= "<cell align=\"center\">".$result->getResult()."</cell>\n";
                        //if( ($result->getReportHead() != "") || ($result->getReportText() != "") )
                        if( $result->getReportID() != 0 )
                                $data .= "<cell><infopopup paramkey=\"matchReportID\" paramval=\"".$result->getReportID()."\"></infopopup></cell>\n";
                        // if( ($result->getPostponeReason() != "") || ($result->getPostponeNewDate() != "") || ($result->getPostponeNewTime() != "") )
                        if( $result->getPostponeID() != 0 )
                                $data .= "<cell><infopopup paramkey=\"matchPostponeID\" paramval=\"".$result->getPostponeID()."\"></infopopup></cell>\n";
                        if( $result->getOthermatchReportID() != 0 )
                                $data .= "<cell><infopopup paramkey=\"othermatchReportID\" paramval=\"".$result->getOthermatchReportID()."\"></infopopup></cell>\n";

                        $data .= "</row>\n";

                }
                $data .= "</table>\n";
?>
