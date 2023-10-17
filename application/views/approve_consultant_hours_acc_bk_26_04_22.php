<?php include 'header.php'; ?>

<?php include 'menu.php'; ?>
<div class="container">
    <div class="starter-template">
        <!-- Error Message -->
        <?php ?>
        <?php if ($this->session->userdata('msg') == 'succ') { ?>
        <div class="alert alert-success " role="alert">
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
            <span class="sr-only">Error:</span> Login Successfully.
        </div>
        <?php } elseif ($this->session->userdata('msg') == 'upt') { ?>
        <div class="alert alert-success " role="alert">
            Successfully Updated the Task.
        </div>
        <?php } elseif ($this->session->userdata('msg') == 'ts_export') { ?>
        <div class="alert alert-success " role="alert">
            Successfully TimeSheet exported.
        </div>
        <?php } else if (validation_errors()) { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo validation_errors(); ?>
        </div>
        <?php } ?>
        <!-- Error Message -->


        <!-- Timesheet export fields -->
        <div class="add_filter">
            <div id="no-more-tbl">
                <form action="<?php echo base_url() . "ApproveConsultant/export"; ?>" id="timesheet_export_form"
                    method="post" class="form-horizontal">
                    <input type="hidden" name="IsActive" id="IsActive" value="1" />
                    <input type="hidden" name="CompanyICode" id="CompanyICode" value="1" />
                    <!--input type="hidden" name="ProjectPhaseICode" id="ProjectPhaseICode" value="1" /-->
                    <input type="hidden" name="Status" id="Status" value="O" />
                    <input type="hidden" name="CreatedBy" id="CreatedBy"
                        value="<?php echo $this->session->userdata('id'); ?>" />
                    <input type="hidden" name="CreatedDate" id="CreatedDate"
                        value="<?php echo date('Y-m-d H:i:s'); ?>" />
                    <table class="col-md-12 table-bordered table-striped table-condensed cf table-hover"
                        id="tbl_timesheet_form">
                        <tbody>
                            <tr>
                                <td data-title="" width="40%">
                                    <div class="type-container monthly-field">
                                        <div class="field-inline">
                                            <div class="est_txt">Select Year:</div>
                                            <input class="form-control" type="text" value="<?php echo date('Y'); ?>"
                                                name="year" id="time_sheet_year" maxlength="4" />
                                        </div>
                                        <div class="field-inline">
                                            <div class="est_txt">Select Month :</div>
                                            <select class="form-control" id="time_sheet_month" name="month">
                                                <?php
											$selctedMonth = date('F');
											for ($i = 12; $i > 0; $i--) {
												$time = strtotime(sprintf('-%d months', $i));
												$label = date('F', $time);
												$selctedM = strtolower($selctedMonth) == strtolower($label) ? 'selected' : '';
												echo "<option value='$label'  $selctedM >$label</option>";
											}
											?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </td>
                                <td data-title="" width="60%" align="left">
                                    <div class="timesheet-action clreafix">
                                        <button type="button" class="btn btn-sm btn-blue pull-left"
                                            id="timesheet_preview" onclick="return false;">View</button>
                                        <button type="submit" class="btn btn-sm btn-green pull-left"
                                            id="timesheet_submit">Submit</button>
                                    </div>
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
                <h3 class="panel-title">Project Entries</h3>
                <!-- <div class="rate_per_hr">
                    Rate per hour: <input type="number" id="rate_per_hr" min="0" value="0"><br>
                    <span id="rate_per_hr_err" class="text-danger"></span>
                </div> -->
            </div>
            <div id="no-more-tables">
                <form action="<?php echo base_url() . "ApproveConsultant/accSubmit"; ?>" method="post"
                    id="timesheet_submitFrm">
                    <table class="table table-hover" id="tbl_list">
                        <thead>
                            <tr>
                                <th>Consultant</th>
                                <th>Project I code</th>
                                <th>Project Name</th>
                                <th>Actual Hours</th>
                                <th>Lead Approved Hours</th>
                                <th>Rate/Hour</th>
                                <th>Accounts Approved Hours</th>
                                <th>Amount</th>
                                <th>Approve</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <input type="hidden" value="" name="deletetasks">
                </form>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="old_txt" id="old_txt" />

