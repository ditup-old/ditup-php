<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Notifications extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index()
    {
        $user_sm=$this::staticModel('User');
        if($this->loggedin && $user_sm::exists($this->username)){
            $note_sm = $this::staticModel('Notifications');
            $notes=$note_sm::getNotifications($this->username);
            $this->view('general/notifications', [
                'notifications' => $notes
            ]);
        }
        else{
            $this->view('general/error', [
                'loggedin' => $this->loggedin,
                'message' => 'you need to log in to be able to see your notifications'
            ]);
        }
    }
}
