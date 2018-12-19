<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
$facebook="";
$twitter="";
$googleplus="";
$linkedin="";
if($settings!=null):
    if(!empty($settings->facebook)):
        $facebook=" checked";
    endif;
    if(!empty($settings->twitter)):
        $twitter=" checked";
    endif;
    if(!empty($settings->googleplus)):
        $googleplus=" checked";
    endif;
    if(!empty($settings->linkedin)):
        $linkedin=" checked";
    endif;
endif;
?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Social Share</h4>
      </div>
        <form id='frmWidget'>
      <div class="modal-body">
        
            <div class="rows">
                <div class="col-md-8">Facebook</div><div class="col-md-4"><input type="checkbox" name="facebook" <?=$facebook?>></div>
            </div>            
            <div class="rows">
                <div class="col-md-8">Twitter</div><div class="col-md-4"><input type="checkbox" name="twitter" <?=$twitter?>></div>
            </div>
            <div class="rows">
                <div class="col-md-8">Google +</div><div class="col-md-4"><input type="checkbox" name="googleplus" <?=$googleplus?>></div>
            </div>    
            <div class="rows">
                <div class="col-md-8">LinkedIn</div><div class="col-md-4"><input type="checkbox" name="linkedin" <?=$linkedin?>></div>
            </div>
        
        
      
         <input type='hidden' name='widget_name' value="<?=$widget?>" /><input type='hidden' name='widget_page_id' value="<?=$template_id?>" /><input type='hidden' name='id' value="<?=$id?>" />
      </div>
        </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='btnSaveWidget'>Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
        </div>
    </div>



