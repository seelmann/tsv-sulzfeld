<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSport.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "sport.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "sport", "sportI");
        $t->set_block("sport", "createbuttons", "createbuttonsI");
        $t->set_block("sport", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdSport");
        if(!empty($sportID))
        {
                $mdSport = new Sport($db);
                if(!$mdSport->load($sportID))
                        session_unregister("mdSport");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdSport");
                $mdSport = null;
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

                if( $errors==0 )
                {
                        switch($job)
                        {
                                case "create":
                                        $sport = new Sport($db);
                                        if(!$sport->create($name))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Sportart wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $mdSport->setDB($db);
                                        if(!$mdSport->modify($name))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Sportart wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $mdSport->setDB($db);
                                        if(!$mdSport->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Sportart wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !is_object($mdSport) )
                // new season
                {
                        $t->set_var(array("SPROT_HEADER" => "Neue Sportart erstellen",
                                          "SPORT_NAME" => $name) );
                        $t->parse("sportI", "sport", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit season
                {
                        $t->set_var(array("SPORT_HEADER" => "Sportart bearbeiten",
                                          "SPORT_NAME" => isset($name)?$name:$mdSport->getName() ) );
                        $t->parse("sportI", "sport", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
