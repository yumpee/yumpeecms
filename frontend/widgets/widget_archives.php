<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

?>
<h4>Archives</h4>

                <div class="box">

                  <div class="archives-box">

                    <ul>
                      <?php
                      foreach($archive_object as $archive_month):
                      ?>
                      <li><a href="#"><?=date("F Y", strtotime($archive_month->archive))?><span></span></a></li>
                      <?php
                      endforeach;
                      ?>

                      

                    </ul>

                  </div>

                </div>

              </div>