<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="row">
            <div class="col-sm-12 col-md-12 col-lg-8">
                    <form class="sendform" id="frmContact">
                        <h4><?=$title?></h4>
                        <hr>
                     
                      
                       
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                
                                <div id="yumpee_block_bottom_content"></div>
                              <div class="row">
                                 <div class="col-sm-12 col-md-12  col-lg-4  ">
                                   <div class="inputForm">
                                       <input class="messageInput" type="text" placeholder="Name" name="name">
                                   </div>
                               </div>
                             <div class="col-sm-12 col-md-12 col-lg-4   ">
                                  <div class="inputForm">
                                      <input class="messageInput" type="email" placeholder="Email" email="email">
                                  </div>
                              </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 ">
                                <div class="inputForm">
                                        <input class="messageInput" type="text" placeholder="subject" name="subject">  
                                  </div>
                            </div>
                            </div>
                            </div>
                                 <div class="col-sm-12 col-md-12 col-lg-12">
                                   <div class="row">
                                       <div class="col-md-12">
                                         <div class="inputForm">
                                            <textarea  class="messageInput form-control" placeholder="Message" rows="10"  required="" data-error="Write your message" name="comments"></textarea>
            
                                         </div>
                                       </div>
                                    </div>
                             </div>
                            <div class="col-sm-12 col-md-12">
                                 <button type="submit" class="contsub" style="pointer-events: all; cursor: pointer;" id="btnContact">Submit Now</button>
            
                            </div>
                            </div>
                    </form>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4  ">
                        <div class="addresssec">
                            <div class="address">
                                <h4>Address</h4>
    
                            </div>
                            <p>
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero explicabo repellendus deleniti eaque, 
                             </p>

                         </div>
                         <div class="contactinfo">
                                <div class="address">
                                        <h4>Contact info</h4>
            
                                </div>
                                <?=$contact_object['setting_value']?>
                                
                         </div>
                </div>
            
    </div>
