<?php
/**
 * Footer Admin Template
 *
 * @package RAT6
 */

?>
		<div id="footer">
      	<div class="footer-left" >
      	<?php 
      		include(RATADMININCPATH ."configuration.php");
      		if (cookie_check()) { 
      			echo '<p><a href="pages.php">'.i18n_r('PAGE_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="upload.php">'.i18n_r('FILE_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="theme.php">'.i18n_r('THEME_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="backups.php">'.i18n_r('BAK_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="plugins.php">'.i18n_r('PLUGINS_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="settinRAT.php">'.i18n_r('GENERAL_SETTINRAT').'</a> &nbsp;&bull;&nbsp; <a href="support.php">'.i18n_r('SUPPORT').'</a></p>';
      		}
      	?>
      		<p>&copy; 2009-<?php echo date('Y'); ?> <a href="http://www.facebook.com/pages/RAT6/196237143792761/" target="_blank" >RAT6 CMS</a> &ndash; <?php echo i18n_r('VERSION') .' '. $site_version_no; ?></p>
      	</div>
      	<div class="RATlogo" >
	      	<a href="http://www.facebook.com/pages/RAT6/196237143792761/" target="_blank" ><img src="template/images/RAT6_logo.gif" alt="RAT6 Content Management System" /></a>
	      </div>
      	<div class="clear"></div>
      	<?php exec_action('footer'); ?>

		</div><!-- end #footer -->
	</div><!-- end .wrapper -->
</body>
</html>