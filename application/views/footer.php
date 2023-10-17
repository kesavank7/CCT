	<footer class="footer">
		<div class="container">
			<p class="text-muted">&copy; &nbsp;CG-VAK Software & Exports Ltd. <?php echo date('Y');?></p>
		</div>
	</footer>
<script type="text/javascript">
	jQuery("#log-bar").click(function(event){
		event.stopPropagation();
	});
	
	/*- Change the theme's -*/
	jQuery('#theme_chooser').change(function(){
		var new_CSS = jQuery(this).val();
		window.location.href="<?php echo base_url();?>theme/newtheme?new_theme="+new_CSS;
		/*
		var css_path = "<?php echo base_url();?>public/css/theme/"+ new_CSS +"-bootstrap.min.css";		
		var rel = jQuery('link[title="synergy_theme"]');
		rel.attr('href', css_path);
		*/
	});
	
	/*- hide Alert Message -*/
	jQuery('.alert').delay(5000).fadeOut('slow'); 

	/*- Project Filter Toggle -*/
	jQuery(function(){	
		jQuery('.container').on('click', '.panel-heading span.filter', function(e){
			jQuery( ".panel-body" ).slideToggle( "slow" );
		});
		
		jQuery('[data-toggle="tooltip"]').tooltip();
	});
</script>
<?php
	/*- Destroying Notification session Session -*/
	if($this->session->userdata('msg'))	{
		$this->session->unset_userdata(array('msg'	=> ''));
	}	
?>