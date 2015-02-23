<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('interests');

$content='
    <div>
        <p>This is a homepage of interests, passions. Interests are example of <a href="/dits">dits</a>.</p>
        <p>For example if you are interested in studying something, you don\'t have to go to school. You study on your own or - better - you can find people with similar interest and study together independently. Don\'t let schools tell you what is important.</p>
        <p>Interest can lead to creation of learning groups, developing of ideas or working on some projects. Or something completely different.</p>
        <p>
        When you connect to people with similar interest, think of something exciting to do together. Make some brainstorming, create idea, project and change the world (or not).
        </p>
        <div>
            Interest examples:
            <ul>
                <li>mathematics</li>
                <li>game development</li>
                <li>anarchy</li>
                <li>LARP</li>
                <li>education system</li>
                <li>wood carving</li>
                <li>botanics</li>
                <li>world peace</li>
            </ul>
        </div>
    </div>
';


$page->add($content);

echo $page->generate();

