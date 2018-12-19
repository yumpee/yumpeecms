<?php
/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>

<?php
    foreach($articles as $article):
?>

<h4><?=$article->title?></h4>

                <div class="box">
                  
                  <div class="frame"><a href="<?=$baseURL?>/<?=$article->indexURL?>/<?=$article->url?>"><?=$article->widgetImage?></a></div>

                  <div class="text-col">

                    <p><?=$article->lead_content?></p>

                  </div>

                </div>

<?php
    endforeach;
?>

                
