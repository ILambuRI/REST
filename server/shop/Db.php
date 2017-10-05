<?php
class Db
{
    protected $dbh;
    protected $pdoError;

    function __construct()
    {
        try
        {
            $this->dbh = new PDO('mysql:host=' .M_HOST. ';dbname=' . M_DB, M_USER, M_PASS);
        }
        catch (PDOException $e)
        {
            /* Send Email to admin */
            $pdoError = 'Problems with the PDO: ' .$e->getMessage(). ' Code: ' .$e->getCode(). ' On line:' . $e->getLine();
        }
    }

    public function execute($query, array $arrParams)
    {
        if (!$this->dbh) return new SoapFault("DB_ERROR", ERR_TO_CLIENT_NO_CONNECTION);
        
        $sth = $this->dbh->prepare($query);
        if (count($arrParams))
        {
            foreach ($arrParams as $key => &$value)
            {
                $sth->bindParam(':' . $key, $value);
            }
        }

        $sth->execute();

        $regExp = preg_match('/(^INSERT\s{1}INTO)|(^DELETE\s)|(^UPDATE\s)/', $query);
        
        if ($regExp) {
            if ($rows = $sth->rowCount())
            {
                return ['success' => $rows];
            }
            else
            {
                return new SoapFault("DB_ERROR", ERR_TO_CLIENT_NO_WRITING);
            }
        }

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}