<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSport.php");
        include("classTeam.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "team.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "team", "teamI");
        $t->set_block("team", "sport", "sportI");
        $t->set_block("team", "createbuttons", "createbuttonsI");
        $t->set_block("team", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdTeam");
        if(!empty($teamID))
        {
                $mdTeam = new Team($db);
                if(!$mdTeam->load($teamID))
                        session_unregister("mdTeam");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdTeam");
                $mdTeam = null;
        }

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
                if( !($validate->countString($shortname)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiger 'Kurzname'");
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
                                        $team = new Team($db);
                                        if(!$team->create($name, $shortname, $activeyouth, $sport))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Mannschaft wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                               case "modify":
                                        $mdTeam->setDB($db);
                                        if(!$mdTeam->modify($name, $shortname, $activeyouth, $sport))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Mannschaft wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                               case "delete":
                                        $mdTeam->setDB($db);
                                        if(!$mdTeam->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Mannschaft wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !isset($mdTeam) || isset($new) )
                // new team
                {
                        $sportIterator  = new SportIterator($db);
                        $sportIterator->createIterator();
                        while($sportIterator->hasNext())
                        {
                                $sport = $sportIterator->next();
                                $t->set_var(array("TEAM_SPORT_VALUE" => $sport->getID(),
                                                  "TEAM_SPORT_NAME" => $sport->getName(),
                                                  "TEAM_SPORT_SELECTED" => $sportID==$sport->getID()?"selected":"" ) );
                        $t->parse("sportI", "sport", true);
                        }

                        $t->set_var(array("TEAM_HEADER" => "Neue Mannschaft erstellen",
                                          "TEAM_NAME" => $name,
                                          "TEAM_SHORTNAME" => $shortname,
                                          "TEAM_ACTIVE_SELECTED" => $activeyouth=="active"?"checked":"",
                                          "TEAM_YOUTH_SELECTED" => $activeyouth=="youth"?"checked":"" ) );
                        $t->parse("teamI", "team", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit team
                {
                        $sportIterator = new SportIterator($db);
                        $sportIterator->createIterator();
                        while($sportIterator->hasNext())
                        {
                                $sport = $sportIterator->next();
                                $mdSport = $mdTeam->getSport();
                                $t->set_var(array("TEAM_SPORT_VALUE" => $sport->getID(),
                                                  "TEAM_SPORT_NAME" => $sport->getName(),
                                                  "TEAM_SPORT_SELECTED" => isset($sportID)?($sportID==$sport->getID()?"selected":""):($mdSport->getID()==$sport->getID()?"selected":"") ) );
                        $t->parse("sportI", "sport", true);
                        }

                        $t->set_var(array("TEAM_HEADER" => "Mannschaft bearbeiten",
                                          "TEAM_NAME" => isset($name)?$name:$mdTeam->getName(),
                                          "TEAM_SHORTNAME" => isset($shortname)?$shortname:$mdTeam->getShortname(),
                                          "TEAM_ACTIVE_SELECTED" => isset($activeyouth)?($activeyouth=="active"?"checked":""):($mdTeam->getActiveYouth()=="active"?"checked":""),
                                          "TEAM_YOUTH_SELECTED" => isset($activeyouth)?($activeyouth=="youth"?"checked":""):($mdTeam->getActiveYouth()=="youth"?"checked":"") ) );
                        $t->parse("teamI", "team", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
