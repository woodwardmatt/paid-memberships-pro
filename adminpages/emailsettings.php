<?php
	//only admins can get this
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_emailsettings")))
	{
		die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}	
	
	global $wpdb, $msg, $msgt;
	
	//get/set settings
	global $pmpro_pages;
	
	//check nonce for saving settings
	if (!empty($_REQUEST['savesettings']) && (empty($_REQUEST['pmpro_emailsettings_nonce']) || !check_admin_referer('savesettings', 'pmpro_emailsettings_nonce'))) {
		$msg = -1;
		$msgt = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
		unset($_REQUEST['savesettings']);
	}	
	
	if(!empty($_REQUEST['savesettings']))
	{                   		
		//email options
		pmpro_setOption("from_email");
		pmpro_setOption("from_name");
		pmpro_setOption("only_filter_pmpro_emails");
		
		pmpro_setOption("email_admin_checkout");
		pmpro_setOption("email_admin_changes");
		pmpro_setOption("email_admin_cancels");
		pmpro_setOption("email_admin_billing");
		
		pmpro_setOption("email_member_notification");
		
		//assume success
		$msg = true;
		$msgt = "Your email settings have been updated.";		
	}
	
	$from_email = pmpro_getOption("from_email");
	$from_name = pmpro_getOption("from_name");
	$only_filter_pmpro_emails = pmpro_getOption("only_filter_pmpro_emails");
	
	$email_admin_checkout = pmpro_getOption("email_admin_checkout");
	$email_admin_changes = pmpro_getOption("email_admin_changes");
	$email_admin_cancels = pmpro_getOption("email_admin_cancels");
	$email_admin_billing = pmpro_getOption("email_admin_billing");	
	
	$email_member_notification = pmpro_getOption("email_member_notification");
	
	if(empty($from_email))
	{
		$parsed = parse_url(home_url()); 
		$hostname = $parsed["host"];
		$host_parts = explode(".", $hostname);
		if ( count( $host_parts ) > 1 ) {
			$email_domain = $host_parts[count($host_parts) - 2] . "." . $host_parts[count($host_parts) - 1];
		} else {
			$email_domain = $parsed['host'];
		}		
		$from_email = "wordpress@" . $email_domain;
		pmpro_setOption("from_email", $from_email);
	}
	
	if(empty($from_name))
	{		
		$from_name = "WordPress";
		pmpro_setOption("from_name", $from_name);
	}
	
	// default from email wordpress@sitename
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}
	$default_from_email = 'wordpress@' . $sitename;
				
	require_once(dirname(__FILE__) . "/admin_header.php");		
