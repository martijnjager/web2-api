<?php
class Database
{
    private $connection, $query;

    /**
     * @param $query
     * @param array $values
     * @return $this
     * @throws Exception
     */
    public function runQuery($query, array $values = [])
    {
        try {
            // Creating the query here instead of the constructor
            // So we can close the connection later and don't have an open connection longer than needed
            $this->connection = new PDO('mysql:host=localhost;dbname=dbi418801', 'root', '');

            // Telling the connection to display errors
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparing the query
            $this->query = $this->connection->prepare($query);

            // Because adding parameters to the query is optional, we first need to check if there are entries in the array
            // If there are elements in the array, we loop through them to easily bind them in the SQL query
            // By checking the type of the value we can specify to the connection what the value actually is
            if(count($values) > 0){
                foreach ($values as $key => $value) {
                    $this->query->bindValue($key, $value, is_int($value) || is_double($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }

            // Executing the prepared query
            $this->query->execute();
        }
        catch(PDOException $exception){

            // In case something goes wrong anywhere we throw a PDOException error with the error information.
            throw new Exception($query . "\n\n\n" . $exception->getMessage());
        }

        // We return the class back so we can reuse it to call the function getResult
        return $this;
    }

    public function getResult()
    {
        // This function does not know what $this->query is, because in the constructor this wasn't specified.
        // To prevent any mistakes from happening, like a null object reference, we first check if $this->query is not null
        if($this->query !== null){
            $data = $this->query->fetchAll(PDO::FETCH_ASSOC);
            $this->CloseConnection();

            return $data;
        }

        return new PDOException("The database class does not have a PDO instance!");
    }

    private function CloseConnection()
    {
        // Here we set the fields to null to close the connection, we no longer need them.
        $this->query = null;
        $this->connection = null;
    }
}
