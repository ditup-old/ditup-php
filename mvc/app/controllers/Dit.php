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
        $self->index($url[0], isset($url[1]) ? $url[1] : '', isset($url[2]) ? $url[2] : '');
    }
    public function index($dit_type, $url = '', $action = '')
    {
        $project = $this->model('Project');
        if($url === ''){
            header('Location: /'.$dit_type.'s');
        }
        elseif($project->exists($url)){
            $pr=$project->getProjectByUrl($url);
            $type=$pr['type'];
            if($type!==$dit_type) header('Location:/'.$type.'/'.$url.'/'.$action);

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
                    $this->view('dits/profile', $data);
                    exit();
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
                            }
                        }
                        else{
                            $this->view('dits/edit-url', $data);
                        }
                    }
                    else{
                        $this->view('general/error',
                        [
                            'loggedin' => $this->loggedin,
                            'user-me' => $this->username,
                            'message' => 'you don\'t have permission to edit url of dit '.$url
                        ]
                        );
                    }
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
}