?>

	<form action="" method="post" enctype="multipart/form-data"> 
		<?php wp_nonce_field('savesettings', 'pmpro_emailsettings_nonce');?>
		
		<h2><?php _e('Email Settings', 'paid-memberships-pro' );?></h2>
		<p><?php _e('By default, system generated emails are sent from <em><strong>wordpress@yourdomain.com</strong></em>. You can update this from address using the fields below.', 'paid-memberships-pro' );?></p>
		
		<p><?php _e('To modify the appearance of system generated emails, add the files <em>email_header.html</em> and <em>email_footer.html</em> to your theme\'s directory. This will modify both the WordPress default messages as well as messages generated by Paid Memberships Pro. <a title="Paid Memberships Pro - Member Communications" target="_blank" href="http://www.paidmembershipspro.com/documentation/member-communications/?utm_source=plugin&utm_medium=pmpro-emailsettings&utm_campaign=documentation&utm_content=member-communications">Click here to learn more about Paid Memberships Pro emails</a>.', 'paid-memberships-pro' );?></p>
		
		<table class="form-table">
		<tbody>                
			<tr>
				<th scope="row" valign="top">
					<label for="from_email"><?php _e('From Email', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="text" name="from_email" size="60" value="<?php echo esc_attr($from_email);?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="from_name"><?php _e('From Name', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="text" name="from_name" size="60" value="<?php echo esc_attr($from_name);?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="only_filter_pmpro_emails"><?php _e('Only Filter PMPro Emails?', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="checkbox" id="only_filter_pmpro_emails" name="only_filter_pmpro_emails" value="1" <?php if(!empty($only_filter_pmpro_emails)) { ?>checked="checked"<?php } ?> />
					<label for="only_filter_pmpro_emails"><?php printf( __('If unchecked, all emails from "WordPress &lt;%s&gt;" will be filtered to use the above settings.', 'paid-memberships-pro' ),  $default_from_email );?></label>
				</td>
			</tr>
		</tbody>
		</table>
		
		<?php /* going to put something like this here in next version 
		<h3><?php _e('Modify System-generated Email Templates', 'paid-memberships-pro' );?>:</h3>
		<?php 
			if (function_exists('pmproet_scripts')) 
			{ 
				_e('You have installed the PMPro Email Templates add on. <a href="' . admin_url('admin.php?page=pmpro-email-templates') . '">Click here to modify email templates</a>');
			}			
		?>		
		<p><?php _e('To modify the subject line and body content of system generated emails, <a title="Paid Memberships Pro - Email Templates Plugin" target="_blank" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=pmpro-email-templates-addon'), 'install-plugin_pmpro-email-templates-addon') . '">Install and Activate the PMPro Email Templates add on</a>.', 'paid-memberships-pro' ); ?></p>					
		*/ ?>
		
		<h3><?php _e('Send the site admin emails', 'paid-memberships-pro' );?>:</h3>
		
		<table class="form-table">
		<tbody>                
			<tr>
				<th scope="row" valign="top">
					<label for="email_admin_checkout"><?php _e('Checkout', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="checkbox" id="email_admin_checkout" name="email_admin_checkout" value="1" <?php if(!empty($email_admin_checkout)) { ?>checked="checked"<?php } ?> />
					<label for="email_admin_checkout"><?php _e('when a member checks out.', 'paid-memberships-pro' );?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="email_admin_changes"><?php _e('Admin Changes', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="checkbox" id="email_admin_changes" name="email_admin_changes" value="1" <?php if(!empty($email_admin_changes)) { ?>checked="checked"<?php } ?> />
					<label for="email_admin_changes"><?php _e('when an admin changes a user\'s membership level through the dashboard.', 'paid-memberships-pro' );?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="email_admin_cancels"><?php _e('Cancellation', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="checkbox" id="email_admin_cancels" name="email_admin_cancels" value="1" <?php if(!empty($email_admin_cancels)) { ?>checked="checked"<?php } ?> />
					<label for="email_admin_cancels"><?php _e('when a user cancels his or her account.', 'paid-memberships-pro' );?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="email_admin_billing"><?php _e('Bill Updates', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="checkbox" id="email_admin_billing" name="email_admin_billing" value="1" <?php if(!empty($email_admin_billing)) { ?>checked="checked"<?php } ?> />
					<label for="email_admin_billing"><?php _e('when a user updates his or her billing information.', 'paid-memberships-pro' );?></label>
				</td>
			</tr>
		</tbody>
		</table>
		
		<h3><?php _e('Send members emails', 'paid-memberships-pro' );?>:</h3>
		
		<table class="form-table">
		<tbody>                
			<tr>
				<th scope="row" valign="top">
					<label for="email_member_notification"><?php _e('New Users', 'paid-memberships-pro' );?>:</label>
				</th>
				<td>
					<input type="checkbox" id="email_member_notification" name="email_member_notification" value="1" <?php if(!empty($email_member_notification)) { ?>checked="checked"<?php } ?> />
					<label for="email_member_notification"><?php _e('Default WP notification email. (Recommended: Leave unchecked. Members will still get an email confirmation from PMPro after checkout.)', 'paid-memberships-pro' );?></label>
				</td>
			</tr>
		</tbody>
		</table>
		
		<p class="submit">            
			<input name="savesettings" type="submit" class="button-primary" value="Save Settings" /> 		                			
		</p> 
	</form>

<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");	
?>
