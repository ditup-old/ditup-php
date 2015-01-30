<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('dits');

$content='
    <div>
        Dit is an abstract object. Something that wants to be done collaboratively. Examples of dits are
        <ul>
            <li><a href="/ideas">ideas</a> to develop</li>
            <li><a href="/projects">projects</a> to do</li>
            <li><a href="/interests">interests</a> to share and enjoy</li>
            <li><a href="/issues">issues</a> to solve</li>
            <li><a href="/topics">topics</a> to focus on</li>
        </ul>
    </div>
    <div>
        
    </div>
';


$page->add($content);

echo $page->generate();

