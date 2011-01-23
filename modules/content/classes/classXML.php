<?php
    include("classXMLTag.php");

    // bug
    // $global_elements = array();;

    class XML
    {
        var $parser;
        var $elements = array();

        var $elementCounter = array();
        var $depth = 0;
        var $key;
        var $superKey;

        var $c=0;

        function XML()
        {
            // bug
            // global $global_elements;
            // $this->elements = &$global_elements;

            $this->parser = xml_parser_create();
        }

        function init()
        {
        }

        function parse($data)
        {
            $this->parser = xml_parser_create();
            xml_set_object($this->parser, &$this);
            xml_set_element_handler($this->parser,"xmlTagOpen","xmlTagClose");

            xml_set_character_data_handler($this->parser,"xmlData");
            xml_parse($this->parser, $data);
            xml_parser_free($this->parser);
        }

        function xmlTagOpen($parser,$name,$attributes)
        {
            // increment depth
            $this->depth ++;

            // check wheather elementCounter has already been used else initialize it
            if(!isset($this->elementCounter[$this->depth]))
                $this->elementCounter[$this->depth] = 0;

            // increment elementCounter for the recent depth
            $this->elementCounter[$this->depth] ++;

            // create the recent key
            $this->key = "e_";
            for ($i=1; $i<=$this->depth; $i++)
                $this->key = $this->key . sprintf("%03d_", $this->elementCounter[$i]);
            $this->key = substr ($this->key, 0, -1);

            // create the key of the super tag
            $this->superKey = substr ($this->key, 0, -4);

            // create new tag object and save it to the element array
            $tag = new XMLTag();
            $tag->setName($name);
            $tag->setAttributes($attributes);
            $tag->setDepth($this->depth);
            $tag->setKey($this->key);
            $tag->setSuperTag($this->elements[$this->superKey]);
            $this->elements[$this->key] = $tag;


            // call the tagOpen Function of the subclass
            $this->tagOpen($this->elements[$this->key]);
        }

        function xmlData($parser,$data)
        {
            $this->elements[$this->key]->appendData($data);
        }

        function xmlTagClose($parser,$name)
        {
            // create the recent key
            $this->key = "e_";
            for ($i=1; $i<=$this->depth; $i++)
                $this->key = $this->key . sprintf("%03d_", $this->elementCounter[$i]);
            $this->key = substr ($this->key, 0, -1);

            $this->tagData($this->elements[$this->key]);
            $this->tagClose($this->elements[$this->key]);

            // reset elementCounter one level above
            $this->elementCounter[$this->depth + 1] = 0;

            // decrement depth
            $this->depth --;
        }

    } // class XML

 ?>
