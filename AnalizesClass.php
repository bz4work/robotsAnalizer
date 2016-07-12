<?php

/**
 * Created by PhpStorm.
 * User: slim
 * Date: 12.07.2016
 * Time: 14:32
 */
require_once "ViewClass.php";
class AnalizesClass
{
    public $headers;
    public $url;

    private $fileRobots;
    private $_domain;
    private $_filename;
    private $_countHostDirective;

    public function index(){
        //стартовый вид
        $view = new ViewClass();
        return $view->render();
    }

    public function load($url){
        //инициализируем начальные данные
        $this->url = $url;
        $this->headers = get_headers($url);

        if(stripos($this->headers[0],'200')){
            $this->fileRobots = file_get_contents($url);
            $domain = $this->cutDomain($url);
            $this->_domain = $domain;
            $this->_filename = 'robots/'.$domain.'-'.date("d-m-Y_H-i",time()).'.robots.txt.';
            file_put_contents($this->_filename, $this->fileRobots);
        }
    }

    private function cutDomain($url){
        //вырезаем из URL домен
        $pos = (stripos($url,"://"))+3;
        $parts = explode('/',substr($url,$pos));
        return $domain = $parts[0];
    }

    public function fileExists(){
        //если файл robots.txt существует на сервере
        if(stripos($this->headers[0],'200')){
            $file_stat['css'] = 'green';
            $file_stat['text'] = 'OK';
            $file_stat['state'] = 'Файл robots.txt присутствует';
            $file_stat['recommend'] = 'Доработки не требуются';
        //если файл robots.txt НЕ существует на сервере
        }else{
            $file_stat['css'] = 'red';
            $file_stat['text'] = 'Error';
            $file_stat['state'] = 'Файл robots.txt отсутствует';
            $file_stat['recommend'] = 'Программист: Создать файл robots.txt и разместить его на сайте.';
        }
        return $file_stat;
    }

    public function checkHost(){
        if(stripos($this->headers[0],"200")){

            //проверяем указана ли директива Host и сколько раз указана
            preg_match_all("/\bHost\b/", $this->fileRobots, $out1);
            preg_match_all("/\bhost\b/", $this->fileRobots, $out2);
            $sum = count($out1[0]) + count($out2[0]);
            $this->_countHostDirective = $sum;

            //если указана 1 или более раз
            if($sum >= 1){
                $file_stat['css'] = 'green';
                $file_stat['text'] = 'OK';
                $file_stat['state'] = 'Директива Host указана';
                $file_stat['recommend'] = 'Доработки не требуются';
            //если не указана вообще
            }else{
                $file_stat['css'] = 'red';
                $file_stat['text'] = 'Error';
                $file_stat['state'] = 'В файле robots.txt не указана директива Host';
                $file_stat['recommend'] = 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.';
            }
        //если файл robots.txt не существует на сервере
        }else{
            $file_stat['css'] = 'red';
            $file_stat['text'] = 'Error';
            $file_stat['state'] = 'Невозможно проверить, файл robots.txt отсутствует';
            $file_stat['recommend'] = 'Невозможно проверить, файл robots.txt отсутствует';
        }
        return $file_stat;
    }

    public function countHost(){
        if(stripos($this->headers[0],"200")){
            //если в файле указана только 1 директива
            if($this->_countHostDirective == 1){
                $file_stat['css'] = 'green';
                $file_stat['text'] = 'OK';
                $file_stat['state'] = "В файле прописана 1 директива Host";
                $file_stat['recommend'] = 'Доработки не требуются';
            //если в файле указано больше 1й директивы
            }elseif($this->_countHostDirective > 1){
                $file_stat['css'] = 'red';
                $file_stat['text'] = 'Error';
                $file_stat['state'] = "В файле прописано несколько директив Host";
                $file_stat['recommend'] = "Программист: Host указано $this->_countHostDirective раз. Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта";
            //другие случаи, например когда вообще не указана директива host
            }else{
                $file_stat['css'] = 'red';
                $file_stat['text'] = 'Error';
                $file_stat['state'] = 'В файле robots.txt не указана директива Host';
                $file_stat['recommend'] = 'В файле robots.txt не указана директива Host';
            }
        //если не существует файл robots.txt
        }else{
            $file_stat['css'] = 'red';
            $file_stat['text'] = 'Error';
            $file_stat['state'] = 'Невозможно проверить, файл robots.txt отсутствует';
            $file_stat['recommend'] = 'Невозможно проверить, файл robots.txt отсутствует';
        }
        return $file_stat;
    }

    public function fileSize(){
        if(stripos($this->headers[0],"200")){
            //получаем разер файла в КБ типа float, 2 знака после запятой
            $size = round(filesize($this->_filename)/1024,2);
            if($size < 32){
                $file_stat['css'] = 'green';
                $file_stat['text'] = 'OK';
                $file_stat['state'] = "Размер файла robots.txt составляет $size Кб, что находится в пределах допустимой нормы";
                $file_stat['recommend'] = 'Доработки не требуются';
            }else{
                $file_stat['css'] = 'red';
                $file_stat['text'] = 'Error';
                $file_stat['state'] = "Размера файла robots.txt составляет __, что превышает допустимую норму";
                $file_stat['recommend'] = 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб';
            }
        //если не существует файл robots.txt
        }else{
            $file_stat['css'] = 'red';
            $file_stat['text'] = 'Error';
            $file_stat['state'] = 'Невозможно проверить, файл robots.txt отсутствует';
            $file_stat['recommend'] = 'Невозможно проверить, файл robots.txt отсутствует';
        }
        return $file_stat;
    }

    public function checkSitemap(){
        if(stripos($this->headers[0],"200")){
            //$data = file_get_contents($this->_filename);
            preg_match_all("/\bSitemap\b/", $this->fileRobots, $out);
            $count = (int)$out[0];

            //если в файле не указана Sitemap
            if($count === 0){
                $file_stat['css'] = 'red';
                $file_stat['text'] = 'Error';
                $file_stat['state'] = 'В файле robots.txt не указана директива Sitemap';
                $file_stat['recommend'] = 'Программист: Добавить в файл robots.txt директиву Sitemap';
            //если указана Sitemap
            }else{
                $file_stat['css'] = 'green';
                $file_stat['text'] = 'OK';
                $file_stat['state'] = 'Директива Sitemap указана';
                $file_stat['recommend'] = 'Доработки не требуются';
            }
        //если не существует файл robots.txt
        }else{
            $file_stat['css'] = 'red';
            $file_stat['text'] = 'Error';
            $file_stat['state'] = 'Невозможно проверить, файл robots.txt отсутствует';
            $file_stat['recommend'] = 'Невозможно проверить, файл robots.txt отсутствует';
        }
        return $file_stat;
    }

    public function responseCode(){
        if(isset($this->headers)){

            //получаем код ответа
            preg_match_all("/\d{3}/", $this->headers[0], $out);
            $code = $out[0][0];

            //если код ответа = 200
            if($code === "200"){
                $file_stat['css'] = 'green';
                $file_stat['text'] = 'OK';
                $file_stat['state'] = 'Файл robots.txt отдаёт код ответа сервера 200';
                $file_stat['recommend'] = 'Доработки не требуются';
            //любые другие коды ответов
            }else{
                $file_stat['css'] = 'red';
                $file_stat['text'] = 'Error';
                $file_stat['state'] = "При обращении к файлу robots.txt сервер возвращает код ответа $code";
                $file_stat['recommend'] = 'Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращал код ответа 200';
            }
        }
        return $file_stat;
    }

}