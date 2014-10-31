<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user']);
$page->title('profile::'.$data['member']);

$content = '
    <div>
        <!--each of these menu things will be optional-->
        <div class="profile-header" >
            <img class="header-avatar" src="" /><h1 class="header-user-name">'. $data['member'] . '</h1>
            <ul class="header-action-menu">
                <li>message</li>
                <li>create connection (friends etc.)</li>
                <li>follow</li>
                <li>reference, comment</li>
                <li>chat</li>
                <li>edit if my profile</li>
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
                <tr><td>username</td><td>' . $data['member'] . '</td></tr>
                <tr><td>member since</td><td>September 2014</td></tr>
            </table>
        </div>
    </div>
';

$page->css('/css/profile.css');

$page->add($content);

echo $page->generate();

