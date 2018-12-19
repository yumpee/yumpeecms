<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

?>
<div class="Loginsec section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-12 col-xs-12">
                    <div class="loginBox">
                        <div class="loginTitle">
                            <h3>Login Now</h3>
                            <?=$page['description']?>
                            <font color="red"><?=$message?></font>
                        </div>
                        <form class="loginForm" action="<?=login_url?>" method="post">
                            <div class="inputform">
                                    <i class="lni-user"></i>
                                    <input type="text" name="username" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
                            </div>
                            <div class="inputform">
                                    <i class="lni-lock"></i>
                                    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="inputform mb-3">
                               <div class="chickBoxLogin">
                                <input type="checkbox" class="checkbox-blue">
                                       <label class="chickboxLabel" for="checkedall">Keep me logged in</label>
                                       <a href="##" class="forgetpassoword">Forgot Passoword ?</a>
                               </div>
                               <div class="text-center"><button class="btn loginbtn" type="submit">Submit</button></div>
                               
                            </div>

                            
                        <input type="hidden" name="<?=$param?>" value="<?=$token?>"><input type="hidden" name="callback" value="<?=$callback?>">
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



    
