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

      <h1><?=$page['title']?></h1>

    </div>

  </section>

<!--MAIN START-->

  <div id="main"> 

    <!--RECENT JOB SECTION START-->

    <section class="resumes-section padd-tb">

      <div class="container">

        <div class="row">

          <div class="col-md-9 col-sm-8">

            <div class="resumes-content">

              <div class="check-filter">

                

              </div>

              <h2>Showing All Profiles for <?=$page['roles']['name']?></h2>
              <?php
              foreach($records as $rec):
              ?>
              <div class="box">

                <div class="frame"><a href="#"><img height='165' src="<?=$rec['displayImage']['uploadDir']."/".$rec['displayImage']['path']?>" alt=""></a></div>

                <div class="text-box">

                  <h2><a href="<?=$page['pagePath']?>/<?=$rec['username']?>"><?=$rec['first_name']?> <?=$rec['last_name']?></a></h2>

                  <h4><?=$rec['title']?></h4>
                  <div><?=$rec['about']?></div>
                  <div class="tags"> <a href="<?=$rec['articlesHome']."/".$rec['username']?>">Articles</a> 
                  <?php
                  foreach($rec['listings'] as $forms):
                      //we can other postings that this user has made
                  ?>
                      <a href="<?=$forms['viewRenderer']['url']."/".$rec['username']?>"> | <?=$forms['title']?></a>
                  <?php
                  endforeach;
                  
                  ?>
                  
                  </div>

                  

                </div>

              </div>
              <?php
              
              endforeach;
              ?>
                           

              <div id="loadMore"> <a href="#" class="load-more"><i class="fa fa-user"></i>Load More Jobs</a></div>

            </div>

          </div>

          <div class="col-md-3 col-sm-4">

           

          </div>

        </div>

      </div>

    </section>

    <!--RECENT JOB SECTION END--> 

  </div>

