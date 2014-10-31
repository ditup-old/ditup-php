<?php

class Header
{
    function __construct($loggedin=false,$username='')
    {
        $this->loggedin = $loggedin;
        $this->user = $username;
        return $this;
    }

    function generate()
    {
        return '<div class="main-menu-top">
    <ul>
        <li><a href="/" ><img src="/img/logo.png" style="height:20px;" /> ditup</a>
            <ul>
                <li><a href="/start">about</a></li>
                <li><a href="/development">development</a></li>
                <li>feedback</li>
            </ul>
        </li>
        <li><a href="/people">people</a>
            <ul>' . ($this->loggedin ? '<li><a href="/user/' . $this->user . '">my profile</a></li>' : '') . '
                <li>groups</li>
            </ul>
        </li>
        <li><a href="/projects" >projects</a>
            <ul>
                <li>explore (categories, tags)</li>' . ($this->loggedin ? '
                <li><a href="/projects/create">+ create new project</a></li>' : '') . '
                
            </ul>
        </li>
        <li><a href="/community" >community</a>
            <ul>
                <li><a href="/wiki">wiki</a></li>
                <li>support</li>
            </ul>
        </li>
        <li>search <input /></li>' . ($this->loggedin ?
        '
        <li>me
            <ul>
                <li><a href="/user/' . $this->user . '">my profile</a></li>
                <li>messages</li>
                <li>my projects</li>
                <li>...</li>
                <li><a href="/logout/">logout</a></li>
            </ul>
        </li>        
        ':
        '<li><a href="/login" >login</a></li>
        <li><a href="/signup" >signup</a></li>') . '
    </ul>
</div>';
    }
}


