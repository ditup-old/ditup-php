<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('message::read');
$content = '
    <div>
        <table>
        <tbody>
            <tr><th>Message</th><td></td></tr>
            <tr><th>From</th><td>';
$content.='<a href="/user/'.$data['message']['from-user']['username'].'">user:'.$data['message']['from-user']['username'].'</a>';
$from_project=$data['message']['from-project'];
$content.= (isset($data['message']['from-project'])&&is_array($from_project))
            ? ' in <a href="/project/'.$from_project['url'].'">project:'.$from_project['projectname'].'</a>'
            : '';

$content.='</td></tr>
            <tr><th>To</th><td>';
$to_users=$data['message']['to-users'];
$to_projects=$data['message']['to-projects'];
for($i=0, $len=sizeof($to_users);$i<$len; $i++){
    $content.='<a href="/user/'.$to_users[$i]['username'].'">user:'.$to_users[$i]['username'].'</a>';
    $content.= ($i==$len-1) ? ((sizeof($to_projects)>0) ? '; ' : '') : ', ';
}
for($i=0, $len=sizeof($to_projects);$i<$len; $i++){
    $content.='<a href="/project/'.$to_projects[$i]['url'].'">project:'.$to_projects[$i]['projectname'].'</a>';
    $content.= $i==$len-1 ? '' : ', ';
}

$date_sent=getdate($data['message']['send-time']);

$content .= '</td></tr>
            <tr><th>Subject</th><td>'.$data['message']['subject'].'</td></tr>
            <tr><th>Message</th><td>'.$data['message']['message'].'</td></tr>
            <tr><th>Sent</th><td>'.$date_sent['weekday'].' '.$date_sent['mday'].' '.$date_sent['month'].' '.$date_sent['year'].'</td></tr>
        </tbody>
        </table>
    </div>
';

$content.=print_r($data['message'], true);

$page->css('/css/messages/compose.css');

$page->add($content);

echo $page->generate();

