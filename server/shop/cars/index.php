<?php
require_once("../../config.php");
require_once("../Db.php");

class Cars extends Rest {

    protected $data = "";
    private $dbh;

    public function __construct()
    {
        $this->dbh = new Db();
    }
    
    protected function getCars()
    {
        $this->response( [$this->params] );
    }
    
    protected function getCarsById()
    {
        // list($this->params['id'],
        //      $this->params['mark'],
        //      $this->params['model'],
        //      $this->params['year'],
        //      $this->params['engine'],
        //      $this->params['color'],
        //      $this->params['speed']) = explode('/', $this->params['params'], 6);

        // $id = $this->params['id'];
        // $result = array('status' => "OK", "msg" => "getCarsById($id)");
        // $this->response( '', 406, ERROR_HEADER_CODE . '001 ' . __METHOD__ , true );
        $this->response([$this->params]);
    }

    protected function postCars()
    {
        // $result = $this->params;
        // $result = array('status' => "OK", "msg" => "postCars");
        $this->response([$this->params]);
    }

    protected function putCars()
    {
        // $result = $this->params;
        // $result = array('status' => "OK", "msg" => "putCars");
        $this->response([$this->params]);
    }

    protected function deleteCars()
    {
        // $result = $this->params;
        // $result = array('status' => "result", "msg" => "deleteCars");
        $this->response([$this->params]);
    }

}

try
{
    $api = new Cars;
    $api->table = 'cars';
    $api->play();
}
catch (Exception $e)
{
    header( "HTTP/1.1 500 Internal Server Error | " . ERROR_HEADER_CODE . $e->getMessage() );
    header("Content-Type:text/html");

    $string = ERROR_HTML_TEXT;
    ksort( $patterns = ['/%STATUS_CODE%/', '/%ERROR_DESCRIPTION%/', '/%CODE_NUMBER%/'] );
    ksort( $replacements = [500, 'Internal Server Error', ERROR_HEADER_CODE . $e->getMessage()] );
    echo preg_replace($patterns, $replacements, $string);

    exit;
}
