<?php
require_once("../../config.php");
require_once("../Db.php");

use services\Validate;

class Users extends Rest
{
    /**Database object (PDO)*/
    private $db;
    
    public function __construct()
    {
        $this->db = new Db();
    }
    
    /**
     * /null(or false)/hash - check hash in table or hash lifetime.
     * /login - check if there is a login in the table.
     * Return 200 or 400+.
     */
    protected function getUsersByParams()
    {
        list($arrParams['login'],
             $arrParams['hash']
        ) = explode('/', $this->params['params'], 3);
        
        if ( ($arrParams['login'] != 'false' && $arrParams['login'] != 'null')
             &&
             ($arrParams['hash'] == 'false' || $arrParams['hash'] == 'null' ||  $arrParams['hash'] == null) )
        {
            if ( !Validate::checkLogin($arrParams['login']) )
                $this->response( '', 406, '007', true );

            $sql = 'SELECT login FROM rest_users WHERE login = :login';
            $result = $this->db->execute($sql, ['login' => $arrParams['login']]);
            
            if (!$result)
                $this->response( '', 404, '002', true );

            $this->response('');
        }

        if ( ($arrParams['hash'] != 'false' && $arrParams['hash'] != 'null')
             &&
             ($arrParams['login'] == 'false' || $arrParams['login'] == 'null' ||  $arrParams['login'] == null) )
        {
            $sql = 'SELECT hash, time FROM rest_users WHERE hash = :hash';
            $result = $this->db->execute($sql, ['hash' => $arrParams['hash']]);
            
            if (!$result)
                $this->response( '', 404, '002', true );
            
            if ( ((int)$result[0]['time'] + HASH_LIFETIME) < time() )
                $this->response( '', 404, '014', true );

            $this->response('');
        }

        $this->response( '', 404, '015', true );        
    }

    /**
     * Registration - write a new user in table.
     * login | firstname | lastname | password - input
     * Return hash.
     */
    protected function postUsers()
    {
        if ( !Validate::checkLogin($this->params['login']) )
            $this->response( '', 406, '003', true );

        if ( !Validate::checkName($this->params['firstname']) )
            $this->response( '', 406, '004', true );

        if ( !Validate::checkName($this->params['lastname']) )
            $this->response( '', 406, '005', true );

        if ( !Validate::checkPassword($this->params['password']) )
            $this->response( '', 406, '006', true );

        if ( $this->checkLogin($this->params['login']) )
            $this->response( '', 406, '008', true );

        $this->params['password'] = Convert::toMd5($this->params['password']);
        $this->params['hash'] = Convert::toMd5( $this->params['login'] . rand(12345, PHP_INT_MAX) );
        $this->params['time'] = time();

        $sql = 'INSERT INTO rest_users(login, firstname, lastname, password, hash, time)
                VALUES (:login, :firstname, :lastname, :password, :hash, :time)';
        $result = $this->db->execute($sql, $this->params);

        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response( [ ['hash' => $this->params['hash']] ] );
    }

    /**
     * Login - user authorization, if it is in the table, we write a new hash and lifetime.
     * login | password - input
     * Return new hash.
     */
    protected function putUsers()
    {
        if ( !Validate::checkLogin($this->params['login']) )
            $this->response( '', 406, '010', true );

        if ( !Validate::checkPassword($this->params['password']) )
            $this->response( '', 406, '011', true );
        
        $this->params['password'] = Convert::toMd5($this->params['password']);
        
        if ( !$this->checkLogin($this->params['login']) )
            $this->response( '', 406, '012', true );

        if ( !$this->checkPassword($this->params['login'], $this->params['password']) )
            $this->response( '', 406, '013', true );
            
        $arrParams['login'] = $this->params['login'];
        $arrParams['hash'] = Convert::toMd5( $this->params['login'] . rand(12345, PHP_INT_MAX) );
        $arrParams['time'] = time();
        
        $sql = 'UPDATE rest_users SET hash = :hash, time = :time
                WHERE login = :login';
        $result = $this->db->execute($sql, $arrParams);

        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response( [ ['hash' => $arrParams['hash']] ] );
    }

    /**
     * Logout - removing (updating) a hash in tables.
     * /hash - input
     * Return 200 or 400+.
     */
    protected function deleteUsers()
    {
        if ( !$this->checkHash($this->params['params']) )
            $this->response( '', 404, '016', true );

        $newHash = Convert::toMd5( rand(12345, PHP_INT_MAX) );        
        $sql = 'UPDATE rest_users SET hash = "' .$newHash. '" WHERE hash = :hash';
        $result = $this->db->execute($sql, ['hash' => $this->params['params']]);
        
        if (!$result)
            $this->response( '', 404, '002', true );

        $this->response('');
    }

    /** 
     * Check login in the table
     * Return bool
     */
    protected function checkLogin($login)
    {
        $sql = 'SELECT login FROM rest_users WHERE login = :login';
        $result = $this->db->execute($sql, ['login' => $login]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }

    /** 
     * Check password in the table
     * Return bool
     */
    protected function checkPassword($login, $password)
    {
        $sql = 'SELECT password FROM rest_users WHERE login = :login AND password = :password';
        $result = $this->db->execute($sql, ['login' => $login, 'password' => $password]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }

    /** 
     * Check hash in the table
     * Return bool
     */
    protected function checkHash($hash)
    {
        $sql = 'SELECT hash FROM rest_users WHERE hash = :hash';
        $result = $this->db->execute($sql, ['hash' => $hash]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }

    /** 
     * Get time from the table by login
     * Return time or false
     */
    protected function getTime($login)
    {
        $sql = 'SELECT time FROM rest_users WHERE login = :login';
        $result = $this->db->execute($sql, ['login' => $login]);
        
        if (!$result)
            return FALSE;

        return $result;
    }
}

try
{
    $api = new Users;
    $api->table = 'users';
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