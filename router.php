<?php

require_once "AnalizesClass.php";

if(isset($_POST['url']) && !empty($_POST['url']) ){
    $analize = new AnalizesClass();
    $analize->load($_POST['url']);

    $data = [
        'fileExists' => $analize->fileExists(),
        'checkHost' => $analize->checkHost(),
        'countHost' => $analize->countHost(),
        'fileSize' => $analize->fileSize(),
        'checkSitemap' => $analize->checkSitemap(),
        'responseCode' => $analize->responseCode(),
        'url' => $analize->url,
    ];

    $view = new ViewClass();
    $view->render($data);
}else{
    header('location: index.php');
}