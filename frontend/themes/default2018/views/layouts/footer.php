
    <!--Add the Yumpee Block Top Footer Stub -->
    <div id="yumpee_block_top_footer"></div>
                       <?php
                        if($footer_theme_setting=="Yes"):
                            echo $footer_content;
                        else:
                        ?>
    <!-- BlackFooter Section Start-->
<div class="blackfooter">
        <div class="container">
                <div class="row">
                                <div class="col-lg-4 col-md-4 col-xs-6 col-mb-12">
                                                <div class="logofooter">
                                                        <div class="logoF"><img src="img/logo.png" alt=""></div>
                                                        <div class="logoFtext" id="yumpee_block_bottom_left">
                                                                <p>
                                                                                
                                                                </p>
                                                        </div>
                                                         <div class="socialfooterIcons">
                                                                <div class="footerIcon facebook"><a  href="##"><i class="lni-facebook-filled"></i></a></div>
                                                                <div class="footerIcon twitter"><a  href="##"><i class="lni-twitter-filled"></i></a></div>
                                                                <div class="footerIcon linkedin"><a href="##"><i class="lni-linkedin-filled"></i></a></div>
                                                                <div class="footerIcon google"><a href="##"><i class="lni-google-plus"></i></a></div>
                                                        </div>
                                                        
                                                </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-xs-6 col-mb-12">
                                                        <div class="QuickLink">
                                                        <h3 class="block-title">Quick Link</h3>
                                                        <ul class="menu">
														<?php
														foreach($footer_menus as $recs):
															echo "<li><a href='".$header_baseURL."/".$recs['url']."'> - ".$recs['menu_title']."</a></li>";
														endforeach;
														?>
                                                        
                                                        </ul>
                                                        </div>
                                         </div>
                                         <div class="col-lg-4 col-md-4 col-xs-6 col-mb-12">
                                                        <div class="QuickLink">
                                                        <h3 class="block-title">Contact info</h3>
                                                        <ul class="ContactInfo">
                                                                <li>
                                                                <span><i class="lni-phone"></i></span>
                                                                <span><?=$settings->getSetting("custom_support_phone_number")?></span>
                                                                </li>
                                                                 <li><span><i class="lni-envelope"></i></span>
                                                                 <span><?=$settings->getSetting("custom_support_email")?></span>
                                                                </li>
                                                                <li><span><i class="lni-map-marker"></i></span>
                                                                <span><a href="##"><?=$settings->getSetting("custom_contact_address")?></a></span>
                                                                </li>
                                                                </ul>                                                        </div>
                                                        </div>
                                         </div>
                </div>
                

        </div>
</div>
<!-- BlackFooter Section End-->
<!--***************************************************************************************************-->
<div class="Footer">
        <div class="container">
                <div class="row">
                        <div class="col-12 text-center">
                                <p>
                                        <?=$settings->getSetting("custom_copyright")?>
                                </p>
                        </div>
                </div>
        </div>
</div>
    <?php
    endif;
    ?>
    <div class="container">
        <div class="bottom-row"> <strong class="copyrights"><center>Powered by <a href='http://www.yumpeecms.com'>Yumpee CMS</a></center></strong>
        
      </div>
    </div>
    <!--Add the Yumpee Block Bottom Footer Stub -->
    <div id="yumpee_block_bottom_footer"></div>
  </footer>
  <!--FOOTER END--> 
