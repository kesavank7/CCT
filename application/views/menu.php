<nav class="navbar navbar-inverse navbar-fixed-top" id="menu_bar">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
					aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo base_url(); ?>">
				<img src="<?php echo base_url(); ?>public/images/logo/logo.png" alt="CG-Vak Logo" name="cgvak_logo"
					 class="header_logo"/>
			</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php if (!$this->session->userdata('id')): ?>
					<li id="home"><a href="#">Login</a></li>
				<?php else : ?>
					<?php if ($this->session->userdata('role') === 'consultant'): ?>
						<li id="consultant_task_progress" class="<?php echo (current_url() == base_url().'cosultantprogress/progress') ? 'active' : ''; ?>"><a
									href="<?php echo base_url(); ?>cosultantprogress/progress">Consultant
								Task Progress</a></li>
						<li id="consultant_invoice" class="<?php echo (current_url() == base_url().'ApproveConsultant/consultantInvoice') ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>ApproveConsultant/consultantInvoice">Consultant Invoice</a></li>
					<?php else : ?>
						<?php if ($this->session->userdata('role') === 'Lead'): ?>
							<li id="consultant_task_entry" class="<?php echo (current_url() == base_url().'consultanttask/entry') ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>consultanttask/entry">Consultant
									Task
									Entry</a></li>
						<?php endif; ?>
						<?php if($this->session->userdata('role') === 'HR' || $this->session->userdata('role') === 'Account' || $this->session->userdata('role') === 'Lead'): ?>
							<li id="consultant" class="<?php echo (current_url() == base_url().'consultant/get_consultant_list') ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>consultant/get_consultant_list">Consultants List</a>
							</li>
						<?php endif; ?>
						<li id="approve_consultant" class="<?php echo (current_url() == base_url().'ApproveConsultant') ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>ApproveConsultant">Consultant Timesheet</a></li>
						<?php if($this->session->userdata('role') === 'Account' || $this->session->userdata('role') === 'HR'): ?>
							<li id="consultant_invoice" class="<?php echo (current_url() == base_url().'ApproveConsultant/consultantInvoice') ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>ApproveConsultant/consultantInvoice">Consultant Invoice</a></li>
						<?php endif; ?>
						<!-- <li id="course"><a href="<?php echo base_url(); ?>Courses">Courses</a></li>
						<li id="course"><a href="<?php echo base_url(); ?>Holiday">Holidays</a></li> -->
						<!--						<li id="course"><a href="--><?php //echo base_url(); ?>
						<!--Sendingemail_Controller">Email</a></li>-->
						<!--<li id="image"><a href="--><?php //echo base_url();?>
						<!--ImageUploadController">ImageUploader</a></li>-->
					<?php endif; ?>
				<?php endif; ?>
			</ul>
			<?php if ($this->session->userdata('id')): ?>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span class="glyphicon glyphicon-user"></span> &nbsp;&nbsp;&nbsp;
							<span
									class="head_red_txt"><strong><?php echo $this->session->userdata('first_name'); ?></strong></span>
							&nbsp;
							<span class="glyphicon glyphicon-chevron-down"></span>
						</a>
						<ul class="dropdown-menu" id="log-bar">
							<li>
								<div class="navbar-login">
									<div class="row remove-mtop">
										<div class="col-lg-4">
											<p class="text-center">
												<!-- User Image--->
												<?php
												/*if($this->session->userdata('emp_photo')):
													echo "<img src='".$this->session->userdata('emp_photo')."' alt='emp_icon' /> ";
													//echo $this->session->userdata('emp_photo');
												endif;
												*/
												?>
												<span class="glyphicon glyphicon-user icon-size"></span>
											</p>
										</div>
										<div class="col-lg-8">
											<p class="text-left">
												<strong><?php echo $this->session->userdata('display_name'); ?></strong>
											</p>
											<?php if ($this->session->userdata('role') !== 'consultant' && $this->session->userdata('role') == 'consultant') { ?>

												<p class="text-left small">
													<a href="<?php echo base_url(); ?>user/passupt" class="passupt"
													   title="Change Password" data-toggle="tooltip"
													   data-placement="top">
														Change Password
													</a>
												</p>
											<?php } ?>
											<p class="text-left">
												<!-- theme's -->
												<select id="theme_chooser" name="bootstrap-theme"
														class="form-control wid-3_4">
													<option value="">Default Theme</option>
													<option value="amelia"
															<?php echo ($current_theme == "amelia") ? "selected" : ''; ?>>
														Amelia
													</option>
													<option value="cerulean"
															<?php echo ($current_theme == "cerulean") ? "selected" : ''; ?>>
														Cerulean
													</option>
													<option value="cosmo"
															<?php echo ($current_theme == "cosmo") ? "selected" : ''; ?>>
														Cosmo
													</option>
													<option value="cyborg"
															<?php echo ($current_theme == "cyborg") ? "selected" : ''; ?>>
														Cyborg
													</option>
													<option value="darkly"
															<?php echo ($current_theme == "darkly") ? "selected" : ''; ?>>
														Darkly
													</option>
													<option value="flatly"
															<?php echo ($current_theme == "flatly") ? "selected" : ''; ?>>
														Flatly
													</option>
													<option value="journal"
															<?php echo ($current_theme == "journal") ? "selected" : ''; ?>>
														Journal
													</option>
													<option value="readable"
															<?php echo ($current_theme == "readable") ? "selected" : ''; ?>>
														Readable
													</option>
													<option value="slate"
															<?php echo ($current_theme == "slate") ? "selected" : ''; ?>>
														Slate
													</option>
													<option value="spacelab"
															<?php echo ($current_theme == "spacelab") ? "selected" : ''; ?>>
														Spacelab
													</option>
													<option value="united"
															<?php echo ($current_theme == "united") ? "selected" : ''; ?>>
														United
													</option>
												</select>
											</p>
										</div>
									</div>
								</div>
							</li>
							<li class="divider"></li>
							<li>
								<div class="navbar-login navbar-login-session">
									<div class="row remove-mtop">
										<div class="col-lg-12">
											<p>
												<?php if ($this->session->userdata('role') == 'consultant') { ?>
													<a role="button" class="btn btn-danger btn-default btn3d  btn-block"
													   href="<?php echo base_url(); ?>consultantuser/logout"
													   data-toggle="tooltip" data-placement="top" title="Log-Out"><span
																class="glyphicon glyphicon-log-out"></span> Logout</a>
												<?php } else { ?>
													<a role="button" class="btn btn-danger btn-default btn3d  btn-block"
													   href="<?php echo base_url(); ?>user/logout" data-toggle="tooltip"
													   data-placement="top" title="Log-Out"><span
																class="glyphicon glyphicon-log-out"></span> Logout</a>
												<?php } ?>
											</p>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			<?php endif; ?>
		</div>
		<!--/.nav-collapse -->
	</div>
</nav>
