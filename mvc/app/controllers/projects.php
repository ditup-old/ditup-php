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
        echo 'general projects page (new projects and ideas, project map, successful projects etc. (project main page, rozcestnik))';
    }

    public function create()
    {
        echo 'create new project (only if you\'re logged in)';
    }
}
