<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader();
$page->title('main page');

$values=[
    'username'=>(isset($data, $data['values'], $data['values']['username']) ? $data['values']['username'] : ''),
    'email'=>(isset($data, $data['values'], $data['values']['email']) ? $data['values']['email'] : '')
];

$errors=[
    'email' => (isset($data, $data['errors'], $data['errors']['email'])) ? [$data['errors']['email'], 1] : ['', 0],
    'username' => (isset($data, $data['errors'], $data['errors']['username'])) ? [$data['errors']['username'], 1] : ['', 0],
    'password' => (isset($data, $data['errors'], $data['errors']['password'])) ? [$data['errors']['password'], 1] : ['', 0],
    'password2' => (isset($data, $data['errors'], $data['errors']['password2'])) ? [$data['errors']['password2'], 1] : ['', 0]
];


$content='<div>first <a href="/logout">log out</a> to sign up</div>';

$page->add($content);

echo $page->generate();

