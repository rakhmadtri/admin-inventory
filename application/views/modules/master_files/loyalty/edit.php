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
										<td class="form-label" width="120px" nowrap>Customer <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="400px" nowrap>
											<select id="custID" name="custID" class="form-control" data-live-search="true" liveSearchNormalize="true" title="Customer" onchange="getCompany()">
		                                  		<option value="">&nbsp;</option>
					                                <?php 
					                                    $this->db->where('status !=','-100');
														$customers = $this->db->get('customers')->result();			
					                                  	foreach($customers as $cust){
					                                  		
					                                  ?>
			                                  			<option value="<?php echo $cust->custID?>" <?php echo $cust->custID == $rec->custID ? 'selected' : '' ?> company="<?php echo $cust->companyName?>"><?php echo $cust->lname.', '.$cust->fname?></option>
			                                 	 <?php } ?>
			                                </select>
										</td>
										<td class="form-label" width="120px" nowrap>Company</td>
										<td class="form-group form-input" width="400px" nowrap>
											<input type="text" class="form-control" name="companyName" id="companyName" title="Company" value="<?php echo $rec->companyName?>" readonly>
										</td>
										<td class="form-label" ></td>
										<td class="form-label" ></td>
									</tr>
									<tr>
										<td class="form-label" width="120px" nowrap>Name <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="400px" nowrap><input type="text" class="form-control" name="name" id="name" value="<?php echo $rec->name?>" title="Name" required></td>

										<td class="form-label" width="120px" nowrap>Products </td>
										<td class="form-group form-input" width="400px" nowrap>
											<select id="productID" name="productID" class="form-control" data-live-search="true" liveSearchNormalize="true" title="Products" >
		                                  		<option value="">&nbsp;</option>
					                           <?php 
					                                    $this->db->where('status !=','-100');
														$products = $this->db->get('products')->result();			
					                                  	foreach($products as $prod){
					                                  ?>
			                                  			<option value="<?php echo $prod->productID?>" <?php echo $prod->productID == $rec->productID ? 'selected' : '' ?>><?php echo $prod->name?></option>
			                                 	 <?php } ?> 
			                                </select>											
										</td>
										<td class="form-label" ></td>
										<td class="form-label" ></td>
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

<!-- Details -->
<div class="modal  fade" id="details_list_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			
			<div class="modal-header">
				<h4 class="modal-title">Details List</h4>
			</div>
			<div class="modal-body">
				<div class="table-row" id="details_list">
					<table class="table-view">
						<tbody>
							<tr>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
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