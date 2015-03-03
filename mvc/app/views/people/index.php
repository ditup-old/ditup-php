<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($this->loggedin, $this->profile);
$page->title('people');

$page->add('<div>this is general people page. if you want to, check <a href="/user/example" >user/example</a> example page</div>
<div>
<!--user statistics. number of users and active users-->
<div>There are '.$data['user-amount-all'].' users registered.</div>
<div>There are '.$data['user-amount-month'].' users active in last month.</div>
<div>There are '.$data['user-amount-week'].' users active in last week.</div>
<div>There are '.$data['user-amount-day'].' users active in last 24 hours.</div>
</div>

<div>
<!--10 new members (if visible for you)-->
</div>
');

echo $page->generate();

