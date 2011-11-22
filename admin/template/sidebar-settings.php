<?php
/**
 * Sidebar SettinRAT Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
<li><a href="settinRAT.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_GEN_SETTINRAT'));?>" <?php check_menu('settinRAT');  ?> ><?php i18n('SIDE_GEN_SETTINRAT'); ?></a></li>
<li><a href="settinRAT.php#profile" accesskey="<?php echo find_accesskey(i18n_r('SIDE_USER_PROFILE'));?>" ><?php i18n('SIDE_USER_PROFILE'); ?></a></li>
<?php exec_action("settinRAT-sidebar"); ?>
</ul>

<?php if(get_filename_id()==='settinRAT') { ?>
<p id="js_submit_line" ></p>
<?php } ?>