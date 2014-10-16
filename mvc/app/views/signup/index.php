<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header())->generate());

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


$content='<form method="post" action="">
<table>
    <tr><td>email</td><td><input type="text" name="email" placeholder="email" value="' . $values['email'] . '" /></td><td>' . $errors['email'][0] . '</td></tr>
    <tr><td>username</td><td><input type="text" name="username" placeholder="username" value="' . $values['username'] . '" /></td><td>' . $errors['username'][0] . '</td></tr>
    <tr><td>password</td><td><input type="password" name="password" placeholder="password" /></td><td>' . $errors['password'][0] . '</td></tr>
    <tr><td>retype</td><td><input type="password" name="password2" placeholder="retype" /></td><td>' . $errors['password2'][0] . '</td></tr>
    <tr><td>full name</td><td><input type="text" name="full-name"></td><td></td></tr>
    <tr><td></td><td><input type="submit" name="signup" value="sign up"></td><td></td></tr>
</table>
</form>';

$page->add($content);

echo $page->generate();

