<?php include 'header.php'; ?>
<style>
input[type=number] {
    text-align: right;
}

</style>
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
        <?php $this->session->unset_userdata('msg'); ?>
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
                                                    for ($m=1; $m<=12; $m++) {
                                                        $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                                        $selctedM = strtolower($selctedMonth) == strtolower($month) ? 'selected' : '';
                                                        echo "<option value=$month  $selctedM >$month</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- <div class="type-container monthly-field">
                                        <div class="field-inline">
                                            <div class="est_txt">Approved Status :</div>
                                            <select name="approved_status" id="approved_status" class="form-control">
                                                <option value="">All</option>
                                                <option value="approved">Approved</option>
                                                <option value="unaproved">Unapproved</option>
                                            </select>
                                        </div>
                                    </div> -->
                                </td>

                                <td data-title="" width="20%">
                                    <!-- <div class="employee_list">
                                        <div class="container-box">
                                            <b>Consultants list:</b>
                                            <div class="employees">
                                                <?php
                                                    if($emp_list) {
                                                        foreach($emp_list as $emp) {
                                                            ?>
                                                <div><input type="checkbox" attr-id="<?= $emp['ConsultantICode'] ?>" checked name="emp_ids[]" class="emp" value="<?= $emp['ConsultantICode'] ?>" /><?= $emp['ConsultantLoginUserName'] ?></div>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div> -->
                                </td>

                                <td data-title="" width="60%" align="left">
                                    <div class="timesheet-action clreafix">
									
                                        <button type="button" class="btn btn-sm btn-blue pull-left"
                                            id="timesheet_preview" onclick="return false;">View</button>
                                        <!-- <button type="submit" class="btn btn-sm btn-green pull-left"
                                            id="timesheet_submit">Submit</button> -->
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
                <h3 class="panel-title">Consultant Invoices</h3>
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
                                <th>Project Name</th>
                                <th>Lead</th>
                                <th>Final Approved Status</th>
                                <th>Consultant Approved Status</th>
                                <th>Invoice</th>
                                <?php 
                                if($this->session->userdata('role') == 'consultant') { 
                                    echo "<th>Action</th>";
                                }
                                ?>
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
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Enter the reason for denying</h5>
			</div>
			<div class="modal-body">
                <label for="reason">Reason</label>
                <textarea class="form-control" id="reason" rows="3"></textarea>
				<span class="text-danger" id="reasonErr"></span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-blue" id="SubmitBtn">Submit</button>
			</div>
		</div>
	</div>
</div>
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




    let userRoleLoggedIn = '<?php echo $this->session->userdata('role');  ?>';
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

    // $('input[type=radio][name=timesheet_type]').change(function() {
    //     $('.type-container').hide();
    //     $('.' + this.value + '-field').show();
    // });

    function padTo2Digits(num) {
        return num.toString().padStart(2, '0');
    }

    function formatDate(date) {
        return [
            padTo2Digits(date.getDate()),
            padTo2Digits(date.getMonth() + 1),
            date.getFullYear(),
        ].join('/');
    }

    function dateFormator(dateString) {
        let AccountsApprovedOn = new Date(dateString);
        let AccountsApprovedOnDateArr = dateString
            .split(' ')[0].split('-');
        let AccountsApprovedOnDate = formatDate(new Date(
            AccountsApprovedOnDateArr[0],
            AccountsApprovedOnDateArr[1] - 1,
            AccountsApprovedOnDateArr[2]
        ));
        let AccountsApprovedOnTime = AccountsApprovedOn
            .toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric'
            });

        return AccountsApprovedOnDate + " " + AccountsApprovedOnTime;
    }

    setTimeout(function() {jQuery('#timesheet_preview').trigger('click');}, 0);

    jQuery('#timesheet_preview').click(function() {
        disable_timesheetbutton();

        // var approvedStatus = jQuery('#approved_status').val();
        var status = ["Not yet approved","Approved","Denied"];
        var c_url = '<?php echo base_url(); ?>';
        var TimeSheetType = 'monthly';
        var postData = {};
        postData.timesheet_type = TimeSheetType;
        // postData.approvedStatus = approvedStatus;
        postData.user_role = userRoleLoggedIn;

        postData.month = $('#time_sheet_month').val();
        postData.year = $('#time_sheet_year').val();
		
		
        // var emp_ids = [];
        // $("input[type=checkbox][name='emp_ids[]']").each(function() {
        //     if ($(this).prop('checked') === true) {
        //         emp_ids.push($(this).val());
        //     }
        // });
        // postData.emp_ids = emp_ids;
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/getConsultantInvoices",
            data: postData
        }).done(function(data) {
            
            if (data) {
                data = JSON.parse(data);
                console.log(data);
                //alert(data.timesheet_list);
                $('#tbl_list tbody').html('');
                if (data.timesheet_list.length < 1) {
                    $('#tbl_list tbody').html('<tr><td colspan="' + ($('#tbl_list thead tr th')
                        .length) + '" align="center">No Records Found.</td></tr>');
                    enable_timesheetbutton();
                    return;
                } else {
                    $.each(data.timesheet_list, function(index, task_detail) {
                        // if(userRoleLoggedIn == 'consultant'){
                        //     if(task_detail.ConsultApprovedStatus == 0 ) {
                        //     approveCheckbox =
                        //     '<input type="checkbox" class="accApproveCk" value="' +
                        //     task_detail.InvoiceId +
                        //     '"><input type="hidden" name="accApprove[]" value="false" />';
                        //     } else {
                        //         approveCheckbox =
                        //         '<input type="checkbox" class="accApproveCk" value="' +
                        //         task_detail.InvoiceId +
                        //         '" checked readonly ><input type="hidden" name="accApprove[]" value="true"/>';
                        //     }
                        // } else {
                        //     approveCheckbox = status[task_detail.ConsultApprovedStatus];
                        // }
                        if(userRoleLoggedIn == 'consultant'){
                            if(task_detail.ConsultApprovedStatus == 0 ) {
                                approveCheckbox =
                                '<button type="button" id="accept_invoice" class="btn btn-sm btn-green" data-id="' + task_detail.InvoiceId + '">Approve</button>' + 
                                '<button type="button" id="deny_invoice" class="btn btn-sm btn-red" data-id="' + task_detail.InvoiceId + '">Deny</button>';
                            } else if(task_detail.ConsultApprovedStatus == 1 ) {
                                approveCheckbox =
                                '<button type="button" id="deny_invoice" class="btn btn-sm btn-red" data-id="' + task_detail.InvoiceId + '">Deny</button>';
                            } else {
                                approveCheckbox =
                                '<button type="button" id="accept_invoice" class="btn btn-sm btn-green" data-id="' + task_detail.InvoiceId + '">Approve</button>';
                            }
                        } else {
                            approveCheckbox = '';
                            // if(task_detail.ConsultApprovedStatus == 1) {
                            //     approveCheckbox = '<span class="glyphicon glyphicon-ok text-success"></span>';
                            // } else {
                            //     approveCheckbox = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                            // }
                        }
                        if(task_detail.InvoiceId) {
                            viewInvoice = '<a href="' + c_url + 'pdf/' + task_detail.InvoiceId + '.pdf " target="_blank"><span class="glyphicon glyphicon-eye-open"></span></a>';
                        } else {
                            viewInvoice = '';
                        }
                        if(task_detail.FinalApprovalStatus == 1) {
                            finalApprovalStatus = '<span class="glyphicon glyphicon-ok text-success"></span>';
                        } else {
                            finalApprovalStatus = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                        }
                        if(task_detail.ConsultApprovedStatus == 1) {
                            consultApprovedStatus = '<span class="glyphicon glyphicon-ok text-success"></span>';
                        } else {
                            consultApprovedStatus = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                        }
                        
                        if(userRoleLoggedIn == 'consultant'){
                            $('#tbl_list tbody').append('<tr><td width="20%">' + task_detail
                                .employee_name + '</td>' +
                                '<td width="20%">' + task_detail.ProjectName +
                                '</td>' +
                                '<td width="10%">' + task_detail.approved_by +
                                '</td>' +
                                '<td width="10%">' + finalApprovalStatus + 
                                '</td>' +
                                '<td width="10%">' + consultApprovedStatus +
                                '</td>' +
                                '<td width="10%">'+ viewInvoice +
                                '</td>' +
                                '<td width="30%">' + approveCheckbox +
                                '</td></tr>'
                            );
                        } else {
                            $('#tbl_list tbody').append('<tr><td width="10%">' + task_detail
                                .employee_name + '</td>' +
                                '<td width="10%">' + task_detail.ProjectName +
                                '</td>' +
                                '<td width="10%">' + task_detail.approved_by +
                                '</td>' +
                                '<td width="10%">' + finalApprovalStatus + 
                                '</td>' +
                                '<td width="10%">' + consultApprovedStatus +
                                '</td>' +
                                '<td width="10%">'+ viewInvoice +
                                '</td></tr>'
                            );
                        }
                    });
                }
                
                $("input[type='number']").each(function() {
                    fixHrsDecimal.call(this);
                    $(this).on('blur', fixHrsDecimal.bind(this));
                });
            }

            enable_timesheetbutton();

            
        });
        return false;
    });


    $(document).on('click', '#accept_invoice', function() {
        // $(this).next('input').val($(this).prop('checked'));
        $(this).attr('value', 'Accepting...');
        $(this).attr("disabled", true);
        let postData = {};
        postData.id = $(this).attr('data-id');
        let c_url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "POST",
            data: postData,
            url: c_url + "ApproveConsultant/invoiceAcceptedByConsultant",
        }).done(function (data) {
            console.log(data);
            alert('Invoice Approved Successfully');
            document.querySelector("#timesheet_preview").dispatchEvent(new Event(
                    "click"));
            // window.close();
        });
        return false;
    });

    $(document).on('click', '#deny_invoice', function() {
  		// alert('Deny');
        // alert( $('#deny_invoice').attr('data-id'));
        $('#exampleModal').modal('show');
	});

    $(document).on('click', '#SubmitBtn', function(e) {
        $(this).attr('value', 'Submitting...');
        $(this).attr("disabled", true);
        let reason = $('#reason').val();
        if (reason) {
            console.log(reason);
            $('#reason').val('');
            $('#reasonErr').attr('textContent',"");
            let postData = {};
            postData.id = $('#deny_invoice').attr('data-id');
            postData.reason = reason;
            let c_url = '<?php echo base_url(); ?>';
            $.ajax({
                type: "POST",
                data: postData,
                url: c_url + "ApproveConsultant/invoiceDeniedByConsultant",
            }).done(function (data) {
                console.log(data);
                $('#exampleModal').modal('hide');
                alert('Invoice Denied successfully');
                document.querySelector("#timesheet_preview").dispatchEvent(new Event(
                    "click"));
            });
        } else {
            console.log(reasonErr);
            $('#reasonErr').attr('textContent',
                "Something went wrong");
        }
    });

    const fixHrsDecimal = function() {
        this.value = parseFloat(this.value).toFixed(2);
    }

});
</script>

<?php include 'footer.php'; ?>
</body>

</html>
