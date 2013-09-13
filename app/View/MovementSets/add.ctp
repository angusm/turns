<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('MovementSet'); ?>
    <fieldset>
        <legend><?php echo __('Add Movement Set'); ?></legend>
        <?php echo $this->Form->input('name');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>