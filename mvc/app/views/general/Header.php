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
        return '<div>
    <ul>
        <li>ditup (logo will be here)
            <ul>
                <li><a href="/start">about</a></li>
                <li><a href="/development">development</a></li>
                <li>feedback</li>
            </ul>
        </li>
        <li>people
            <ul>' . ($this->loggedin ? '<li><a href="/people/' . $this->user . '">my profile</a></li>' : '') . '
            </ul>
        </li>
        <li>projects</li>
        <li>community
            <ul>
                <li><a href="/wiki">wiki</a></li>
                <li>support</li>
            </ul>
        </li>
        <li>search <input /></li>' . ($this->loggedin ?
        '':
        '<li><a href="/login" >login</a></li>
        <li><a href="/signup" >signup</a></li>') . '
    </ul>
</div>';
    }
}


