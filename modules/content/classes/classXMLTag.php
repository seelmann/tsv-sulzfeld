<?php

    class XMLTag
    {
        var $depth = 0;
        var $name = "";
        var $attributes = array();
        var $data = "";
        var $key = "";
        var $superTag;

        function XMLTag()
        {
        }

        function setDepth($depth)
        {
            $this->depth = $depth;
        }

        function setName($name)
        {
            $this->name = $name;
        }

        function setAttributes($attributes)
        {
            $this->attributes = $attributes;
        }

        function setData($data)
        {
            $this->data = $data;
        }

        function appendData($data = "")
        {
            $this->data = $this->data . $data;
        }

        function setKey($key)
        {
            $this->key = $key;
        }

        function setSuperTag($tag)
        {
            $this->superTag = $tag;
        }

        function getDepth()
        {
            return $this->depth;
        }

        function getName()
        {
            return $this->name;
        }

        function getAttributes()
        {
            return $this->attributes;
        }

        function getAttribute($key)
        {
            return $this->attributes[$key];
        }

        function getData()
        {
            return $this->data;
        }

        function getKey()
        {
            return $this->key;
        }

        function getSuperTag()
        {
            return $this->superTag;
        }
    }

?>
