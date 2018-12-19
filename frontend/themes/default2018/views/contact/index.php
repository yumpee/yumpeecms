<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = $page['title'];
?>
<!--Start googleMap Section-->
    <div class="googlemap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <object style="border:0; height: 450px; width: 100%;" data="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d34015.943594576835!2d-106.43242624069771!3d31.677719472407432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86e75d90e99d597b%3A0x6cd3eb9a9fcd23f1!2sCourtyard+by+Marriott+Ciudad+Juarez!5e0!3m2!1sen!2sbd!4v1533791187584">
                    </object>
                </div>
            </div>
        </div>
    </div>
    <!--End googleMap Section-->
<!--***************************************************************************************************-->

<div class="sendMessageUs">
<div class="container">
    <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-8">
                    <form class="sendform" id="frmContact">
                        <h4><?=$page['title']?></h4>
                        <hr>
                     
                        
                       
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <p><?=$page['description']?></p>
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
                                 <button type="submit" class="contsub" style="pointer-events: all; cursor: pointer;">Submit Now</button>
            
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
                                    Below is a list of contact information about where I live
                                    
                             </p>

                         </div>
                         <div class="contactinfo">
                                <div class="address">
                                        <h4>Contact info</h4>
            
                                </div>
                                <ul>
                                        <li><span>Address:</span><p> <?=$settings->getSetting("custom_contact_address")?></p></li>
                                        <li><span>Email:</span><p><a href="##"><?=$settings->getSetting("custom_support_email")?></a></p></li>
                                        <li><span>Phone:</span><p><?=$settings->getSetting("custom_support_phone_number")?></p></li>
                                </ul>
                         </div>
                </div>
            
    </div>
    
<div id="yumpee_bottombar_widgets" class="row">
</div>
</div>
    
</div>





  