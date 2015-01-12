<?php

use Mrkvon\Ditup\View\User\UserPage as UserPage;

require_once '../app/views/people/UserPage.php';

$up=new UserPage($data['loggedin'], $data['user-me']);
$up->setUsername($data['member']);
$up->setMe($data['user-me']===$data['member'] && $data['loggedin']);
$up->title('projects');

$content = '
        Projects of user ' . $data['member'] . ':
        <ul>';
$projects = $data['projects'];
for($i=0, $len=sizeof($projects); $i<$len; $i++){
   $content.='<li><a href="/project/'.$projects[$i]['url'].'">'.$projects[$i]['projectname'].'</a></li>';
}
$content.= '
        </ul>';

$up->add($content);

echo $up->generate();

