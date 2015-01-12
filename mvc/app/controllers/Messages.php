<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Messages extends Controller
{
    public static function route($url=[]){
        $self = new self();
        //print_r($_SESSION);
        if(!isset($url[0])){
            //show main message page
            $self->messages();
        }
        elseif($url[0]==='compose'){
            //show message form
            $self->composeMessage();
        }
        elseif($url[0]==='drafts'){
            //show draft messages
            $self->draftMessages();
        }
        elseif($url[0]==='received'){
            //show received messages
            $self->receivedMessages();
        }
        elseif($url[0]==='sent'){
            //show sent messages
            $self->sentMessages();
        }
        else{
            $self->view('general/error', [
                'loggedin' => $self->loggedin,
                'user-me' => $self->username,
                'message' => '404 Page Not Found'
            ]);
        }
    }
    
    private function messages(){
        echo 'messages';
    }

    private function composeMessage(){
        if($this->loggedin){
            if(isset($_POST, $_POST['from-project'], $_POST['to-users'], $_POST['to-projects'], $_POST['subject'], $_POST['message'], $_POST['submit'])){
                $messages=$this->staticModel('Messages');
                $errors=[];
                $values=[
                    'from-user' => $this->username,
                    'from-project' => strlen($_POST['from-project'])>0 ? $_POST['from-project'] : null,
                    //TODO do this explosion better
                    'to-users' => explode(', ', $_POST['to-users']),
                    'to-projects' => explode(', ', $_POST['to-projects']),
                    'subject' => $_POST['subject'],
                    'message' => $_POST['message']
                ];
                //if message cancelled, redirect to /messages
                if($_POST['submit']==='cancel'){
                    header('Location:/messages');
                }
                //if values are valid, send message or save it to drafts
                elseif($messages::validate($values, $errors)){
                    //if clicked send button, send.
                    if($_POST['submit']==='send'){
                        $success=$messages::sendMessage($values);
                        if($success){
                            $this->view('general/message', [
                                'loggedin' => $this->loggedin,
                                'user-me' => $this->username,
                                'message' => '<a href="/message/'.$success['username'].'/'.$success['timestamp'].'" >message</a> was successfuly sent'
                            ]);
                        }
                        else{
                            $this->view('general/error', [
                                'loggedin' => $this->loggedin,
                                'user-me' => $this->username,
                                'message' => 'something unexpected happened while sending message'
                            ]);
                        }
                    }
                    elseif($_POST['submit']==='save to drafts'){
                        $success=$messages::saveDraftMessage($values);

                        if($success){
                            $this->view('general/message', [
                                'loggedin' => $this->loggedin,
                                'user-me' => $this->username,
                                'message' => '<a href="/message/'.$success['username'].'/'.$success['timestamp'].'" >message</a> was saved to drafts'
                            ]);
                        }
                        else{
                            $this->view('general/error', [
                                'loggedin' => $this->loggedin,
                                'user-me' => $this->username,
                                'message' => 'something unexpected happened while saving message to drafts'
                            ]);
                        }
                    }
                }
                else{
                    $this->view('messages/compose', [
                        'loggedin' => $this->loggedin,
                        'user-me' => $this->username,
                        'message' => $values,
                        'errors' => $errors
                    ]);
                }
            }
            else{
                $this->view('messages/compose', [
                    'loggedin' => $this->loggedin,
                    'user-me' => $this->username
                ]);
            }
        }
        else{
            $this->view('general/error', [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'message' => 'you need to <a href="/login">log in</a> to be able to send messages'
            ]);
        }
    }

    private function sentMessages(){
        echo 'sent messages';
    }

    private function receivedMessages(){
        if($this->loggedin){
            $message_model = $this->staticModel('Messages');
            $messages = $message_model::getReceivedMessages($this->username);

            $this->view('messages/show-received', [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'messages' => $messages
            ]);
        }
        else{
            $this->view('general/error', [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'message' => 'you need to <a href="/login">log in</a> to be able to view messages'
            ]);
        }
    }

    private function draftMessages(){
        echo 'message drafts';
    }
}
