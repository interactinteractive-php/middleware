<script type="text/javascript">
var $checkList_<?php echo $this->uniqId; ?> = $('#mv-checklist-render-parent-<?php echo $this->uniqId; ?>');
var $checkListMenu_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.mv-checklist-menu');
var viewMode_<?php echo $this->uniqId; ?> = '<?php echo $this->mode; ?>';
var strIndicatorId_<?php echo $this->uniqId; ?> = '<?php echo $this->strIndicatorId; ?>';
    
$(function() { 
    
    $checkListMenu_<?php echo $this->uniqId; ?>.height($(window).height() - $checkListMenu_<?php echo $this->uniqId; ?>.offset().top - 51);
    
    $checkListMenu_<?php echo $this->uniqId; ?>.on('click', 'a.nav-link:not(.disabled)', function() {
        var $this = $(this);
        
        $checkListMenu_<?php echo $this->uniqId; ?>.find('a.nav-link.active').removeClass('active');
        $this.addClass('active');
        
        var rowJson = $this.attr('data-json'), uniqId = $this.attr('data-uniqid'), indicatorId = $this.attr('data-indicatorid'), 
            isComment = $this.hasAttr('data-iscomment') ? $this.attr('data-iscomment') : 0;
        
        if (typeof rowJson === 'undefined') {
            if ($this.parent().hasClass('nav-group-sub-mv-opened')) {
                $this.parent().removeClass('nav-group-sub-mv-opened');
            } else {
                $this.parent().addClass('nav-group-sub-mv-opened');
            }
            return;
        }
        
        var viewProcess_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.mv-checklist-render:visible');
        var viewProcessComment_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.mv-checklist-render-comment:visible');
        
        if (typeof rowJson !== 'object') {
            var jsonObj = JSON.parse(html_entity_decode(rowJson, 'ENT_QUOTES'));
        } else {
            var jsonObj = rowJson;
        }
        
        jsonObj.mainIndicatorId = indicatorId;
        
        var metaDataId = jsonObj.metaDataId, metaTypeId = jsonObj.metaTypeId, 
            indicatorId = jsonObj.indicatorId, kpiTypeId = jsonObj.kpiTypeId;
        
        if (metaDataId != '' && metaDataId != null) {
            
            if (metaTypeId == '200101010000011') { //Process

                $.ajax({
                    type: 'post',
                    url: 'mdwebservice/callMethodByMeta',
                    data: {
                        metaDataId: metaDataId,
                        isDialog: false, 
                        isHeaderName: true, 
                        isBackBtnIgnore: 1, 
                        isIgnoreSetRowId: 1, 
                        kpiIndicatorMapConfig: jsonObj, 
                        callerType: 'dv', 
                        openParams: '{"callerType":"dv","afterSaveNoAction":true}'
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Core.blockUI({message: 'Loading...', boxed: true});
                    },
                    success: function(data) {
                        viewProcess_<?php echo $this->uniqId; ?>.empty().append(data.Html).promise().done(function () {
                            viewProcess_<?php echo $this->uniqId; ?>.find('.bp-btn-back, .bpTestCaseSaveButton').remove();
                            viewProcess_<?php echo $this->uniqId; ?>.find('.meta-toolbar').addClass('not-sticky');
                            Core.initBPAjax(viewProcess_<?php echo $this->uniqId; ?>);
                            Core.unblockUI();
                        });
                    },
                    error: function() { alert('Error'); Core.unblockUI(); }
                });

            } else if (metaTypeId == '200101010000016') { //Dataview
        
                $.ajax({
                    type: 'post',
                    url: 'mdobject/dataview/' + metaDataId + '/0/json',
                    data: {kpiIndicatorMapConfig: jsonObj},
                    dataType: 'json',
                    beforeSend: function() {
                        Core.blockUI({message: 'Loading...', boxed: true});
                    },
                    success: function (data) {
                        if (data.hasOwnProperty('Html')) {
                            viewProcess_<?php echo $this->uniqId; ?>.empty().append(data.Html).promise().done(function () {
                                viewProcess_<?php echo $this->uniqId; ?>.find('> .row > .col-md-12:eq(0)').remove();
                                Core.unblockUI();
                            });
                        } else {
                            viewProcess_<?php echo $this->uniqId; ?>.removeClass('pl-3 pr-3').addClass('pl5 pr5');
                            viewProcess_<?php echo $this->uniqId; ?>.empty().append(data.html).promise().done(function () {
                                Core.unblockUI();
                            });
                        }
                    },
                    error: function(){ alert('Error'); Core.unblockUI(); }
                });
                
            } else if (metaTypeId == '200101010000032') { //Chart
            
                $.ajax({
                    type: 'post',
                    url: 'mddashboard/diagramRenderByPost',
                    data: {metaDataId: metaDataId},
                    dataType: 'json',
                    beforeSend: function() {
                        Core.blockUI({message: 'Loading...', boxed: true});
                    },
                    success: function (data) {
                        viewProcess_<?php echo $this->uniqId; ?>.empty().append(data.Html).promise().done(function () {
                            Core.unblockUI();
                        });
                    },
                    error: function(){ alert('Error'); Core.unblockUI(); }
                });
            } else if (metaTypeId == '200101010000035') { //Statement
            
                $.ajax({
                    type: 'post',
                    url: 'mdstatement/index/' + metaDataId,
                    data: {kpiIndicatorMapConfig: jsonObj},
                    beforeSend: function() {
                        Core.blockUI({message: 'Loading...', boxed: true});
                    },
                    success: function (dataHtml) {
                        viewProcess_<?php echo $this->uniqId; ?>.empty().append(dataHtml).promise().done(function () {
                            Core.unblockUI();
                        });
                    },
                    error: function(){ alert('Error'); Core.unblockUI(); }
                });
            }
            
        } else {
            
            var strIndicatorId = jsonObj.strIndicatorId;
            var mapId = jsonObj.mapId;
            var isMartRender = Number(jsonObj.isMartRender);
            
            var $parent = $this.closest('.mv-checklist-render-parent');
            var $headerParams = $parent.find('input[data-path="headerParams"]');
            var headerRecordId = '';
            
            if ($headerParams.length) {
                var rowParse = JSON.parse(html_entity_decode($headerParams.val(), "ENT_QUOTES"));
                headerRecordId = $parent.find('input[data-path="headerRecordId"]').val();
            }
            
            if (kpiTypeId == '2008' || isMartRender > 0) { 
        
                var postData = {
                    mainIndicatorId: jsonObj.mainIndicatorId, 
                    structureIndicatorId: strIndicatorId, 
                    trgIndicatorId: indicatorId, 
                    trgIndicatorKpiTypeId: kpiTypeId, 
                    typeCode: '', 
                    recordId: '', 
                    srcMapId: mapId, 
                    selectedRow: ''
                };

                if ($headerParams.length) {
                    postData.selectedRow = rowParse;
                    postData.recordId = headerRecordId;
                }
                
                $.ajax({
                    type: 'post',
                    url: 'mdform/renderValueMapStructure',
                    data: postData,
                    dataType: 'json',
                    beforeSend: function() {
                        Core.blockUI({message: 'Loading...', boxed: true});
                    },
                    success: function(dataHtml) {
                        var html = [];

                        var renderHeader = '<div class="meta-toolbar is-bp-open-">'+
                            '<div class="main-process-text">\n\
                                <div>'+$this.text()+'</div>\n\
                                <div class="main-process-text-description">'+(dataHtml.indicatorInfo && dataHtml.indicatorInfo.DESCRIPTION ? dataHtml.indicatorInfo.DESCRIPTION : '')+'</div>\n\
                            </div>';
                        
                        if (viewMode_<?php echo $this->uniqId; ?> != 'view') {
                            renderHeader += '<div class="ml-auto">'+
                                    '<button type="button" class="btn btn-sm btn-circle btn-success bpMainSaveButton bp-btn-save" onclick="checkListSaveKpiIndicatorForm(this, \'\', \''+strIndicatorId+'\');"><i class="icon-checkmark-circle2"></i> '+plang.get('save_btn')+'</button>'+
                                '</div>';
                        }
                        
                        renderHeader += '</div>';
                
                        html.push('<form method="post" enctype="multipart/form-data">');
                            html.push(renderHeader);
                            html.push(dataHtml.html);
                        html.push('</form>');

                        viewProcess_<?php echo $this->uniqId; ?>.empty().append(html.join('')).promise().done(function() {
                            
                            if (viewMode_<?php echo $this->uniqId; ?> == 'view') {
                                
                                var $render = viewProcess_<?php echo $this->uniqId; ?>;
                                
                                $render.find('.bp-add-one-row').parent().remove();
                                $render.find('.bp-remove-row, button.red, button.bp-btn-save, button.green-meadow, button.bp-file-choose-btn, a[onclick*="bpFileChoosedRemove"], span.filename, a[onclick*="kpiIndicatorRelationRemoveRows"], div.input-group.quick-item-process').remove();
                                $render.find('input[type="text"], textarea').addClass('kpi-notfocus-readonly-input').attr('readonly', 'readonly');
                                $render.find("div[data-s-path]").addClass('select2-container-disabled kpi-notfocus-readonly-input');
                                $render.find('button[onclick*="dataViewSelectableGrid"], button[onclick*="chooseKpiIndicatorRowsFromBasket"]').prop('disabled', true);
                                $render.find('[data-action-name="exportexcel"]').removeClass('d-none');

                                var $radioElements = $render.find("input[type='radio']");
                                if ($radioElements.length) {
                                    $radioElements.attr({'data-isdisabled': 'true', style: 'cursor: not-allowed', 'tabindex': '-1'});
                                    $radioElements.closest('.radio').addClass('disabled');
                                }

                                var $checkElements = $render.find("input[type='checkbox']");
                                $checkElements.attr({'data-isdisabled': 'true', style: 'cursor: not-allowed', 'tabindex': '-1'});
                                $checkElements.closest('.checker').addClass('disabled');
                            }
                            
                            if (isComment == '1' && postData.hasOwnProperty('recordId')) {
                                
                                viewProcessComment_<?php echo $this->uniqId; ?>.empty().append('<div style="font-weight: bold;padding: 10px 0 7px 0;">Сэтгэгдэл</div>');
                                
                                $.ajax({
                                    type: 'post',
                                    url: 'mdwebservice/renderEditModeBpCommentTab',
                                    data: {
                                        uniqId: uniqId, 
                                        refStructureId: jsonObj.mainIndicatorId, 
                                        sourceId: postData.recordId, 
                                        listMetaDataId: indicatorId
                                    },
                                    success: function(data) {
                                        viewProcessComment_<?php echo $this->uniqId; ?>.append(data);
                                        Core.unblockUI();
                                    }
                                });
                            } else {
                                Core.unblockUI();
                            }
                        });
                    }
                });
                
            } else {
                
                var recordId = headerRecordId;
                var postData = {
                    mapSrcMapId: mapId, 
                    mapSelectedRow: $headerParams.val(), 
                    srcMapId: mapId, 
                    isIgnoreFilter: 1, 
                    isHideCheckBox: 0, 
                    isIgnoreTitle: 1
                };
                
                if (isComment == '1' && recordId != '') {
                    postData.isComment = 1;
                    postData.dynamicHeight = ($(window).height() / 2) - 40;
                }
                    
                $.ajax({
                    type: 'post',
                    url: 'mdform/indicatorList/' + indicatorId,
                    data: postData, 
                    beforeSend: function() {
                        Core.blockUI({message: 'Loading...', boxed: true});
                    },
                    success: function(dataHtml) {
                        $.ajax({
                            type: 'post',
                            url: 'mdform/getIndicatorDescription',
                            data: {
                                indicatorId: indicatorId
                            }, 
                            dataType: 'json',
                            beforeSend: function() {
                                Core.blockUI({message: 'Loading...', boxed: true});
                            },
                            success: function(dataJson) {
                                var html = [];

                                var renderHeader = '<div class="meta-toolbar is-bp-open-">'+
                                    '<div class="main-process-text">\n\
                                        <div>'+$this.text()+'</div>\n\
                                        <div style="" class="main-process-text-description">'+(dataJson && dataJson.DESCRIPTION ? dataJson.DESCRIPTION : '')+'</div>\n\
                                    </div>'+
                                '</div>';

                                html.push(renderHeader);
                                html.push(dataHtml);

                                viewProcess_<?php echo $this->uniqId; ?>.empty().append(html.join('')).promise().done(function() {
                                    if (postData.hasOwnProperty('isComment') && postData.isComment == '1') {

                                        viewProcessComment_<?php echo $this->uniqId; ?>.empty().append('<div style="font-weight: bold;padding: 10px 0 7px 0;">Сэтгэгдэл</div>');

                                        $.ajax({
                                            type: 'post',
                                            url: 'mdwebservice/renderEditModeBpCommentTab',
                                            data: {
                                                uniqId: uniqId, 
                                                refStructureId: jsonObj.mainIndicatorId, 
                                                sourceId: recordId, 
                                                listMetaDataId: indicatorId
                                            },
                                            success: function(data) {
                                                viewProcessComment_<?php echo $this->uniqId; ?>.append(data);
                                                Core.unblockUI();
                                            }
                                        });
                                    } else {
                                        Core.unblockUI();
                                    }
                                });
                            }
                        });      
                    }
                });
            }
        }
    });
    
    <?php
    if (isset($this->rowData) && $this->rowData) {
        echo Mdform::checkListRelationCriteriaScript($this->rowData, $this->relationList, $this->uniqId);
    }
    ?>
    
});

