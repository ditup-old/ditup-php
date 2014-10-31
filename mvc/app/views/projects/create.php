<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('projects::create');

$content='
    <div>
        create new project
<br />
        name
<br />
        description
    </div>
';


$page->add($content);

echo $page->generate();

