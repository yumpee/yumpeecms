<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$this->title = $page['title'];
?>
<!--INNER BANNER START-->

  <section id="inner-banner">

    <div class="container">

      <h1><?=$page['title']?></h1>

    </div>

  </section>

  <!--INNER BANNER END--> 

  

  <!--MAIN START-->

  <div id="main"> 

    

    <!--POST AREA START-->

    <section class="post-section blog-post">

      <div class="container">

            <div class="row">

                <div class="col-md-9 col-sm-8">

                    <div class="post-box">

                        <div class="frame"><a href="#"><?=$header_image?></a></div>

                        <div class="text-box">

                            <div class="clearfix"> </div>
                            <div id="yumpee_block_top_content"></div>
                            <h4><?=$page['title']?></h4>   
                            <p><?=$page['description']?></p>
                            <div id="yumpee_block_bottom_content"></div>
                        </div>
              

                    </div>

                    <div id="yumpee_bottombar_widgets"></div>

                </div>

          <div class="col-md-3 col-sm-4">

            <aside>

              <div class="sidebar">
                  <div id="yumpee_sidebar_widgets">
                  </div>
              </div>

            </aside>

          </div>

        </div>

      </div>

    </section>

    <!--POST AREA END--> 

  </div>

  <!--MAIN END--> 