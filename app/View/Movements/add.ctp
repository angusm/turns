<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('Movement'); ?>
    <fieldset>
        <legend><?php echo __('Add Movement'); ?></legend>
        <?php 	echo $this->Form->input('movement_sets_uid');
        		echo $this->Form->input('spaces');
         		echo $this->Form->input('priority');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>