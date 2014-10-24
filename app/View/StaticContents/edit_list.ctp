<?php

foreach( $UIDs as $uidIndex => $uid ){
    echo $this->Html->link(
        $uid['StaticContent']['uid'],
        [
            'controller' => 'StaticContents',
            'action' => 'edit',
            '?' => [
                'uid'   => $uid['StaticContent']['uid']
            ]
        ]
    );
    echo '<BR>';
}
