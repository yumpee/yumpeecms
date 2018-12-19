<?php
$this->title = $tags_record->name;
?>

<div class="mainclass section">
            <div class="container">
                <?=$tags_record->name?>
                <div class="row justify-content-center">
                    <div class="col-sm-12 col-md-12 col-lg-8">
                        <?php
                            $contents="";
                            $counter=0;
                            foreach($articles as $rec):
                            if($counter > 2):
                                    $counter=0;
                                    $contents.="<div class='row'></div>";
                            endif;
                            $counter++;
                        ?>
                        <div class="blogPost boxshadow">
                            <div class="postImage ">
                                <a href="#"><img src='<?=$rec['displayImage']['uploadDir']."/".$rec['displayImage']['path']?>' style='width:auto;height:auto' alt="plogPost" class="img-fluid"></img></a>
                            </div>


                            <div class="postContent">
                                <h2 class="postTitle">
                                    <a href="<?=$rec['formattedIndexURL']?>/<?=$rec['url']?>"><?=$rec['title']?></a>
                                </h2>
                                <div class="meta">
                                    <span>
                                       <a href='<?=$rec->authorsDirectory."/".$rec->author->username?>'>
                                            <i class="lni-user"></i>
                                            <?=$rec->author->first_name." ".$rec->author->last_name?></a>
                                        </a>
                                    </span>
                                    <span>
                                        <a href="#">
                                            <i class="lni-alarm-clock"></i>
                                            <?=$rec['publishDate']?>
                                        </a>
                                    </span>

                                    <span>
                                        <a href="#">
                                            <i class="lni-folder"></i>
                                            Sticky
                                        </a>
                                    </span>
                                    <span>
                                        <a href="#">
                                            <i class="lni-comments-alt"></i>
                                            1 Comments
                                        </a>
                                    </span>
                                </div>
                                <div class="postSummary">
                                    <p><?=$rec['lead_content']?>
                                    </p>

                                </div>
                                <a href="<?=$rec['formattedIndexURL']."/".$rec['url']?>" class="btn btn-common">Read more</a>

                            </div>
                        </div>
                        <?php
                            endforeach;
       
                        ?>
                       
                        <!---->
                        <div class="pagination-bar">
                            <nav>
                            <ul class="pagination">
                            <li class="page-item"><a class="page-link active" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                            </nav>
                            </div>
                    </div>
                    <!------------------------------------------------->

                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="right-side" id="yumpee_sidebar_widgets">
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>








 