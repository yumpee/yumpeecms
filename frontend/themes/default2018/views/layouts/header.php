<?php
/*
 * $header_baseURL contains data on the baseURL
 * $header_menu_logo has the reference to the logo as set up in the settings in backend
 * $header_request_url has information on the requested url
 * $header_menus has the reference to the Page object where the show in menu is set to 1
 */

$this->title = 'Welcome to YumpeeCMS';
 ?>
<?php
                        if($header_theme_setting=="Yes"):
                            echo $header_content;
                        else:
                            ?>
<link rel="stylesheet" href="https://cdn.lineicons.com/1.0.0/LineIcons.min.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
<meta name="viewport"  content="width=device-width, initial-scale=1">
							
    <body>
        <!--***********************************************************-->
		<a href="#" class="up">
        <i class="lni-chevron-up"></i>
        </a>
        <!--Start Black-header Section-->
        <div class="black-header">
                        <div class="header-contenent">
								<div class="container">
                                <div class="row">
                                        <div class="col-sm-12 col-md-5  col-lg-7 ">
                                                <ul class="support">
                                                        <li><i class="lni-phone"></i><?=$settings->getSetting("custom_support_phone_number")?></li>
                                                        <li><i class="lni-envelope"></i> <?=$settings->getSetting("custom_support_email")?></li>
                                                </ul>
                                        </div>
                                        <div class="col-sm-12 col-md-7 col-lg-5 ">
                                                <div class="socialIcons float-right">
                                                        <a class="facebook" href="##"><i class="lni-facebook-filled"></i></a>
                                                        <a class="twitter" href="##"><i class="lni-twitter-filled"></i></a>
                                                        <a class="instagram" href="##"><i class="lni-instagram-filled"></i></a>
                                                        <a class="linkedin" href="##"><i class="lni-linkedin-filled"></i></a>
                                                        <a class="google" href="##"><i class="lni-google-plus"></i></a>
                                                </div>
                                                <div class="regist float-right">
												<?php
												if(Yii::$app->user->isGuest):
												?>
												<a href="<?=$header_baseURL?>/login"><i class="lni-lock"></i> Log In</a> |
                                                <a href="<?=$header_baseURL?>/signup"><i class="lni-pencil"></i> Register</a>
												<?php
												else:
												?>
												<a href="<?=$header_baseURL?>/my-account"><i class="fa fa-dashboard"></i> My Account</a> | <a href="<?=$header_baseURL?>/logout"><i class="lni-lock"></i> Log Out</a> 
												<?php
												endif;
												?>
                                                </div>
                                        </div>

                                        <div class="clear-fix"></div>
                                </div>
								</div>
                        </div>
                        <div class="main-menu">
                                <nav class="navbar navbar-expand-lg bg-white fixed-top scrolling-navbar top-nav-collapse">
                                       <div class="container">
                                                <div class="logo">
                                                        <img src="img/logo.png" alt="">
														<?php
															if($settings->hasLogo):           
														?><a href="<?=$header_baseURL?>"><?=$header_menu_logo?></a>
														<?php
															else:
														?>
														<a href="<?=$header_baseURL?>"><h4><?=$settings->websiteName?></h4><br><?=$settings->websiteTagline?> </a>
														<?php
															endif;        
														?>
                                                </div>
                                                <div class="navbar-header"></div>
                                       <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                                       <ul class="nav nav-pills mr-auto w-100 justify-content-center">
														<?php
                        
                        //We process the URL to determine which is the active url
                        $a = explode("/",$header_request_url);
                        $url_length = count($a);
                        $current_tab_url = $a[$url_length-1];
                        //End URL
                        $rec_counter=0;
                        foreach($header_menus as $rec):
                            if($rec['url']==$current_tab_url):
                                if(!$rec->isTopMenu):
                                    //echo "<li><a href='".$header_baseURL."/".$rec['url']."' class='active'>".$rec['menu_title']."</a>";
									
                                
									$submenu = $rec->topMenus;
									if(count($submenu) > 0):
                                                                                echo "<li class='nav-item dropdown activepage'>";
										echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" rle="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$rec['menu_title'].' <i class="fa fa-caret-down"></i></a>';
										echo "<div class='dropdown-menu' aria-labelledby='navbarDropdown'>";
										foreach($submenu as $recs):
											//echo "<li><a href='".$header_baseURL."/".$recs['url']."'>".$recs['menu_title']."</a>";
											echo "<a class='dropdown-item' href='".$header_baseURL."/".$recs['url']."'>".$recs['menu_title']."</a>";
										endforeach;
										echo "</div>";
                                                                                echo "</li>";
                                                                        else:
                                                                                echo "<li class='nav-item activepage'><a href='".$header_baseURL."/".$rec['url']."' class='nav-link '>".$rec['menu_title']."</a></li>";	
									endif;
									
								
                                endif;
                            else:
                                if(!$rec->isTopMenu):
                                    $submenu = $rec->topMenus;
									if(count($submenu) > 0):
                                                                                echo "<li class='nav-item dropdown'>";
										echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" rle="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$rec['menu_title'].' <i class="fa fa-caret-down"></i></a>';
										echo "<div class='dropdown-menu' aria-labelledby='navbarDropdown'>";
										foreach($submenu as $recs):
											//echo "<li><a href='".$header_baseURL."/".$recs['url']."'>".$recs['menu_title']."</a>";
											echo "<a class='dropdown-item' href='".$header_baseURL."/".$recs['url']."'>".$recs['menu_title']."</a>";
										endforeach;
										echo "</div>";
                                                                                echo "</li>";
                                                                        else:
                                                                                echo "<li class='nav-item'><a href='".$header_baseURL."/".$rec['url']."' class='nav-link '>".$rec['menu_title']."</a></li>";	
									endif;
                                endif;
                            endif;
                        endforeach;
                        ?>
                                                           
                                                        </ul> 
                                                         
                                        </div>
                                </div>
                                <ul class="mobile-menu">
                                                <?php
								foreach($header_menus as $rec):
								if(!$rec->isTopMenu):
                                    $submenu = $rec->topMenus;
									if(count($submenu) > 0):
                                        echo "<li>";
										echo '<a>'.$rec['menu_title'].'</a>';
										echo '<ul class="dropdown">';
										foreach($submenu as $recs):
											//echo "<li><a href='".$header_baseURL."/".$recs['url']."'>".$recs['menu_title']."</a>";
											echo "<li><a class='dropdown-item' href='".$header_baseURL."/".$recs['url']."'>".$recs['menu_title']."</a></li>";
										endforeach;
										echo "</ul>";
                                        echo "</li>";
                                    else:
                                        //echo "<li class='nav-item'><a href='".$header_baseURL."/".$rec['url']."' class='nav-link '>".$rec['menu_title']."</a></li>";
										echo "<li><a href='".$header_baseURL."/".$rec['url']."'>".$rec['menu_title']."</a></li>";
									endif;
                                endif;
								endforeach;
												?>
                                                 <!--End mobile Menu-->
                                                 
                                 </ul>   
                                </nav>
                                </div>
                                <!---->
                        <!--Start Hero Section-->
                        <div id="yumpee_pos_header_hero"></div>
                        
                <!--End Hero Section-->

        </div>
        
                </div>
<?php
    endif;
    ?>
    <div id="yumpee_block_bottom_header" class="row"></div> 
  </header>
  </body>
  <!--HEADER END--> 