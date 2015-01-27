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
            header('Location: /projects');
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
                'projectname' => $pr['projectname'],
                'url' => $pr['url'],
                'action'=> $action,
                'subtitle' => $pr['subtitle'],
                'description' => $pr['description']
            ];
            switch($action){
                case 'join':
                    $this->view('projects/profile', $data);
                    break;
                case 'follow':
                    $this->view('projects/profile', $data);
                    break;
                case 'info':
                    $this->view('projects/profile', $data);
                    break;
                case 'people':
                    $data['members'] = $project->getMembers($url);
                    $data['admins'] = $project->getAdmins($url);
                    $this->view('projects/profile', $data);
                    break;
                case 'location':
                    $data['location'] = $project->getLocation($url);
                    $this->view('projects/profile', $data);
                    break;
                case 'edit':
                    /***
                    if admin, you can edit. else error, not enough permission. 
                    ***/
                    if($data['is_admin']){
                        if(isset($_POST, $_POST['projectname'], $_POST['url'], $_POST['subtitle'], $_POST['description'])){
                            $project_data = [
                                
                            ];
                            try{
                                $errors = [];
                                $project_model = $this->model('EditProject');
                                if(!$project_model->edit($project_data, $errors)){
                                    $this->view('projects/edit-project', [
                                        'loggedin' => $this->loggedin,
                                        'user-me' => $this->username,
                                        'values' => $project_data,
                                        'errors' => $errors
                                    ]); 
                                }
                                else{
                                    header('Location:/project/'.$project_data['url']);
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
                            $this->view('projects/edit-project', $data);
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
                default:
                    $data['profile'] = $project->getProjectByUrl($url);
                    $this->view('projects/profile', $data);
                    break;
            }
                    
/*            $this->view('projects/profile',
            [
                'loggedin' => $this->loggedin,
                'user-me' => $this->username,
                'is_member' => $project->isMember($name, $this->username),
                'is_admin' => $project->isAdmin($name, $this->username),
                'projectname' => $name,
                'action'=> $action,
                'profile' => $project->getProfile($name)
            ]);
*/
        }
        else{
            $this->view('general/error', ['loggedin' => $this->loggedin, 'user-me' => $this->username, 'message'=> 'Project '.$url.' does not exist or is hidden from you.'.($this->loggedin?'':' If you know the project exists, maybe <a href="/login">logging in</a> may help.')]);
        }
    }
}
