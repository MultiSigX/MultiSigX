<h4>Recent reviews:</h4>
<p>Write / read a review to help us server you better. </p>
<div class="btn-group">		
	<a href="/users/review" class="btn btn-success">Write a review</a>
</div>				

<?php
$i=0;

foreach($reviews as $review){?>
    <blockquote>
	<h4><?=$review['review.title'];?></h4>	
	<p><?=$review['review.text'];?></p>	
    <small><cite title="<?=$review['username'];?>"><?=$review['username'];?></cite> <?=$review['review.datetime.date'];?></small>
<!--
	<?=$this->form->create('',array('url'=>'/users/addvote/')); ?>
	<?php for($j=1;$j<=5;$j++){ ?>
		<?php foreach($average['result'] as $av){
			if($av['_id']['user_id']==$review['user_id']){?>
			<?php if((int)$av['point']==$j){?>
				<input class="star" type="radio" name="Rate" value="<?=$j?>" checked="checked"/>			
				<?php }else{?>
				<input class="star" type="radio" name="Rate" value="<?=$j?>" />							
				<?php }?>
			<?php
			}
			}
		?>

	<?php }?>
		<input type="hidden" name="user_id" value="<?=$review['user_id']?>">
	<?=$this->form->end(); ?>
-->
    </blockquote>
	<br>
<?php	
$i++;
}

?>
