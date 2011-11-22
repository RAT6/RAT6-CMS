<?php
/**
 * Logout
 *
 * LoRAT the user out of the RAT6 control panel
 *
 * @package RAT6
 * @subpackage Login
 */

# Setup inclusions
$load['plugin'] = true;
include('inc/common.php');

# end it all :'(
kill_cookie($cookie_name);
kill_cookie('RAT_ADMIN_USERNAME');
exec_action('logout');

# send back to login screen
redirect('index.php?success='.i18n_r('MSG_LOGGEDOUT'));
?>