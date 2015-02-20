<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;    //to be removed when DbAccess will be finished
use PDOException;   //to be removed when DbAccess will be finished
use Exception;

require_once dirname(__FILE__).'/db-login.php';
require_once dirname(__FILE__).'/DbAccess.php';

class Projects extends DbAccess
{
    public static function updateUrl($old_url, $new_url){
        if(true){
            $pdo = self::newPDO();
            $pdo->beginTransaction();
            
            try{
                // Prepare the statements
                $statement=$pdo->prepare('UPDATE projects SET url=:nurl WHERE url=:url');

                $statement->bindValue(':nurl' ,strval($new_url), PDO::PARAM_STR);
                $statement->bindValue(':url' ,strval($old_url), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);

                $pdo->commit();

                unset($pdo);

                return true;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                //throw new Exception('database problem: ' . $e);
                unset($pdo);
                return false;
                // Report errors
            }
            unset($pdo);
            return false;
        }
        else{
            return false;
        }
    }

    public static function updateProject($url, Array $values){
        if(true){
            $pdo = self::newPDO();
            $pdo->beginTransaction();
            
            try{
                // Prepare the statements
                $statement=$pdo->prepare('UPDATE projects SET projectname=:pn, type=:type, subtitle=:st, description=:de WHERE url=:url');

                $statement->bindValue(':pn' ,strval($values['ditname']), PDO::PARAM_STR);
                $statement->bindValue(':type' ,strval($values['type']), PDO::PARAM_STR);
                $statement->bindValue(':st' ,strval($values['subtitle']), PDO::PARAM_STR);
                $statement->bindValue(':de' ,strval($values['description']), PDO::PARAM_STR);
                $statement->bindValue(':url' ,strval($url), PDO::PARAM_STR);
                $statement->execute();
                unset($statement);

                $pdo->commit();

                unset($pdo);

                return true;
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                //throw new Exception('database problem: ' . $e);
                unset($pdo);
                return false;
                // Report errors
            }
            unset($pdo);
            return false;
        }
        else{
            return false;
        }
    }

    public static function insertProject(Array $values){
        if(isset($values, $values['ditname'], $values['url'], $values['subtitle'], $values['description'], $values['creator'])){
            $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
        
            //****************without these lines it will not catch error and not transaction well. not rollback.********
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
            $pdo->beginTransaction();
                // 
            try
            {
                // Prepare the statements
                $statement=$pdo->prepare('INSERT INTO projects (projectname, url, subtitle, description, created, type) VALUES (:pn, :ur, :st, :de, UNIX_TIMESTAMP(), :type)');
                $statement->bindValue(':pn' ,strval($values['ditname']), PDO::PARAM_STR);
                $statement->bindValue(':ur' ,strval($values['url']), PDO::PARAM_STR);
                $statement->bindValue(':st' ,strval($values['subtitle']), PDO::PARAM_STR);
                $statement->bindValue(':de' ,strval($values['description']), PDO::PARAM_STR);
                $statement->bindValue(':type' ,strval($values['type']), PDO::PARAM_STR);
                $statement->execute();
                $project_id = $pdo->lastInsertId();
                unset($statement);

                $statement=$pdo->prepare('SELECT user_id FROM user_accounts WHERE username = :un');
                $statement->bindValue(':un' ,strval($values['creator']), PDO::PARAM_STR);
                $statement->execute();
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                unset($statement);

                $statement=$pdo->prepare('INSERT INTO project_user (user_id, project_id, relationship) VALUES (:ui, :pi, \'admin\')');
                $statement->bindValue(':ui', strval($rows[0]['user_id']), PDO::PARAM_STR);
                $statement->bindValue(':pi', strval($project_id), PDO::PARAM_STR);
                $statement->execute();
      //
                $pdo->commit();
            }
            catch(PDOException $e)
            {
                $pdo->rollBack();
                throw new Exception('database problem: ' . $e);
                // Report errors
            }
            unset($pdo);
        }
        else{
            if(!isset($values)) throw new Exception ('Dits Error: array of values must be provided!');
            elseif(!isset($values['username'])) throw new Exception ('Users::updateVerifyCode Error: username must be provided!');
            elseif(!isset($values['email'])) throw new Exception ('Users::updateVerifyCode Error: email must be provided!');
            elseif(!isset($values['verify_code'])) throw new Exception ('Users::updateVerifyCode Error: verification code must be provided!');
            elseif(!isset($values['delete_code'])) throw new Exception ('Users::updateVerifyCode Error: deletion code must be provided!');
            else throw new Exception ('general exception, debugging will be needed');
        }
    }

