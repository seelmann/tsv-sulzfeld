<?php
                $data .= "<table align=\"center\">\n";

                $data .= "<row>\n";
                $data .= "<cellh span=\"3\">Platz</cellh>\n";
                $data .= "<cellh>Mannschaft</cellh>\n";
                $data .= "<cellh>Spiele</cellh>\n";
                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                $data .= "<cellh>g</cellh>\n";
                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                $data .= "<cellh>u</cellh>\n";
                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                $data .= "<cellh>v</cellh>\n";
                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                if($table->getSportName() == "Fuﬂball")
                        $data .= "<cellh span=\"3\" align=\"center\">Tore</cellh>\n";
                else if ($table->getSportName() == "Tischtennis")
                        $data .= "<cellh span=\"3\" align=\"center\">Spiele</cellh>\n";
                $data .= "<cellh>Punkte</cellh>\n";
                $data .= "</row>\n";

                $count = 0;
                while($table->nextRow())
                {
                        $count++;
                        $data .= "<row>\n";
                        if($table->getOwn())
                        {
                                $data .= "<cellh>&amp;nbsp;</cellh><cellh align=\"right\">".$count.".</cellh><cellh>&amp;nbsp;</cellh>\n";
                                $data .= "<cellh>".$table->getTeamName()."</cellh>\n";
                                $data .= "<cellh align=\"center\">".$table->getNumberOfMatches()."</cellh>\n";
                                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                                $data .= "<cellh align=\"center\">".$table->getWon()."</cellh>\n";
                                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                                $data .= "<cellh align=\"center\">".$table->getRemis()."</cellh>\n";
                                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                                $data .= "<cellh align=\"center\">".$table->getLost()."</cellh>\n";
                                $data .= "<cellh>&amp;nbsp;</cellh>\n";
                                $data .= "<cellh align=\"right\">".$table->getDiffPos()."</cellh><cellh>:</cellh><cellh align=\"right\">".$table->getDiffNeg()."</cellh>\n";
                                $data .= "<cellh align=\"center\">".$table->getPoints()."</cellh>\n";
                        }
                        else
                        {
                                $data .= "<cell>&amp;nbsp;</cell><cell align=\"right\">".$count.".</cell><cell>&amp;nbsp;</cell>\n";
                                $data .= "<cell>".$table->getTeamName()."</cell>\n";
                                $data .= "<cell align=\"center\">".$table->getNumberOfMatches()."</cell>\n";
                                $data .= "<cell>&amp;nbsp;</cell>\n";
                                $data .= "<cell align=\"center\"> ".$table->getWon()." </cell>\n";
                                $data .= "<cell>&amp;nbsp;</cell>\n";
                                $data .= "<cell align=\"center\"> ".$table->getRemis()." </cell>\n";
                                $data .= "<cell>&amp;nbsp;</cell>\n";
                                $data .= "<cell align=\"center\"> ".$table->getLost()." </cell>\n";
                                $data .= "<cell>&amp;nbsp;</cell>\n";
                                $data .= "<cell align=\"right\">".$table->getDiffPos()."</cell><cell>:</cell><cell align=\"right\">".$table->getDiffNeg()."</cell>\n";
                                $data .= "<cell align=\"center\">".$table->getPoints()."</cell>\n";
                        }
                        $data .= "</row>\n";
                }
                $data .= "</table>\n";
?>