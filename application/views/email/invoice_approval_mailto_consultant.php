<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="<?php echo base_url(); ?>public/css/mail-template.css" rel="stylesheet"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registration</title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<center>
	<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
		<tr>
			<td valign="top" id="bodyCell">
				<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateContainer">
					<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateHeader">
							<tr>
								<td valign="top" class="headerContainer">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" class="DividerBlock">
										<tbody class="DividerBlockOuter">
										<tr>
											<td class="DividerBlockInner" style="padding: 20px 18px;">
												<table class="DividerContent" border="0" cellpadding="0" cellspacing="0"
													   width="100%">
													<tbody>
													<tr>
														<td>
															<span></span>
														</td>
													</tr>
													</tbody>
												</table>
											</td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateBody">
								<tr>
									<td valign="top" class="bodyContainer">
										<table border="0" cellpadding="0" cellspacing="0" width="100%"
											   class="TextBlock">
											<tbody class="TextBlockOuter">
											<tr>
												<td valign="top" class="TextBlockInner">
													<table align="left" border="0" cellpadding="0" cellspacing="0"
														   width="600" class="TextContentContainer">
														<tbody>
														<tr>
															<td valign="top" class="TextContent"
																style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
																<div><span style="font-family:arial,helvetica neue,helvetica,sans-serif"><?= $mailHead; ?>,<br>
                                                                        <br>
                                                                            <span style="color:#697b7c;font-size:14px; line-height:14px; text-align:justify">
                                                                                <?= $mailBody; ?>
                                                                        </span>
                                                                    </span>
																</div>
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											</tbody>
										</table>
										<table border="0" cellpadding="0" cellspacing="0" width="100%"
											   class="ButtonBlock">
											<tbody class="ButtonBlockOuter">
											<?php if (isset($id)) { ?>
												<tr>
													<td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;"
														valign="top" align="center" class="ButtonBlockInner">
														<table border="0" cellpadding="0" cellspacing="0"
															   class="ButtonContentContainer"
															   style="border-collapse: separate !important;border-radius: 5px;background-color: #00BCC8;">
															<tbody>
															<tr>
																<td align="center" valign="middle" class="ButtonContent"
																	style="font-family: Arial; font-size: 16px; padding: 16px;">
																	<?php
																	if (isset($id) && $id !== '' && !isset($toHr)){
																		?>
																		<a class="Button " title="Click to approve"
																		   href="<?= base_url() ?>ApproveConsultant/approveInvoiceByConsultant/<?= $id ?>"
																		   target="_blank"
																		   style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;">
																			Click to view & approve</a>
																	<?php }?>
																</td>
															</tr>
															</tbody>
														</table>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td valign="top" class="TextBlockInner">
													<table align="left" border="0" cellpadding="0" cellspacing="0"
														   width="600" class="TextContentContainer">
														<tbody>
														<tr>
															<td valign="top" class="TextContent"
																style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
																<div>
                                                      <span style="font-family:arial,helvetica neue,helvetica,sans-serif">
                                                         <br>
                                                         <br>
                                                      <span style="color:#697b7c"><span
																  style="font-size:14px; line-height:14px; text-align:justify">Regards,<br>
                                                         Consultant Management System</span></span></span>
																</div>
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="ImageBlock">
								<tbody class="ImageBlockOuter">
								<tr>
									<td valign="top" style="padding:0px" class="ImageBlockInner">
										<table align="right" width="100%" border="0" cellpadding="0" cellspacing="0"
											   class="ImageContentContainer">
											<tbody>
											<tr>
												<td class="ImageContent" valign="top"
													style="padding-right: 0px; padding-left: 0px; padding-top: 0; padding-bottom: 0;">
													<img align="right" alt=""
														 src="<?= base_url() ?>public/images/logo/logo.png" width="600"
														 style="max-width:1200px; padding-bottom: 0; display: inline !important; vertical-align: bottom;"
														 class="Image">
												</td>
											</tr>
											</tbody>
										</table>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</center>
</body>
</html>
