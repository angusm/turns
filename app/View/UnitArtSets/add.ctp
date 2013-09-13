<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to either register or login users and
//should serve as the basis for the homepage
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('UnitArtSet'); ?>
    <fieldset>
        <legend><?php echo __('Add Unit Art Set'); ?></legend>
        <?php echo $this->Form->input('name');
        echo $this->Form->input('unit_types_uid');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>