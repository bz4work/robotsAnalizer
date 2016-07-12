<?php

class ViewClass
{

    public function render($data = null){
        ob_start();
        if (isset($data)) {
            extract($data);
        }
        include "html/content.html";
    }

}