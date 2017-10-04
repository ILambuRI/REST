<?php
require_once("Rest.php");

class Cars extends Rest {

    protected $data = "";
    private $dbh;
    
    protected function getCars()
    {
        $result = array('status' => "OK", "msg" => "getCars");
        $this->response($this->toJson( [$this->params, $result] ), 200);
        // $this->response($this->toJson( [$this->params, $result] ), 200);
    }
    
    protected function getCarsById()
    {
        $id = $this->params['id'];
        $result = array('status' => "OK", "msg" => "getCarsById($id)");
        $this->response($this->toJson( [$this->params, $result] ), 200);
        // $this->response($this->toJson( [$this->params, $result] ), 200);
    }

    protected function postCars()
    {
        // $result = $this->params;
        $result = array('status' => "OK", "msg" => "postCars");
        $this->response($this->toJson( [$this->params, $result] ), 200);
    }

    protected function putCars()
    {
        // $result = $this->params;
        $result = array('status' => "OK", "msg" => "putCars");
        $this->response($this->toJson( [$this->params, $result] ), 200);
    }

    protected function deleteCars()
    {
        // $result = $this->params;
        $result = array('status' => "result", "msg" => "deleteCars");
        $this->response($this->toJson( [$this->params, $result] ), 200);
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
    echo json_encode( ['status' => "Error", "msg" => $e->getMessage()] );
}