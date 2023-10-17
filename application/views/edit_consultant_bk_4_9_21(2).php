<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>
<div class="container">
    <div class="starter-template">
        <?php if ($this->session->flashdata('success')) {?>
            <div class="alert alert-success " role="alert">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php }?>
        <?php if ($this->session->flashdata('warning')) {?>
            <div class="alert alert-danger " role="alert">
                <?php echo $this->session->flashdata('warning'); ?>
            </div>
        <?php }?>
        <div class="alert_messages">
        </div>
        <div class="panel panel-info ovrhid">
            <div class="panel-heading">
                <h3 class="panel-title">Edit Consultant Details</h3>
                <div class="pull-right">

                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <form action="<?php echo base_url() . 'consultant/update_consultant'; ?>" id="consultant_details_edit" method="post" onsubmit="return checkForm(this);" enctype="multipart/form-data">
                        <fieldset style="border: 1px solid gray; padding: 20px;">
                        <input type="hidden" name="consultant_i_code" value="<?php echo $consultant[0]['ConsultantICode']; ?>">
                            <div class="row">
                            <legend>Personal Details:</legend>
                                <div class="form-group col-md-6">
                                    <label for="consultant_first_name">First Name <span class="text-danger">*</span> </label>
                                    <!-- $consultant[0]['ConsultantFirstName']; -->
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_first_name"
                                            name="consultant_first_name" value="<?php echo set_value('consultant_first_name', $consultant[0]['ConsultantFirstName'])  ?>" required/>
                                        <?php echo form_error('consultant_first_name',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="consultant_last_name">Last Name <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" class="form-control" id="consultant_last_name"
                                            name="consultant_last_name"  value="<?php echo set_value('consultant_last_name',$consultant[0]['ConsultantLastName']); ?> " required/>
                                        <?php echo form_error('consultant_last_name',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="consultant_email">Email <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" class="form-control" id="consultant_email"
                                            name="consultant_email" value="<?php echo set_value('consultant_email',$consultant[0]['ConsultantEmailId']); ?>" required/>
                                        <?php echo form_error('consultant_email',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="consultant_mobile_number">Mobile Number <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_mobile_number"
                                            name="consultant_mobile_number" value="<?php echo set_value('consultant_mobile_number',$consultant[0]['ConsultantMobileNo']); ?>" required/>
                                        <?php echo form_error('consultant_mobile_number',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="consultant_phone_number">Phone Number</label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_phone_number"
                                            name="consultant_phone_number" value="<?php echo set_value('consultant_phone_number',$consultant[0]['ConsultantPhoneNo']); ?>" />
                                        <?php echo form_error('consultant_phone_number',"<small class='text-danger'>","</small>"); ?>
                                </div>
                            </div>
                        </fieldset>
                        <br>

                        <fieldset style="border: 1px solid gray; padding: 20px;">
                            <div class="row">
                            <legend>Address for communication:</legend>
                                <div class="form-group col-md-6">
                                    <label for="consultant_current_address1">Street Name <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                        id="consultant_current_address1"
                                        name="consultant_current_address1"  value="<?php echo set_value('consultant_current_address1',$consultant[0]['ConsultantCurrentAddress1']); ?>" required/>
                                    <?php echo form_error('consultant_current_address1',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="consultant_current_address2">Apartment, studio, or floor <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                        id="consultant_current_address2"
                                        name="consultant_current_address2" value="<?php echo set_value('consultant_current_address2',$consultant[0]['ConsultantCurrentAddress2']); ?>" required/>
                                    <?php echo form_error('consultant_current_address2',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="consultant_current_address3">Address3 <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                        id="consultant_current_address3"
                                        name="consultant_current_address3" value="<?php echo set_value('consultant_current_address3',$consultant[0]['ConsultantCurrentAddress3']); ?>" required/>
                                    <?php echo form_error('consultant_current_address3',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="consultant_current_city">City <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_current_city"
                                            name="consultant_current_city" value="<?php echo set_value('consultant_current_city',$consultant[0]['ConsultantCurrentCity']); ?>" required/>
                                        <?php echo form_error('consultant_current_city',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="consultant_current_state">State <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_current_state"
                                            name="consultant_current_state" value="<?php echo set_value('consultant_current_state',$consultant[0]['ConsultantCurrentState']); ?>" required/>
                                        <?php echo form_error('consultant_current_state',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="consultant_current_country">Country <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_current_country"
                                            name="consultant_current_country" value="<?php echo set_value('consultant_current_country',$consultant[0]['ConsultantCurrentCountry']); ?>" required/>
                                        <?php echo form_error('consultant_current_country',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="consultant_pincode">Zip Code <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control" id="consultant_pincode"
                                            name="consultant_pincode" value="<?php echo set_value('consultant_pincode',$consultant[0]['ConsultantPinCode']); ?>" required/>
                                        <?php echo form_error('consultant_pincode',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="consultant_alternative_address">Alternative Address</label>
                                    <input type="text" autocomplete="off" class="form-control"
                                        id="consultant_alternative_address"
                                        name="consultant_alternative_address" value="<?php echo set_value('consultant_alternative_address',$consultant[0]['ConsultantAlternativeAddress']); ?>" />
                                    <?php echo form_error('consultant_alternative_address',"<small class='text-danger'>","</small>"); ?>
                                </div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset style="border: 1px solid gray; padding: 20px;">
                            <div class="row">
                                <legend>Other Details</legend>
                                <div class="form-group col-md-6">
                                    <label for="consultant_technology">Technology <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_technology"
                                            name="consultant_technology" value="<?php echo set_value('consultant_technology',$consultant[0]['ConsultantTechnology']); ?>" required/>
                                        <?php echo form_error('consultant_technology',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="consultant_reference">Reference</label>
                                    <input type="text" autocomplete="off" class="form-control" id="consultant_reference"
                                            name="consultant_reference" value="<?php echo set_value('consultant_reference',$consultant[0]['ConsultantReference']); ?>"/>
                                        <?php echo form_error('consultant_reference',"<small class='text-danger'>","</small>"); ?>
                                </div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset style="border: 1px solid gray; padding: 20px;">
                            <div class="row">
                                <legend>Bank Details</legend>
                                <div class="form-group col-md-12">
                                    <label for="consultant_bank_name">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control" id="consultant_bank_name"
                                            name="consultant_bank_name" value="<?php echo set_value('consultant_bank_name',$consultant[0]['ConsultantBankName']); ?>" required/>
                                        <?php echo form_error('consultant_bank_name',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="consultant_bank_account_number">Bank Account Number <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_bank_account_number"
                                            name="consultant_bank_account_number" value="<?php echo set_value('consultant_bank_account_number',$consultant[0]['ConsultantBankAccountNo']); ?>" required/>
                                        <?php echo form_error('consultant_bank_account_number',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="consultant_bank_ifsc_code">IFSC Code <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_bank_ifsc_code"
                                            name="consultant_bank_ifsc_code" value="<?php echo set_value('consultant_bank_ifsc_code',$consultant[0]['ConsultantBankIFSCCode']); ?>" required/>
                                        <?php echo form_error('consultant_bank_ifsc_code',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="consultant_pan_number">PAN Number <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_pan_number"
                                            name="consultant_pan_number" value="<?php echo set_value('consultant_pan_number',$consultant[0]['ConsultantPANNo']); ?>" required/>
                                        <?php echo form_error('consultant_pan_number',"<small class='text-danger'>","</small>"); ?>
                                </div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset style="border: 1px solid gray; padding: 20px;">
                            <div class="row">
                                <legend>Alternative Bank Details</legend>
                                <div class="form-group col-md-12">
                                    <label for="consultant_alternative_bank_name">Alternative Bank Name</label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_alternative_bank_name"
                                            name="consultant_alternative_bank_name" value="<?php echo set_value('consultant_alternative_bank_name',$consultant[0]['ConsultantalrternativeBankName']); ?>" />
                                        <?php echo form_error('consultant_alternative_bank_name',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="consultant_alternative_bank_account_number">Alternative Bank Account Number</label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_alternative_bank_account_number"
                                            name="consultant_alternative_bank_account_number"
                                            value="<?php echo set_value('consultant_alternative_bank_account_number',$consultant[0]['ConsultantalternativeBankAccountNo']); ?>" />
                                        <?php echo form_error('consultant_alternative_bank_account_number',"<small class='text-danger'>","</small>"); ?>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="consultant_alternative_bank_ifsc_code">IFSC Code</label>
                                    <input type="text" autocomplete="off" class="form-control"
                                            id="consultant_alternative_bank_ifsc_code"
                                            name="consultant_alternative_bank_ifsc_code"
                                            value="<?php echo set_value('consultant_alternative_bank_ifsc_code',$consultant[0]['ConsultantalternativeBankIFSCCode']); ?>" />
                                    <?php echo form_error('consultant_alternative_bank_ifsc_code',"<small class='text-danger'>","</small>"); ?>
                                </div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset style="border: 1px solid gray; padding: 20px;">
                            <div class="row">
                            <legend>File Upload</legend>
                                <div class="form-group col-md-6">
                                    <label for="pictureupload">Picture</label> <br>
                                    <img src="<?php echo base_url(); echo $consultant[0]['pictureupload'];  ?>" alt="picture" height="100" weight="100">
                                    <br>
                                    <br>
                                    <input type="file" autocomplete="off" class="form-control" id="pictureupload" name="pictureupload"/>
                                    <?php echo form_error('pictureupload',"<small class='text-danger'>","</small>"); ?>
                                        <?php if(isset($imgTypeError)){ ?>
                                            <small class="text-danger">
                                                <?php echo $imgTypeError; ?> 
                                            </small>
                                        <?php } ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="pictureupload">Resume</label> <br>
                                    <!-- <img src="<?php echo base_url(); echo $consultant[0]['pictureupload'];  ?>" alt="picture" height="100" weight="100"> -->
                                    <!-- <iframe src="<?php echo base_url(); echo $consultant[0]['resume_path'];  ?>&embedded=true"></iframe> -->
                                    <!-- <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url(); echo $consultant[0]['pictureupload'];  ?>' width='1366px' height='623px' frameborder='0'>This is an embedded <a target='_blank' href='http://office.com'>Microsoft Office</a> document, powered by <a target='_blank' href='http://office.com/webapps'>Office Online</a>.</iframe> -->
                                    <a class='actions'href="<?= base_url().$consultant[0]['resume_path']; ?>" class="pos-rel">
											<span class="glyphicon glyphicon-file" title="Edit" data-toggle="tooltip" data-placement="top"></span>
									</a>
                                    <br>
                                    <br>
                                    <input type="file" autocomplete="off" class="form-control" id="resumeupload" name="resumeupload"/>
                                    <?php echo form_error('resumeupload',"<small class='text-danger'>","</small>"); ?>
                                        <?php if(isset($fileTypeError)){ ?>
                                            <small class="text-danger">
                                                <?php echo $fileTypeError; ?> 
                                            </small>
                                        <?php } ?>
                                </div>
                                
                            </div>
                            
                        </fieldset>
                        <br>
                        <div class="text-center">
                        <button type="submit" class="btn btn-blue">Submit</button>
                        <a href="<?php echo base_url();?>consultant/get_consultant_list" class="btn btn-red">Cancel</a>
                        </div>
                        <br>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
