<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Message extends Controller
{
    public static function route($url=[]){
        $self = new self;
        if(!isset($url[0], $url[1])){
            //redirect to /messages
            header('Location:/messages');
            exit();
        }
        elseif(!isset($url[2])){
            //show message with id $url[0]
            $self->showMessage($url[0], $url[1]);
        }
        elseif($url[2]==='delete'){
            //delete message with id url[0]
            $self->deleteMessage($url[0],$url[1]);
        }
        else{
            $self->view('general/error', [
                'loggedin' => $self->loggedin,
                'user-me' => $self->username,
                'message' => '404 Page Not Found'
            ]);
        }
    }

    private function showMessage($username, $timestamp){
        //show message from user ... with timestamp ...
        if($this->loggedin){
            $messages_class=$this->staticModel('Messages');
            $message=$messages_class::readMessage($username, $timestamp, $this->username);
            if(is_array($message) && sizeof($message)){
                if($message['send-time']!=null){
                    $this->view('messages/show-message',[
                        'loggedin' => $this->loggedin,
                        'user-me' => $this->username,
                        'message' => $message
                    ]);
                }
                elseif($message['from-user']['username']==$this->username){
                    $this->view('messages/compose',[
                        'loggedin' => $this->loggedin,
                        'user-me' => $this->username,
                        'message' => $message
                    ]);
                }
                else{
                    $this->view('general/error',[
                        'loggedin' => $this->loggedin,
                        'user-me' => $this->username,
                        'message' => 'This error shouldn\'t happen.'
                    ]);
                }
            }
            else{
                $this->view('general/error',[
                    'loggedin' => $this->loggedin,
                    'user-me' => $this->username,
                    'message' => 'requested message doesn\'t exist or you aren\'t allowed to read it'
                ]);
            }
        }
        else{
            $this->view('general/error',[
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'message' => '<a href="/login" >log in</a> to browse messages'
            ]);
        }
    }
    
    private function deleteMessage($username, $timestamp){
        //show message from user ... with timestamp ...
        if($this->loggedin){
            echo 'delete message from '. $username . ' with timestamp ' . $timestamp . '.';
        }
        else{
            $this->view('general/error',[
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'message' => '<a href="/login" >log in</a> to browse messages'
            ]);
        }
    }
}
