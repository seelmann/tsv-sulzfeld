<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classSeason.php");
        include("classTemplate.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "season.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "season", "seasonI");
        $t->set_block("season", "createbuttons", "createbuttonsI");
        $t->set_block("season", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdSeason");
        if(!empty($seasonID))
        {
                $mdSeason = new Season($db);
                if(!$mdSeason->load($seasonID))
                        session_unregister("mdSeason");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdSeason");
                $mdSeason = null;
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

                $dateFrom = $validate->transformDate($from);
                if( !$validate->checkDate($dateFrom) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Datum in 'Von:'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                $dateTo = $validate->transformDate($to);
                if( !$validate->checkDate($dateTo) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiges Datum in 'Bis:'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        switch($job)
                        {
                                case "create":
                                        $season = new Season($db);
                                        if(!$season->create($name, $dateFrom, $dateTo))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Saison wurde erstellt");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        $mdSeason->setDB($db);
                                        if(!$mdSeason->modify($name, $dateFrom, $dateTo))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Saison wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        $mdSeason->setDB($db);
                                        if(!$mdSeason->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "Saison wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !is_object($mdSeason) )
                // new season
                {
                        $t->set_var(array("SEASON_HEADER" => "Neue Saison erstellen",
                                          "SEASON_NAME" => $name,
                                          "SEASON_FROM" => $from,
                                          "SEASON_TO" => $to ) );
                        $t->parse("seasonI", "season", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit season
                {
                        $t->set_var(array("SEASON_HEADER" => "Saison bearbeiten",
                                          "SEASON_NAME" => isset($name)?$name:$mdSeason->getName(),
                                          "SEASON_FROM" => isset($from)?$from:$mdSeason->getFrom(),
                                          "SEASON_TO" => isset($to)?$to:$mdSeason->getTo() ) );
                        $t->parse("seasonI", "season", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
