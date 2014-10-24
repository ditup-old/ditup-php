<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

/**class Projects*
 * /projects/"action"
 * as opposed to /project/"project-name"
 * /projects/new, /projects/create
**/

class Project extends Controller
{
    public function index($name = '', $action = '')
    {
        if($name === ''){
            header('Location: /projects');
        }
        else{
            $this->view('projects/profile', ['loggedin' => $this->loggedin, 'username' => $this->username, 'projectname' => $name]);
        }
    }
}
