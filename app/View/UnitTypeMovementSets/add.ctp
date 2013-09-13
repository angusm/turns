<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('UnitTypeMovementSet'); ?>
    <fieldset>
        <legend><?php echo __('Add Unit Type Movement Set'); ?></legend>
        <?php 	echo $this->Form->input('movement_sets_uid');
        		echo $this->Form->input('unit_types_uid');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>