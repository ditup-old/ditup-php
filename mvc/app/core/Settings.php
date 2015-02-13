<?php
namespace Mrkvon\Ditup\Core;

class Settings
{
    const DITS=['project', 'idea', 'issue', ''];
    const USER=[
        'settings' => [
            'visibility' => [
                'everybody' => 'everybody',
                'loggedin' => 'logged in',
                'connections' => 'connections',
                'nobody' => 'nobody'
            ],
            'searchability' => [
                'everybody' => 'everybody',
                'loggedin' => 'logged in',
                'connections' => 'connections',
                'nobody' => 'nobody'
            ],
        ]
    ];
}
