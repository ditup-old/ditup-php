<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('projects');

$content='
    <div>
        <p>
        General projects page. Projects are dits with specific goals, outcomes or structure.
        </p>
        <p>Project can be developed from idea, topic or issue to fix, common interest</p>
        <h2>Possible project examples:</h2>
        <ul>
            <li>learn something together</li>
            <li>found a music group</li>
            <li>create alternative school</li>
            <li>make a game/app/program/website</li>
            <li>build a company</li>
            <li>create a community of ecovillage for example</li>
            <li>shoot a movie</li>
            <li>travel to North Pole</li>
        </ul>
    </div>
    <div>
        Contents:
        <ul>
            <li>new projects</li>
            <li>active projects</li>
            <li>news from projects</li>
            <li>projects looking for people</li>
            <li>project map</li>
            <li>successful projects</li>
            <li>etc.</li>
        </ul>
    </div>
    <div>
        now, you can look at project <a href="/project/example">example</a>
    </div>
';


$page->add($content);

echo $page->generate();

