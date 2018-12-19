<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = $page['title'];
?>
<div class="container">
    <?=$page['description']?>
<form id="rendered-form" id="yumpee_form_register" method="post"><div class="rendered-form">
        <div class="fb-text form-group field-first_name">
            <label for="first_name" class="fb-text-label">First Name<br></label>
            <input type="text" class="form-control" name="first_name" id="first_name">
        </div><div class="fb-text form-group field-last_name">
            <label for="last_name" class="fb-text-label">Last Name<br></label>
            <input type="text" class="form-control" name="last_name" id="last_name">
        </div><div class="fb-text form-group field-email">
            <label for="email" class="fb-text-label">Email</label>
            <input type="email" class="form-control" name="email" id="email">
        </div><div class="fb-text form-group field-username">
            <label for="username" class="fb-text-label">Username</label>
            <input type="text" class="form-control" name="username" id="username">
        </div><div class="fb-text form-group field-password">
            <label for="password" class="fb-text-label">Password</label>
            <input type="password" class="form-control" name="password" id="password">
        </div><div class="fb-text form-group field-password2">
            <label for="password2" class="fb-text-label">Confirm Password<br></label>
            <input type="password" class="form-control" name="password2" id="password2">
        </div><div class="fb-button form-group field-btnSubmit">
            <button type="submit" name="btnSubmit" id="btnSubmit" class="btn loginbtn">Register</button>
        </div></div>
    <input type="hidden" name="csrf-Param" value="<?=$form['param']?>"><input type="hidden" name="<?=$form['param']?>" value="<?=$form['token']?>">
</form>
</div>

