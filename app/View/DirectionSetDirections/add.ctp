<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('DirectionSetDirection'); ?>
    <fieldset>
        <legend><?php echo __('Add Direction Set Directions'); ?></legend>
        <?php 	echo $this->Form->input('direction_sets_uid');
				echo $this->Form->input('directions_uid');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>