var isKpiAddonScript2 = true;

function kpiDataMartRelationConfig2(elem, processMetaDataId, dataViewId, selectedRow, paramData) {
    var id = selectedRow.id;
    var $dialogName = 'dialog-dmart-relationconfig2';
    if (!$("#" + $dialogName).length) { $('<div id="' + $dialogName + '"></div>').appendTo('body'); }
    var $dialog = $('#' + $dialogName);

    $.ajax({
        type: 'post',
        url: 'mdform/kpiDataMartRelationConfig2',
        data: {id: id}, 
        dataType: 'json', 
        beforeSend: function(){
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function (data) {
            
            if (data.status == 'success') {
                
                $dialog.dialog({
                    cache: false,
                    resizable: false,
                    bgiframe: true,
                    autoOpen: false,
                    title: plang.get('dmart_relation_title'),
                    width: $(window).width(),
                    height: $(window).height(),
                    modal: false,
                    open: function() {

                        disableScrolling();

                        $dialog.empty().append(data.html).promise().done(function() {

                            var setHeight = $(window).height() - 190;
                            // var $editor = $('#datamart-editor');
                            
                            $('#app').css({'height': setHeight, 'max-height': setHeight});
                            // $editor.css({'height': setHeight - 2, 'max-height': setHeight - 2});
                            
                            Core.unblockUI();
                        });
                    }, 
                    close: function() {
                        $dialog.empty().dialog("destroy").remove();
                        enableScrolling();
                    }, 
                    buttons: [
                        {text: plang.get('save_btn'), class: 'btn btn-sm green bp-btn-save', click: function() {
                            Core.blockUI({message: 'Saving...', boxed: true});
                            PNotify.removeAll();

                            saveRappidjs(function(data){
                                new PNotify({
                                    title: data.status,
                                    text: data.message,
                                    type: data.status,
                                    addclass: pnotifyPosition,
                                    sticker: false
                                });
                                
                                if (data.status == 'success') {
                                    $dialog.dialog('close');
                                }                           
                            });
                
                            Core.unblockUI();                            
                        }},
                        {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function() {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                $dialog.dialog('open');
            
            } else {
                
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false
                });
                Core.unblockUI();
            }
        }
    });
}

function kpiDataMartAddObject2(elem) {
    dataViewSelectableGrid('nullmeta', '0', '16511984441409', 'multi', 'nullmeta', elem, 'kpiDataMartFillEditor2');
}

function kpiDataMartFillEditor2(metaDataCode, processMetaDataId, chooseType, elem, rows, paramRealPath, lookupMetaDataId, isMetaGroup) {
    selectModels(rows);
}    

function kpiDataMartRelationConfig3(elem, processMetaDataId, dataViewId, selectedRow, paramData) {
    var id = selectedRow.id;
    var $dialogName = 'dialog-dmart-relationconfig3';
    if (!$("#" + $dialogName).length) { $('<div id="' + $dialogName + '"></div>').appendTo('body'); }
    var $dialog = $('#' + $dialogName);

    $.ajax({
        type: 'post',
        url: 'mdform/kpiDataMartRelationConfig3',
        data: {id: id, mainId: $('input[name="param[id]"]').val(), idIndicatorId: selectedRow.trgindicatorid}, 
        dataType: 'json', 
        beforeSend: function(){
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function (data) {
            
            if (data.status == 'success') {
                
                $dialog.dialog({
                    cache: false,
                    resizable: false,
                    bgiframe: true,
                    autoOpen: false,
                    title: plang.get('dmart_relation_title'),
                    width: $(window).width(),
                    height: $(window).height(),
                    modal: false,
                    open: function() {

                        disableScrolling();

                        $dialog.empty().append(data.html).promise().done(function() {

                            var setHeight = $(window).height() - 150;
                            // var $editor = $('#datamart-editor');
                            
                            $('#canvas').css({'height': setHeight, 'max-height': setHeight});
                            // $editor.css({'height': setHeight - 2, 'max-height': setHeight - 2});
                            
                            Core.unblockUI();
                        });
                    }, 
                    close: function() {
                        if ($('#dialog-configs-datamapping').length) {
                            $('#dialog-configs-datamapping').empty().dialog("destroy").remove();
                        }
                        $dialog.empty().dialog("destroy").remove();
                        enableScrolling();
                    }, 
                    buttons: [
                        {text: plang.get('save_btn'), class: 'btn btn-sm green bp-btn-save', click: function() {
                            Core.blockUI({message: 'Saving...', boxed: true});
                            PNotify.removeAll();

                            saveRappidjs(function(data){
                                new PNotify({
                                    title: data.status,
                                    text: data.message,
                                    type: data.status,
                                    addclass: pnotifyPosition,
                                    sticker: false
                                });
                                
                                if (data.status == 'success') {
                                    $dialog.dialog('close');
                                }                           
                            });
                
                            Core.unblockUI();                            
                        }},
                        {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function() {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                $dialog.dialog('open');
            
            } else {
                
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false
                });
                Core.unblockUI();
            }
        }
    });
}

function kpiDataMartAddObject3(elem) {
    dataViewSelectableGrid('nullmeta', '0', '16511984441409', 'multi', 'nullmeta', elem, 'kpiDataMartFillEditor2');
}

function kpiDataMartFillEditor3(metaDataCode, processMetaDataId, chooseType, elem, rows, paramRealPath, lookupMetaDataId, isMetaGroup) {
    selectModels(rows);
}    