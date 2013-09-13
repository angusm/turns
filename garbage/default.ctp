<?php
/**
	*Layout set to match that created by Abendego for avpbc.com
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');

/*Document Type*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>E-Ordering</title>
	<?php
		echo $this->Html->meta('icon');

//Include all of our CSS
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('colorbox');
		echo $this->Html->css('global');
		echo $this->Html->css('template');
		echo $this->Html->css('e.orders');

//Include all of our Javascript
		echo $this->Html->script('JQuery');
		echo $this->Html->script('Core');
		echo $this->Html->script('JQueryUI');
		echo $this->Html->script('JQueryUITouch');
		echo $this->Html->script('JQUIFunctions');

	?>
</head>
<body id="members">
<script>isIE = false;</script>
<?/***********************************************
	HEADER
***********************************************/?>
	<div id="wrapper">
		<div id="avpbc_container">
			<div id="avpbc_header">
				<div id="logo"><a href="http://www.avpbc.com/index.php">Associated Veterinary Purchasing Co. Ltd.</a></div>
			</div><!-- end of avpbc header -->
			<div id ="frame">
				<div id="mainnav">
					<ul>
						<li class="level_1 first"><a href="http://www.avpbc.com/index.php?id=7" target="_self"  class="level_1 first">About Us</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=10" target="_self"  class="level_1">Ordering</a></li>
						<li class="level_1 active current"><a href="http://www.avpbc.com/index.php?id=25" target="_self"  class="level_1 active
						current">
						E-Ordering </a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=20" target="_self"  class="level_1">Veterinary Software</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=34" target="_self" class="level_1">Products</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=27" target="_self" class="level_1">Damages, Tracking and Returns
						</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=43" target="_self"  class="level_1">My Account</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=54" target="_self"  class="level_1">Shareholders</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=53" target="_self"  class="level_1">News and Events</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=59" target="_self"  class="level_1">Industry Links</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=49" target="_self"  class="level_1">Suppliers</a></li>
						<li class="level_1"><a href="http://www.avpbc.com/index.php?id=61" target="_self"  class="level_1">Forms </a></li>
						<li class="level_1 last"><a href="http://www.avpbc.com/index.php?id=60" target="_self"  class="level_1 last">E-Newsletter</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div><!-- end of mainnav -->
				<div id="introtext" class="bottom">
					<?php echo $this->Html->image('header/building.jpg', array('alt' => 'intro image', 'id' => 'headimg', 'class' => 'bottom')); ?>
					<!--<img src="/img/header/building.jpg" alt="intro image" id="headimg" class="bottom"/>-->
					<h1>E-Ordering</h1>
				</div><!-- end of introtext -->
				<div id="memberlogin">
					<div class="pad">
						<h3>AVP Announcements</h3>
						<ul>
							<?php
							foreach( $announcements as $announcement ){
								echo '<div class="item"><li>';
								echo $announcement['Announcement']['strText'];
								echo '</li></div>';
							}
							?>
						</ul>
					</div><!-- pad  -->
				</div><!-- end of memberlogin -->
				<div class="clearfix"></div>
			</div><!-- end of frame -->
			<div id="mainbody" class="wide">
				<div id="col-a">
					<ul>
						<li><a href="<?php echo $this->Html->url(array("controller" => "HistoricOrders", "action" => "searchHistory")); ?>"><span>Order History</span></a></li>
					</ul><ul>
						<li><a href="<?php echo $this->Html->url(array("controller" => "Orders", "action" => "orderForm")); ?>"><span>Order Form</span></a></li>
						<li><a href="<?php echo $this->Html->url(array("controller" => "Items", "action" => "searchCatalogue")); ?>"><span>Search Catalogue</span></a></li>
						<li><a href="<?php echo $this->Html->url(array("controller" => "ProductCategories", "action" => "manageProductCategories")); ?>"><span>Product Categories</span></a></li>
						<li><a href="<?php echo $this->Html->url(array("controller" => "ProductCategories", "action" => "manageClinicCatalogue")); ?>"><span>Personal/Clinic Catalogue</span></a></li>
						<li><a href="<?php echo $this->Html->url(array("controller" => "Backorders", "action" => "viewBackorders")); ?>"><span>Back Ordered Items</span></a></li>
					</ul><ul>
						<li class="unavailable"><a href="http://www.avpbc.com"><span>Login To Narcotics</span></a>
					</ul><ul>
						<li><a href="<?php echo $this->Html->url(array("controller" => "ClinicAdmins", "action" => "manageAccounts")); ?>"><span>Account Management</span></a>
						<?php
						 	//Handle pop down if selected
							if( isset($managingAccounts) and $managingAccounts ){
							
								echo '<ul><li><a href="' . 
									$this->Html->url(array("controller" => "ClinicAdmins", "action" => "setupNewAccount")) .
									'"><span>';
								echo 'Add Account';
								echo '</span></a></li></ul>';
							
							}
						?>
						</li>
					</ul>

				</div>
				<div id="bodycontent" class="wide">
					<div class="pad">

<?/***********************************************


	CONTENTS
***********************************************/?>

							<?php echo $this->Session->flash();

							//This line loads up the actual page contents...
							echo $this->fetch('content'); ?>
						</div>
<?/***********************************************
	FOOTER
***********************************************/?>
					</div><!-- End Pad -->
				</div><!-- end of body content -->
				<div class="clearfix"></div>
				<div id="cookie"><p><a href="http://www.avpbc.com/index.php?id=5" class="">Members</a> <span>></span> E-Ordering </p></div>
			</div><!-- end of main body -->
			<div id="util">
				<ul>
					<li><a href="http://www.avpbc.com/index.php?id=63">Privacy policy</a></li>
					<li><a href="http://www.avpbc.com/index.php">Find a clinic</a></li>
					<li><a href="http://www.avpbc.com/index.php?id=9">Contact us</a></li>
					<li><a href="http://www.avpbc.com/index.php">Public Home</a></li>
					<li><a href="http://www.avpbc.com/_customelements/_actions/logout.php">Logout</a></li>
				</ul>
				<p><?php 
				echo $clinicName; 
				?></p>
			</div> <!-- end of util -->
			<div id="avpbc_footer">
				<div id="footer-contact">
					<p>27533 50th Avenue  |  Langley, BC  |  V4W 0A2  |  Tel 604.856.2110  |  Fax 604.856.2115  | Toll free tel 1-800-663-1926  |  Toll
					free
					fax 1-877-363-5533</p>
				</div>
			</div><!-- end of avpbc_footer -->
		</div><!-- end of avpbc_container -->
	</div><!-- end of wrapper -->

	<?php //Script
		echo $this->element('JSVars/allTheURLs');
		echo '<script class="scriptDump"></script>';
		echo '<div class="scriptDump"></div>';
		echo '<script>var getJSURL = "'. $this->Html->url(array("controller" => "js", "action" => "")) .'/";</script>';
		echo '<script>sessionRenewURL = "'. $this->Html->url(array("controller" => "Sessions", "action" => "renewSession")) .'";</script>';
		echo $this->Html->script('TemplateSessionCountdown');
		echo $this->Html->script('PopUpHandler');

	?>

	<?php /*DEBUG JUNK*/ ?>

	<?php echo $this->element('sql_dump'); ?>
					<div class="blackBackdrop"></div>
					<div class="renewSessionPrompt">Are you still there?<BR>You've been inactive for a very long time.<BR>
						<input type="button" class="renewSessionButton" value="I'm still here"><BR>
						<div class="sessionRenewalNotification"></div>
					</div>
					<div id="popUpContainer"></div>


</body>
</html>
















