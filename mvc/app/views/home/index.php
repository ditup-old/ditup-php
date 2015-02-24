<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('main page');

$content = '<div>
Welcome to ditup!
</div>
<div>
This is a landing page of the platform. For more information visit <a href="/start">starting page</a>.
</div>
<div>
Please, be aware that this platform is in active development, unstable and not released, yet. Feel free to test it, report bugs, share your opinion, ideas, knowledge or skills. (You can send your suggestions to <a href="http://mrkvon.org">mrkvon</a> for now.) Database might change, your profile may change or disappear etc.
</div>';


$page->add($content);

echo $page->generate();