<!-- Using this div for removing tooltip in appending edit content -->
<div id="remove_html"></div>
<script type="text/javascript">
/*
		Validate Start date and End date
	*/
function DateCheck(StartDate, EndDate) {
    var sdate = changeDate(StartDate);
    var edate = changeDate(EndDate);
    if (sdate > edate) {
        return false;
    }
    return true;
}

function disable_timesheetbutton() {
    jQuery('#timesheet_submit').attr('disabled', 'disabled');
    jQuery('#timesheet_preview').attr('disabled', 'disabled');
}

function enable_timesheetbutton() {
    jQuery('#timesheet_submit').removeAttr('disabled');
    jQuery('#timesheet_preview').removeAttr('disabled');
}

function show_loader() {
    jQuery('body').append(
        '<div class="ajax-loader"><div class="overlay"><img src="public/images/ajax-loader.gif" alt="ajax-loader"></div></div>'
    );
}

function hide_loader() {
    jQuery('.ajax-loader').remove();
}
</script>

<script type="text/javascript">
jQuery(document).ready(function($) {
    function amntPerDay() {
        let closeTR = $(this).closest('tr');
        let currentEl = closeTR.find('.rate_per_hr');
        let approved_hours = closeTR.find('.approved_hours');
        let amount_per_day = closeTR.find('.amount_per_day');
        let amount = (parseFloat(currentEl.val()) * parseFloat(approved_hours.val())).toFixed(2);
        amount_per_day.val(amount);
    }
    console.log('<?php echo USERROLE;  ?>');
    let userRoleLoggedIn = '<?php echo USERROLE;  ?>';
    //show the default type monthy timesheet
    $('.type-container').hide();
    $("input[type=radio][name=timesheet_type]:first").prop("checked", true).trigger("click");
    $('.monthly-field').show();

    jQuery('.datepicker').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        closeOnDateSelect: true
    });
    if (<?php echo count($entry_list) ?> >
        0
    ) {
        var table = jQuery('#tbl_list').dataTable({
            //"paging":   false,
            "bSort": false,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "bLengthChange": false,
            "info": false,
            "filter": false,
            //"bDestroy":true,
            //"mData": null,
            //"mDataProp": null,
            //"aoColumns": [null,{ "sType": 'num-html' },null],
            "order": [
                [0, "asc"]
            ]
        });
    }
    <?php if($this->input->get('page')): ?>
    var page_no = "<?php echo $this->input->get('page'); ?>";
    var total_page = Math.ceil(parseInt(table.fnGetData().length) / parseInt(10));
    if (total_page <= page_no) {
        page_no = total_page;
    }
    table.fnPageChange(--page_no, true);
    <?php endif; ?>

    $('input[type=radio][name=timesheet_type]').change(function() {
        $('.type-container').hide();
        $('.' + this.value + '-field').show();
    });
    jQuery("#ProjectICode").change(function(e) {
        e.preventDefault();
        show_loader();
        disable_timesheetbutton();
        $('.employee_list .employees').html('');
        if (jQuery("#ProjectICode").val()) {
            var ProjectICode = jQuery("#ProjectICode").val();
            var c_url = '<?php echo base_url(); ?>';
            var TimeSheetType = $('input[type=radio][name=timesheet_type]:checked').val();
            var postData = {};
            postData.ProjectICode = ProjectICode;
            postData.timesheet_type = TimeSheetType;
            if (TimeSheetType == 'monthly') {
                postData.month = $('#time_sheet_month').val();
                postData.year = $('#time_sheet_year').val();
            } else {
                postData.fromDate = $('#time_sheet_fromDate').val();
                postData.toDate = $('#time_sheet_toDate').val();
            }
            jQuery.ajax({
                type: "POST",
                url: c_url + "ApproveConsultant/get_emp",
                data: postData
            }).done(function(data) {
                if (data) {
                    console.log(data);
                    $('.employee_list .employees').html('');
                    data = JSON.parse(data);
                    $.each(data.emp_list, function(index, emp) {
                        $('.employee_list .employees').append(
                            '<div><input type="checkbox" attr-id="' + emp
                            .ConsultantICode +
                            '" checked name="emp_ids[]" class="emp" value="' + emp
                            .ConsultantICode + '"/>' + emp.ConsultantLoginUserName +
                            '</div>');
                    });
                }
                hide_loader();
                enable_timesheetbutton();
            });
        }
    });

    jQuery('#timesheet_preview').click(function() {
        disable_timesheetbutton();
        let deletetasks = $('[name="deletetasks"]');
        deletetasks.val('');
        var ProjectICode = jQuery("#ProjectICode").val();
        var c_url = '<?php echo base_url(); ?>';
        var TimeSheetType = 'monthly';
        var postData = {};
        postData.ProjectICode = ProjectICode;
        postData.timesheet_type = TimeSheetType;

        postData.month = $('#time_sheet_month').val();
        postData.year = $('#time_sheet_year').val();

        var emp_ids = [];
        $("input[type=checkbox][name='emp_ids[]']").each(function() {
            if ($(this).prop('checked') === true) {
                emp_ids.push($(this).val());
            }
        });
        console.log(emp_ids);
        console.log(c_url);
        postData.emp_ids = emp_ids;
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/preview",
            data: postData
        }).done(function(data) {
            let rate_per_hr = $('#rate_per_hr').val();
            console.log(data);
            console.log("data");
            if (data) {
                data = JSON.parse(data);
                //alert(data.timesheet_list);
                $('#tbl_list tbody').html('');
                console.log('length = ', data.timesheet_list.length);
                if (data.timesheet_list.length < 1) {
                    $('#tbl_list tbody').html('<tr><td colspan="' + ($('#tbl_list thead tr th')
                        .length) + '" align="center">No Records Found.</td></tr>');
                    enable_timesheetbutton();
                    return;
                }
                $.each(data.timesheet_list, function(index, task_detail) {
                    console.log(new Date(task_detail.date).toLocaleDateString('en-us', {
                        year: "numeric",
                        month: "long"
                    }));
                    let projectDate = new Date(task_detail.date).toLocaleDateString(
                        'en-us', {
                            year: "numeric",
                            month: "long"
                        }).split(" ");
                    let billable_hours;
                    let approved_hours;
                    let amount_per_day;
                    let approveButton;
                    let appendHtml;
                    let taskApprovedHour = (task_detail.AccountsApprovedHours > 0) ?
                        task_detail.AccountsApprovedHours : task_detail
                        .LeadApprovedHours;
                    rate_per_hr = parseFloat(task_detail.AccountsRateHour);

                    billable_hours =
                        '<input type="text" id="billable_hours" value="' +
                        task_detail.WorkedHours + '">';
                    approved_hours =
                        '<td width="10%"><input type="number" class="approved_hours" id="approved_hours" name="approved_hours[]" value="' +
                        taskApprovedHour + '"></td>';
                    amount_per_day =
                        `<td width="10%">
                            <input type="number" class="amount_per_day" id="amount_per_day" name="amount_per_day[]" value="${ rate_per_hr ? parseFloat(rate_per_hr * taskApprovedHour).toFixed(2) : ''}">
                            </td>`;
                    approveButton =
                        '<button type="button" class="btn btn-sm btn-red acc_delete" id="acc_delete" data-id="' +
                        task_detail.id + '">Delete</button>';
                    appendHtml = '<tr>' +
                        '<td width="10%">' + task_detail.employee_name + '</td>' +
                        '<td width="10%">' + task_detail.ProjectIcode + '</td>' +
                        '<td width="10%">' + task_detail.ProjectName + '</td>' +
                        '<td width="10%">' + task_detail.WorkedHours + '</td>' +
                        '<td width="10%">' + task_detail.LeadApprovedHours + '</td>' +
                        '<td width="10%"><input type="number" class="rate_per_hr" name="rate_per_hr[]" min="0" value="' +
                        rate_per_hr + '"></td>' +
                        approved_hours +
                        amount_per_day +
                        '<td width="10%"><input type="checkbox" class="accApproveCk" value="' +
                        task_detail.id +
                        '"><input type="hidden" name="accApprove[]" value="false" /></td>' +
                        '<td width="10%"><input type="hidden" value="' + task_detail
                        .id +
                        '" class="task_progress_id" id="task_progress_id" name="task_progress_id[]">' +
                        approveButton + '</td>' +
                        '</tr>';

                    $('#tbl_list tbody').append(appendHtml);
                });
                if (data.total_hours != '')
                    $('#tbl_list tbody').append(
                        '<tr><td colspan="' + ($('#tbl_list thead tr th').length) +
                        '" align="right" class="bold-16">Total Hours</td><td class="bold-16">' +
                        data.total_hours + '</td></tr>');
            }

            // amntPerDay()
            enable_timesheetbutton();
        });
        return false;
    });

    jQuery('#timesheet_export_remove').click(function() {
        disable_timesheetbutton();
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/preview",
            data: postData
        }).done(function(data) {
            if (data) {
                $('#tbl_list tbody').html('');
                data = JSON.parse(data);
                alert(data.timesheet_list);
                $.each(data.timesheet_list, function(index, task_detail) {
                    $('#tbl_list tbody').append('<tr><td width="10%">' + task_detail
                        .taskprogressdate + '</td>' +
                        '<td width="10%">' + task_detail.taskprogressdate +
                        '</td>' +
                        '<td width="20%">' + task_detail.employee_name +
                        '</td>' +
                        '<td width="30%">' + task_detail.workdescription +
                        '</td>' +
                        '<td width="10%">' + task_detail.actual_wrked +
                        '</td>' +
                        '<td width="10%">' + task_detail.Hours_Worked +
                        '</td>' +
                        '<td width="10%">' + task_detail.approved_hrs +
                        '</td></tr>'
                    );
                });
            }
            enable_timesheetbutton();
        });
    });

    $(document).on('click', '#timesheet_submit', function() {
        console.log($('#timesheet_submitFrm').serialize());
        $('#timesheet_submitFrm').submit();
        return false;
    });

    $(document).on('change', '.accApproveCk', function() {
        $(this).next('input').val($(this).prop('checked'));
        return false;
    });

    $(document).on('click', '#acc_delete', function() {
        if (confirm('Are you sure, you want delete?')) {
            let deletetasks = $('[name="deletetasks"]');
            let deletetasksVals = deletetasks.val().split(',');
            let currentEl = $(this);
            deletetasksVals.push(currentEl.attr('data-id'));
            deletetasks.val(deletetasksVals.join(','));
            currentEl.closest('tr').remove();
        }
        return false;
    });

    $(document).on('click', '.lead_approve', function() {
        let taskProgressId = $(this).siblings("#task_progress_id").val();
        let BillableHours = $(this).parent().parent().children().eq(5).children().eq(0).val();
        let c_url = '<?php echo base_url(); ?>';
        let postData = {};
        postData.approved_hours = BillableHours;
        postData.task_progress_id = taskProgressId;
        console.log(postData);
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/addLeadApprovedHours",
            data: postData
        }).done(function(data) {
            console.log(data);
            if (data) {

            }
        });
        return false;
    });

    $(document).on('change', '.rate_per_hr, .approved_hours', amntPerDay);


    $(document).on('click', '.hr_approve', function() {
        // console.log("yes");
        // console.log($(this).parent().parent().children().eq(6).children().eq(0).val());
        let rate_per_hour = $('#rate_per_hr').val();
        if (parseFloat(rate_per_hour) == 0 || rate_per_hour == '') {
            $('#rate_per_hr_err').text("Please enter a rate per hour");
            return;
        } else {
            $('#rate_per_hr_err').text("");
        }
        let approved_hours = $(this).parent().parent().children().eq(6).children().eq(0).val();
        let taskProgressId = $(this).siblings("#task_progress_id").val();

        // let ProjectICode = jQuery("#ProjectICode").val();


        let amount_per_day = $(this).parent().parent().children().eq(7).children().eq(0).val();
        // let BillableHours = jQuery("#billable_hours").val();
        let c_url = '<?php echo base_url(); ?>';
        let postData = {};
        // postData.task_progress_id = ProjectICode;
        postData.hr_approved_hours = approved_hours;
        postData.rate_per_hour = rate_per_hour;
        postData.task_progress_id = taskProgressId;
        postData.amount_per_day = amount_per_day;
        // postData.emp_ids = emp_ids;
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/hrApprovedHours",
            data: postData
        }).done(function(data) {
            console.log(data);
            if (data) {

            }
        });
        return false;
    });
});
</script>

<?php include 'footer.php'; ?>
</body>

</html>
