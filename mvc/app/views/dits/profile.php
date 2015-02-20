<?php

require_once '../app/views/dits/DitPage.php';
use Mrkvon\Ditup\View\Dit\DitPage as DitPage;

$action=$data['action'];
$values=$data['profile'];
//print_r($data);
$page=new DitPage($data['loggedin'], $data['user-me']);
$page->setProjectname($values['ditname'], $values['url']);
$page->setMember($data['is_member']);
$page->setAdmin($data['is_admin']);
$page->setSubtitle($values['subtitle']);

switch($action){
    case '':
        $content = '
            <table>
                <tbody>
                    <tr><th>general</th><td>projectname</td><td>' . $values['ditname'] . '</td></tr>
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
        $content .= '
    </ul>
    await-members:
    <ul>';
        foreach($data['await-members'] as $awm){
            $content .= '<li><a href="/user/'.$awm.'">'.$awm.'</a>'.($data['is_admin'] ? ' (<a href="/'.$values['type'].'/'.$values['url'].'/join-request/'.$awm.'">process</a>)' : '').'</li>';
        }
        $content.='</ul>';
        break;
    default:
        $page->title('error');
        $content = $values['ditname'].'::'.$action.' This option does not exist. '.print_r($data, true);
        break;
}



$page->add($content);

echo $page->generate();

