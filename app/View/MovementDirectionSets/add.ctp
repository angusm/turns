<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('MovementDirectionSet'); ?>
    <fieldset>
        <legend><?php echo __('Add Movement Direction Set'); ?></legend>
        <?php 	echo $this->Form->input('direction_sets_uid');
				echo $this->Form->input('movements_uid');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>