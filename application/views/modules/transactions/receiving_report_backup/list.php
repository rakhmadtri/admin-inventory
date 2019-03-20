
<form name="frmFilter" id="frmFilter" method="POST" action="<?php echo $controller_page ?>/show">
	<input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>" />
	<input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>" />
	<div class="subheader">
		<div class="d-flex align-items-center">
			<div class="title mr-auto">
				<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></span></h3>
			</div>
			<?php if ($roles['create']) {?>
			<div class="subheader-tools">
				<a href="<?php echo $controller_page?>/create" class="btn btn-xs btn-primary btn-raised btn-sm pill"><i class="icon left la la-plus lg"></i>Add New</a>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-12">
				<div class="card-box full-body">
					<div class="card-head">
						<div class="head-caption">
							<div class="head-title">
								<h4 class="head-text"><?php echo $current_module['module_label'] ?> List</h4>
							</div>
						</div>
						<div class="card-head-tools">							
                            <ul class="tools-list">
                            	<li>
                            		<button id="btn-apply" type="submit" class="btn btn-primary btn-xs pill collapse multi-collapse show">Apply Filter</button>
                            	</li>
                            	<li>
                            		<button type="button" id="btn-filter" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Filters" onclick="#"><i class="la la-sort-amount-asc"></i></button>
                            	</li>
                            	<li>
                            		<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo $controller_page.'/printlist' ?>', 800, 500)"><i class="la la-print"></i></button>
                            	</li>
                            	<li>
                            		<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo $controller_page.'/exportlist' ?>';"><i class="la la-file-excel-o"></i></button>
                            	</li>
                            </ul>
						</div>
					</div>
					<div class="card-body">
						<div class="datatables_wrapper">
							<div class="table-responsive">
								<table class="table table-striped hover">
									<thead>
										<tr>
											<?php 
		$headers = array(
			array('column_header'=>'RR No','column_field'=>'iarDate','width'=>'w-15','align'=>''),
			array('column_header'=>'RR Date.','column_field'=>'iarNo','width'=>'w-15','align'=>''),																
			
			array('column_header'=>'DR No.','column_field'=>'drNo','width'=>'w-15','align'=>''),
			array('column_header'=>'Supplier','column_field'=>'poNo','width'=>'w-15','align'=>''),
			array('column_header'=>'Requisitioning Office','column_field'=>'requisitioningOffice','width'=>'w-20','align'=>''),
			array('column_header'=>'Status','column_field'=>'status','width'=>'w-10','align'=>''),
		);
		
		echo $this->htmlhelper->tabular_header($headers, $sortby, $sortorder);
		?>
										</tr>
										<tr id="filter-group" class="collapse multi-collapse table-filter show">
											<th class="form-group form-input">
												<input type="text" class="form-control datepicker" data-toggle="datetimepicker" data-target="#iarDate" id="iarDate" name="iarDate" value="<?php echo ($iarDate)? date('M d, Y', strtotime($iarDate)) : ""; ?>">
											</th>
											<th class="form-group form-input">
												<input type="text" class="form-control" id="iarNo" name="iarNo" value="<?php echo $iarNo ?>">
												
											</th>
											<th class="form-group form-input">
												<input type="text" class="form-control" id="poNo" name="poNo" value="<?php echo $poNo ?>">
											</th>
											<th class="form-group form-input">
												<input type="text" class="form-control" id="drNo" name="drNo" value="<?php echo $drNo ?>">
											</th>
											<th class="form-group form-input">
												<input type="text" class="form-control" id="requisitioningOffice" name="requisitioningOffice" value="<?php echo $requisitioningOffice ?>">
											</th>


											<th class="form-group form-input">
												<select class="form-control" id="status" name="status">
													<option value="">&nbsp;</option>
													<option value="1" <?php echo ($status == "1") ? "selected" : ""; ?>>Pending</option>
													<!-- <option value="2" <?php //echo ($status == "2") ? "selected" : ""; ?>>Inspected</option> -->
													<option value="3" <?php echo ($status == "3") ? "selected" : ""; ?>>Accepted</option>
													<option value="0" <?php echo ($status == "0") ? "selected" : ""; ?>>Cancelled</option>
													
												</select>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if (count($records)) {
											  foreach($records as $row) {
										?>
										<tr onclick="location.href='<?php echo $controller_page.'/view/'.$this->encrypter->encode($row->$pfield); ?>'">
											
											<td><?php echo date('Y-m-d', strtotime($row->iarDate)); ?></td>
											<td><?php echo $row->iarNo ?></td>
											<td><?php echo $row->poNo ?></td>
											<td><?php echo $row->drNo ?></td>
											<td><?php echo $row->requisitioningOffice ?></td>
											
											<td align="center">
												<?php 
													if ($row->status == 1) {
														echo "<span class='badge badge-pill badge-warning'>Pending</span>";
													} else if ($row->status == 0) {
														echo "<span class='badge badge-pill badge-danger'>Cancelled</span>";
													} else if ($row->status == 2) {
														echo "<span class='badge badge-pill badge-info'>Inspected</span>";
													} else if ($row->status == 3) {
														echo "<span class='badge badge-pill badge-success'>Accepted</span>";
													}
												?>
											</td>
										</tr>
										<?php     } //End foreach ?>
										<?php } else { ?>
										<tr>
											<td nowrap="nowrap" colspan="3" align="center"><b><i>No result found.</i></b></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="datatable-footer d-flex">
								<div class="datatable-pagination">
									Pages: &nbsp;&nbsp; 
								</div>
								<div class="datatable-pagination">
									<?php 
										$pagination = $this->pagination->create_links(); 
										
										if ($pagination) {
										     echo $pagination;      
										} else {
										    echo "1";
										}
									?>
								</div>
								<div class="datatable-pager-info float-right ml-auto">
									<div class="d-flex">
										<div class="datatable-pager-size">
											<div class="dataTables_length">
												<select aria-controls="table1" class="form-control select-sm pill" tabindex="-98" id="limit" name="limit" onchange="$('#frmFilter').submit();">
													<option value="25" <?php if ($limit==25) echo "selected"; ?>>25</option>
													<option value="50" <?php if ($limit==50) echo "selected"; ?>>50</option>
													<option value="75" <?php if ($limit==75) echo "selected"; ?>>75</option>
													<option value="100" <?php if ($limit==100) echo "selected"; ?>>100</option>
													<option value="125" <?php if ($limit==125) echo "selected"; ?>>125</option>
													<option value="150" <?php if ($limit==150) echo "selected"; ?>>150</option>
													<option value="175" <?php if ($limit==175) echo "selected"; ?>>175</option>
													<option value="200" <?php if ($limit==200) echo "selected"; ?>>200</option>
													<option value="all" <?php if ($limit=='All') echo "selected"; ?>>All</option>
												</select>
											</div>
										</div>
										<div class="datatable-pager-detail">
											<div class="dataTables_info">Displaying <?php echo ($offset+1) ?> - <?php if ($offset+$limit < $ttl_rows) { echo ($offset+$limit); } else  { echo $ttl_rows; } ?> of <?php echo number_format($ttl_rows,0)?> records</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

