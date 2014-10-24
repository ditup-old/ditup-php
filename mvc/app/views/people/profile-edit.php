<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $data['user']);
$page->title('profile::' . $data['member'] . '::edit');

$page->add('<div>profile of ' . $data['member'] . '</div>
<form method="post" action="">
<textarea name="basics" placeholder="basics">asdf</textarea>
<input type="submit" name="submit" value="save changes" />
</form>

');

echo $page->generate();

