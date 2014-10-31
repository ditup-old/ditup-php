<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

/**class Projects*
 * /projects/"action"
 * as opposed to /project/"project-name"
 * /projects/new, /projects/create
**/

class Projects extends Controller
{
    public function index($action='')
    {
        $this->view('projects/index', ['loggedin' => $this->loggedin, 'user-me' => $this->username]);
    }

    public function create()
    {
    /***if user is logged in, go to create page.
        if user is not logged in, go to /log in first
    *****/
        if($this->loggedin){
            $this->view('projects/create', ['loggedin' => $this->loggedin, 'user-me' => $this->username]);
        }
        else{
            $this->view('projects/create-log-in-first', ['loggedin' => $this->loggedin, 'user-me' => $this->username]);
        }
    }
}
