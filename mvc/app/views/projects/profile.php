<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['username']);
$page->title('projects::profile');

$content='
    <div>
        each of these menu things will be optional. no need to "support" or "follow" etc. <br />
        <div>

            (top) project name (----' . $data['projectname'] . '----), picture and action buttons
            <ul>action buttons
                <li>join</li>
                <li>follow</li>
                <li>make as friend project to your project</li>
                <li>support</li>
                <li>chat, comment</li>
                <li>edit if admin</li>
            </ul>
        </div>
        <div>
            (side) info menu
            <ul>
                <li>general summary (landing page)</li>
                <li>who? list of members, do we accept new people? are we looking for someone new?</li>
                <li>what? description, tags. what is the stage of project? just idea, or prepared, or running? news</li>
                <li>why? short target/point of project or idea. important.</li>
                <li>where? place, map, area</li>
                <li>how? what shall be done? what rules? how will the project be funded? do they accept visitors?</li>
                <li>when? what is the progress and time plan?</li>
            </ul>
        </div>
        <div>
            all the info will be in this div. this is a "content div".
        </div>
    </div>
';


$page->add($content);

echo $page->generate();

