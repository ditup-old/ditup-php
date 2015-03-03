<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class People extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index($action='')
    {
        $people_sm=$this::staticModel('User');
        $username=$this->username;

        $this->view('people/index', [
            'user-amount-all' => $people_sm::countUsers($username, 'all'),
            'user-amount-month' => $people_sm::countUsers($username, 'month'),
            'user-amount-week' => $people_sm::countUsers($username, 'week'),
            'user-amount-day' => $people_sm::countUsers($username, 'day')
        ]);
    }
}
