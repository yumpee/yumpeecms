<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<style>
  .stars<?=$rating_profile['id']?>{
  displaY:inline-block;
  position:relative;
}
.stars<?=$rating_profile['id']?> span{
  color:#999;
  displaY:inline-block;
  vertical-align:middle;
  float:right;
  cursor:pointer;
}
.stars<?=$rating_profile['id']?>>span:hover:after{
  position:absolute;
  content: attr(title);
  left:100%;
}
<?php
for($i=1;$i <= count($rating_profile['details']);$i++):
    $j=$i-1;
?>
.stars<?=$rating_profile['id']?>>span:nth-child(<?=$i?>).active,
.stars<?=$rating_profile['id']?>>span:nth-child(<?=$i?>).active~span,
.stars<?=$rating_profile['id']?>>span:nth-child(<?=$i?>):hover,
.stars<?=$rating_profile['id']?>>span:nth-child(<?=$i?>):hover~span{color: <?=$rating_profile['details'][$j]['rating_rgb_color']?>;}
<?php
endfor;
?>
.fa-star,.fa-star-half-empty,.fa-star-o{
  color: gold;
}
</style>
<script>
    $(".stars<?=$rating_profile['id']?>").on("click", "span", function(){  
  
  var rating = <?=count($rating_profile['details'])?> - $(this).index();
  var rating = $(this).attr("value");
  $(this).addClass("active").siblings().removeClass("active");
  
  // and submit vote usign AJAX to server
  //alert("You gave "+ rating +" stars! <?=$rating_submit_url?>");
  $.post( "<?=$rating_submit_url?>", { "<?=$param?>": "<?=$token?>", rating: rating, target_id: "<?=$rating_article_id?>",rating_profile : <?=$rating_profile['id']?> } );
});
</script>


<?php
if($rating_method=="set"):
?>
<div class="stars<?=$rating_profile['id']?>">
    <?=$rating_profile['default_label']?> 
    <?php
    foreach($rating_profile['details'] as $details):
    ?>
    <span title="<?=$details['rating_name']?>" value="<?=$details['rating_value']?>">&#9733;</span>
    
    <?php
    endforeach;
    ?>
</div>
<?php
else:
?>


<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">

<span class="starss" data-rating="<?=$rating_value?>" data-num-stars="<?=count($rating_profile['details'])?>" ></span>

<script>
    $.fn.stars = function() {
        return $(this).each(function() {

            var rating = $(this).data("rating");

            var numStars = $(this).data("numStars");

            var fullStar = new Array(Math.floor(rating + 1)).join('<i class="fa fa-star"></i>');

            var halfStar = ((rating%1) !== 0) ? '<i class="fa fa-star-half-empty"></i>': '';

            var noStar = new Array(Math.floor(numStars + 1 - rating)).join('<i class="fa fa-star-o"></i>');

            $(this).html(fullStar + halfStar + noStar);

        });
    }

    $('.starss').stars();
    </script>
    <?php
    endif;
    ?>
