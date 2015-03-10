<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class AjaxSearch extends Controller
{
    public static function route($url){
        $self = new self;
        if(isset($_POST['szuk']) && !empty($_POST['szuk'])){
            $self->search($_POST['szuk']);
        }
        else echo json_encode([]);
    }

    private function search($szuk, $options=[]){
        //this should return page with list of search
        //$szuk=rawurldecode($szuk);
        
        $search_sm = $this::staticModel('Search');
        if($this->loggedin){
            if(in_array('users', $options)){
                $found = $search_sm::searchUsers($szuk, $options, $this->username);
            }
            elseif(in_array('dits', $options)){
                $found = $search_sm::searchDits($szuk, $options, $this->username);
            }
            else{
                $found = $search_sm::search($szuk, $options, $this->username);
            }
        }
        else{
            if(in_array('users', $options)){
                $found = $search_sm::searchUsers($szuk, $options);
            }
            elseif(in_array('dits', $options)){
                $found = $search_sm::searchDits($szuk, $options);
            }
            else{
                $found = $search_sm::search($szuk, $options);
            }
        }
        echo json_encode($found);
    }
}
