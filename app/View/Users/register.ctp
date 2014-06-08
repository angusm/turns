<!-- app/View/Users/add.ctp -->

<?php
//This page will be used to either register or login users and
//should serve as the basis for the homepage
?>
<?php echo $this->Session->flash('auth'); ?>

<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Register:'); ?></legend>
        <?php echo $this->Form->input('username');
        echo $this->Form->input('password');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Register')); ?>
</div>