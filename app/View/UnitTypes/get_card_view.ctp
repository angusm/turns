<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to either register or login users and
//should serve as the basis for the homepage
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('UnitType'); ?>
    <fieldset>
        <legend><?php echo __('View Card'); ?></legend>
        <?php echo $this->Form->select('UID', $uids);
    ?>
    </fieldset>
<?php echo $this->Form->end(__('View Card')); ?>
</div>