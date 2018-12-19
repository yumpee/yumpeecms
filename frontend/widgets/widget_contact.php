<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="post-box"><p>
              <div class="col-md-9 col-sm-8">
                <h2>Get in Touch</h2>
                <form action="#" id="frmContact">
                  <div class="row">
                    <div class="col-md-4">
                      <input name="name" class="form-control" required pattern="[a-zA-Z ]+" type="text" placeholder="Name">
                    </div>
                    <div class="col-md-4">
                      <input name="email" class="form-control" required pattern="^[a-zA-Z0-9-\_.]+@[a-zA-Z0-9-\_.]+\.[a-zA-Z0-9.]{2,5}$" type="text" placeholder="Email">
                    </div>
                    <div class="col-md-4">
                      <input name="subject" class="form-control" required type="text" placeholder="Subject">
                    </div>
                    <div class="col-md-12">
                      <br /><br /><textarea class="form-control" name="comments" required cols="10" rows="10" placeholder="Comments"></textarea>
                    </div>
                    <div class="col-md-12">
                        <input name="comments" type="button" class="btn btn-primary" value="Submit" id="btnContact"><p>
                    </div>
                  </div>
                </form>
              </div>
              <div class="col-md-3 col-sm-4">
                <div class="address-box">
                  <address>
                  <ul>
                    <li> <i class="fa fa-phone"></i> <strong>+440 875 369 200</strong> <strong>+440 875 123 697</strong> </li>
                    <li> <i class="fa fa-envelope-o"></i> <a href="mailto:">info@jobinn.com</a> </li>
                    <li> <i class="fa fa-globe"></i> <a href="#">www.jobinn.com</a> </li>
                  </ul>
                  </address>
                </div>
              </div>
</div>
