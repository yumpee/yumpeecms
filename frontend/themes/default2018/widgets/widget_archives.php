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
                      foreach($archive_object as $archive_month):
                      ?>
                      <li><a href="<?=$archive_month->archivesDirectory?>/<?=$archive_month->archive?>"><?=$archive_month->archiveDate?></a></li>
                      <?php
                      endforeach;
                      ?>
                                    
                                </ul>
                            </div>



              