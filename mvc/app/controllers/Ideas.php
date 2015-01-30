<?php
namespace Mrkvon\Ditup\Controller;

use Mrkvon\Ditup\Core\Controller as Controller;

/**class Projects*
 * /projects/"action"
 * as opposed to /project/"project-name"
 * /projects/new, /projects/create
**/

class Ideas extends Controller
{
    public static function route($url){
        $self = new self;
        $self->index(isset($url[1]) ? $url[1] : '', isset($url[2]) ? $url[2] : '');
    }
    public function index($url = '', $action = '')
    {                
        $this->view('dits/ideas/index', ['loggedin' => $this->loggedin, 'user-me' => $this->username]);
    }
}
