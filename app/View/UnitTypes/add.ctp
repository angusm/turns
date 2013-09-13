<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to either register or login users and
//should serve as the basis for the homepage
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('UnitType'); ?>
    <fieldset>
        <legend><?php echo __('Add Unit'); ?></legend>
        <?php echo $this->Form->input('name');
        echo $this->Form->input('damage');
        echo $this->Form->input('defense');
        echo $this->Form->input('teamcost');
        echo $this->Form->input('playcost');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Add')); ?>
</div>