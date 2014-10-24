<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header($data['loggedin'], $data['user']))->generate());

$page->add('<div>profile of ' . $data['member'] . '</div>
<form method="post" action="">
<textarea name="basics" placeholder="basics">asdf</textarea>
<input type="submit" name="submit" value="save changes" />
</form>

');

echo $page->generate();

