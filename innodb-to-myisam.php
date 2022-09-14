<?php
/*
Plugin Name: InnoDB to MyISAM
Description: Using this plugin we can convert InnoDB storage engine type to MyISAM . We always recommend backing up your MySQL database before using this plugin.
Author: Visual Web Click
Version: 1.04
Author URI: https://visualwebclick.com/
License: GPLv2 or later
Text Domain: innodb-to-myisam
*/
/*  Copyright 2022  Visual Web Click (email : contact@visualwebclick.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

		add_action('admin_menu', 'ITM_add_option');
		include_once(ABSPATH . 'wp-includes/pluggable.php');
	
		// Add style Function
		function ITM_stylesheet()
        {
	        wp_enqueue_style( 'ITM_css', plugin_dir_url(__FILE__).'css/style.css' );
        }
        add_action( 'admin_enqueue_scripts','ITM_stylesheet' );

		// current user's info 
		$current_user = wp_get_current_user(); 
		if ( !($current_user instanceof WP_User) ) 
    	return; 
		
		function ITM_add_option(){
        	add_menu_page( 'InnoDB to MyISAM', 'INNODB to MyISAM', 'manage_options',basename(__FILE__),'ITM_manage_update');
		}
 
		function ITM_manage_update(){	

		global $wpdb;
	    $list_of_table = $wpdb->get_results("SHOW TABLE STATUS");
		
		?>
		<p><strong>We always recommend backing up your MySQL database before using this plugin.</strong></p>

		<table width="95%" align="center" class="db-rp">
			<tr><td><strong>Table Name</strong></td><td align="center"><strong>Status</strong></td><td align="center"><strong>Downgrade</strong></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<form name="ITM_form" action="admin.php?page=<?php echo basename(__FILE__); ?>" method="post">
			<?php wp_nonce_field('ITM_submit','ITM_nonce'); ?>

			<?php
			foreach($list_of_table as $check) {
			
			?>
				<tr><td><?php echo $check->Name; ?></td><td align="center"  <?php if($check->Engine!='InnoDB') { ?> bgcolor="#009900" <?php } else { ?> bgcolor="#FF0000" <?php } ?>><?php echo $check->Engine; ?></td><td align="center"><input name="tables[]" type="checkbox" value="<?php echo esc_attr($check->Name); ?>" <?php if($check->Engine=='MyISAM') {?> disabled <?php } ?>></td></tr>
			<?php
			}
			?>
			<tr><td colspan="2" align="center"><input type="submit" name="ITM_form_submit" value="Submit"></td><td>For any query or question please email to contact@visualwebclick.com .</td></tr>
			
			
		
		<?php
		if ( isset( $_POST['ITM_form_submit'] ) && !check_admin_referer('ITM_submit','ITM_nonce')){	
		
				$table_checked = esc_attr($_POST['tables']);
			
			echo '<div id="message" class="error fade"><p><strong>'.__('ERROR','innodb-to-myisam').' - '.__('Please try again.','innodb-to-myisam').'</strong></p></div>';

		}
		elseif( isset( $_POST['ITM_form_submit'] ) && isset($_POST['ITM_nonce']) )
		{
			$table_checked = $_POST['tables'];
			//print_r($table_checked);
			if(!empty($table_checked)) {
			foreach($table_checked as $table)
			{
					$repair_db = $wpdb->query("ALTER TABLE $table ENGINE=MYISAM");
					if(!$repair_db) {
						echo '<p style="color: red;">'.esc_html($table).' Engine Type could not be downgraded!</p>';
					} else {
						echo '<p style="color: green;align:middle;">'.esc_html($table).' Engine Type is downgraded!</p>';
					}
				}
			}
			else
			{
				echo '<p style="color: red;"><strong>'.__('ERROR','innodb-to-myisam').' - '.__('Please select a table to downgrade!.','innodb-to-myisam').'</strong></p>';
			}
			?>
			<script type="text/javascript">
				window.location.href = '<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']; ?>';
			</script>
			<?php
		}
		?>
		</form>
		</table>
		<?php
	 }  
?>