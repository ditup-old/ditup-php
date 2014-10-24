<?php
namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

class DbAccess {
    private $db;

    protected function dbConnect(){
        require_once dirname(__FILE__).'/db-login.php';
        $this->db = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
        //****************without these lines it will not catch error and not transaction well. not rollback.********
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    
    protected function dbDisconnect(){
        unset($this->db);
    }

    protected function dbExecute($query, $params=[], $flags=[]){
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $this->db->beginTransaction();
        // 
        try
        {
            // Prepare the statements
            $statement=$this->db->prepare($query);
            foreach($params as $alias => $value){
                $statement->bindValue($alias,strval($value), PDO::PARAM_STR);
            }
            $statement->execute();
                
            $this->db->commit();
        }
        catch(PDOException $e)
        {
            $this->db->rollBack();
            throw new Exception('database problem: ' . $e);
        }
    }
}
