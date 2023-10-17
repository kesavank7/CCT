<?php include 'header.php'; ?>

<?php include 'menu.php'; ?>
<div class="container">
	<div class="starter-template">
		<!-- Error Page -->
		<?php
		if (validation_errors()) {
			?>
			<div class="alert alert-danger" role="alert">
				<?php echo validation_errors(); ?>
			</div>
		<?php } else if ($this->session->userdata('msg') == 'error') { ?>
			<div class="alert alert-danger" role="alert">
				Invalid User Name and Password combination
			</div>
		<?php } else if ($this->session->userdata('msg') == 'access') { ?>
			<div class="alert alert-danger" role="alert">
				You have No Access
			</div>
		<?php } ?>
		<!-- Error Page -->

		<div class="col-md-12 col-md-offset-3">
			<form action="<?php echo base_url(); ?>user/valid" method="post" class="form-horizontal">
				<div class="form-group">
					<label for="user_name" class="col-sm-2 control-label">Username:</label>
					<div class="col-sm-3">
						<input type="text" autocomplete="off" class="form-control" id="username" name="username"
							   placeholder="Username" value="<?php if (set_value('username')) {
							echo set_value('username');
						} else {
							echo get_cookie('rem_username');
						} ?>" maxlength="40"/>
					</div>
				</div>

				<div class="form-group">
					<label for="password" class="col-sm-2 control-label">Password:</label>
					<div class="col-sm-3">
						<input type="password" autocomplete="off" class="form-control" id="password" name="password"
							   placeholder="Password" value="<?php if (set_value('password')) {
							echo set_value('password');
						} else {
							echo get_cookie('rem_password');
						} ?>" maxlength="40"/>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<label>
							<input type="checkbox" name="remember" id="remember" checked/>
							&nbsp;Remember Me
						</label>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-sm btn-green">Login</button>
						<!-- <button type="submit" class="btn btn-danger" >Cancel</button> -->
						<button type="button" class="btn btn-sm btn-red "
								onclick="window.location.href='<?php echo base_url(); ?>user/login'">Cancel
						</button>
						<!-- <a href="<?php echo base_url(); ?>user/login" class="btn btn-danger" role="button">Cancel</a> -->

					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery("#home").addClass('active');
	jQuery("#username").focus();
</script>

<?php include 'footer.php'; ?>

</body>
</html>
