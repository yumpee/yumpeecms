<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="comment-form">

              <form action="#" id="frmBlogFeedback">
                <h2>Leave a Comment</h2>
                <div class="row">
                  <div class="col-md-4">
                    <input name="name" required pattern="[a-zA-Z ]+" type="text" placeholder="Name">
                  </div>
                  <div class="col-md-4">
                    <input name="email" required pattern="^[a-zA-Z0-9-\_.]+@[a-zA-Z0-9-\_.]+\.[a-zA-Z0-9.]{2,5}$" type="text" placeholder="Email">
                  </div>
                  <div class="col-md-4">
                    <input name="website" required type="text" placeholder="Website">
                  </div>
                  <div class="col-md-12">
                    <textarea name="comments" required cols="10" rows="10" placeholder="Comments"></textarea>
                  </div>
                  <div class="col-md-12">
                    <input type="button" class="btn btn-primary" value="Submit" id="btnBlogFeedback">
                    <input type="hidden" name="url" value="">
                  </div>
                </div>
              </form>

</div>

