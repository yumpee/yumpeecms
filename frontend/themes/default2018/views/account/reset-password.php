<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$this->title = $page['title'];
?>
<section id="inner-banner">
    <div class="container">
      <h1>Reset Password</h1>
    </div>
  </section>
<p>&nbsp;&nbsp;<br><br>
<div id="main">
    <div class="container">
        

<form action="#" id="yumpee_form_register" method="post">
    <div id="pwdModal"  tabindex="-1" aria-hidden="true">
  
          <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                          
                          <p>Enter your new password</p>
                            <div class="panel-body">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control input-lg" placeholder="Enter Password" name="password" type="password">
                                        <input class="form-control input-lg" placeholder="Re-enter Password" name="password2" type="password">
                                    </div>
                                    <input class="btn btn-lg btn-primary btn-block" value="Save" type="submit">
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      
  </div>
 
   <input type="hidden" name="csrf-Param" value="<?=$form['param']?>"><input type="hidden" name="<?=$form['param']?>" value="<?=$form['token']?>"><input type="hidden" name="reset_token" value="<?=$form['reset_token']?>" />
</form>
    </div>
</div>