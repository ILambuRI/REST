<?php
class RestServer
{
    protected $method;

    protected function getMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    protected function setFunction()
    {
        switch($this->method)
        {
        case 'GET':
            $this->setMethod('get'.ucfirst($table), explode('/', $path));
            break;
        case 'DELETE':
            $this->setMethod('delete'.ucfirst($table), explode('/', $path));
            break;
        case 'POST':
            $this->setMethod('post'.ucfirst($table), explode('/', $path));
            break;
        case 'PUT':
            $this->setMethod('put'.ucfirst($table), explode('/', $path));
            break;
        default:
            return false;
        }
    }
}
