<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">PROVINCE</td>
		<td class="top left">CITY</td>
		<td class="top left">BARANGAY</td>
		<td class="top left">REMARKS</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach ($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->province ?></td>
		<td class="top left"><?php echo $row->city ?></td>
		<td class="top left"><?php echo $row->barangay ?></td>
		<td class="top left"><?php echo $row->remarks ?></td>
		<td class="top left right" align="center">
			<?php 
				if ($row->status == 1) {
					echo "<font color='green'>Active</font>";
				} else {
					echo "<font color='red'>Inactive</font>";
				}
				?>
		</td>
	</tr>
	<?php 
		}
		?>
	<tr style="background-color: #ffffff;">
		<td class="top" colspan="5">&nbsp;</td>
	</tr>
</table>
