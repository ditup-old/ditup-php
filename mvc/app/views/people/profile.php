<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header($data['loggedin'], $data['user']))->generate());

$content = '
    <div>
        each of these menu things will be optional
        <div>
            (top) member name (----' . $data['member'] . '----), picture and action buttons
            <ul>action buttons
                <li>message</li>
                <li>create connection (friends etc.)</li>
                <li>follow</li>
                <li>reference, comment</li>
                <li>chat</li>
                <li>edit if my profile</li>
            </ul>
        </div>
        <div>
            (side) info menu
            <ul>
                <li>general summary (landing page)</li>
                <li>personal info</li>
                <li>member of what projects?</li>
                <li>interests, what to do</li>
                <li>recent activity</li>
                <li>connections</li>
                <li>references</li>
                <li>....</li>
            </ul>
        </div>
        <div>
            all the info will be in this div. this is a "content div".
        </div>
    </div>
';

$page->add($content);

echo $page->generate();

