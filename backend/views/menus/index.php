<?php
/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */
$this->title = 'Menus';


use kartik\sortable\Sortable;
$saveURL = \Yii::$app->getUrlManager()->createUrl('menus/save');
$saveMenuProfileURL = \Yii::$app->getUrlManager()->createUrl('menus/save-profile');
$saveFooterURL = \Yii::$app->getUrlManager()->createUrl('menus/save-footer');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('menus/delete');

$this->registerJs( <<< EOT_JS
      $('#sortable4, #sortable5').sortable({
	connectWith: '.connected'
                                
      });
        
        $('#sortable6, #sortable7').sortable({
	connectWith: '.connected'
                                
      });
        
       $(document).on('click', '#btnSubmit',
       function(ev) {   
        $("#enabled_menus").val(document.getElementById('sortable4').innerText);
        $("#disabled_menus").val(document.getElementById('sortable5').innerText);
        $("#top_profile").val($('select[name="menu_profile_list"]').val());
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
            
       $(document).on('click', '#btnFooterSubmit',
       function(ev) {   
        $("#footer_enabled_menus").val(document.getElementById('sortable6').innerText);
        $("#footer_disabled_menus").val(document.getElementById('sortable7').innerText);
        $.post(
            '{$saveFooterURL}',$( "#frm2" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
     
  $("#menu_profile_list" ).change(function() {
  location.href='index.php?r=menus/index&profile=' + $('select[name="menu_profile_list"]').val();
});
   
$('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var block = $(this).attr('menu_name');
                    if(confirm('Are you sure you want to delete - ' + block)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
  $(document).on('click', '#btnSaveMenu',
       function(ev) {   
        $.post(
            '{$saveMenuProfileURL}',$( "#frmMenus" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
         
if($("#edit_menu").val()=="edit_menu"){
  $('#menu_tab').trigger('click')        
 }
            
EOT_JS
); 
echo Sortable::widget([
    'items'=>[
        
    ]
]);

?>
<style>
		#demos section {
			overflow: hidden;
		}
		.sortable {
			width: 310px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		.sortable.grid {
			overflow: hidden;
		}
		.sortable li {
			list-style: none;
			border: 1px solid #CCC;
			background: #F6F6F6;
			color: #1C94C4;
			margin: 5px;
			padding: 5px;
			height: 30px;
		}
		.sortable.grid li {
			line-height: 80px;
			float: left;
			width: 80px;
			height: 80px;
			text-align: center;
		}
		.handle {
			cursor: move;
		}
		.sortable.connected {
			width: 300px;
			min-height: 250px;
			float: left;
		}
		li.disabled {
			opacity: 0.5;
		}
		li.highlight {
			background: #CCCCCC;
		}
		li.sortable-placeholder {
			border: 1px dashed #CCC;
			background: none;
		}
	</style>
        
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#configure">Configure</a></li>
        <li><a data-toggle="tab" href="#menus" id="menu_tab">Menu Profiles</a></li>
  
    </ul>
    <div class="tab-content">
    <div id="configure" class="tab-pane fade in active">
        <div class="container">
            <br>Select Menu Profile <select name="menu_profile_list" id="menu_profile_list"><option value='0'>System:Default</option>
                <?php
                foreach($records as $rec):
                    if(Yii::$app->request->get('profile')==$rec["id"]):
                            echo "<option value='".$rec["id"]."' selected>".$rec["name"]."</option>";
                        else:
                            echo "<option value='".$rec["id"]."'>".$rec["name"]."</option>";
                    endif;
                    
                endforeach;
                ?>
            </select><p>
    <section>
		<h4>Top Navigation Menu</h4>
		<ul id="sortable4" class="connected sortable list">Enabled
                        <?php
                        foreach ($active_menu as $menu) :
                        ?>
                                <li id="<?=$menu['id']?>" style="cursor:pointer"><a><?=$menu['menu_title']?></a>
                        <?php endforeach  ?> 
                           
			
		</ul>                
		<ul id="sortable5" class="connected sortable list">Disabled
                        <?php
			foreach ($inactive_menu as $menu) :
                        ?>
                                <li id="<?=$menu['id']?>" style="cursor:pointer"><a><?=$menu['menu_title']?></a>
                        <?php endforeach  ?> 
		</ul>
	</section>
    <br />
    <form id="frm1" name="frm1" action="post">
        <input type="hidden" name="enabled_menus" id="enabled_menus" value="" />
        <input type="hidden" name="disabled_menus" id="disabled_menus" value="" />
        <input type="hidden" name="top_profile" id="top_profile" value="0" />
    <input type="button" value="Save" id="btnSubmit" class="btn btn-primary" /><input type="hidden" name="processor" value="true" />
    </form>
    <hr>
        </div>
        <div class="container">
 <section>
		<h4>Bottom Navigation Menu</h4>
		<ul id="sortable6" class="connected sortable list">Enabled
                        <?php
			foreach ($footer_active_menu as $menu) :
                        ?>
                                <li id="<?=$menu['id']?>" style="cursor:pointer"><a><?=$menu['menu_title']?></a>
                        <?php endforeach  ?> 
		</ul>
                
		<ul id="sortable7" class="connected sortable list">Disabled
			<?php
			foreach ($footer_inactive_menu as $menu) :
                        ?>
                                <li id="<?=$menu['id']?>" style="cursor:pointer"><a><?=$menu['menu_title']?></a>
                        <?php endforeach  ?> 
		</ul>
	</section> 
        <form id="frm2" name="frm2" action="post">
        <input type="hidden" name="footer_enabled_menus" id="footer_enabled_menus" value="" />
        <input type="hidden" name="footer_disabled_menus" id="footer_disabled_menus" value="" />
        <input type="button" value="Save" id="btnFooterSubmit" class="btn btn-primary" /><input type="hidden" name="processor" value="true" />
        </form>
</div>
</div>
        <div id="menus" class="tab-pane fade in">
            <form action="index.php?r=events/index" method="post" id="frmMenus">
        <table class="table">
        <tr><td>Menu Name<td><input name="name" id="name" value="<?=$menu_rs['name']?>" class="form-control" type="text" />
        <tr><td>Description<td><textarea name="description" id="description" rows="3" cols="30" class="form-control"><?=$menu_rs['description']?></textarea>  
        
        <tr><td colspan="2"><button type="submit" id="btnSaveMenu" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="menu_id" value="<?=$menu_rs["id"]?>" />
            
        </td>
    </table>
    </form><input id="edit_menu" type="hidden" value="<?=Yii::$app->request->get("actions")?>"/>
            <div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-hover"><thead><tr><th>Name</th><th>Description<th>Action</thead>
        <tbody>
<?php
    foreach($records as $record):
                
?>
    <tr><td><?=$record['name']?></td></td><td><?=$record['description']?></td><td><a href='?actions=edit_menu&menu_id=<?=$record['id']?>&r=menus/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' menu_name='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a> </td>
        
 <?php
        endforeach;
?>
        </tbody>
</table>
</div>
</div>
        </div>
    </div>
</div>
        
              