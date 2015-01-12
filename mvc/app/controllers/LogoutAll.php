<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class LogoutAll extends Controller
{   
    public static function route($url){
        $self = new self;
        $self->index();
    }

    public function index()
    {
        if($this->loggedin){
            session_destroy();
            //deleting login cookies
            $cookie_login = $this->staticModel('CookieLogin');
            $cookie_login::destroyAllLoginCookies(['username' => $this->username]);

            echo 'successfuly logged out from all machines';
            header("Location:/"); //improve!!
        }
        else {
            $this->view('general/error', [
                    'loggedin' => $this->loggedin,
                    'user-me' => $this->username,
                    'message' => 'no need to log out. you are not logged in.'
                ]
            );
            header("Location:/"); //improve!!
        }
    }
}
