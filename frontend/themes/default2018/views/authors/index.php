<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

?>
<section id="inner-banner">
    <div class="container">
      <h1>Authors</h1>
    </div>
  </section>
<br />
<div id="main"> 
    <div class="container">

        <div class="row">

          <div class="col-md-9 col-sm-8">
              <div class="resumes-content">
            
              <?php
              foreach ($records as $rec):
              ?>
              <div class="box">

                <div class="frame">
                    <?php
                    if($rec['displayImage']!=null):
                    ?>
                    <a href="<?=$rec['articlesHome']."/".$rec['username']?>">
                    <img height='165' src="<?=$rec['displayImage']->uploadDir."/".$rec['displayImage']->path?>" alt="img">
                    </a>
                    <?php
                    endif;
                    ?>
                </div>

                <div class="text-box">

                  <h2><a href="<?=$rec['articlesHome']."/".$rec['username']?>"><?=$rec['first_name']." ".$rec['last_name']?></a></h2>

                  <h4><?=$rec['title']?></h4>
                  <p><?=$rec['about']?></p>

                  

                </div>

              </div>
                  <?php
                  endforeach;
                  ?>
                  
          </div></div></div>
</div>