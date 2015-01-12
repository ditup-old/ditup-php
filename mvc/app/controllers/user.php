<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

class User extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index(isset($url[0]) ? $url[0] : '', array_slice($url, 1));
    }

    public function index($name = '', $action=[])
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
            $action[0] = isset($action[0]) ? $action[0] : '';
            switch($action[0]){
                case 'edit':
                    $this->edit($user->name, $member->name, $user_profile, $this->loggedin);
                    break;
                case 'info':
                    echo 'implement info!! (controllers/user.php)';
                    break;
                case 'change-password':
                    if($member->name === $user->name && $this->loggedin){
                        if(isset($_POST, $_POST['submit'])){
                            $errors = [];
                            if($user->changePassword([
                                'username' => $member->name,
                                'old-password' => $_POST['old-password'],
                                'new-password' => $_POST['new-password'],
                                'new-password2' => $_POST['new-password2']
                            ], $errors)){
                                $this->view('general/message', [
                                    'loggedin' => $this->loggedin,
                                    'user-me' => $user->name,
                                    'message' => 'Password of user '.$member->name.' was successfuly changed.'
                                ]);
                            }
                            else{
                                $this->view('people/change-password', [
                                    'loggedin' => $this->loggedin,
                                    'user-me' => $user->name,
                                    'member' => $member->name,
                                    'errors' => $errors
                                ]);
                            }
                            /*validate and process data... submit to database or show form with errors...*/   
                        }
                        else{
                            $this->view('people/change-password', [
                                'loggedin' => $this->loggedin,
                                'user-me' => $user->name,
                                'member' => $member->name
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
                    break;
                case 'message':
                    // /user/[username]/message/[message-id]
                    break;
                case 'messages':
                    // /user/[username]/messages

                    break;
                case 'projects':
                    $this->projects($member->name, $user->name);
                    break;
                case '':
		    $static_user_profile = $this->staticModel('UserProfile');
		    $tags = $static_user_profile::getTags($member->name);
                    $profile_data=$user_profile->getProfile($member->name);
		    $profile_data['tags'] = $tags;
                    if(is_array($profile_data)){
                        if($member->name === $user->name && $this->loggedin){
                            $this->view('people/profile', ['loggedin' => $this->loggedin, 'user-me' => $user->name, 'member' => $member->name, 'profile' => $profile_data]);
                        }
                        else{
                            $this->view('people/profile', ['loggedin' => $this->loggedin, 'user-me' => $user->name, 'member' => $member->name, 'profile' => $profile_data]);       
                        }
                    }
                    elseif($profile_data===false){
                        $this->view('general/error',
                            [
                                'loggedin' => $this->loggedin,
                                'user-me' => $user->name,
                                'message' => 'user '.$member->name.' doesn\'t exist.'
                            ]
                        );
                    }
                    break;
                default:
                    exit('404 Page Not Found');
            }
        }
        else{
            $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $user->name, 'member' => $member->name, 'message' => 'User ' . $member->name . ' was not found or is hidden from you.']);
        }
    }

    private function edit($username_me, $username_member, $user_profile, $loggedin){
        if($username_me === $username_member && $loggedin){
            if(!empty($_POST)){
                /*here data should be entered to database.
                what data?
                
                */
                $data = $_POST;
                //print_r($_POST);
                $errors=array();
                if($user_profile->validate($data, $errors)){
                    $data['username'] = $username_member;
                    $data['v_age'] = (isset($data['v_age'])&&$data['v_age']=='on') ? true : false;
                    $data['v_about'] = (isset($data['v_about'])&&$data['v_about']=='on') ? true : false;
                    $data['v_gender'] = (isset($data['v_gender'])&&$data['v_gender']=='on') ? true : false;
                    $data['v_website'] = (isset($data['v_website'])&&$data['v_website']=='on') ? true : false;
                    $data['v_bewelcome'] = (isset($data['v_bewelcome'])&&$data['v_bewelcome']=='on') ? true : false;
                    //print_r($data);
                    $user_profile->setProfile($data);
                    
                    header('Location:/user/'.$this->username);
                }
                else{
                    $this->view('people/profile-edit', [
                        'loggedin' => $this->loggedin,
                        'user' => $username_me,
                        'member' => $username_member,
                        'profile' => $data,
                        'errors' => $errors
                    ]);
                }
            }
            else{
                $this->view('people/profile-edit', ['loggedin' => $loggedin, 'user' => $username_me, 'member' => $username_member, 'profile' => $user_profile->getProfile($username_member)]);
            }
        }
        else {
            $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $username_me, 'member' => $username_member, 'message' => 'Sorry, you don\'t have rights to edit profile of user '.$username_member.'.']);
        }
    }

    private function projects($username_member, $username_me){
        $static_user_profile = $this->staticModel('UserProfile');
        $projects=$static_user_profile::getProjects($username_member);
        $this->view('people/profile/projects', ['loggedin' => $this->loggedin, 'user-me' => $username_me, 'member' => $username_member, 'projects' => $projects]);       
        exit('implement projects!');
    }
}
