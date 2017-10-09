<?php
class Db
{
    protected $dbh;
    protected $pdoError;

    /**
     * Connecting to database.
     */
    function __construct()
    {
        try
        {
            $this->dbh = new PDO('mysql:host=' .M_HOST. ';dbname=' . M_DB, M_USER, M_PASS);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            /* Send Email to admin */
            $this->pdoError = 'Problems with the PDO: ' .$e->getMessage(). ' Code: ' .$e->getCode(). ' On line:' . $e->getLine();
            exit($this->pdoError);
        }
    }

    /**
     * Run a query (with bind parameters).
     * Return assoc array when SELECT or count rows
     * when INSERT, DELETE and UPDATE.
     */
    public function execute($query, array $arrParams)
    {
        if (!$this->dbh)
            return false;
        
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
        if ($regExp)
        {
            if ($rows = $sth->rowCount())
            {
                return $rows;
            }
            else
            {
                return false;
            }
        }

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}