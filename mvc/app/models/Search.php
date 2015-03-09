<?php

namespace Mrkvon\Ditup\Model;

use Mrkvon\Ditup\Model\Database as Database;
use Exception;

require_once dirname(__FILE__).'/database/Search.php';

class Search
{
    public static function search($szuk, $options=[], $username=''){
        $users=self::searchUsers($szuk);
        $dits=self::searchDits($szuk, $options, $username);

        $users = is_array($users) ? ['users' => $users] : ['users' => []];
        $dits  = is_array( $dits) ? ['dits'  => $dits ] : ['dits' => []];

        return array_merge($users, $dits);
    }

    public static function searchUsers($szuk, $options=[], $username=''){
        $users=Database\Search::selectUsersSearch($szuk);
        return $users;
    }

    public static function searchDits($szuk, $options=[], $username=''){
        $dits=Database\Search::selectDitsSearch($szuk);
        return $dits;
    }
}
