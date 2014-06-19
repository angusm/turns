<?php

foreach( $UIDs as $uidIndex => $uid ){
    echo $this->Html->link(
        $uid['StaticContent']['uid'],
        array(
            'controller' => 'StaticContents',
            'action' => 'edit',
            '?' => array(
                'uid'   => $uid['StaticContent']['uid']
            )
        )
    );
    echo '<BR>';
}
