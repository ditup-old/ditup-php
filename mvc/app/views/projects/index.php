<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('projects');

$content='
    <div>
        general projects page (new projects and ideas, project map, successful projects etc. (project main page, rozcestnik))
    </div>
';


$page->add($content);

echo $page->generate();
