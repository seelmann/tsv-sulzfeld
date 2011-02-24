<?php
    ini_set("include_path","./:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/content/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/image/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/common/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/auth/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/leaguemanager/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/date/classes:/homepages/25/d358692369/htdocs/tsv-sulzfeld/modules/news/classes");
        include_once("classValidation.php");
        include("classNews.php");
        include("classTemplate.php");
        include("../classPermcheck.php");

        include("auth.php");

        // Template starten
        $t = new Template("./templates/");
        $t->set_file(array("page" => "news.ihtml"));
        $t->set_block("page", "error", "errorI");
        $t->set_block("page", "news", "newsI");
        $t->set_block("news", "createbuttons", "createbuttonsI");
        $t->set_block("news", "modifybuttons", "modifybuttonsI");
        $t->set_block("page", "success", "successI");

        session_register("mdNews");
        if(!empty($newsID))
        {
                $mdNews = new News($db);
                if(!$mdNews->load($newsID))
                        session_unregister("mdNews");
        }
        if(!empty($new) && ($new=="new"))
        {
                session_unregister("mdNews");
                $mdNews = null;
        }

        if(isset($job))
        // perform action
        {
                $errors = 0;

                // validate fields
                $validate = new Validation();
                if( !($validate->countString($head)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültige 'Überschrift'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }
                if( !($validate->countString($text)>3) )
                {
                        $t->set_var("ERROR_TEXT", "Ungültiger 'Text'");
                        $t->parse("errorI", "error", true);
                        unset($job);
                        $errors++;
                }

                if( $errors==0 )
                {
                        $permcheck = new Permcheck($db, $error);

                        switch($job)
                        {
                                case "create":
                                        if( $permcheck->hasUserNewsCreatePermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $news = new News($db);
                                        if(!$news->create($head, $text))
                                                $error->printErrorPage("Fehler beim Erstellen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "News wurde eingetragen");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "modify":
                                        if( $permcheck->hasUserNewsModifyPermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdNews->setDB($db);
                                        if(!$mdNews->modify($head, $text))
                                                $error->printErrorPage("Fehler beim Ändern aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "News wurde geändert");
                                        $t->parse("successI", "success", true);
                                        break;

                                case "delete":
                                        if( $permcheck->hasUserNewsDeletePermission() == false )
                                        {
                                                $error->printAccessDenied();
                                                exit;
                                        }
                                        $mdNews->setDB($db);
                                        if(!$mdNews->delete())
                                                $error->printErrorPage("Fehler beim Löschen aufgetreten");
                                        $t->set_var("SUCCESS_TEXT", "News wurde gelöscht");
                                        $t->parse("successI", "success", true);
                                        break;
                        }
                }
        }

        if(!isset($job))
        // print formular
        {
                if( !is_object($mdNews) )
                // new
                {
                        $t->set_var(array("NEWS_HEADER" => "Neue News eintragen",
                                          "NEWS_HEAD" => $head,
                                          "NEWS_TEXT" => $text
                                          ) );
                        $t->parse("newsI", "news", true);
                        $t->parse("createbuttonsI", "createbuttons", true);
                }
                else
                // edit
                {
                        $t->set_var(array("NEWS_HEADER" => "News bearbeiten",
                                          "NEWS_HEAD" => isset($head)?$head:$mdNews->getHead(),
                                          "NEWS_TEXT" => isset($text)?$text:$mdNews->getText()
                                           ) );
                        $t->parse("newsI", "news", true);
                        $t->parse("modifybuttonsI", "modifybuttons", true);
                }
        }

        // Template parsen
        $t->parse("page", "page");

        // HTML schreiben
        $t->p("page");
?>
