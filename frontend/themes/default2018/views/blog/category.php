<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = $page['title'];
?>
<div class="container">
    <h1><?=$page['title']?></h1>
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
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="productBox gridBox">
                    <figure>
                        <a href="<?=$rec['categoryURL']?>/<?=$rec['url']?>"><?=$rec['image']?></a>
                        
                     </figure>


                    <div class="prodDesc">
                        
                        <h4><?=$rec['name']?></h4>
                        
                        <p><?=$rec['description']?></p>
                        <div class="last">
                        <a href="<?=$rec['categoryURL']?>/<?=$rec['url']?>" class="btn float-right pricedetails">View Details</a>
                        </div>
                        
                    </div>
                            
                </div>
                    
            </div>
            <?php
                endforeach;
       
          ?>    
        </div>
            
</div>
</div>
                                                

  