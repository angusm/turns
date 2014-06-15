<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
        echo $this->element('css');
        echo $this->element('javascript');

		echo $this->Html->meta('icon');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
        <div id="headerGhost" class="layoutHeader"></div>
        <div id="mainPage">
            <div id="contentHeader">
                <?php echo $this->element('content_header'); ?>
            </div>
            <div id="content">
                <?php echo $this->fetch('content'); ?>
            </div>
            <div id="footer">
                <?php echo $this->fetch('footer'); ?>
            </div>

            <?php
                echo $this->element('sql_dump');
            ?>

        </div>
        <div id="mainMenu" class="shadow">
            <?php echo $this->element('main_menu'); ?>
        </div>
        <div id="header" class="layoutHeader shadow">
            <div id="logo"></div>
            <?php echo $this->element('user_bar'); ?>
        </div>
	</div>
</body>
</html>
