<?php
/**
 * Created by PhpStorm.
 * User: slim
 * Date: 12.07.2016
 * Time: 0:55
 */
$url = $_POST["url"];

if(isset($url)){
    $data = get_headers($url);
}

if(isset($data)){
    foreach ($data as $row) {
        if (stripos($row, '200')) {
            $stat = $row;
        }else{
            $stat = $data[0];
        }
    }
}

print_r($_POST);

echo isset($stat)?$stat:"";