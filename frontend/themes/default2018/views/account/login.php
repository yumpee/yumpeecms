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
                        <div class="loginTitle">
                            <h3>Login Now</h3>
                            <?=$page['description']?>
                            <font color="red"><?=$form['message']?></font>
                        </div>
                        <form class="loginForm" action="<?=$form['login_url']?>" method="post">
                            <div class="inputform">
                                    <label>Username</label>
                                    <input type="text" name="username" id="inputEmail" class="form-control" placeholder="" required autofocus>
                            </div>
                            <div class="inputform">
                                    <label>Password</label>
                                    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="" required>
                            </div>
                            <div class="inputform mb-3">
                               <div class="chickBoxLogin">
                                <input type="checkbox" class="checkbox-blue">
                                       <label class="chickboxLabel" for="checkedall">Keep me logged in</label>
                                       <a href="forgot-password" class="forgetpassoword">Forgot Password ?</a>
                               </div>
                               <div class="text-center"><button class="btn loginbtn" type="submit">Submit</button></div>
                               
                            </div>

                            
                        <input type="hidden" name="<?=$form['param']?>" value="<?=$form['token']?>"><input type="hidden" name="callback" value="<?=$form['callback']?>">
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

