<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('messages::received');
$content = '<div><h1>Received Messages of User <a href="/user/'.$data['user-me'].'">'.$data['user-me'].'</a></h1>';
foreach($data['messages'] as $msg){
    $is_read=$msg['read-time']?true:false;
    $content.= '<div>'.$msg['id'].' '.($is_read?'':'<b>').'<a href="/message/'.$msg['from-user']['username'].'/'.$msg['create-time'].'" >message</a>'.($is_read?'':'</b>').'</div>';
}
$content.='</div>';
//$content.=print_r($data['messages'], true);

$page->css('/css/messages/compose.css');

$page->add($content);

echo $page->generate();

