<?php

use Mrkvon\Ditup\View\User\UserPage as UserPage;

require_once '../app/views/people/UserPage.php';

$up=new UserPage($data['loggedin'], $data['user-me']);
$up->setUsername($data['member']);
$up->setMe($data['user-me']===$data['member'] && $data['loggedin']);
$up->title('main');

$content = '
            <table>
                <tbody>
                    <tr><th>bio</th><td>username</td><td>' . $data['member'] . '</td></tr>'
                    . ($data['profile']['v_age'] ? 
                    '
                    <tr><th></th><td>age</td><td>' . $data['profile']['age'] . '</td></tr>':''
                    )
                    . ($data['profile']['v_gender'] ? 
                    '
                    <tr><th></th><td>gender</td><td>' . $data['profile']['gender'] . '</td></tr>':''
                    )
/*                    . ($data['profile']['v_location'] ? 
                    '
                    <tr><th></th><td>location</td><td>' . $data['profile']['location'] . '</td></tr>':''
                    )*/.'
                </tbody>
                <tbody>'
                    . ($data['profile']['v_website'] ? 
                    '
                    <tr><th>web</th><td>website</td><td><a target="_blank" href="' . $data['profile']['website'] . '" >'.$data['profile']['website'].'</a></td></tr>':''
                    )
                    . ($data['profile']['v_bewelcome'] ? 
                    '
                    <tr><th></th><td>bewelcome</td><td><a target="_blank" href="http://bewelcome.org/members/' . $data['profile']['bewelcome'] . '">' . $data['profile']['bewelcome'] . '</a></td></tr>':''
                    )
/*                    . ($data['profile']['v_couchsurfing'] ? 
                    '
                    <tr><th></th><td>couchsurfing</td><td><a target="_blank" href="http://couchsurfing.org/people/' . $data['profile']['couchsurfing'] . '">' . $data['profile']['couchsurfing'] . '</a></td></tr>':''
                    )
                    . ($data['profile']['v_facebook'] ? 
                    '
                    <tr><th></th><td>facebook</td><td>' . $data['profile']['facebook'] . '</td></tr>':''
                    )
                    . ($data['profile']['v_twitter'] ? 
                    '
                    <tr><th></th><td>twitter</td><td>' . $data['profile']['twitter'] . '</td></tr>':''
                    )*/
                    .'
                    <tr><th></th><td>...</td><td>' . '' . '</td></tr>
                </tbody>
                <tbody>
                    <tr><th>visits</th><td>member since</td><td>' . $data['profile']['member-since'] . '</td></tr>
                    <tr><th></th><td>last login</td><td>' . $data['profile']['last-login'] . '</td></tr>
                </tbody>
                <tbody>
                    <tr><th>stats</th><td>profile views</td><td>' . 'implement!' . '</td></tr>
                </tbody>
            </table>'
            .print_r($data,true).'
';

$up->add($content);

echo $up->generate();

