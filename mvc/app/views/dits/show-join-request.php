<?php

require_once '../app/views/general/PageWithHeader.php';
use \PageWithHeader as Page;
/*
'loggedin' => $this->loggedin,
'user-me'=> $this->username,
'awaiting-user' => $awaiting,
'question' => $question,
'answer' => $answer
*/

$page = new Page($data['loggedin'], $this->profile);
//print_r($data);

$awaiting = $data['awaiting-user'];
$dit=$data['dit'];

$content = '<div>
    <h2>process join request of user <a href="/user/'.$awaiting.'">'.$awaiting.'</a></h2>
    <div>
    <h3>joining question</h3>
    <p>'.$data['question'].'</p>
    <h3>joining answer</h3>
    <p>'.$data['answer'].'</p>
    <a href="/'.$dit['type'].'/'.$dit['url'].'/join-request/'.$awaiting.'/accept">accept</a><br />
    <a href="/'.$dit['type'].'/'.$dit['url'].'/join-request/'.$awaiting.'/decline">decline</a><br />
    <a href="/messages/compose">write message</a><br />
    <a href="/user/'.$awaiting.'/chat">chat</a><br />
    </div>
</div>';

$page->title($dit['type'].'::'.$dit['url'].'::join-request::'.$awaiting);
$page->add($content);
echo $page->generate();

