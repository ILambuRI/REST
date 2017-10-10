<?php
require_once("../../config.php");
require_once("../Db.php");

class Orders extends Rest
{
    /**Database object (PDO)*/
    private $db;
    
    public function __construct()
    {
        $this->db = new Db();
    }
    
    /**
     * All orders for admin
     */
    protected function getOrders()
    {
        $sql = 'SELECT
                    rest_orders.id,
                    rest_cars.mark,
                    rest_cars.model,
                    rest_cars.year,
                    rest_cars.engine,
                    rest_cars.color,
                    rest_cars.speed,
                    rest_cars.price,
                    rest_users.login,
                    rest_users.firstname,
                    rest_users.lastname,
                    rest_users.password,
                    rest_users.hash,
                    rest_users.time,
                    rest_orders.payment,
                    rest_orders.status
                FROM rest_orders
                    INNER JOIN rest_cars
                    ON rest_orders.id_cars = rest_cars.id
                    INNER JOIN rest_users
                    ON rest_orders.id_users = rest_users.id
                GROUP BY rest_orders.id';

        $result = $this->db->execute($sql,[]);
        
        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response($result);
    }
    
    /**
     * Receive all orders through a unique users hash.
     * /hash - input.
     * Return all users orders.
     */
    protected function getOrdersByParams()
    {
        $hash = $this->params['params'];

        $sql = 'SELECT
                    rest_orders.id,
                    rest_cars.mark,
                    rest_cars.model,
                    rest_cars.year,
                    rest_cars.engine,
                    rest_cars.color,
                    rest_cars.speed,
                    rest_cars.price,
                    rest_users.login,
                    rest_users.firstname,
                    rest_users.lastname,
                    rest_orders.payment,
                    rest_orders.status
                FROM rest_orders
                    INNER JOIN rest_cars
                    ON rest_orders.id_cars = rest_cars.id
                    INNER JOIN rest_users
                    ON rest_orders.id_users = rest_users.id
                WHERE rest_users.hash = :hash
                GROUP BY rest_orders.id';

        $result = $this->db->execute($sql, ['hash' => $hash]);

        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response($result);
    }

    /**
     * Record a new order in the table.
     * hash | idCars | payment - input.
     */
    protected function postOrders()
    {
        $arrParams['id_users'] = $this->getUserIdByHash( $this->params['hash'] )[0]['id'];

        if ( !$this->checkCarsId($this->params['idCars']) )
            $this->response( '', 406, '019', true );

        $arrParams['id_cars'] = $this->params['idCars'];

        if ($this->params['payment'] == 'cash' or $this->params['payment'] == 'card')
            $arrParams['payment'] = $this->params['payment'];
        else
            $this->response( '', 406, '020', true );

        
        $sql = 'INSERT INTO rest_orders (id_cars, id_users, payment, status)
                VALUES (:id_cars, :id_users, :payment, 0)';
        $result = $this->db->execute($sql, $arrParams);
        
        if (!$result)
            $this->response( '', 404, '002', true );
        
        $this->response('');
    }

    /**
     * Change of orders status. "0"(Not delivered) | "1"(Delivered).
     * hash | idOrders | status - input
     */
    protected function putOrders()
    {
        if ( !$this->checkOrdersId($this->params['idOrders']) )
            $this->response( '', 406, '021', true );
        
        $arrParams['id'] = $this->params['idOrders'];
        $arrParams['id_users'] = $this->getUserIdByHash( $this->params['hash'] )[0]['id'];
        
        if ( !$this->checkOrdersAndUsersId($arrParams['id'], $arrParams['id_users']) )
            $this->response( '', 406, '023', true );

        if ($this->params['status'] == '1' or $this->params['status'] == '0')
            $arrParams['status'] = $this->params['status'];
        else
            $this->response( '', 406, '022', true );

        $sql = 'UPDATE rest_orders
                SET status = :status
                WHERE id = :id
                AND id_users = :id_users';
        $result = $this->db->execute($sql, $arrParams);
        
        if (!$result)
            $this->response( '', 404, '002', true );

            
        $this->response('');
    }

    /** 
     * Get id by hash from the users table
     * Return user id
     */
    protected function getUserIdByHash($hash)
    {
        $sql = 'SELECT id FROM rest_users WHERE hash = :hash';
        $result = $this->db->execute($sql, ['hash' => $hash]);
        
        if (!$result)
            $this->response( '', 406, '018', true );
        
        return $result;
    }

    /** 
     * Check id in the cars table
     * Return bool
     */
    protected function checkCarsId($id)
    {
        $sql = 'SELECT id FROM rest_cars WHERE id = :id';
        $result = $this->db->execute($sql, ['id' => $id]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }

    /** 
     * Check id in the orders table
     * Return bool
     */
    protected function checkOrdersId($id)
    {
        $sql = 'SELECT id FROM rest_orders WHERE id = :id';
        $result = $this->db->execute($sql, ['id' => $id]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }

    /** 
     * Check orders id and users id in the orders table
     * Return bool
     */
    protected function checkOrdersAndUsersId($orderId, $userId)
    {
        $sql = 'SELECT id FROM rest_orders
                WHERE id = :id
                AND id_users = :id_users';
        $result = $this->db->execute($sql, ['id' => $orderId, 'id_users' => $userId]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
}

try
{
    $api = new Orders;
    $api->table = 'orders';
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