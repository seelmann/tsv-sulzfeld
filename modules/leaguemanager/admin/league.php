<?php
    ini_set("include_path","./:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/42/d73812555/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSport.php");
        include("classLeague.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "league.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "league", "leagueI");
        $t->set_block("league", "sport", "sportI");
        $t->set_block("league", "createbuttons", "createbuttonsI");
        $t->set_block("league", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdLeague");
        if(!empty($leagueID))
        {
                $mdLeague = new League($db);
                if(!$mdLeague->load($leagueID))
                        session_unregister("mdLeague");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdLeague");
                $mdLeague = null;
        }

echo $mdLeague;

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                if( !($validate->countString($name)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiger 'Name'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !( ($activeyouth=="active") || ($activeyouth=="youth") ) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Altersgruppe'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                $sportIterator = new SportIterator($db);
                $sportIterator->createIterator();
                if( ! $validate->isInIterator($sportID, $sportIterator) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Sportart'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        $sport = new Sport($db);
                        $sport->load($sportID);
                        switch($job)
                        {
                                case "create":
                                        $league = new League($db);
                                        if(!$league->create($name, $activeyouth, $sport))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Liga wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $mdLeague->setDB($db);
                                        if(!$mdLeague->modify($name, $activeyouth, $sport))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Liga wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $mdLeague->setDB($db);
                                        if(!$mdLeague->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Liga wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !isset($mdLeague) )
                // new league
                {
                        $sportIterator  = new SportIterator($db);
                        $sportIterator->createIterator();
                        while($sportIterator->hasNext())
                        {
                                $sport = $sportIterator->next();
                                $t->set_var(array("LEAGUE_SPORT_VALUE" => $sport->getID(),
                                                  "LEAGUE_SPORT_NAME" => $sport->getName(),
                                                  "LEAGUE_SPORT_SELECTED" => $sportID==$sport->getID()?"selected":"" ) );
                        $t->parse("sportI", "sport", true);
                        }

                        $t->set_var(array("LEAGUE_HEADER" => "Neue Liga erstellen",
                                          "LEAGUE_NAME" => $name,
                                          "LEAGUE_ACTIVE_SELECTED" => $activeyouth=="active"?"checked":"",
                                          "LEAGUE_YOUTH_SELECTED" => $activeyouth=="youth"?"checked":"" ) );
                        $t->parse("leagueI", "league", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit sport
                {
                        $sportIterator = new SportIterator($db);
                        $sportIterator->createIterator();
                        while($sportIterator->hasNext())
                        {
                                $sport = $sportIterator->next();
                                $mdSport = $mdLeague->getSport();
                                $t->set_var(array("LEAGUE_SPORT_VALUE" => $sport->getID(),
                                                  "LEAGUE_SPORT_NAME" => $sport->getName(),
                                                  "LEAGUE_SPORT_SELECTED" => isset($sportID)?($sportID==$sport->getID()?"selected":""):($mdSport->getID()==$sport->getID()?"selected":"") ) );
                        $t->parse("sportI", "sport", true);
                        }

                        $t->set_var(array("LEAGUE_HEADER" => "Liga bearbeiten",
                                          "LEAGUE_NAME" => isset($name)?$name:$mdLeague->getName(),
                                          "LEAGUE_ACTIVE_SELECTED" => isset($activeyouth)?($activeyouth=="active"?"checked":""):($mdLeague->getActiveYouth()=="active"?"checked":""),
                                          "LEAGUE_YOUTH_SELECTED" => isset($activeyouth)?($activeyouth=="youth"?"checked":""):($mdLeague->getActiveYouth()=="youth"?"checked":"") ) );
                        $t->parse("leagueI", "league", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
