<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<h4>Recent Posts</h4>
                <div class="box">
                  <div class="recent-post">
                    <ul>
                        <?php
                        foreach($articles as $article):
                        ?>
                                <li> <strong><a href="<?=$baseURL?>/<?=$article->indexURL?>/<?=$article->url?>"><?=$article->title?></a></strong>

                                <div class="img-frame"><a href="<?=$baseURL?>/<?=$article->indexURL?>/<?=$article->url?>"><?=$article->thumbnailImage?></a></div>

                                <div class="text-area"> <a href="<?=$baseURL?>/<?=$article->indexURL?>/<?=$article->url?>"><i class="fa fa-user"></i><?=$article->author->first_name?> <?=$article->author->last_name?></a> <a href="#"><i class="fa fa-calendar"></i><?=$article->updated?></a> </div>

                      </li>
                      <?php
                       endforeach;
                      ?>
                    </ul>
                  </div>
                </div>
