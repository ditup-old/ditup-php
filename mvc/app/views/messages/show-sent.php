<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('messages::sent');
$content = '
<div>
    <h1>Sent Messages of User <a href="/user/'.$data['user-me'].'">'.$data['user-me'].'</a></h1>';
foreach($data['messages'] as $msg){
    $content.= '
    <div><a href="/message/'.$msg['from-user']['username'].'/'.$msg['create-time'].'" >message</a></div>';
}
$content.='
</div>';

$page->css('/css/messages/compose.css');

$page->add($content);

echo $page->generate();