function saveKpiIndicatorHeaderForm(elem) {
    var $this = $(elem);
    var $form = $this.closest('form');
    var uniqId = $form.find('[data-bp-uniq-id]').attr('data-bp-uniq-id');

    if (window['kpiIndicatorBeforeSave_' + uniqId]($this) && bpFormValidate($form)) {
        
        var $parent = $this.closest('.mv-checklist-render-parent');
        var listIndicatorId = $parent.find('input[data-path="listIndicatorId"]').val();
        
        $form.ajaxSubmit({
            type: 'post',
            url: 'mdform/saveKpiDynamicDataByList',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
                var $inputLogId = $parent.find('input[data-path="endToEndLogHdrId"]');
                if ($inputLogId.length) {
                    formData.push({name: 'endToEndLog[listIndicatorId]', value: listIndicatorId});
                    formData.push({name: 'endToEndLog[hdrId]', value: $inputLogId.val()});
                }
            },
            beforeSend: function () {
                Core.blockUI({message: 'Loading...', boxed: true});
            },
            success: function (data) {

                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    sticker: false, 
                    addclass: pnotifyPosition
                });

                if (data.status == 'success') {
                    
                    $form.find('input[name="sf[ID]"]').val(data.rowId);
                    
                    var $headerParams = $parent.find('input[data-path="headerParams"]');
                    
                    if ($headerParams.length) {
                        var dataResult = data.result;
                        $parent.find('input[data-path="headerRecordId"]').val(data.rowId);
                        
                        if (!dataResult.hasOwnProperty('ID')) {
                            dataResult.ID = data.rowId;
                        }
                        
                        $headerParams.val(htmlentities(JSON.stringify(dataResult), 'ENT_QUOTES', 'UTF-8'));
                        
                        if (dataResult.hasOwnProperty('endToEndLogHdrId')) {
                            $parent.find('input[data-path="endToEndLogHdrId"]').val(dataResult.endToEndLogHdrId);
                        }
                    }
                    
                    $parent.find('.mv-checklist-menu').find('.nav-link.disabled').removeClass('disabled');
                    
                    window['kpiIndicatorAfterSave_' + uniqId]($this, data.status, data);
                    
                    if ($parent.find('.mv-checklist-criteria').length) {
                        runCheckListRelationCriteria($parent, data.rowId, strIndicatorId_<?php echo $this->uniqId; ?>);
                    }
                    
                    dataViewReload(listIndicatorId);
                } 

                Core.unblockUI();
            }
        });
    }
}
function checkListSaveKpiIndicatorForm(elem) {
    var $this = $(elem);
    var $form = $this.closest('form');
    var uniqId = $form.find('[data-bp-uniq-id]').attr('data-bp-uniq-id');  

    if (window['kpiIndicatorBeforeSave_' + uniqId]($this) && bpFormValidate($form)) {
        
        var $parent = $this.closest('.mv-checklist-render-parent');
        var $active = $parent.find('ul.nav-sidebar a.nav-link.active[data-json]');
                
        $form.ajaxSubmit({
            type: 'post',
            url: 'mdform/saveKpiDynamicDataByList',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
                var $headerParams = $parent.find('input[data-path="headerParams"]');
                var $inputLogId = $parent.find('input[data-path="endToEndLogHdrId"]');
                var headerRecordId = $parent.find('input[data-path="headerRecordId"]').val();
                
                formData.push({name: 'mapHidden[recordId]', value: headerRecordId});
                formData.push({name: 'mapHidden[params]', value: $active.attr('data-hidden-params')});
                formData.push({name: 'mapHidden[selectedRow]', value: $headerParams.val()});
                
                if ($inputLogId.length) {
                    var rowJson = JSON.parse(html_entity_decode($active.attr('data-json'), 'ENT_QUOTES'));
                    formData.push({name: 'endToEndLog[hdrId]', value: $inputLogId.val()});
                    formData.push({name: 'endToEndLog[stepIndicatorId]', value: rowJson.indicatorId});
                    formData.push({name: 'endToEndLog[recordId]', value: headerRecordId});
                }
            },
            beforeSend: function () {
                Core.blockUI({message: 'Loading...', boxed: true});
            },
            success: function (data) {

                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    sticker: false, 
                    addclass: pnotifyPosition
                });

                if (data.status == 'success') {
                    window['kpiIndicatorAfterSave_' + uniqId]($this, data.status, data);
                    
                    if (data.hasOwnProperty('rowId')) {
                        $form.find('input[name="sf[ID]"]').val(data.rowId);
                    }
                    
                    var dataResult = data.result;
                        
                    if (dataResult.hasOwnProperty('checkListStatus') && dataResult.checkListStatus != '') {
                        if (dataResult.checkListStatus == 'done') {
                            $active.find('i:eq(0)').removeClass('far fa-square').addClass('fas fa-check-square');
                        } else {
                            $active.find('i:eq(0)').removeClass('fas fa-check-square').addClass('far fa-square');
                        }
                    }
                } 

                Core.unblockUI();
            }
        });
    }
}
function saveMvCheckListCheck(elem) {
    var $this = $(elem), 
        $parent = $this.closest('.mv-checklist-render-parent'), 
        $inputLogId = $parent.find('input[data-path="endToEndLogHdrId"]');
    
    if ($inputLogId.length && $inputLogId.val() != '') {
        $.ajax({
            type: 'post',
            url: 'mdform/mvRunAllCheckQuery',
            dataType: 'json', 
            data: {
                hdrLogId: $inputLogId.val(), 
                headerParams: $parent.find('input[data-path="headerParams"]').val()
            },
            beforeSend: function () {
                Core.blockUI({message: 'Шалгаж байна...', boxed: true});
            },
            success: function(data) {
                if (data.status == 'success') {
                    
                    /*if (data.hasOwnProperty('messageDtl') && data.messageDtl && Object.keys(data.messageDtl).length) {
                        var messageDtl = data.messageDtl, message = [];
                        
                        message.push('<ul>');
                            for (var m in messageDtl) {
                                message.push('<li>' + messageDtl[m]['message'] + '</li>');
                            }
                        message.push('</ul>');
                        
                        bpCenterMessage('info', message.join('<br />'));
                    }*/
                    
                    if (data.hasOwnProperty('statusDtl') && data.statusDtl) {
                        var statusDtl = data.statusDtl;
                        for (var s in statusDtl) {
                            var $menu = $parent.find('ul.nav-sidebar a.nav-link[data-stepid="'+statusDtl[s]['indicatorId']+'"]');
                            if ($menu.length) {
                                if (statusDtl[s]['statusCode'] == 'done') {
                                    $menu.find('i:eq(0)').removeClass('far fa-square').addClass('fas fa-check-square');
                                } else {
                                    $menu.find('i:eq(0)').removeClass('fas fa-check-square').addClass('far fa-square');
                                }
                            }
                        }
                    }
                }
                Core.unblockUI();
            }, 
            error: function() {
                Core.unblockUI();
            }
        });
    }
}
function runCheckListRelationCriteria($parent, rowId, strIndicatorId) {
    $.ajax({
        type: 'post',
        url: 'mdform/runCheckListRelationCriteria',
        dataType: 'json', 
        data: {strIndicatorId: strIndicatorId, rowId: rowId},
        success: function(data) {
            if (data.status == 'success' && data.hasOwnProperty('criteria') && data.criteria) {
                var criteria = data.criteria;
                for (var c in criteria) {
                    var $menu = $parent.find('ul.nav-sidebar li.nav-item[data-stepid="'+criteria[c]['indicatorId']+'"]');
                    if ($menu.length) {
                        if (criteria[c]['criteria'] == 'show') {
                            $menu.removeClass('d-none');
                        } else {
                            $menu.addClass('d-none');
                        }
                    }
                }
                
                checkListParentMenuShowHide(null, $parent);
            }
        }
    });
}
function checkListParentMenuShowHide(uniqId, $parent) {
    setTimeout(function() {
        if (uniqId) {
            var $subMenu = window['$checkListMenu_' + uniqId].find('li.nav-item-submenu');
        } else {
            var $subMenu = $parent.find('.mv-checklist-menu').find('li.nav-item-submenu');
        }

        if ($subMenu.length) {
            $subMenu.each(function() {
                var $this = $(this);
                var $child = $this.find('ul.nav-group-sub');
                var $totalMenu = $child.find('li.nav-item');
                var $hideMenu = $child.find('li.nav-item.d-none');

                if ($totalMenu.length == $hideMenu.length) {
                    $this.addClass('d-none');
                } else {
                    $this.removeClass('d-none');
                }
            });
        }
    }, 1);
}
</script>