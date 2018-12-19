<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>

<div class="categoriesBox boxshadow">
        <h4 class="BoxshadowTitle"><?=$title?></h4>
        <figure>
            <div class="textSide">
              <div class="prodDesc  FeaturesDesc"> <i class="fa fa-quote-left"></i>
                  <div class="details">
                <?=$testimonials->content?>
                  </div>
              </div>
              <strong class="name"><?=$testimonials->author?></strong> <em><?=$testimonials->author_position?></em> 
            </div>
        </figure>
</div>
