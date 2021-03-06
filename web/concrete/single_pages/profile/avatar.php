<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
    <?php Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">	

        <h2><?php echo t('User Avatar')?></h2>
        <p><?php echo t('Change the picture attached to my posts.')?></p>
                            
        <div style="position:relative; width:100%; height:500px ;">		

          <script language="javascript">
            <?php if(strlen($error)) { ?>
            window.parent.ccmAlert.notice("<?php echo t('Upload Error')?>", "<?php echo str_replace("\n", '', nl2br($error))?>");
            window.parent.ccm_alResetSingle();
            <?php } ?>
          </script>

          <?php $valt = Loader::helper('validation/token');
          if ($this->controller->getTask() == 'upload_avatar' && $error=="") {
            $html = Loader::helper('html');
            echo $html->javascript('/js/jquery.Jcrop.js');
            echo $html->css('/css/jquery.Jcrop.css');
            ?>
            <script type="text/javascript">
              var pic_real_width, pic_real_height;
              function checkCoords(){
                if (parseInt($('#w').val())) return true;
                alert("<?php echo t('Please select a crop region then press submit.'); ?>");
                return false;
              };
              $(document).ready(function(){
                pic_real_width = <? echo $targetImageWidth; ?>;
                pic_real_height = <? echo $targetImageHeight; ?>;
                // Create variables (in this scope) to hold the API and image size
                var jcrop_api,
                  boundx,
                  boundy,
                // Grab some information about the preview pane
                  $preview = $("img[src|='<?php echo $av->getImagePath($ui,false)?>'], img[src|='<? echo AVATAR_NONE;?>']"),
                  xsize = <?php echo AVATAR_WIDTH?>,
                  ysize = <?php echo AVATAR_HEIGHT?>,
                  loaded_xsize = $('#avatarCropArea').width(),
                  loaded_ysize = $('#avatarCropArea').height();
                prepareAvatarsOnPage($preview);

                $('#avatarCropArea').Jcrop({
                  onChange: updatePreview,
                  onSelect: updatePreview,
                  aspectRatio: xsize / ysize,
                  minSize: [xsize/pic_real_width*loaded_xsize,ysize/pic_real_height*loaded_ysize],
                  setSelect: [0,0,xsize/pic_real_width*loaded_xsize,ysize/pic_real_height*loaded_ysize],
                  bgColor: ''
                },function(){
                  // Use the API to get the real image size
                  var bounds = this.getBounds();
                  boundx = bounds[0];
                  boundy = bounds[1];
                  // Store the API in the jcrop_api variable
                  jcrop_api = this;
                  jcrop_api.animateTo([ 0, 0, xsize, ysize ]);
                });
                $('#shown_w').val($('#avatarCropArea').width());
                $('#shown_h').val($('#avatarCropArea').height());
                $('#real_w').val(pic_real_width);
                $('#real_h').val(pic_real_height);
                function updatePreview(c){
                  if (parseInt(c.w) > 0){
                    $.each($preview, function($index, $avatar){
                      var rx = $($avatar).parent().width() / c.w;
                      var ry = $($avatar).parent().height() / c.h;
                      $($avatar).css("width",Math.round(rx * boundx) + 'px')
                        .css("height",Math.round(ry * boundy) + 'px')
                        .css("marginLeft",-Math.round(rx * c.x) + 'px')
                        .css("marginTop",-Math.round(ry * c.y) + 'px');
                    });
                  }
                  updateCoords(c);
                };
                function updateCoords(c){
                  $('#x').val(c.x);
                  $('#y').val(c.y);
                  $('#w').val(c.w);
                  $('#h').val(c.h);
                };
                function prepareAvatarsOnPage($avatarList){
                  $.each($avatarList, function($index, $avatar){
                    $($avatar).after("<div class='avatar_placeholder' style='width:"+$avatar.width+"px;height:"+$avatar.height+"px;'></div>");
                    $($avatar).wrap("<div class='avatar_preview' style='width:"+$avatar.width+"px;height:"+$avatar.height+"px;'></div>");
                    $($avatar).removeClass("u-avatar").addClass("p-avatar");
                    $($avatar).attr('src',$('#avatarCropArea').attr("src"));
                  });
                }
              });
            </script>

            <?php if($targetImage != "") { ?>
              <div>
                <form method="post" enctype="multipart/form-data" action="<?php echo $this->action('upload_avatar')?>" class="ccm-file-manager-submit-single">
                  <input type="file" name="Filedata" class="ccm-al-upload-single-file"  />
                  <input class="ccm-al-upload-single-submit btn" type="submit" value="<?php echo t('Upload File')?>" />
                  <?php echo $valt->output('upload');?>
                </form>
              </div>
              <img src="<?php echo $targetImage;?>" style="max-width:100%; max-height:400px;" id="avatarCropArea"/>
              <div>
                <form method="post" enctype="multipart/form-data" action="<?php echo $this->action('crop_and_save_avatar')?>" onsubmit="return checkCoords();">
                  <input type="submit" class="btn" value="<?php echo t('Save')?>">
                  <input type="hidden" id="imageId" name="imageId" value="<?php echo $targetImageId?>" />
                  <input type="hidden" id="x" name="x" />
                  <input type="hidden" id="y" name="y" />
                  <input type="hidden" id="w" name="w" />
                  <input type="hidden" id="h" name="h" />
                  <input type="hidden" id="shown_w" name="shown_w" />
                  <input type="hidden" id="shown_h" name="shown_h" />
                  <input type="hidden" id="real_w" name="real_w" />
                  <input type="hidden" id="real_h" name="real_h" />
                  <?php echo $valt->output('crop_n_save_upload');?>
                </form>
              </div>
            <?php } ?>
          <?php }else{ ?>

            <?php if ($ui->hasAvatar()) { ?>
              <a href="<?php echo $this->action('delete')?>"><?php echo t('Remove your user avatar &gt;')?></a>
            <?php } ?>

            <div class="spacer"></div>
            <br/>

            <?php echo t('Upload a file to add a new avatar.')?>
            <div>
              <form method="post" enctype="multipart/form-data" action="<?php echo $this->action('upload_avatar')?>" class="ccm-file-manager-submit-single">
                <input type="file" name="Filedata" class="ccm-al-upload-single-file"  />
                <input class="ccm-al-upload-single-submit btn" type="submit" value="<?php echo t('Upload File')?>" />
                <?php echo $valt->output('upload');?>
              </form>
            </div>
          <?php } ?>

        </div>
    </div>
	
	<div class="ccm-spacer"></div>
</div>
