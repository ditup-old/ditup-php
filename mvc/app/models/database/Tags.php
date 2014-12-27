<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class Tags{

    public static function searchTags($string){
        /*for a given string this will return tagnames which contain this string
          i.e. "alternative education, education, alter, alternative-education, tive edu" will find "alternative-education" tag
        */
    }

    public static function selectTagsByUsername($username){
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
            $statement=$pdo->prepare('SELECT ua.username, tg.* FROM user_accounts AS ua
                INNER JOIN user_tag AS ut ON ua.user_id = ut.user_id
                INNER JOIN tags AS tg ON ut.tag_id = tg.tag_id
                WHERE ua.username=:un');
            $statement->bindValue(':un',strval($username), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
  // 
            $pdo->commit();

            $data = $rows;
            unset ($pdo);
            return $data;

        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
        }
        unset($pdo);
    }
    
    public static function insertTag($values){
        /** create new tag
         *  $values=[tagname, description, type: (suggested, active)];
         *  tagname must be unique!!!
         */

        $values['type'] = isset($values['type']) ? $values['type'] : 'active';

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
            $statement=$pdo->prepare('INSERT IF tagname UNIQUE INTO tags (tagname, description, type, created) VALUES (:tn, :ds, :ty, UNIX_TIMESTAMP())');
            $statement->bindValue(':tn', strval($values['tagname']), PDO::PARAM_STR);
            $statement->bindValue(':ds', strval($values['description']), PDO::PARAM_STR);
            $statement->bindValue(':ty', strval($values['type']), PDO::PARAM_STR);
            $statement->execute();
            
            $count = $pdo->rowCount();
  // 
            $pdo->commit();

            unset ($pdo);
            
            if($count===1){
                return true;
            }
            else{
                return false;
            }
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
        }
        unset($pdo);
    }
    
    public static function deleteTag($tagname){
        /**
         * delete tag by tagname
         * use with care! all the user_tag and project_tag connections must be deleted before.
         */
        return 'implement!';
    }
    
    public static function insertUserTag($values){
        /** insert data to user_tag: usage: add tag to user
         *  $values=[username: username, tagname: tagname];
         *
         */
        return 'implement!';
    }
    
    public static function deleteUserTag($values){
        /** 
         * delete row from user_tag table: use to remove tag from user
         * $values=[username, tagname]
         *
         */
        return 'implement!';
    }

    public static function insertProjectTag($values){
        /** insert data to project_tag: usage: add tag to project
         *  $values=[url: project url, tagname: tagname];
         *
         */
        return 'implement!';
    }
    
    public static function deleteProjectTag($values){
	/**values: [url:project url, tagname]*****/
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


	    $statement=$pdo->prepare('DELETE FROM project_tag
		WHERE project_id IN 
		    (SELECT project_id FROM projects WHERE url=:url)
		AND tag_id IN
		    (SELECT tag_id FROM tags WHERE tagname=:tn)');
	    $statement->bindValue(':url', strval($values['url']), PDO::PARAM_STR);
	    $statement->bindValue(':tn', strval($values['tagname']), PDO::PARAM_STR);
            $statement->execute();

	    $count = $pdo->rowCount();

            $pdo->commit();

	    if($count>0){
		return true;
	    }
	    else{
		return false;
	    }
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
        }
        unset($pdo);
    }

    public static function selectTagsByProjectUrl($url){
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
            $statement=$pdo->prepare('SELECT ps.url, tg.* FROM projects AS ps
                INNER JOIN project_tag AS pt ON ps.project_id = pt.project_id
                INNER JOIN tags AS tg ON pt.tag_id = tg.tag_id
                WHERE ps.url=:url');
            $statement->bindValue(':url',strval($url), PDO::PARAM_STR);
            $statement->execute();
            
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
  // 
            $pdo->commit();

            $data = $rows;
            unset ($pdo);
            return $data;

        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
      
            $outcome=htmlentities(print_r($e,true));
            echo $outcome;
            // Report errors
        }
        unset($pdo);
    }
}
