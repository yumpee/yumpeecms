<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="container">
  <h2><?=$slider_object['title']?></h2>  
<div class="slider-section">
<div id="myCarousel" class="carousel slide" data-ride="carousel">
<div class="carousel-inner">
<?php
       $counter=0;
       foreach($slider_object->imagesObject as $rec):
           
       
		if($counter==0):
		?>
			<div class="carousel-item active">
		<?php else : ?>
			<div class="carousel-item">
		<?php endif;?>
		
<div class="overlay">&nbsp;</div>
<img src="<?=$slider_object->uploadURL?>/<?=$rec['media_id']?>" alt="slide1" style="width:<?=$slider_object['default_width']?>;height:<?=$slider_object['default_height']?>"/>
<div class="carousel-caption">
<h1 class="animated wow fadeInDown hero-heading animated" data-wow-delay=".4s"></h1>
<p class="animated fadeInUp wow hero-sub-heading animated" data-wow-delay=".6s"></p>
</div>
</div>


<?php 
	$counter++;
endforeach; 
?>
</div>
<a id="qq" class="carousel-control-prev" href="#myCarousel" data-slide="prev"> <span id="car1" class="carousel-control-prev-icon"></span> </a> <a id="bb" class="carousel-control-next" href="#myCarousel" data-slide="next"> <span id="car2" class="carousel-control-next-icon"></span></a>
</div>
</div>
</div>




