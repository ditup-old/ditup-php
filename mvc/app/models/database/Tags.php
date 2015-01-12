<?php

namespace Mrkvon\Ditup\Model\Database;

use PDO;
use PDOException;
use Exception;

require_once dirname(__FILE__).'/db-login.php';

class Tags{
    
    /*****
    
    public static function searchTags($string) test!
    public static function selectTagsByUsername($username) written. test!
    public static function insertTag($values)  test!
    public static function deleteTag($tagname) test!
    public static function insertUserTag($values) test!
    public static function deleteUserTag($values) test!
    public static function insertProjectTag($values) test!
    public static function deleteProjectTag($values) test!
    public static function selectTagsByProjectUrl($url) test!
    ******/

    public static function searchTags($string){
        /*for a given string this will return tagnames which contain this string
          i.e. "alternative education, education, alter, alternative-education, tive edu" will find "alternative-education" tag
        //select * from tags where tagname regexp '.*est .*';
        .*preg_quote($string).*
        */
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
            $regexp='.*' . preg_quote($string) . '.*';
            $statement=$pdo->prepare('SELECT * FROM tags WHERE tagname REGEXP :re');
            $statement->bindValue(':re',strval($regexp), PDO::PARAM_STR);
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
            $statement=$pdo->prepare('INSERT INTO tags (tagname, description, type, created) VALUES (:tn, :ds, :ty, UNIX_TIMESTAMP())');
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
      
            //$outcome=htmlentities(print_r($e,true));
            //echo $outcome;
            // Report errors
            return false;
        }
        unset($pdo);
    }
    
    public static function deleteTag($tagname){
        /**
         * delete tag by tagname
         * use with care! all the user_tag and project_tag connections must be deleted before.
         */
        
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
            $statement=$pdo->prepare('DELETE tags, project_tag, user_tag FROM
                (project_tag RIGHT JOIN tags ON tags.tag_id=project_tag.tag_id)
                LEFT JOIN user_tag ON tags.tag_id=user_tag.tag_id
                    WHERE tags.tagname=:tn');
            $statement->bindValue(':tn', strval($tagname), PDO::PARAM_STR);
            $statement->execute();
            
            $count = $pdo->rowCount();
  // 
            $pdo->commit();

            unset ($pdo);
            
            if($count>=1){
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
    
    public static function insertUserTag($values){
        /** insert data to user_tag: usage: add tag to user
         *  $values=[username: username, tagname: tagname];
         *
         */
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


	    $statement=$pdo->prepare('INSERT INTO user_tag (user_id, tag_id) SELECT ua.user_id, tg.tag_id FROM user_accounts AS ua, tags AS tg WHERE ua.username=:un AND tg.tagname=:tn');
	    $statement->bindValue(':un', strval($values['username']), PDO::PARAM_STR);
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
    
    public static function deleteUserTag($values){
        /** 
         * delete row from user_tag table: use to remove tag from user
         * $values=[username, tagname]
         *
         */
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


	    $statement=$pdo->prepare('DELETE FROM user_tag
		WHERE user_id IN 
		    (SELECT user_id FROM user_accounts WHERE username=:un)
		AND tag_id IN
		    (SELECT tag_id FROM tags WHERE tagname=:tn)');
	    $statement->bindValue(':un', strval($values['username']), PDO::PARAM_STR);
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

    public static function insertProjectTag($values){
        /** insert data to project_tag: usage: add tag to project
         *  $values=[url: project url, tagname: tagname];
         *
         */
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


	    $statement=$pdo->prepare('INSERT INTO project_tag (project_id, tag_id) SELECT pr.project_id, tg.tag_id FROM projects AS pr, tags AS tg WHERE pr.url=:url AND tg.tagname=:tn');
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
