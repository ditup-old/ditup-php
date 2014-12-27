<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user']);
$page->title('profile::'.$data['member']);
$profile_is_me = $data['user'] === $data['member'] && $data['loggedin'];
$content = '
    <div>
        <!--each of these menu things will be optional-->
        <div class="profile-header" >
            <img class="header-avatar" src="" /><h1 class="header-user-name">'. $data['member'] . '</h1>
            <ul class="header-action-menu">'.
            (
            $profile_is_me ? 
            ''
            :
            '
                <li>message</li>'
            ).
            (
            $profile_is_me ? 
            ''
            :
            '
                <li>create connection (friends etc.)</li>'
            ).
            '
                <li>follow</li>
                <li>reference, comment</li>
                <li>chat</li>'.
            (
            $profile_is_me ? 
            '
                <li><a href="/user/'.$data['user'].'/edit">edit profile</a></li>'
            :
            ''
            ).
            '
            </ul>
        </div>
        <nav class="side-menu">
            <!--(side) info menu-->
            <ul>
                <li><a href="/user/' . $data['member'] . '">general summary (landing page)</a></li>
                <li><a href="/user/' . $data['member'] . '/info">personal info</a></li>
                <li><a href="/user/' . $data['member'] . '/projects">projects</a></li>
                <li><a href="/user/' . $data['member'] . '/interests">interests, what she wants to do</a></li>
                <li><a href="/user/' . $data['member'] . '/activity">recent activity</a></li>
                <li><a href="/user/' . $data['member'] . '/connections">connections (friends)</a></li>
                <li><a href="/user/' . $data['member'] . '/references">references (' . '0' . ')</a></li>
            </ul>
        </nav>
        <div class="user-profile-content">
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
                    <tr><th>visits</th><td>member since</td><td>' . 'implement!' . '</td></tr>
                    <tr><th></th><td>last login</td><td>' . 'implement!' . '</td></tr>
                </tbody>
                <tbody>
                    <tr><th>stats</th><td>profile views</td><td>' . 'implement!' . '</td></tr>
                </tbody>
            </table>'
            .print_r($data,true).'
        </div>
    </div>
';

$page->css('/css/profile.css');

$page->add($content);

echo $page->generate();

