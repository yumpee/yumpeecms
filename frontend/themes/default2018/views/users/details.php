<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

?>
<!--INNER BANNER START-->

  <section id="inner-banner">

    <div class="container">

      <h1><?=$blogger['first_name']?> <?=$blogger['last_name']?> Page</h1>

    </div>

  </section>
<div class="container">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profile</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#articles" role="tab" aria-controls="profile" aria-selected="false">Articles</a>
  </li>
  <?php
  foreach($blogger['listings'] as $listing):  
  ?>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#<?=$listing['name']?>" role="tab" aria-controls="contact" aria-selected="false"><?=$listing['title']?></a>
  </li>
  <?php
  endforeach;
  ?>
</ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="home-tab">Profile Content</div>
  <div class="tab-pane fade" id="articles" role="tabpanel" aria-labelledby="profile-tab">Articles</div>
  <?php
  foreach($blogger['listings'] as $listing):  
  ?>
  <div class="tab-pane fade" id="<?=$listing['name']?>" role="tabpanel" aria-labelledby="contact-tab">
  <?php
        //we get the associated form submits for this
        foreach($listing['dataRecords'] as $forms):
            foreach($forms['data'] as $record):
                  ?> 
        <?=$record['param']?> : <?=$record['param_val']?><br />
  <?php
            endforeach;
            echo "<a href=''>Details</a><hr>";
        endforeach;
  ?>
  </div>
  <?php
  endforeach;  
  ?>
  
</div>
</div>