    public static function selectProjectByUrl ($url){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
        //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
            // 
        try
        {
            // Prepare the statements
            $statement=$pdo->prepare('SELECT * FROM projects WHERE url=:url');
            $statement->bindValue(':url',strval($url), PDO::PARAM_STR);
            $statement->execute();
                
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $rows;
      //
            $pdo->commit();
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            throw new Exception('database problem: ' . $e);
            // Report errors
        }
        unset($pdo);
        // echo $data;
        return isset($data[0])?$data[0]:false;
    }

    public static function selectProjectByName ($projectname){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
        //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
            // 
        try
        {
            // Prepare the statements
            $statement=$pdo->prepare('SELECT * FROM projects WHERE projectname=:pn');
            $statement->bindValue(':pn',strval($projectname), PDO::PARAM_STR);
            $statement->execute();
                
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $rows;
      //
            $pdo->commit();
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            throw new Exception('database problem: ' . $e);
            // Report errors
        }
        unset($pdo);
        // echo $data;
        return isset($data[0])?$data[0]:false;
    }
    

    /**
     * getRelations returns Array of relations between $username (user) and $url (project)
     * @param String $url
     * @param String $username
     * @return Array of relationships
     */
    public static function getRelations ($url, $username){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
        //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
            // 
        try
        {
            // Prepare the statements
            
            $statement = $pdo->prepare('SELECT relationship FROM project_user
                WHERE project_id IN
                    (SELECT project_id FROM projects WHERE url=:url)
                AND user_id IN
                    (SELECT user_id FROM user_accounts WHERE username=:un)'
            );
            $statement->bindValue(':url',strval($url), PDO::PARAM_STR);
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo->commit();
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            throw new Exception('database problem: ' . $e);
            // Report errors
        }
        unset($pdo);
        // echo $data;
        $ret = Array();
        foreach($rows as $row){
            //print_r($row);
            $ret[]=$row['relationship'];
        }
        return $ret;
    }
    
    /**
     * getPeople. input = url of project, output = array of people who are related to the project [[username, relation, ?time],[username, relation, ?time],[]]
     *
     */
    public static function getPeople($url, $flag='ALL'){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
        //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        $pdo->beginTransaction();
        
        $ret = [];
        try
        {   
            /*******
            1. provided url: select project_id from projects
            2. provided project_id: select all user_id and relationship of this project_id from project_user
            3. for each user_id select username from user_accounts and to return array add [username, relationship].
            it can be done much simpler way, i'm sure. by one query with sql joins.
            
            *******/
            // Prepare the statements

            if ($flag==='ALL'){
                $flag_query='TRUE';
            }
            elseif($flag==='AWAIT_MEMBERS'){
                $flag_query='pu.relationship=\'await-member\'';
            }
            else{
                $flag_query='FALSE';
            }

            //print_r($flag_query);

            $statement = $pdo->prepare('SELECT ua.username AS username, pu.relationship AS relationship FROM project_user AS pu
            INNER JOIN user_accounts AS ua ON ua.user_id=pu.user_id
                WHERE pu.project_id IN
                    (SELECT project_id FROM projects WHERE url=:url)
                AND '.$flag_query);
            $statement->bindValue(':url',strval($url), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            $pdo->commit();
            unset($pdo);
            return $rows;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            unset($pdo);
            throw new Exception('database problem: ' . $e);
            // Report errors
        }
    }

    private static function newPDO(){
        $pdo = new PDO('mysql:host='.Login\HOSTNAME.';dbname='. Login\DATABASE .';charset=utf8', Login\USERNAME, Login\PASSWORD);
    
    //****************without these lines it will not catch error and not transaction well. not rollback.********
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // Start the transaction. PDO turns autocommit mode off depending on the driver, you don't need to implicitly say you want it off
        return $pdo;
    }
}
