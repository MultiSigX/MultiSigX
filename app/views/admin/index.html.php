<h3>User registrations</h3>
<?php 
if($StartDate==""){
	$StartDate = gmdate('Y-m-d',mktime(0,0,0,gmdate('m',time()),gmdate('d',time()),gmdate('Y',time()))-60*60*24*30);
	$EndDate = gmdate('Y-m-d',time()+1*60*60*24);
}else{
	$StartDate=gmdate('Y-m-d',$StartDate->sec);
	$EndDate=gmdate('Y-m-d',$EndDate->sec);
}
?>


<form action="/Admin/index" method="post" >
<div class="row">
	<div class="col-md-4">
		<div class="input-append date " id="StartDate" data-date="<?=$StartDate?>" data-date-format="yyyy-mm-dd"> From: &nbsp;
			<input  size="16" name="StartDate" type="text" value="<?=$StartDate?>" readonly >
			<span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
		</div>
	</div>
	<div class="col-md-4">
		<div class="input-append date " id="EndDate" data-date="<?=$EndDate?>" data-date-format="yyyy-mm-dd">
		To:&nbsp;
			<input size="16"  name="EndDate" 	type="text" value="<?=$EndDate?>" readonly>
			<span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
		</div>
	</div>
	<div class="col-md-4">
	<input type="submit" value="Get report" class="btn btn-primary btn-sm">
	<div class="alert alert-error" id="alert"><strong></strong></div>
	</div>
</div>
	</form>
	<hr>
<div >
<table class="table table-condensed table-bordered table-hover" style="width:40% ">
	<tr>
		<th style="text-align:center;">Date</th>
		<th style="text-align:center ">Users</th>
	</tr>
	<?php foreach($new as $key=>$value){	?>
	<tr>
		<td><?=$key;?></td>
		<td style="text-align:center "><?=$value['Register']?>&nbsp;</td>
	</tr>
		<?php  
				$users = $users + $value['Register'];
		?>
<?php }?>
<tr>
<th>Total</th>
<th style="text-align:center " ><?=$users?></th>		
</tr>
</table>
</div>
<script src="/js/admin.js"></script>