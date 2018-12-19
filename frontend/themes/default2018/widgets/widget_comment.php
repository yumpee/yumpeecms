<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>

<div class="commentContiner boxshadow">
<?php
if(count($article_object['approvedComments']) > 0){
?>
<h3>Comments</h3>

<?php
}

?>

                            <ul class="commentList">
                                <?php
              foreach ($article_object['approvedComments'] as $record):
              ?>
                                <li>
                                    <div class="media">
                                        <div class="image float-left">
                                            <a href="#">
                                                <img src="img/blog/comment1.jpeg" alt="comment1">
                                            </a>
                                        </div>
                                        <div class="commentText float-left boxshadow">
                                            <h4 class="name"><?=$record['commentor']?></h4>
                                            <span class="date"><i class="lni-alarm-clock"></i><?=$record['publishDate']?></span>
                                            <a href="#" class="reply"><i class="lni-reply"></i> Reply</a>
                                            <p><?=$record['comment']?></p>
                                        </div>
                                        <div class="clear-fix"></div>
                                    </div>
                                </li>

                                
                                <?php
                                    endforeach;
                                ?>

                            </ul>
<div class="respond">
                                <h2><?=$title?></h2>
                                <form action="#" id="frmBlogFeedback">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <input id="author" class="form-control" name="name" type="text" value=""
                                                    size="30" placeholder="Your Name">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <input id="email" class="form-control" name="email" type="text" value=""
                                                    size="30" placeholder="Your E-Mail">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <textarea id="comment" class="form-control" name="comments" cols="45"
                                                    rows="8" placeholder="Message..." style="margin-top: 0px; margin-bottom: 0px; height: 171px;"></textarea>
                                            </div>
                                        </div>
                                        <button type="submit" id="btnBlogFeedback" class="btn btn-primary">Post Comment</button>
                                        <input type="hidden" name="url" value="">
                                    </div>

                                </form>
                            </div>
</div>


