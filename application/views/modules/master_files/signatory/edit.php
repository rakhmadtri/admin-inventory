<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools"></div>
	</div>
</div>
<div class="content">
	<div class="row">
		<div class="col-12">
			<div class="card-box">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">Edit <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools"></div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo $controller_page.'/update'; ?>" >
						<input type="hidden" id="<?php echo $pfield ?>" name="<?php echo $pfield ?>" value="<?php echo $this->encrypter->encode($rec->$pfield); ?>"/>
						<div class="table-form column-3">
							<table class="table-form column-3">
								<tbody>
									<tr>
										<td class="form-label" width="120px" nowrap>Code <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="400px" nowrap>
											<input type="text" class="form-control" name="code" id="code" value="<?php echo $rec->code?>" readonly maxlength="10" title="Code" required>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label" width="120px" nowrap>Name <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="400px" nowrap>
											<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $rec->fullname?>" title="Name" required>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label" width="120px" nowrap>Designation 1 <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="400px" nowrap>
											<input type="text" class="form-control" name="designation1" id="designation1" value="<?php echo $rec->designation1?>" title="Designation" required>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label" width="120px" nowrap>Designation 2</td>
										<td class="form-group form-input" width="400px" nowrap>
											<input type="text" class="form-control" name="designation2" id="designation2" value="<?php echo $rec->designation2?>">
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label" width="120px" nowrap>Designation 3</td>
										<td class="form-group form-input" width="400px" nowrap>
											<input type="text" class="form-control" name="designation3" id="designation3" value="<?php echo $rec->designation3?>">
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Status <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<select id="status" name="status" class="form-control" title="Status" >
												<option value="" selected>&nbsp;</option>
												<option value="1" <?php if ($rec->status==1) { echo "selected"; } ?>>Active</option>
												<option value="0" <?php if ($rec->status==0) { echo "selected"; } ?>>Inactive</option>
											</select>
										</td>
										<td class="d-xxl-none" colspan="3"></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="form-sepator solid"></div>
						<div class="form-group mb-0">
							<button class="btn btn-xs btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">
								Save
							</button>
							<input type="button" id="cmdCancel" class="btn btn-xs btn-outline-danger btn-raised pill" value="Cancel"/>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#cmdSave').click(function(){
		if (check_fields()) {
			$('#cmdSave').attr('disabled','disabled');
			$('#cmdSave').addClass('loader');
			$('#frmEntry').submit();
		}
	});

	function check_fields()
	{
		var valid = true;
		var req_fields = "";

		$('#frmEntry [required]').each(function(){
			if($(this).val()=='' ) {
				req_fields += "<br/>" + $(this).attr('title');
				valid = false;
			} 
		})

		if (!valid) {
			swal("Required Fields",req_fields,"warning");
		}

		return valid;
	}

	$('#cmdCancel').click(function(){
		swal({
			title: "Are you sure?",
			text: "",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes',
			cancelButtonText: 'No'
		})
		.then((willDelete) => {
			if (willDelete.value) {
				window.location = '<?php echo $controller_page.'/show' ?>';
			}
		});

	});

</script>