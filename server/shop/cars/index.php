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
        $result = array('status' => "OK", "msg" => "getCars");
        $this->response($this->converting( [$this->params, $result] ), 200);
        // $this->response($this->converting( [$this->params, $result] ), 200);
    }
    
    protected function getCarsById()
    {
        list($this->params['id'],
             $this->params['mark'],
             $this->params['model'],
             $this->params['year'],
             $this->params['engine'],
             $this->params['color'],
             $this->params['speed']) = explode('/', $this->params['params'], 6);

        $id = $this->params['id'];
        $result = array('status' => "OK", "msg" => "getCarsById($id)");
        $this->response($this->converting( [$this->params, $result] ), 200);
        // $this->response($this->converting( [$this->params, $result] ), 200);
    }

    protected function postCars()
    {
        // $result = $this->params;
        $result = array('status' => "OK", "msg" => "postCars");
        $this->response($this->converting( [$this->params, $result] ), 200);
    }

    protected function putCars()
    {
        // $result = $this->params;
        $result = array('status' => "OK", "msg" => "putCars");
        $this->response($this->converting( [$this->params, $result] ), 200);
    }

    protected function deleteCars()
    {
        // $result = $this->params;
        $result = array('status' => "result", "msg" => "deleteCars");
        $this->response($this->converting( [$this->params, $result] ), 200);
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
    // echo json_encode( ['status' => "Error", "msg" => $e->getMessage()] );
    header( "HTTP/1.1 500 Internal Server Error | Code: " . $e->getMessage() );
    header("Content-Type:text/html");
    echo '500 Internal Server Error | Code: ' .$e->getMessage(). '<br> <a href="http://rest/server/ErrorCodeInformation.html">View Error Code Information here</a>';
    exit;
}