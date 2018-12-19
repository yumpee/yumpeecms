<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!--INNER BANNER START-->
  <section id="inner-banner">
    <div class="container">
      <h1>Register Your Account</h1>
    </div>
  </section>
  <!--INNER BANNER END--> 
  
  <!--MAIN START-->
  <div id="main"> 
    
    <!--SIGNUP SECTION START-->
    <section class="signup-section">
      <div class="container">
        <div class="holder">
          <div class="thumb"><img src="images/signup.png" alt="img"></div>
          <form action="#" id="yumpee_form_register">
            <div class="input-box"> <i class="fa fa-pencil"></i>
              <input type="text" placeholder="First Name">
            </div>
            <div class="input-box"> <i class="fa fa-pencil-square-o"></i>
              <input type="text" placeholder="Last Name">
            </div>
            <div class="input-box"> <i class="fa fa-envelope-o"></i>
              <input type="text" placeholder="Email">
            </div>
            <div class="input-box"> <i class="fa fa-user"></i>
              <input type="text" placeholder="Username">
            </div>
            <div class="input-box"> <i class="fa fa-unlock"></i>
              <input type="text" placeholder="Password">
            </div>
            <div class="input-box"> <i class="fa fa-unlock"></i>
              <input type="text" placeholder="Confirm Password">
            </div>
                      
            
            <div class="check-box">
              <input id="id3" type="checkbox" />
              <strong>I’ve read <a href="#">Terms</a> &amp; <a href="#">Conditions</a></strong> </div>
            <input type="submit" value="Sign up">
            <em>Already a Member? <a href="#">LOG IN NOW</a></em>
            <input type="hidden" name="param" value="<?=$form['param']?>"><input type="hidden" name="token" value="<?=$form['token']?>">
          </form>
        </div>
      </div>
    </section>
    <!--SIGNUP SECTION END--> 
    
  </div>
  <!--MAIN END--> 
  