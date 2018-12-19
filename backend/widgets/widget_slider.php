<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Slider</h4>
      </div>
        <form id='frmWidget'>
      <div class="modal-body">
        <p>No of records to display <input type="text" size='2' maxlength='2' id='widget_limit' name='widget_limit' value="<?=$settings ? $settings->widget_limit:''?>"/></p>
        <p>Title for widget <br><input type="text" size='50' id='widget_title' name='widget_title' value="<?=$settings ? $settings->widget_title:''?>"/> (HTML allowed)</p>
      </div>
         <input type='hidden' name='widget_name' value="<?=$widget?>" /><input type='hidden' name='widget_page_id' value="<?=$template_id?>" /><input type='hidden' name='id' value="<?=$id?>" />
        </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='btnSaveWidget'>Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div>

  </div>
