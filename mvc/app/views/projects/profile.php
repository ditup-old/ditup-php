<?php

require_once '../app/views/projects/ProjectPage.php';
use Mrkvon\Ditup\View\Project\ProjectPage as ProjectPage;

$action=$data['action'];
$page=new ProjectPage($data['loggedin'], $data['user-me']);
$page->setProjectname($data['projectname']);
$page->setMember($data['is_member']);
$page->setAdmin($data['is_admin']);
$page->setSubtitle($data['subtitle']);

switch($action){
    case '':
        $content = '
            <table>
                <tbody>
                    <tr><th>general</th><td>projectname</td><td>' . $data['projectname'] . '</td></tr>
                </tbody>
            </table>' . print_r($data,true).
            
            '<ul>
                <li>who? list of members, do we accept new people? are we looking for someone new?</li>
                <li>what? description, tags. what is the stage of project? just idea, or prepared, or running? news</li>
                <li>why? short target/point of project or idea. important.</li>
                <li>where? place, map, area</li>
                <li>how? what shall be done? what rules? how will the project be funded? do they accept visitors?</li>
                <li>when? what is the progress and time plan?</li>
            </ul>';
        break;
    case 'follow':
        $page->title('follow');
        $content = 'TODO';
        break;
    case 'info':
        $page->title('info');
        $content = 'info';
        break;
    case 'join':
        $content = 'TODO';
        break;
    case 'location':
        $page->title('location');
        $content = 'location: ' . $data['location'];
        break;
    case 'people':
        $page->title('people');
        $content = 'members:
    <ul>';
        foreach($data['members'] as $member){
            $content .= '<li><a href="/user/'.$member.'">'.$member.'</a></li>';
        }
        $content .= '
    </ul>
    admins:
    <ul>';
        foreach($data['admins'] as $admin){
            $content .= '<li><a href="/user/'.$admin.'">'.$admin.'</a></li>';
        }
        break;
    default:
        $page->title('error');
        $content = $data['projectname'].'::'.$action.' This option does not exist. '.print_r($data, true);
        break;
}



$page->add($content);

echo $page->generate();

