<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->title = $page['title'];
?>

<div class="mainclass section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-12 col-md-12 col-lg-8">
                        <div class="blogPost boxshadow">
                            <div class="postImage ">
                                <?php
                        switch($page['article_type']):
                        case "1":
                        ?>
                        <a href="#"><img src='<?=$page['displayImage']['uploadDir']."/".$page['displayImage']['path']?>' style='width:auto;height:auto' alt="plogPost" class="img-fluid"></img></a>
                        <?php
                        break;
                        case "2":
                         ?>
                        <video width="100%" height="314" controls><source src="<?=$page['featured_media']?>"></source></video> 
                        <?php
                        break;
                        case "3":
                        ?>
                         <iframe src="https://www.youtube.com/embed/<?=$page['featured_media']?>" width="100%" height="100%" allowfullscreen="allowfullscreen"></iframe>
                        <?php
                         break;
                        case "4":
                        ?>
                         <audio width="560" height="314" controls><source src="<?=$page['featured_media']?>"></source></audio>
                         <?php
                        endswitch;
                        ?>
                                
                        </div>


                            <div class="postContent">
                                <h2 class="postTitle">
                                    <a href="#"><?=$page['lead_content']?></a>
                                </h2>
                                <div class="meta">
                                    <span>
                                        <a href="<?=$page->authorsDirectory."/".$page->author->username?>">
                                            <i class="lni-user"></i>
                                            <?=$blogger['first_name']." ".$blogger['last_name']?></a>
                                        </a>                                    
                                    </span>
                                    <span>
                                        <a href="#">
                                            <i class="lni-alarm-clock"></i>
                                            <?=$page['publishDate']?>
                                        </a>
                                    </span>

                                    <span>
										<?php
											foreach($page->blogIndex as $index):
										?>
                                        <a href="<?=$index->page->url?>">
                                            <i class="lni-folder"></i>
												<?=$index->page->title?>
                                        </a>
										<?php
											break;
											endforeach;
										?>
										
                                    </span>
                                    <span>
                                        <a href="#">
                                            <i class="lni-comments-alt"></i>
                                            <?=count($page->approvedComments)?> Comments
                                        </a>
                                    </span>
                                </div>
                                <div class="postSummary">
                                    <p><?=$page['body_content']?></p>
                                    <div class="respond" id="yumpee_block_bottom_content">  
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                        <div id="yumpee_bottombar_widgets">
                        </div>
                    </div>


                    <!---->
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="right-side" id="yumpee_sidebar_widgets">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>







