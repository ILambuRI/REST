<?php
require_once("../../config.php");
require_once("../Db.php");

class Cars extends Rest
{
    /**Database object (PDO)*/
    private $db;

    public function __construct()
    {
        $this->db = new Db();
    }
    
    /**
     * Get the whole table of cars
     */
    protected function getCars()
    {
        $sql = 'SELECT id, mark, model, year, engine, color, speed, price FROM rest_cars';
        $result = $this->db->execute($sql,[]);

        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response($result);
    }
    
    /**
     * Get all cars by parameters.
     * id/mark/model/year/engine/color/speed/price - input.
     * FALSE or NULL - that would not take this.
     * Example: false/null/X6/false/false/white/300
     */
    protected function getCarsByParams()
    {
        list($arrParams['id'],
             $arrParams['mark'],
             $arrParams['model'],
             $arrParams['year'],
             $arrParams['engine'],
             $arrParams['color'],
             $arrParams['speed'],
             $arrParams['price']
        ) = explode('/', $this->params['params'], 9);

        $sql = 'SELECT id, mark, model, year, engine, color, speed, price
                FROM rest_cars WHERE ';

        $cnt = 0;
        foreach ($arrParams as $key => $value)
        {
            if ($value == 'null' or $value == null or $value == 'false')
            {
                unset($arrParams[$key]);
                continue;
            }

            if ($cnt > 0)
                $sql .= ' AND ' .$key. ' = :' .$key;
            else
                $sql .= $key. ' = :' .$key;

            $cnt++;
        }

        if (count($arrParams) == 0)
            $this->response( '', 406, '017', true );
        
        $result = $this->db->execute($sql, $arrParams);

        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response($result);
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
