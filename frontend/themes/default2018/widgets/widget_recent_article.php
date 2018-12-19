<?php
/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="categoriesBox boxshadow">
                                <h4 class="BoxshadowTitle"><?=$title?></h4>
                                <ul>
                                    <?php
                                        foreach($articles as $article):
                                    ?>
                                    <li>
                                        <div class="imageSide float-left">
                                            <a href="<?=$baseURL?>/<?=$article->indexURL?>/<?=$article->url?>"><img src='<?=$article['displayImage']['uploadDir']."/".$article['displayImage']['path']?>' style='width:auto;height:auto' alt="plogPost" class="img-fluid"></img></a>
                                        </div>
                                        <div class="textSide float-left">
                                            <a href="<?=$baseURL?>/<?=$article->indexURL?>/<?=$article->url?>"><?=$article->title?></a>
                                            <span><?=$article->publishDate?></span>
                                        </div>
                                        <div class="clear-fix"></div>
                                    </li>
                                    <?php
                                        endforeach;
                                    ?>                                    
                                </ul>
                            </div>




