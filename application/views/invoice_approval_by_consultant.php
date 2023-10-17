<!DOCTYPE html>
<html lang="en">
<head>
    <link href="<?php echo base_url(); ?>public/css/consultant-invoice-template.css" rel="stylesheet"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant Invoice</title>
	<link rel="icon" href="<?php echo base_url(); ?>public/favicon.png">
    <link href="<?php echo base_url(); ?>public/css/bootstrap.min.css" rel="stylesheet" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>
<body>
    <section class="invoice">
        <div class="invoice-container">
            <table>
                <tr class="name">
                    <th width="10%">Name</th>
                    <td width="60%"><?= $consultant['ConsultantFirstName']; ?> <?= $consultant['ConsultantLastName']; ?></td>
                    <th width="10%" class="date">Date</th>
                    <td width="20%"><?= date('d-m-Y') ?></td>
                </tr>
                <tr class="address">
                    <th >Address:</th>
                    <td colspan="3"><?= $consultant['ConsultantCurrentAddress1']; ?>, <?= $consultant['ConsultantCurrentAddress2']; ?>, <?= $consultant['ConsultantCurrentAddress3']; ?>,
                    <?= $consultant['ConsultantCurrentCity']; ?>, <?= $consultant['ConsultantCurrentState']; ?>, <?= $consultant['ConsultantCurrentCountry']; ?> - 
                    <?= $consultant['ConsultantPinCode']; ?></td>
                </tr>
                <tr class="empty">
                    <td colspan="4"></td>
                </tr>
                <tr class="pan">
                    <th width="10%">PAN No:</th>
                    <td colspan="3"><?= $consultant['ConsultantPANNo']; ?></td>
                </tr>
                <tr class="empty">
                    <td colspan="4"></td>
                </tr>
                <tr class="company-address">
                    <td colspan="4">To
                        <br>
                        CG-VAK Software and Exports Limited
                        <br>
                        171 MTP Road
                        <br>
                        Coimbatore 641043
                        <br>
                        India</td>
                </tr>
                <tr class="invoice-title">
                    <th colspan="4">INVOICE</th>
                </tr>
            </table>
            <table class="invoice-table">
                <tr>
                    <th width="35%">Particulars</th>
                    <th width="30%">No. Of&nbsp Hours</th>
                    <th width="20%">Per Hour Rate</th>
                    <th width="15%" class="amount">Amount <div class="rupees">Rs</div></th>
                </tr>
                <tr>
                    <td width="35%">Professional charges for the IT services provided</td>
                    <td width="30%"><?= round($consultant_timesheet['HrApprovedHours'],2); ?></td>
                    <td width="20%"><?= round($consultant_timesheet['HrApprovedRateHour'],2); ?></td>
                    <td width="15%"><?= round($consultant_timesheet['HrApprovedBillAmount'],2); ?></td>
                </tr>
                <tr>
                    <td width="35%">01-Oct-2022 to 31-Oct-2022</td>
                    <td width="30%"></td>
                    <td width="20%"></td>
                    <td width="15%"> </td>
                </tr>
                <tr>
                    <td width="35%">Total Amount</td>
                    <td width="30%"></td>
                    <td width="20%"></td>
                    <td width="15%"><?= round($consultant_timesheet['HrApprovedBillAmount'],2); ?></td>
                </tr>
                <tr class="r-words">
                    <td colspan="4">(Rupees in words)</td>
                </tr>
                <tr class="cheque">
                    <th >Please make cheque favouring :  </th>
                    <td colspan="3"></td>
                </tr>
                <tr class="acc-name">
                    <th>Account Name  </th>
                    <td colspan="3"><?= $consultant['ConsultantFirstName']; ?> <?= $consultant['ConsultantLastName']; ?></td>
                </tr>
                <tr class="acc-no">
                    <th>Account No  </th>
                    <td colspan="3"><?= $consultant['ConsultantBankAccountNo']; ?></td>
                </tr>
                <tr class="bank-name">
                    <th>Bank Name  </th>
                    <td colspan="3"><?= $consultant['ConsultantBankName']; ?></td>
                </tr>
                <tr class="ifsc">
                    <th>IFSC Code  </th>
                    <td colspan="3"><?= $consultant['ConsultantBankIFSCCode']; ?></td>
                </tr>
            </table>
            <div class="form-buttons">
                <div class="accept">
                    <a href="#" id="accept_invoice" data-id="<?= $consultant_timesheet['InvoiceId'] ?> ">Accept</a>
                </div>
                <div class="deny">
                    <a href="#" id="deny_invoice" data-id="<?= $consultant_timesheet['InvoiceId'] ?> ">Deny</a>
                </div>
            </div>
        </div>
    </section>
</body>

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

<script>
    $(`#accept_invoice`).click(function() {
  		// alert('Accept');
        // alert( $('#accept_invoice').attr('data-id'));
        let postData = {};
        postData.id = $('#accept_invoice').attr('data-id');
        let c_url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "POST",
            data: postData,
            url: c_url + "ApproveConsultant/invoiceAcceptedByConsultant",
        }).done(function (data) {
            console.log(data);
            alert('Invoice Approved Successfully');
            window.close();
        });
	});

    $(`#deny_invoice`).click(function() {
  		// alert('Deny');
        // alert( $('#deny_invoice').attr('data-id'));
        $('#exampleModal').modal('show');
	});

    $('#SubmitBtn').click(function (e) {
			let reason = $('#reason').val();
			if (reason) {
				console.log(reason);
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
                    alert('Invoice Denied Successfully');
                    window.close();
				});
			} else {
				console.log(reasonErr);
				$('#reasonErr').attr('textContent',
					"Something went wrong");
			}
		});
</script>

</html>