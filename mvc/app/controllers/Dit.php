<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

/**class Projects*
 * /projects/"action"
 * as opposed to /project/"project-name"
 * /projects/new, /projects/create
**/

class Dit extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index($url[0], isset($url[1]) ? $url[1] : '', isset($url[2]) ? $url[2] : '', $url);
    }

    public function index($dit_type, $url = '', $action = '', $all_url)
    {
        $project = $this->model('Project');
        if($url === ''){
            header('Location: /'.$dit_type.'s');
        }
        elseif($project->exists($url)){
            $pr=$project->getProjectByUrl($url);
            $type=$pr['type'];
            if($type!==$dit_type){
                $location='/'.$type.'/'.$url.'/'.$action;
                $i=3;
                while(isset($all_url[$i])){
                    $location.='/'.$all_url[$i];
                    ++$i;
                }
                header('Location:'.$location);
                unset($location);
            }

            $data = [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'is_member' => $project->isMember($url, $this->username),
                'is_admin' => $project->isAdmin($url, $this->username),
                'action'=> $action
            ];
            $data['profile']=[
                'ditname' => $pr['projectname'],
                'url' => $pr['url'],
                'subtitle' => $pr['subtitle'],
                'description' => $pr['description'],
                'type' => $pr['type']
            ];
            switch($action){
                case 'join':
                    $this->join($url);
                    break;
                case 'follow':
                    $this->view('dits/profile', $data);
                    exit();
                    break;
                case 'info':
                    $this->view('dits/profile', $data);
                    exit();
                    break;
                case 'people':
                    $data['members'] = $project->getMembers($url);
                    $data['admins'] = $project->getAdmins($url);
                    $data['await-members'] = $project->getAwaitMembers($url);
                    $this->view('dits/profile', $data);
                    exit();
                    break;
                case 'location':
                    $data['location'] = $project->getLocation($url);
                    $this->view('dits/profile', $data);
                    exit();
                    break;
                case 'edit':
                    /***
                    if admin, you can edit. else error, not enough permission. 
                    ***/
                    if($data['is_admin']){
                        if(isset($_POST, $_POST['ditname'], /*$_POST['url'],*/ $_POST['type'], $_POST['subtitle'], $_POST['description'], $_POST['save'])){
                            $project_data = [
                                'ditname' => $_POST['ditname'],
                                'url' => $data['profile']['url'],
                                'old-type' => $data['profile']['type'],
                                'type' => $_POST['type'],
                                'subtitle' => $_POST['subtitle'],
                                'description' => $_POST['description'],
                                'save' => $_POST['save']
                            ];
                            try{
                                $errors = [];
                                $project_model = $this->model('CreateProject');
                                if(!$project_model->edit($data['profile']['url'], $project_data, $errors)){
                                    $this->view('dits/edit', [
                                        'loggedin' => $this->loggedin,
                                        'user-me' => $this->username,
                                        'profile' => $project_data,
                                        'errors' => $errors
                                    ]); 
                                }
                                else{
                                    header('Location:/'.$type.'/'.$project_data['url']);
                                }
                            }
                            catch(Exception $e){
                                $this->view('general/error', [
                                    'loggedin' => $this->loggedin,
                                    'user-me' => $this->username,
                                    'message' => print_r($e,true)
                                ]);
                            }
                        }
                        else{
                            //print_r($data);
                            $this->view('dits/edit', $data);
                        }
                    }
                    else{
                        $this->view('general/error',
                        [
                            'loggedin' => $this->loggedin,
                            'user-me' => $this->username,
                            'message' => 'you don\'t have permission to edit project '.$url
                        ]
                        );
                    }
                    break;
                case 'edit-url':
                    /***
                    if admin, you can edit. else error, not enough permission. 
                    ***/
                    if($data['is_admin']){
                        $data['profile']['new-url']='';
                        if(isset($_POST, $_POST['new-url'], $_POST['save'])){
                            $data['profile']['new-url']=$new_url=$_POST['new-url'];
                            try{
                                $errors = [];
                                $project_model = $this->model('CreateProject');
                                if(!$project_model->editUrl($data['profile']['url'], $new_url, $errors)){
                                    $this->view('dits/edit-url',[
                                        'loggedin' => $this->loggedin,
                                        'user-me' => $this->username,
                                        'profile' => $data['profile'],
                                        'errors' => $errors
                                    ]); 
                                }
                                else{
                                    header('Location:/'.$type.'/'.$new_url);
                                }
                            }
                            catch(Exception $e){
                                $this->view('general/error', [
                                    'loggedin' => $this->loggedin,
                                    'user-me' => $this->username,
                                    'message' => print_r($e,true)
                                ]);
                                exit();
                            }
                        }
                        else{
                            $this->view('dits/edit-url', $data);
                            exit();
                        }
                    }
                    else{
                        $this->view('general/error',[
                            'loggedin' => $this->loggedin,
                            'user-me' => $this->username,
                            'message' => 'you don\'t have permission to edit url of dit '.$url
                        ]);
                        exit();
                    }
                    break;
                case 'join-request':
                    $this->joinRequest($all_url);
                    break;
                case 'settings':
                    $this->settings();
                    break;
                default:
                    //$data['profile'] = $project->getProjectByUrl($url);
                    $this->view('dits/profile', $data);
                    break;
            }
        }
        else{
            $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $this->username, 'message'=> 'Project '.$url.' does not exist or is hidden from you.'.($this->loggedin?'':' If you know the project exists, maybe <a href="/login">logging in</a> may help.')]);
        }
    }

    private function join($dit_url){
        //this function needs existing $dit_url.
        if($this->loggedin){
            $project_class = $this::staticModel('Project');
            $membership=$project_class::getUserMembership($this->username, $dit_url);
            if(sizeof($membership)===0 || (!in_array('admin', $membership) && !in_array('member', $membership) && !in_array('await-member', $membership))){
                if(isset($_POST, $_POST['submit'])&&$_POST['submit']==='cancel'){
                    header('Location:/dit/'.$dit_url);
                    exit();
                }
                elseif(isset($_POST, $_POST['request-message'], $_POST['submit'])){
                    $form_data=[
                        'request-message' => $_POST['request-message']
                    ];
                    $errors=[];
                    if($project_class::sendJoinRequest($this->username, $dit_url, $form_data, $errors)){
                        $this->view('general/message', [
                            'loggedin' => $this->loggedin,
                            'user-me' => $this->username,
                            'message' => 'Your request to join dit <a href="/dit/'.$dit_url.'">'.$dit_url.'</a> was sent successfuly. Now you need to wait for response from project admins.'
                        ]);
                    }
                    else{
                        $this->view('dits/join-form',[
                            'loggedin'=>$this->loggedin,
                            'user-me'=>$this->username,
                            'message-from-dit'=>'implement message from dit',
                            'values' => $form_data,
                            'url'=>$dit_url,
                            'errors'=>$errors
                        ]);
                    }
                }
                else{
                    $this->view('dits/join-form',[
                        'loggedin'=>$this->loggedin,
                        'user-me'=>$this->username,
                        'message-from-dit'=>'implement message from dit',
                        'url'=>$dit_url
                    ]);
                }
            }
            elseif(in_array('await-member', $membership)){
                exit('awaiting member, probably editing the join request will be possible');
            }
            elseif(in_array('member', $membership) || in_array('admin', $membership)){
                $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $this->username, 'message'=> 'You are already member of dit <a href="/dit/'.$dit_url.'">'.$dit_url.'</a> so you don\'t need to join anymore']);
                
            }else{
                exit('bad option in controllers/Dit.php in private function join');
            }
            //exit('project exists and you are logged in as '.$this->username);
        }
        else{
           $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $this->username, 'message'=> 'You have to <a href="/login">log in</a> to be able to join the dit <a href="/dit/'.$dit_url.'"></a>']);
        }
    }

    private function joinRequest($all_url){
        $dit_class=self::staticModel('Project');
        if(!$dit_class::isAdmin($all_url[1],$this->username)) exit('you don\'t have rights to accept users to '.$all_url[0].' '.$all_url[1]);
        if(!isset($all_url[3])) exit('username not provided');

        $awaiting=$all_url[3];
        $url=$all_url[1];
        $dit_type=$all_url[0];
        $user_class=self::staticModel('User');
        if($user_class::exists($awaiting)){
//            echo $awaiting.' '.$url[1];
            if($dit_class::isAwaiting($awaiting, $url)){
                if(isset($all_url[4])){
                    if($all_url[4]==='accept'){
                        exit('accept');
                    }
                    elseif($all_url[4]==='decline'){
                        exit('decline');
                    }
                    else exit('404');
                }
                $dit_usr_class=self::staticModel('DitUser');
                $question=$dit_usr_class::joinQuestion($url);
                $answer=$dit_usr_class::joinAnswer($url, $awaiting);
                $this->view('dits/show-join-request',[
                    'loggedin' => $this->loggedin,
                    'user-me'=> $this->username,
                    'dit' => [
                        'type' => $dit_type,
                        'name' => 'add dit name',
                        'url' => $url
                    ],
                    'awaiting-user' => $awaiting,
                    'question' => $question,
                    'answer' => $answer
                ]);
            }
            else exit($awaiting.' user is not awaiting.');
        }
        else exit($awaiting.' user does not exist.');
    }

    private function settings(){
        exit('TODO dit settings');
    }
}
