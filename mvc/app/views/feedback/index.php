<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('feedback');
$content = '
    <div>
        Please write email to <i>michal dot salajka at email dot cz</i> to send your feedback. The embedded feedback is still TODO. Thank you for your interest in improving this platform.
    </div>
';

$page->add($content);

echo $page->generate();

