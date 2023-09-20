<div class="kpi-component-wrap" id="kpi-component-<?php echo $this->componentUniqId; ?>">
    <?php include_once "recordmap2content.php"; ?>
</div>

<style>
  #kpi-component-<?php echo $this->componentUniqId; ?> .reldetail.active {
    background-color: #afe4fb !important;
  }
</style>
<script type="text/javascript">
$(function() {
    
    $('#kpi-component-<?php echo $this->componentUniqId; ?>').on('change', 'input[data-listen-path="1"]', function() {
        var $this = $(this);
        if ($this.val() != '') {
            var attributes = $this.attr('data-attributes');
            var rowData = $this.attr('data-row-data');
            var $parent = $this.closest('.quick-item-process');
            
            $parent.find('input[id*="_nameField"], input[id*="_valueField"]').val('').attr({'data-attributes': '', 'data-row-data': ''});
            
            if (typeof attributes !== 'object') {
                var attributesObj = JSON.parse(html_entity_decode(attributes, 'ENT_QUOTES'));
            } else {
                var attributesObj = attributes;
            }
            
            if (typeof rowData !== 'object') {
                var rowDataObj = JSON.parse(html_entity_decode(rowData, 'ENT_QUOTES'));
            } else {
                var rowDataObj = rowData;
            }
            var rows = [rowDataObj];
            
            kpiIndicatorRelationFillRows($this, $this.attr('data-lookupid'), rows, attributesObj.id, attributesObj.code, attributesObj.name, 'single');
        }
    });
    
    $('#kpi-component-<?php echo $this->componentUniqId; ?>').on('click', '.reldetail', function() {
        var $this = $(this);
        $('#kpi-component-<?php echo $this->componentUniqId; ?>').find('.reldetail').removeClass('active');
        $this.addClass('active');
    });
    
});  
function kpiIndicatorMainRelationFillRows(elem, indicatorId, rows, idField, codeField, nameField, chooseType) {
    
    var html = [], $tbody = elem.closest('.reldetail').find('table.mv-record-map-tbl > tbody:eq(0)');
    var delete_btn = plang.get('delete_btn');
    var view_btn = plang.get('view_btn');
    var isAddonForm = false;
    var indicatorRecordMaps = [];
    
    if (elem.hasAttr('data-config')) {
        var configObj = elem.attr('data-config');
        if (typeof configObj !== 'object') {
            configObj = JSON.parse(html_entity_decode(configObj, "ENT_QUOTES"));
        } 
        if (Number(configObj.isAddonForm) > 0 && configObj.metaInfoIndicatorId != '') {
            var metaInfoIndicatorId = configObj.metaInfoIndicatorId;
            isAddonForm = true;
        }
    }

    for (var i in rows) {
        
        var row = rows[i], rowId = row[idField], rowName = row[nameField];
        var $checkRow = $tbody.find('> tr[data-basketrowid="'+rowId+'"]');
        var childRowData = '';
        
        if ($checkRow.length == 0) {
            
            if (isAddonForm) {
                childRowData = JSON.stringify(row);
            }
            
            indicatorRecordMaps.push(rowId);

            // html.push('<tr data-basketrowid="'+rowId+'">');
            //     html.push('<td style="height: 25px; max-width: 0;" class="text-left text-truncate">');
                
            //         html.push('<input type="hidden" name="metaDmRecordMaps[indicatorId][]" value="'+indicatorId+'">');
            //         html.push('<input type="hidden" name="metaDmRecordMaps[recordId][]" value="'+rowId+'">');
            //         html.push('<input type="hidden" name="metaDmRecordMaps[mapId][]">');
            //         html.push('<input type="hidden" name="metaDmRecordMaps[rowState][]" value="added">');
            //         html.push('<input type="hidden" name="metaDmRecordMaps[childRecordId][]">');
            //         html.push('<textarea class="d-none" name="metaDmRecordMaps[childRowData][]">'+childRowData+'</textarea>');
                    
            //         html.push('<a href="javascript:;" onclick="bpCallKpiIndicatorForm(this, this, \''+indicatorId+'\', \''+rowId+'\', \'view\');" class="font-size-14" title="'+view_btn+'">');
            //             html.push('<i style="color:blue" class="far fa-file-search mr-1"></i>');
            //             html.push(rowName);
            //         html.push('</a>');
                    
            //     html.push('</td>');
                
            //     html.push('<td style="width: 60px" class="text-right">');
                
            //         if (isAddonForm) {
            //             html.push('<a href="javascript:;" onclick="kpiIndicatorRelationSubRows(this, \''+metaInfoIndicatorId+'\');" class="font-size-16 mr-3" title="Холбоос"><i style="color:#5c6bc0;" class="far fa-external-link-square"></i></a>');
            //         }
            //         html.push('<a href="javascript:;" onclick="kpiIndicatorRelation2RemoveRows(this);" class="font-size-14 d-none" title="'+delete_btn+'"><i style="color:red" class="far fa-trash"></i></a>');
                    
            //     html.push('</td>');
            // html.push('</tr>');
        }
    }    

    $.ajax({
      type: "post",
      url: "mdform/kpiSaveMetaDmRecordMap2",
      data: {
        mainIndicatorId: '<?php echo $this->indicatorId; ?>',
        indicatorId: indicatorId,
        indicatorRecordMaps: indicatorRecordMaps
      },
      dataType: "json",
      beforeSend: function () {
        Core.blockUI({
          message: "Loading...",
          boxed: true,
        });
      },
      success: function (data) {
        if (data.status == "success") {
            //$tbody.append(html.join(''));        
            $.ajax({
              type: "post",
              url: "mdform/renderRelationKpiReload",
              data: {
                indicatorId: '<?php echo $this->indicatorId; ?>'
              },
              dataType: "json",                 
              success: function (data) {
                $("#kpi-component-<?php echo $this->componentUniqId; ?>").empty().append(data.html);
                Core.unblockUI();
              },
            });             
            new PNotify({
            title: data.status,
            text: data.message,
            type: data.status,
            sticker: false,
          });            
        } else {
          Core.unblockUI();
        }
      },
    });    
}
function runKpiRelatonBp(elem, metaDataId) {
  var $this = $(elem);
  var workSpaceId = '', workSpaceParams = '';    
  var $dialogName = "dialog-kpi-relation-bp";
  if (!$("#" + $dialogName).length) {
    $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo(
      "body"
    );
  }
  var $dialog = $("#" + $dialogName);
  var recordId = $('#kpi-component-<?php echo $this->componentUniqId; ?>').find('.reldetail.active').data('rowid');

  if ($this.closest("div.ws-area").length > 0) {
      var wsArea = $this.closest("div.ws-area");
      var workSpaceIdAttr = wsArea.attr("id").split("-");
      workSpaceId = workSpaceIdAttr[2];
      workSpaceParams = $("div.ws-hidden-params", wsArea).find("input[type=hidden]").serialize();
  }  

  $.ajax({
    type: "post",
    url: "mdwebservice/callMethodByMeta",
    data: {
      metaDataId: metaDataId,
      dmMetaDataId: 16425125540641,
      isDialog: true,
      isSystemMeta: false,
      oneSelectedRow: {id: recordId},
      responseType: "",
      workSpaceId: workSpaceId,
      workSpaceParams: workSpaceParams,
    },
    dataType: "json",
    beforeSend: function () {
      Core.blockUI({
        message: "Loading...",
        boxed: true,
      });
    },
    success: function (data) {
      $dialog.empty().append(data.Html);

      var processForm = $("#wsForm", "#" + $dialogName);
      var processUniqId = processForm.parent().attr("data-bp-uniq-id");

      var buttons = [
        {
          text: data.run_btn,
          class: "btn green-meadow btn-sm bp-btn-save",
          click: function (e) {
            if (window["processBeforeSave_" + processUniqId]($(e.target))) {
              processForm.validate({
                ignore: "",
                highlight: function (element) {
                  $(element).addClass("error");
                  $(element).parent().addClass("error");
                  if (
                    processForm.find("div.tab-pane:hidden:has(.error)").length
                  ) {
                    processForm
                      .find("div.tab-pane:hidden:has(.error)")
                      .each(function (index, tab) {
                        var tabId = $(tab).attr("id");
                        processForm
                          .find('a[href="#' + tabId + '"]')
                          .tab("show");
                      });
                  }
                },
                unhighlight: function (element) {
                  $(element).removeClass("error");
                  $(element).parent().removeClass("error");
                },
                errorPlacement: function () { },
              });

              var isValidPattern = initBusinessProcessMaskEvent(processForm);

              if (processForm.valid() && isValidPattern.length === 0) {
                processForm.ajaxSubmit({
                  type: "post",
                  url: "mdwebservice/runProcess",
                  dataType: "json",
                  beforeSend: function () {
                    Core.blockUI({
                      boxed: true,
                      message: "Loading..."
                    });
                  },
                  success: function (responseData) {
                    if (responseData.status === "success") {
                      var responseParam = responseData.paramData;

                      PNotify.removeAll();
                      new PNotify({
                        title: "Амжилттай",
                        text: responseData.message,
                        type: "success",
                        sticker: false,
                        addclass: "pnotify-center",
                      });
                      $dialog.dialog("close");

                      $.ajax({
                        type: "post",
                        url: "mdform/renderRelationKpiReload",
                        data: {
                          indicatorId: '<?php echo $this->indicatorId; ?>'
                        },
                        dataType: "json",                 
                        success: function (data) {
                          $("#kpi-component-<?php echo $this->componentUniqId; ?>").empty().append(data.html);
                          Core.unblockUI();
                        },
                      });                                            
                    }                    
                  },
                  error: function () {
                    alert("Error");
                  },
                });
              }
            }
          },
        },
        {
          text: data.close_btn,
          class: "btn blue-madison btn-sm",
          click: function () {
            $dialog.dialog("close");
          },
        },
      ];

      var dialogWidth = data.dialogWidth,
        dialogHeight = data.dialogHeight;

      if (data.isDialogSize === "auto") {
        dialogWidth = 1200;
        dialogHeight = "auto";
      }

      $dialog
        .dialog({
          cache: false,
          resizable: true,
          bgiframe: true,
          autoOpen: false,
          title: data.Title,
          width: dialogWidth,
          height: dialogHeight,
          modal: true,
          closeOnEscape:
            typeof isCloseOnEscape == "undefined" ? true : isCloseOnEscape,
          close: function () {
            $dialog.empty().dialog("destroy").remove();
          },
          buttons: buttons,
        })
        .dialogExtend({
          closable: true,
          maximizable: true,
          minimizable: true,
          collapsable: true,
          dblclick: "maximize",
          minimizeLocation: "left",
          icons: {
            close: "ui-icon-circle-close",
            maximize: "ui-icon-extlink",
            minimize: "ui-icon-minus",
            collapse: "ui-icon-triangle-1-s",
            restore: "ui-icon-newwin",
          },
        });
      if (data.dialogSize === "fullscreen") {
        $dialog.dialogExtend("maximize");
      }
      $dialog.dialog("open");
    },
    error: function () {
      alert("Error");
    },
  }).done(function () {
    Core.initBPAjax($dialog);
    Core.unblockUI();
  });
}
function kpiIndicatorRelation2RemoveRows(elem, mapId) {
  var $dialogName = "dialog-relation-config-confirm";
    if (!$("#" + $dialogName).length) {
      $('<div id="' + $dialogName + '"></div>').appendTo("body");
    }
    var $dialog = $("#" + $dialogName);

    $dialog.empty().append(plang.get('msg_delete_confirm'));
    $dialog.dialog({
      cache: false,
      resizable: false,
      bgiframe: true,
      autoOpen: false,
      title: plang.get('msg_title_confirm'),
      width: 300,
      height: "auto",
      modal: true,
      close: function () {
        $dialog.empty().dialog("destroy").remove();
      },
      buttons: [
        {
          text: plang.get('yes_btn'),
          class: "btn green-meadow btn-sm",
          click: function () {
            $.ajax({
              type: "post",
              url: "mdform/deleteRelationpKpi",
              data: {
                mapId: mapId
              },
              dataType: "json",                 
              success: function (data) {
                $(elem).closest('tr').remove();
                new PNotify({
                    title: 'Success',
                    text: plang.get('msg_save_success'),
                    type: 'success',
                    sticker: false, 
                    addclass: 'pnotify-center'
                });                         
                Core.unblockUI();
              },
            }); 
            $dialog.dialog("close");
          },
        },
        {
          text: plang.get('no_btn'),
          class: "btn blue-madison btn-sm",
          click: function () {
            $dialog.dialog("close");
          },
        },
      ],
    });

    $dialog.dialog("open");  
}
</script>