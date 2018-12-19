<?php
/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="categoriesBox boxshadow">
                                <h4 class="BoxshadowTitle"> <?=$title?></h4>
                                <ul>
                                    <?php
                      foreach($category_object as $category_record):
                          if($category_record->count > 0): //if a category item is more than 1 then display
                      ?>
                      <li><a href="<?=$baseURL."/".$category_record->indexURL."/".$category_record->url?>"><?=$category_record->name?><span class="category-counter">(<?=$category_record->count?>)</span></a></li>
                      <?php
                           endif;
                      endforeach;
                      ?>
                                    
                                </ul>
                            </div>



