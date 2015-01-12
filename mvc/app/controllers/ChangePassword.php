<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class ChangePassword extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index()
    {
        $user = $this->model('User');
                    if($this->loggedin){
                        if(isset($_POST, $_POST['submit'])){
                            $errors = [];
                            if($user->changePassword([
                                'username' => $this->username,
                                'old-password' => $_POST['old-password'],
                                'new-password' => $_POST['new-password'],
                                'new-password2' => $_POST['new-password2']
                            ], $errors)){
                                $this->view('general/message', [
                                    'loggedin' => $this->loggedin,
                                    'user-me' => $this->username,
                                    'message' => 'Password of user '.$this->username.' was successfuly changed.'
                                ]);
                            }
                            else{
                                $this->view('people/change-password', [
                                    'loggedin' => $this->loggedin,
                                    'user-me' => $this->username,
                                    'member' => $this->username,
                                    'errors' => $errors
                                ]);
                            }
                            /*validate and process data... submit to database or show form with errors...*/   
                        }
                        else{
                            $this->view('people/change-password', [
                                'loggedin' => $this->loggedin,
                                'user-me' => $this->username
                            ]);
                        }
                    }
                    else{
                        $this->view('general/error',
                            [
                                'loggedin' => $this->loggedin,
                                'user-me' => $user->name,
                                'message' => 'You don\'t have rights to change password of user '.$member->name.'.'
                            ]
                        );
                    }
    }
}
