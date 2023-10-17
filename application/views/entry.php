<?php include 'header.php'; ?>	

<?php include 'menu.php'; ?>	
	<div class="container">
		<div class="starter-template">
			<!-- Error Message -->
			<?php if($this->session->userdata('msg') == 'succ'){ ?>
				<div class="alert alert-success " role="alert">
					<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
					<span class="sr-only">Error:</span> Login Successfully.
				</div>				
				<?php } elseif($this->session->userdata('msg') == 'upt'){ ?>
				<div class="alert alert-success " role="alert">
					Successfully Updated the Task.
				</div>				
				<?php } elseif($this->session->userdata('msg') == 'new'){ ?>
				<div class="alert alert-success " role="alert">
					Successfully Added New Task.
				</div>				
				<?php } elseif($this->session->userdata('msg') == 'act'){ ?>
				<div class="alert alert-success " role="alert">
					Successfully Deactived the Task.
				</div>
				<?php } else if(validation_errors()) { ?>
					<div class="alert alert-danger" role="alert">
						<?php echo validation_errors(); ?>						
					</div>
				<?php }	?>
			<!-- Error Message -->
			
			
			<!-- Adding Fields -->
			<div class="add_filter">
				<div id="no-more-tbl">
					<form action="<?php echo base_url()."task/entry_insert"; ?>" id="task_entry_form" method="post" class="form-horizontal">
						<input type="hidden" name="IsActive" id="IsActive" value="1" />
						<input type="hidden" name="CompanyICode" id="CompanyICode" value="1" />
						<!--input type="hidden" name="ProjectPhaseICode" id="ProjectPhaseICode" value="1" /-->
						<input type="hidden" name="Status" id="Status" value="O" />
						<input type="hidden" name="CreatedBy" id="CreatedBy" value="<?php echo $this->session->userdata('id');?>" />
						<input type="hidden" name="CreatedDate" id="CreatedDate" value="<?php echo date('Y-m-d H:i:s'); ?>" />
						<table class="col-md-12 table-bordered table-striped table-condensed cf table-hover" id="tbl_list_entry">
							<!--
							<thead>
								<tr class="btn-default">
									<th width="22%">Project Name</th>
									<th width="30%">Task Description</th>
									<th width="30%">Test Scenarios</th>
									<th width="13%">Estimated Hours / Start / End Date</th>
									<th width="5%">Action</th>
								</tr>
							</thead>
							-->
							<tbody>
								<tr>
									<td data-title="" width="20%">
										<select class="form-control" id="ProjectICode" name="ProjectICode">
											<option value="">Select Project</option>
											<?php foreach($emp_project as $project_detail){?>
												<option value="<?php echo $project_detail['ProjectICode'];?>" <?php echo ($search_project_id ==  $project_detail['ProjectICode']) ? ' selected ' : "" ?>>
													<?php echo trim($project_detail['ProjectName']);?>
												</option>
											<?php } ?>
										</select></br>
											<select class="form-control" name="PhaseTypeICode" id="PhaseTypeICode">
											<option value=''> Select Phase</option>		
											</select>
											</br>
											<select class="form-control" name="TaskTypeICode" id="TaskTypeICode">
											<option value=''> Select Task Type</option>		
											</select>
									</td>
									<td data-title="" width="25%">
										<textarea class="form-control" rows="8" id="TaskDescription" name="TaskDescription" placeholder="Task Description"></textarea>
									</td>
									<td data-title="" width="25%">
										<textarea class="form-control" rows="8" id="TestScenarioDescription" name="TestScenarioDescription" placeholder="Test Scenarios"></textarea>
									</td>
									<td data-title="" width="12%">
										<div class="est_txt">Estimated Hours :</div>
										<input type="text" name="EstimatedHours" id="EstimatedHours" class="form-control timepicker"  placeholder="Est Hours" value="08.00" />
										<div class="est_txt">Start Date :</div>
                                                                                
                                                                                <?php
                                                                                $dat = date('d-m-Y', strtotime('-6 hour'));
                                                                                ?>
										<input type="text" name="TaskStartDate" id="TaskStartDate" class="form-control datepicker"   placeholder="Start Date"  value="<?php echo $dat; ?>" />
										<div class="est_txt">End Date :</div>
										<input type="text" name="TaskEndDate" id="TaskEndDate" class="form-control datepicker"  placeholder="End Date" value="<?php echo $dat; ?>" />
									</td>
									<td data-title="" width="5%" align="center">
										<a href='javascript:' class=" center_save pos-rel" onclick="return valid();">
											<!--task-save
											<span class="glyphicon glyphicon-plus"></span>
											glyphicon glyphicon-save-file
											-->
											<span class="glyphicon glyphicon-saved" data-placement="top" data-toggle="tooltip"  title="Save" ></span>
										</a> 
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
			<!-- Adding Fields -->

		<!-- Filter --->
		<div class="panel panel-info ovrhid">
			<div class="panel-heading">
				<h3 class="panel-title">Task Entries</h3>
				<div class="pull-right">
					<span class="clickable filter" data-toggle="tooltip" title="Project Filter" data-container="body">
						<i class="glyphicon glyphicon-filter"></i>
					</span>
				</div>
			</div>
					
			<div class="panel-body">
				<form action="<?php echo base_url()."task/entry_search"; ?>" id="task_entry_search_form" method="post" class="form-horizontal">			
					<div class="form-group col-sm-12">
						<div class="col-md-2">
							<label for="pro_search_filter" class="control-label ">Project Filter:</label>
						</div>
						<div class="col-md-6">
							<select class="form-control" id="entry_search_filter" name="entry_search_filter" onchange="this.form.submit();">
								<option value="">Select Project</option>
								<?php foreach($emp_project as $project_detail){?>
									<option value="<?php echo $project_detail['ProjectICode'];?>" <?php echo ($search_project_id ==  $project_detail['ProjectICode']) ? ' selected ' : "" ?> >											
										<?php echo trim($project_detail['ProjectName']);?>
									</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</form>
			</div>
			<!-- Filter --->			
			
				<div class="form-group col-sm-12">
					<!-- Loding Bar -->
					<div class="loading-bar" id="loading-bar">
						<div class="loading-inner">
							<div class="progress">
							  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								<span class="sr-only">100% Complete</span>
							  </div>
							</div>
						</div>
					</div>					
					<!-- Table Entrying -->
					<form action="<?php echo base_url(); ?>task/entry_update" id="task_entry_edit_form" method='post' class='form-horizontal'>
						<div id="no-more-tables">
							<!-- <table class="col-md-12 table-bordered table-striped table-condensed cf table-hover" id="tbl_list"> -->
							<table class="table table-hover" id="tbl_list">
								<thead>
									<tr>
										<th width="1%">#</th>
										<th width="22%">Project Name</th>
										<th width="30%">Task Description</th>
										<th width="30%">Test Scenarios</th>
										<th width="12%">Estimated Hours / Start / End Date</th>
										<th width="5%">Action</th>
									</tr>
								</thead>
								<tbody>
								<?php if(count($entry_list) == 0 )  {?>
									<tr><td colspan="6">No Task Found </td></tr>
								<?php } else { 
										$sno = 0; /* Serial number*/
										foreach($entry_list as $list_val) 
										{ 									
									?>
									<tr id="new_task_list_<?php echo $list_val['TaskICode']; ?>">								
										<td data-title="#" scope="row"><?php echo $sno = $sno + 1;  ?></td>
										<td data-title="Project Name">
											<!-- <input type="text" name="TaskICode" id="TaskICode" value="<?php echo $list_val['TaskICode']; ?>" /> -->
												<?php foreach($emp_project as $project_detail){ ?>
													<?php echo ($list_val['ProjectICode'] ==  $project_detail['ProjectICode']) ? $project_detail['ProjectName'] : "" ?>
												<?php } ?>
										</td>

										<td data-title="Task Description" class="pos-rel task-desc">
											<div class="tbl_row_scroll_entry">
												<?php echo $list_val['TaskDescription']; ?>
											</div>											
										</td>
										
										<?php  
											$scenario = "";
											$scenarioList = "";
											foreach ($scenario_list[$list_val['TaskICode']] as $scenario_lists) { 
												$scenario .= $scenario_lists['TestScenarioDescription'].' <br/>';
												//$scenarioList .= $scenario_lists['TestScenarioDescription'].' ';
											} 
										
										?>
										<td data-title="Test Scenarios" class="pos-rel task-desc">
											<div class="tbl_row_scroll_entry">
												<?php echo $scenario;?>	
											</div>
										</td>
										<td data-title="Estimated Hours / Start / End Date">
											<?php echo $list_val['EstimatedHours']; ?> 
											 / <br/>
											<?php if($list_val['TaskStartDate']) { echo date('d-m-Y', strtotime($list_val['TaskStartDate']));}  ?> 
											 / <br/>
											<?php if($list_val['TaskEndDate'])  { echo date('d-m-Y', strtotime($list_val['TaskEndDate'])); }?>
										</td>
										<td data-title="Action" onmouseover='init_tooltip();'>&nbsp; &nbsp;
											<!-- <a href='javascript:'class="pos-rel" onclick="return edit_entry(<?php //echo $list_val['TaskICode']. ",".$sno; ?>);"> 
												<span class="glyphicon glyphicon-pencil" title="Edit" data-toggle="tooltip" data-placement="top"></span>
											</a>
											<div>&nbsp;</div>
											&nbsp; &nbsp;-->
											<a href='javascript:' onclick="return inactive_entry(<?php echo $list_val['TaskICode']; ?>);" class="task-close pos-rel">
												<span class="glyphicon glyphicon-ban-circle" title="Deactivate" data-toggle="tooltip" data-placement="top"></span>
											</a>
										</td>
									</tr>							
								<?php
										} //foreach
									} //else 						
								?>
								</tbody>
							</table>
						</div>
					</form>
					<p>&nbsp;</p>			
				</div>
			</div>
	</div>	
</div>	
<input type="hidden" name="old_txt" id="old_txt" />
<?php 
$val = "\"<td data-title='#' id='edit_sno'>&nbsp;</td><td data-title='Project Name'><input type='hidden' name='TaskICode' id='edit_TaskICode' /><select class='form-control' id='edit_ProjectICode' name='ProjectICode'><option value=''>Select Project</option>";

foreach($emp_project as $project_detail){
	$val .= "<option value='".$project_detail['ProjectICode']."'>". trim($project_detail['ProjectName']) ."</option>";
}
$val .="</select></td><td data-title='Task Description'><textarea class='form-control' rows='5' id='edit_TaskDescription' name='TaskDescription' placeholder='Task Description'></textarea></td><td data-title='Test Scenarios'><textarea class='form-control' rows='5' id='edit_TestScenarioDescription' name='TestScenarioDescription'   placeholder='Test Scenarios'></textarea></td> <td data-title='Estimated Hours / Start / End Date'><input type='text' name='EstimatedHours' id='edit_EstimatedHours' class='form-control timepicker' placeholder='Est Hours' /><div>&nbsp;</div><input type='text' name='TaskStartDate' id='edit_TaskStartDate' class='form-control datepicker' placeholder='Start Date'/><div>&nbsp;</div><input type='text' name='TaskEndDate' id='edit_TaskEndDate' class='form-control datepicker'  placeholder='End Date' /></td><td data-title='Action' onmouseover='init_tooltip();'><a href='javascript:' onclick='return update_entry();' class='pos-rel'><span class='glyphicon glyphicon-ok-circle' title='Update' data-toggle='tooltip' data-placement='top' ></span></a> <div>&nbsp;</div><a href='javascript:' onclick='return cancel_entry();' class='pos-rel'><span class='glyphicon glyphicon-remove-circle' title='Cancel' data-toggle='tooltip' data-placement='top'></span></a></td><input type='hidden' name='page' id='page' value='' /> \"";

?>
	<!-- Using this div for removing tooltip in appending edit content -->
	<div id="remove_html"></div>
	<script type="text/javascript">
	function init_tooltip()
	{
		jQuery('[data-toggle="tooltip"]').tooltip();
	}	
	var htm = <?php echo $val; ?>;
	var edit_open  = 0;	
	/*
		Cancel the Edit Record
	*/	
	function cancel_entry()
	{
		id = jQuery("#edit_TaskICode").val();		
		//jQuery("#new_task_list_" + id).html(jQuery("#old_txt").val());
			jQuery("#remove_html").html(jQuery("#old_txt").val());
			jQuery("#remove_html .tooltip.fade.top").remove();
			jQuery("#new_task_list_" + id).html(jQuery("#remove_html").html());
		edit_open = 0;
		
		jQuery(".glyphicon.glyphicon-pencil").tooltip('hide');
		// .tooltip.fade.top
		return false;
	}
	
	/*
		Convert the Date format
	*/	
	function changeDate(myDate)
	{
		myDate =myDate.split("-");
		var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
		return new Date(newDate).getTime();	
	}
	
	/*
		Calculate total work time (24 hours per day)
	*/	
	function total_worktime(StartDate, EndDate)
	{
		var sdate = changeDate(StartDate);
		var edate = changeDate(EndDate);
		var timeDiff = Math.abs(edate - sdate);
		var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
		return ((diffDays * 24) + 24);
	}
	
	function check_esttime(StartDate, EndDate, esttime)
	{
		var est = esttime.split(".");
		var tot_time = total_worktime(StartDate, EndDate);
		esttime = parseFloat(esttime);		
		if(est[0] == "00" && est[1] == "00")
		{
			return "not valid";
		}
		else if(est[0] == "00" && est[1] < "15")
		{
			return "Minimum Hours";
		}
		else if( tot_time >= esttime)
		{
			return true;
		}
		return false;
	}

	
	/*
		Validate Start date and End date
	*/
	function DateCheck(StartDate, EndDate)
	{

		var sdate = changeDate(StartDate);
		var edate = changeDate(EndDate);
		 if (sdate >  edate) {			
			return false;
		}		
		return  true;
	}
	
	/*
		Valid the Editing Records
	*/
	function update_entry()
	{
		if(!jQuery("#edit_ProjectICode").val().trim())
		{
			alert("Choose the Project Name...");
			jQuery("#edit_ProjectICode").focus();
			return false;
		}
		if(!jQuery("#edit_TaskDescription").val().trim())
		{
			alert("Enter the Task Description...");
			jQuery("#edit_TaskDescription").focus();
			return false;
		}
		if(!jQuery("#edit_TestScenarioDescription").val().trim())
		{
			alert("Enter the Test Scenario Description...");
			jQuery("#edit_TestScenarioDescription").focus();
			return false;
		}	
		if(!jQuery("#edit_EstimatedHours").val().trim())
		{
			alert("Enter the Estimated Hours...");
			jQuery("#edit_EstimatedHours").focus();
			return false;
		}
		/*Check Decimal Format*/
		var pattern_time=  /^[+]?[0-9]+\.[0-9]+$/;
		var est_hour_val = jQuery("#edit_EstimatedHours").val();
		if(!est_hour_val.match(pattern_time))  {
			alert("Please check the Estimated Hours Format[00.00] ");
			jQuery("#edit_EstimatedHours").focus();
			return false;
		}
		/*- Validate the Decimal Points-*/
		var arr = document.getElementById("edit_EstimatedHours").value.split('.');
		var length = arr[1].length 
		if (length > 2)
		{
			alert("Allowed Only 2 digit after decimal point.");
			jQuery("#edit_EstimatedHours").focus();
			return false;
		}
		if(!jQuery("#edit_TaskStartDate").val().trim())
		{
			alert("Enter the Actual Start Date...");
			jQuery("#edit_TaskStartDate").focus();
			return false;
		}		
		if(!jQuery("#edit_TaskEndDate").val().trim())
		{
			alert("Enter the Task End Date...");
			jQuery("#edit_TaskEndDate").focus();
			return false;
		}
		var sdate = jQuery("#edit_TaskStartDate").val().trim();
		var edate = jQuery("#edit_TaskEndDate").val().trim();
		var est_time = jQuery("#edit_EstimatedHours").val().trim();
		
		/*- Validate start date and end date -*/
		var DateCheckval = DateCheck(sdate, edate);
		if(!DateCheckval)
		{
			alert("End date should be greater than Start date");
			jQuery("#edit_TaskStartDate").focus();
			return false;
		}
		
		/*- Checking the estimate hours and different days hours -*/
		var valid_esttime = check_esttime( sdate, edate, est_time);
		if( valid_esttime == "not valid")
		{
			alert('Please enter valid hours');
			jQuery("#edit_EstimatedHours").focus();
			return false;
		}
		else if(valid_esttime == "Minimum Hours")
		{
			alert('Minimum allowd hours is 00.15 hrs');
			jQuery("#edit_EstimatedHours").focus();
			return false;
		}
		else if(!valid_esttime)
		{
			var alow_hours = total_worktime(sdate, edate);
			alert('Maximum allowd hours  is ' + alow_hours + ".00 hrs for the date range.");
			jQuery("#edit_EstimatedHours").focus();
			return false;
		}
		document.getElementById("page").value = jQuery(".paginate_button.current").html();
		jQuery("#task_entry_edit_form").submit();
	}
	
	/* 
		Valid the New Record Saving
	*/
	function valid()
	{
		if(!jQuery("#ProjectICode").val())
		{
			alert("Choose the Project Name...");
			jQuery("#ProjectICode").focus();
			return false;
		}

		if(!jQuery("#PhaseTypeICode").val())
		{
			alert("Choose the Phase Type...");
			jQuery("#PhaseTypeICode").focus();
			return false;
		}

		if(!jQuery("#TaskTypeICode").val())
		{
			alert("Choose the Task Type...");
			jQuery("#TaskTypeICode").focus();
			return false;
		}
		
		if(!jQuery("#TaskDescription").val().trim())
		{
			alert("Enter the Task Description...");
			jQuery("#TaskDescription").focus();
			return false;
		}
		if(!jQuery("#TestScenarioDescription").val().trim())
		{
			alert("Enter the Test Scenario Description...");
			jQuery("#TestScenarioDescription").focus();
			return fasle;
		}		
		if(!jQuery("#EstimatedHours").val())
		{
			alert("Enter the Estimated Hours...");
			jQuery("#EstimatedHours").focus();
			return false;
		}
		
		/*Check decimal Format*/
		var pattern_time=  /^[+]?[0-9]+\.[0-9]+$/;
		var est_hour_val = jQuery("#EstimatedHours").val();
		if(!est_hour_val.match(pattern_time))  {
			alert("Please check the Estimated Hours Format[00.00] ");
			jQuery("#EstimatedHours").focus();
			return false;
		}
		/*- Validate the Decimal Points-*/
		var arr = document.getElementById("EstimatedHours").value.split('.');
		var length = arr[1].length 
		if (length > 2)
		{
			alert("Allowed Only 2 digit after decimal point.");
			jQuery("#EstimatedHours").focus();
			return false;
		}		
		if(!jQuery("#TaskStartDate").val().trim())
		{
			alert("Enter the Actual Start Date...");
			jQuery("#TaskStartDate").focus();
			return false;
		}		
		if(!jQuery("#TaskEndDate").val().trim())
		{
			alert("Enter the Actual End Date...");
			jQuery("#TaskEndDate").focus();
			return false;
		}
		var sdate = jQuery("#TaskStartDate").val().trim();
		var edate = jQuery("#TaskEndDate").val().trim();
		var est_time = jQuery("#EstimatedHours").val().trim();
		
		/*- Validate start date and end date -*/
		var DateCheckval = DateCheck(sdate, edate);
		if(!DateCheckval)
		{
			alert("End date should be greater than Start date");
			jQuery("#TaskStartDate").focus();
			return false;
		}
		
		/*- Checking the estimate hours and different days hours -*/
		var valid_esttime = check_esttime( sdate, edate, est_time);
		if( valid_esttime == "not valid")
		{
			alert('Please enter valid hours');
			jQuery("#EstimatedHours").focus();
			return false;
		}
		else if(valid_esttime == "Minimum Hours")
		{
			alert('Minimum allowd hours is 00.15 hrs');
			jQuery("#EstimatedHours").focus();
			return false;
		}
		else if(!valid_esttime)
		{
			var alow_hours = total_worktime(sdate, edate);
			alert('Maximum allowd hours  is ' + alow_hours + ".00 hrs for the date range.");
			jQuery("#EstimatedHours").focus();
			return false;
		}		
		jQuery("#task_entry_form").submit();
	}
	
	/*
		DeActive the record
	*/
	function inactive_entry(id) {
		if(confirm("Are you sure want to deactive this task?"))
		{
			window.location.href="<?php echo base_url(); ?>task/entry_inactive/?request_id=" + id +"&page="+jQuery(".paginate_button.current").html();		
		}
	}
	
	function edit_entry(id, sno){
		if(edit_open == 0)
		{
			/*- Show the Loading Bar -*/
			jQuery("#loading-bar").show();
			edit_open = 1;
			jQuery("#old_txt").val(jQuery("#new_task_list_" + id).html());
			jQuery("#new_task_list_" + id).html(htm);
			jQuery.ajax({
				type: "POST",
				url: "<?php echo base_url(); ?>task/entry_edit/", 
				data: {TaskICode:id},
				success: function(resp) 
				{
					
					var obj = jQuery.parseJSON( resp );	
					jQuery("#edit_sno").html(sno);
					jQuery("#edit_TaskICode").val(id);
					jQuery("#edit_ProjectICode").val(obj[0][0].ProjectICode);
					jQuery("#edit_TaskDescription").val(obj[0][0].TaskDescription);
					jQuery("#edit_EstimatedHours").val(obj[0][0].EstimatedHours);
					jQuery("#edit_TaskStartDate").val(obj[0][0].TaskStartDate);
					jQuery("#edit_TaskEndDate").val(obj[0][0].TaskEndDate);
					var txt = "";
					jQuery.each(obj[1][id], function(key, value) {
						txt = txt + value['TestScenarioDescription'] + "\n";
					});
					txt = txt.substring(0, txt.length - 1);				
					jQuery("#edit_TestScenarioDescription").val(txt);
					jQuery('.datepicker').datetimepicker({
						timepicker:false,
						format : 'd-m-Y',
						closeOnDateSelect:true
					});
					// jQuery('.timepicker').datetimepicker({
						// datepicker:false,
						// format : 'H.i',
						// step : 5,
						// closeOnDateSelect:true
					// });	
					/*- Hide The Loding Bar -*/
					jQuery("#loading-bar").hide();
				}			
			});
		}
		else {
			alert('Already Edit Opend.');
		}
	}	

	</script>
	
	<script type="text/javascript">
		jQuery("#task_entry").addClass('active');
		jQuery("#TaskDescription").focus();
		
		jQuery(function () {
			// jQuery('.timepicker').datetimepicker({
				// datepicker:false,
				// format : 'H.i',
				// step : 5,
				// closeOnDateSelect:true
			// });			
			jQuery('.datepicker').datetimepicker({
				timepicker:false,
				format : 'd-m-Y',
				closeOnDateSelect:true
			});
			
			/*- hide Alert Message
			jQuery('.alert').delay(5000).fadeOut('slow'); 
			-*/
		});
		
		jQuery(document).ready(function() {
			if(<?php echo count($entry_list) ?> > 0) {
				var table = jQuery('#tbl_list').dataTable( {
					//"paging":   false,
					"bSort" : false,
					"lengthMenu" : [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"bLengthChange": false,
					"info":     false,
					"filter":   false,
					//"bDestroy":true,
					//"mData": null,
					//"mDataProp": null,
					//"aoColumns": [null,{ "sType": 'num-html' },null],
					"order": [[ 0, "asc" ]]
				});
			}
			<?php if($this->input->get('page')): ?>
				var page_no = "<?php echo $this->input->get('page'); ?>";
				var total_page = Math.ceil(parseInt(table.fnGetData().length) / parseInt(10));
				if(total_page <= page_no) {
					page_no = total_page;
				}
				table.fnPageChange(--page_no, true);
			<?php endif; ?>

			/*** Phase and task type Drop down **/
			jQuery("#ProjectICode").change(function(){
		    	if(jQuery("#ProjectICode").val()){		    		
		        	var selectedProject = jQuery("#ProjectICode").val();
		        
			        var c_url = '<?php echo base_url(); ?>';
			        
			        jQuery.ajax({
			            type: "POST",
			            url: c_url+"task/phaselist",
			            data: { projectID : selectedProject } 
			        }).done(function(data){
			        	if(data){
				            jQuery("#PhaseTypeICode").html(data);
				        }
			        });
		    	}    	
	   		 });
		    jQuery("#PhaseTypeICode").change(function(){
		    	if(jQuery("#PhaseTypeICode").val()){    		
		        	var selectedPhase = jQuery("#PhaseTypeICode").val();        
			        var c_url = '<?php echo base_url(); ?>';
			        
			        jQuery.ajax({
			            type: "POST",
			            url: c_url+"task/tasktypelist",
			            data: { phaseID : selectedPhase } 
			        }).done(function(data){
			        	if(data){
				            jQuery("#TaskTypeICode").html(data);
				        }
			        });
		    	}    	
		    });		


		});
	</script>
	
	<?php include 'footer.php'; ?>
</body>
</html>