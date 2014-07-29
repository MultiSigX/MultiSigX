
<div class="row">
	<div class="col-md-6" >
	<h4>Review</h4>	
<?=$this->form->create(null,array('class'=>'form-group has-error')); ?>
<?=$this->form->field('review.title', array('label'=>'Title','placeholder'=>'Title of the review','class'=>'form-control')); ?><br>
<?=$this->form->textarea('review.text', array('label'=>'Your review','class'=>'form-control','style'=>'height:200px')); ?><br>
<?=$this->form->hidden('review.datetime.date',array('value'=>gmdate('Y-m-d',time())));?>
<?=$this->form->hidden('review.datetime.time',array('value'=>gmdate('H:i:s',time())));?>
<?=$this->form->submit('Review',array('class'=>'btn btn-primary')); ?>
<?=$this->form->end(); ?>
</div>
<div class="col-md-6">
<h4>Recent reviews:</h4>
<?php
foreach($reviews as $review){?>
    <blockquote>
	<h5><?=$review['review.title'];?></h5>	
	<p><?=$review['review.text'];?></p>	
    <small><cite title="<?=$review['username'];?>"><?=$review['username'];?></cite> <?=$review['review.datetime.date'];?></small>
    </blockquote>
<?php	
}
?>
</div>
</div>