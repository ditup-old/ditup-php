<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Search extends Controller
{
    public static function route($url){
        $self = new self;
        if(isset($url[0])){
            $self->search($url[0]);
        }
        else $self->searchForm();
    }

    private function search($szuk, $options=[]){
        //this should return page with list of search
        $szuk=rawurldecode($szuk);
        $search_sm = $this::staticModel('Search');
        if($this->loggedin){
            $found = $search_sm::search($szuk, $options, $this->username);
        }
        else $found = $search_sm::search($szuk, $options);
        $this->view('search/list', ['list' => $found]);
    }

    private function searchForm(){
        if(isset($_POST, $_POST['search-string'])){
            header('Location:/search/'.rawurlencode($_POST['search-string']));
        }
        else{
            $this->view('search/form', []);
        }
    }
}
