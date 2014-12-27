<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class Change_password extends Controller
{
    public function index($name = '', $action='')
    {
        $user = $this->model('User');
        $user->name = $this->username;
        $member = $this->model('User');
        $member->name = $name;

        $user_profile = $this->model('UserProfile');
        $user_profile->setUsername($name);
        
        if($name===''){
            /****redirect to general people page*****/
            header('Location:/people');
            exit();
        }
        elseif($member->exists($name)){
            //get profile data from database
            
            switch($action){
                case 'edit':
                    if($member->name === $user->name && $this->loggedin){
                        if(!empty($_POST)){
                            /*here data should be entered to database.
                            what data?
                            
                            */
                            $data = $_POST;
                            $errors=array();
                            if($user_profile->validate($data, $errors)){
                                $user_profile->setProfile($data);
                            }
                            else{
                                $this->view('people/profile-edit', [
                                    'loggedin' => $this->loggedin,
                                    'user' => $user->name,
                                    'member' => $member->name,
                                    'profile' => $_POST
                                ]);
                            }
                        }
                        else{
                            $this->view('people/profile-edit', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name, 'profile' => $user_profile->getProfile()]);
                        }
                    }
                    else {
                        $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $user->name, 'member' => $member->name, 'message' => 'Sorry, you don\'t have rights to edit profile of user '.$member->name.'.']);
                    }
                    break;
                case 'info':
                    
                    break;
                default:
                    $profile_data=$user_profile->getProfile();
                    if($member->name === $user->name && $this->loggedin){
                        $this->view('people/profile', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name, 'profile' => $profile_data]);
                    }
                    else{
                        $this->view('people/profile', ['loggedin' => $this->loggedin, 'user' => $user->name, 'member' => $member->name, 'profile' => $profile_data]);       
                    }
            }
        }
        else{
            $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $user->name, 'member' => $member->name, 'message' => 'User ' . $member->name . ' was not found or is hidden from you.']);
        }
    }
}
