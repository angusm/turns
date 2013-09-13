<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to add direction set names
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('CardArtLayerSet'); ?>
    <fieldset>
        <legend><?php echo __('Add Card Art Layer Set'); ?></legend>
        <?php 	echo $this->Form->input('position');
        		echo $this->Form->input('card_art_layers_uid');
        		echo $this->Form->input('unit_art_sets_uid');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>