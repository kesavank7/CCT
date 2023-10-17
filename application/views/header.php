<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>CGVAK - Consultants Time Tracking</title>

		<!-- Bootstrap -->
		<link rel="icon" href="<?php echo base_url(); ?>public/favicon.png">
		<link href="<?php echo base_url(); ?>public/css/bootstrap.min.css" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>public/css/jquery.dataTables.css" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>public/css/starter-template.css" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>public/css/final_over.css" rel="stylesheet" />
		<!-- <link href="<?php echo base_url(); ?>public/css/datepicker.css" rel="stylesheet" /> -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.4.2/jquery.datetimepicker.min.css" />
		<link href="<?php echo base_url(); ?>public/css/bootstrap-3d.css" rel="stylesheet" />
		
		 
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.4.1/jquery.datetimepicker.js"></script>		 -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.4.2/jquery.datetimepicker.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
		
		<!--<script src="public/js/jquery.min.js"></script>		
		<script src="public/js/bootstrap.min.js"></script>
		<script src="public/js/jquery.dataTables.min.js"></script>
		<script src="public/js/bootstrap-datepicker.js"></script>
		<script src="public/js/jquery.maskedinput.js"></script>-->
		<!--<script src="<?php echo base_url(); ?>public/js/common.js"></script>-->
		
		<!-- Change Theme CSS -->
		<?php 
			$current_theme = "";
			$curr_user 	= $this->session->userdata('id');			
			if($this->session->userdata('id') == get_cookie('current_theme_user'. $curr_user) && get_cookie('current_theme'. $curr_user)) {
				$current_theme = get_cookie('current_theme'.$curr_user);
				echo "<link type='text/css' rel='stylesheet' title='synergy_theme' href='". base_url() ."public/css/theme/". $current_theme ."-bootstrap.min.css' />";
			}
		?>		
		<link href="<?php echo base_url(); ?>public/css/final_over.css" rel="stylesheet" />		
		<script type="text/javascript">
			var jQuery = jQuery.noConflict();
			jQuery(document).ready(function(){
				jQuery('#tbl_lists').DataTable();
				
			});
		</script>		
	<script type="text/javascript"></script><script type="text/javascript"></script><script type="text/javascript"></script></head>
<body>
