<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<?=$title?>
<div class="tab-pane fadeIn show active" id="grid">
        <div class="row">
            <?php
          $contents="";
          $counter=0;
          foreach($records as $rec):
           if($counter > 2):
                $counter=0;
                $contents.="<div class='row'></div>";
           endif;
           
           $counter++;
       ?>
            <div class="col-lg-4 col-md-6 col-12">
                                        <div class="BlogBox">
                                                <figure>
                                                        <a href="<?=$rec['formattedIndexURL']."/".$rec['url']?>"><img src='<?=$rec['displayImage']['uploadDir']."/".$rec['displayImage']['path']?>' style='width:auto;height:auto' alt="plogPost" class="img-fluid"></img></a>
                                                        <div class="prodDesc  FeaturesDesc">
                                                                <div class="details">
                                                                        <span class="float-left">

                                                                                
                                                                            <a href='<?=$rec->authorsDirectory."/".$rec->author->username?>'><i class="lni-user"></i><?=$rec->author->first_name." ".$rec->author->last_name?></a>
                                                                        </span>
                                                                        <span class="float-right">

                                                                                <a href="#"><i class="lni-calendar"></i>
                                                                                        <?=$rec['publishDate']?></a>
                                                                        </span>
                                                                        <div class="clear-fix"></div>

                                                                </div>

                                                                <h3><?=$rec['title']?></h3>

                                                                <p><?=$rec['lead_content']?>
                                                                </p>
                                                                <a href="<?=$rec['formattedIndexURL']."/".$rec['url']?>" class="btn  readmore">Read more</a>

                                                        </div>

                                                </figure>

                                        </div>

                                </div>
            <?php
                endforeach;
       
          ?>    
        </div>
            
</div>



          