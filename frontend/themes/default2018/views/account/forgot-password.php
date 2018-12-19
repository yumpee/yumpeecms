<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$this->title = $page['title'];
?>

<div class="Loginsec section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-12 col-xs-12">
                    <div class="loginBox">
                        <?=$page['description']?>
                        <div class="loginTitle">
                            <h3></h3>
                        </div>
                        <form class="loginForm" action="#" id="yumpee_form_register" method="post">                            
                            <div class="inputform">     
									<label>Enter your registered email</label>
                                    <input placeholder="" name="email" type="email" class="form-control">
                            </div>
                            <div class="inputform mb-3">                               
                               <div class="text-center"><button type="submit" class="btn loginbtn">Submit</button></div>                             
                            </div>

                            
                        <input type="hidden" name="csrf-Param" value="<?=$form['param']?>"><input type="hidden" name="<?=$form['param']?>" value="<?=$form['token']?>">
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>






