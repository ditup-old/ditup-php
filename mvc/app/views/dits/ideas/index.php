<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('ideas');

$content='
    <div>
        This is a homepage of ideas. Idea is a typical <a href="/dits">dit</a>. It can be collaboratively crafted. When it attracts enough interested people it can be developed into a project. Ideas without action are dead. There should be brainstorming enabled for ideas.
    </div>
    <ul>
    <lh>Contents</lh>
    <li>newest ideas</li>
    <li>good ideas</li>
    <li>...</li>
    </ul>
';


$page->add($content);

echo $page->generate();

