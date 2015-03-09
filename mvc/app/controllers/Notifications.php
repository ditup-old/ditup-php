<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Notifications extends Controller
{
    public static function route($url){
        $self = new self;
        if(empty($url)){
            $self->index();
        }
        elseif(isset($url[0], $url[1])){
            if($url[1]==='delete'){
                $self->delete((int)$url[0]);
            }
            elseif($url[1]==='go'){
                $self->go((int)$url[0]);
            }
            else exit('404');
        }
        else exit('404');
    }

    public function index(){
        $user_sm=$this::staticModel('User');
        if($this->loggedin && $user_sm::exists($this->username)){
            $note_sm = $this::staticModel('Notifications');
            //select notifications from database
            $notes=$note_sm::getNotifications($this->username);
            //update notification visiting time
            $update_notes = $note_sm::visitNotifications($this->username);
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

    public function delete($id){
        $user_sm=$this::staticModel('User');
        if($this->loggedin && $user_sm::exists($this->username)){
            $note_sm = $this::staticModel('Notifications');
            //try to delete notification from database
            $deleted=$note_sm::deleteNotification($id, $this->username);

            if($deleted===true){
                header('Location:/notifications');
                exit();
            }
            else exit('notification does not exist or database problem or notification does not belong to you');
        }
        else exit('you are not logged in.');
    }

    public function go($id){
        $user_sm=$this::staticModel('User');
        if($this->loggedin && $user_sm::exists($this->username)){
            $note_sm = $this::staticModel('Notifications');
            //*****update view time of notification if previously null
            //*****get following url of notification with id $id
            $url=$note_sm::processNotification($id, $this->username);
            //*****redirect to the url
            header('Location:'.$url);
            exit();
        }
        else exit('you are not logged in.');
    }
}
