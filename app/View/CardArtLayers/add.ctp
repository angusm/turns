<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('CardArtLayer'); ?>
    <fieldset>
        <legend><?php echo __('Add Card Art Layer'); ?></legend>
        <?php 	echo $this->Form->input('name');
        		echo $this->Form->input('image');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>