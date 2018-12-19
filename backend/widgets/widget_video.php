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
        <h4 class="modal-title">Video Widget</h4>
      </div>
        <form id='frmWidget'>
      <div class="modal-body">
        <p>Title<input type="text" size='50'  id='widget_title' name='widget_title' value="<?=$settings ? $settings->widget_title:''?>"/></p>
        <p>URL<input type="text" size='50'  id='widget_url' name='widget_url' value="<?=$settings ? $settings->widget_url:''?>"/></p>
        <p>Height<input type="text" size='5' maxlength='5' id='widget_height' name='widget_height' value="<?=$settings ? $settings->widget_height:'500px'?>"/></p>
        <p>Width<input type="text" size='5' maxlength='5' id='widget_width' name='widget_width' value="<?=$settings ? $settings->widget_width:'500px'?>"/></p>
      </div>
         <input type='hidden' name='widget_name' value="<?=$widget?>" /><input type='hidden' name='widget_page_id' value="<?=$template_id?>" /><input type='hidden' name='id' value="<?=$id?>" />
        </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='btnSaveWidget'>Save</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div>

  </div>