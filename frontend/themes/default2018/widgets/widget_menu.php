<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<ul class="parent_menu">
<?php
$rec_counter=0;
                        foreach($header_menus as $rec):
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
                            endforeach;
?>
</ul>