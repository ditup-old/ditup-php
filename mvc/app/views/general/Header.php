<?php

class Header
{
    function __construct($loggedin=false, $profile=['username'=> '?', 'unseen_messages' => '?'])
    {
        $this->loggedin = $loggedin;
        $this->user = $profile['username'];
        $this->profile=$profile;
        return $this;
    }

    function generate()
    {
        $profile_picture='/img/no-picture.png';
        if(file_exists('img/profile/'.$this->user.'.jpg')){
             $profile_picture='/img/profile/'.$this->user.'.jpg';
        }
        elseif(file_exists('img/profile/'.$this->user.'.png')){
             $profile_picture='/img/profile/'.$this->user.'.png';
        }

        return '<div class="main-menu-top">
    <ul>
        <li><a href="/" ><img src="/img/logo.png" style="height:20px;" /> ditup</a>
            <ul>
                <li><a href="/start">about</a></li>
                <li><a href="/development">development</a></li>
                <li><a href="/feedback">feedback</a></li>
            </ul>
        </li>
        <li><a href="/dits" >dits</a>
            <ul>
                <li><a href="/ideas">ideas</a></li>
                <li><a href="/projects">projects</a></li>
                <li><a href="/interests">interests</a></li>
                <li>explore (categories, tags)</li>' . ($this->loggedin ? '
                <li><a href="/dits/create">+ create new dit</a></li>' : '') . '
                
            </ul>
        </li>
        <li><a href="/people">people</a>
            <ul>' . ($this->loggedin ? '<li><a href="/user/' . $this->user . '">my profile</a></li>' : '') . '
                <li>groups</li>
            </ul>
        </li>
        <li><a href="/community" >community</a>
            <ul>
                <li><a href="/wiki">wiki</a></li>
                <li>support</li>
                <li><a href="/map">map</a></li>
            </ul>
        </li>
        <li><form id="search-form-header" method="post" action="/search"><input type="text" name="search-string" /><button type="submit" name="search" ><span class="fa fa-search" ></span></button></form></li>'
        
        . ($this->loggedin ?
        '
        <li><a href="/user/'.$this->user.' "><img src="'.$profile_picture.'" style="height:30px;" />'.$this->user.'</a>
            <ul>
                <li><a href="/user/' . $this->user . '">my profile</a></li>
                <li><a href="/user/' . $this->user . '/settings">settings</a></li>
                <li><a href="/messages" >messages</a></li>
                <li><a href="/user/' . $this->user . '/dits">my dits</a></li>
                <li>...</li>
                <li><a href="/logout/">logout</a></li>
                <li><a href="/logout-all/">logout everywhere</a></li>
            </ul>
        </li>        
        ':
        '<li><a href="/login" >login</a></li>
        <li><a href="/signup" >signup</a></li>') . '
        ' . ($this->loggedin ?
        '
        <li><a href="/notifications"><span class="fa fa-bell-o" ></span>'
        .(
            $this->profile['notifications']===0
            ?
            ''
            :
            ' ('.$this->profile['notifications'].')'
        ).'
        </a> <a href="/messages/received" ><span class="fa fa-envelope-o" ></span>'
        .(
            $this->profile['unseen-messages']===0
            ?
            ''
            :
            ' ('.$this->profile['unseen-messages'].')'
        ).
        '</a></li>'
        :
        '').'
    </ul>
</div>';
    }
}


