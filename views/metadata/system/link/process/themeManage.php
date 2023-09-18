<div id="theme-manage-wrapper">

  <div id="theme-manage-canvas">

    <div id="sidebar">
      <div id="sidebar-menu">
        <ul>          
          <li class="active"><a class="mbcode_c_sidebar_button" href="javascript:;" data-target="#styles" rel="tooltip" data-toggle="tooltip" data-placement="right" title="" data-original-title="Styles"><i class="fa fa-th"></i></a></li>
          <li class=""><a class="mbcode_c_sidebar_button" href="javascript:;" data-target="#params" rel="tooltip" data-toggle="tooltip" data-placement="right" title="" data-original-title="Оролтын параметр "><i class="fa fa-list"></i></a></li>
        </ul>	
      </div>
      <div id="submenu">
        <div id="styles" class="sidebar-tabs">
          <div>
            <div class="sidebar-title">Style</div>
          </div>
          <div class="row style-content">
              <?php echo $this->styleList; ?>
          </div>
        </div>
        <div id="params" class="sidebar-tabs" style="display: none"> 
          <div>
            <div class="sidebar-title">Оролтын параметр</div>
          </div>
          <div class="input-meta-tags">
            <ul id="metas">
                <?php
                echo $this->metaInputParamList;
                ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="row columns">      
      <div class="col-md-12" id="themeManageSection">  
        <div class="col-md-4 no-padding">
            <?php
            echo Form::select(
                    array(
                        'name' => 'metaThemeId',
                        'id' => 'metaThemeId',
                        'text' => '-Theme сонгох-',
                        'class' => 'form-control form-control-sm select2',
                        'data' => $this->metaThemeList,
                        'op_value' => 'ID',
                        'op_text' => 'NAME'
                    )
            );
            ?>
        </div>

        <div class="col-md-4 no-padding">
          <label class="checkbox-inline">
              <?php
              echo Form::checkbox(
                    array(
                        'name' => 'isMultiLang',
                        'id' => 'isMultiLang',
                        'value' => '1',
                        'saved_val' => issetParam($this->themeLinkData['IS_MULTI_LANG'])
                    )
              );
              ?>
            Is Multi Language
          </label>
        </div>

        <div id="metaThemeContent">
          <?php
          if (isset($this->themeHtmlContent)) {
              echo $this->themeHtmlContent;
          }
          ?>
        </div>
      </div>
    </div>

  </div>

</div>

<script type="text/javascript">
    /* global processThemeManage*/
    var themeSectionDetailData, metaThemeArray = {};
<?php if (isset($this->themeSectionDetailData)) { ?>themeSectionDetailData=<?php echo json_encode($this->themeSectionDetailData); ?>;<?php } ?>
$(function(){
    
    if (typeof processThemeManage === 'undefined') {
        $.getScript(URL_APP + 'middleware/assets/js/process-theme/processThemeManage.js', function(){
            $.getStylesheet(URL_APP + 'middleware/assets/css/process-theme/style.css');
            iniProcessThemeManage();
        });
    } else {
        iniProcessThemeManage();
    }

    function iniProcessThemeManage(){
        processThemeManage.init();

<?php if (isset($this->themeSectionData)) { ?>
    processThemeManage.fillUpdateMode(<?php echo json_encode($this->themeLinkData['THEME_ID']); ?>, <?php echo json_encode($this->themeSectionData); ?>);
<?php } ?>
    }
});

</script>