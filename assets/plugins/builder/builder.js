var builderPageManagment = true;
$( window ).on('load', function() { 
    /* $('#loader').fadeOut(); */
});

var _oldIcon = new Array();
var Builder = function() {
    
    var main = function (el) {
        var _btns = actionBtns();
        actionButtons = $(_btns);
        var _elementSelector = $(el).find('#pageList > ul');
        _elementSelector.find('.standart-section > .action-btns').remove();
        _elementSelector.find('.standart-section').append(actionButtons);

        $(el).closest('.layout-builder-v0').find('.ui-sortable-handle').each(function (i, r) { 
            if ($(r).children().length < 1) {
                $(r).remove();
            }
        })
    };

    var pageEmpty = function (el) {
        if ($(el).find('#pageList ul:visible > div').size() == 0 ) {
            $(el).find('#start').show();
            $(el).find('#frameWrapper').addClass('empty');
        } else {
            $(el).find('#start').hide();
            $(el).find('#frameWrapper').removeClass('empty');
        }
    
    };
    
    var allEmpty = function (el) {
    
        var allEmpty = false;
    
        if ($(el).find('#pageList li').size() == 0 ) {
            allEmpty = true;
        } else {
            allEmpty = false;
        }
    
        if( allEmpty ) {
            /* 
            $(el).find('.saveLayoutBtn').each(function(){
                $(this).addClass('disabled');
            });
            */
        } else {
            /* $(el).find('.saveLayoutBtn').each(function(){
                $(this).removeClass('disabled');
            }); */
        }
    
    };
    
    var makeDraggable = function (mainId, el) {
    
        $(mainId).find('#elements li:not(.chooseWidget)').each(function() {
            $(this).draggable({
                helper: function() {
                    return $('<div style="height: 100px; width: 300px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 28px; color: #16A085"><span class="fa fa-list"></span></div>');
                },
                revert: 'invalid',
                appendTo: 'body',
                connectToSortable: el,
                stop: function(event, ui){
                    $(this).addClass('selected-item');
                    pageEmpty(el);
                    allEmpty(el);
                },
                start: function() {
                    var mainSelector = $(this).closest('.build-page');
                    mainSelector.find('.selected-item').removeClass('selected-item');
                }
            });
        });
    
    };

    var remakeDraggable = function (mainId, el) {
    
        $(mainId).each(function() {
            $(this).draggable({
                helper: function() {
                    return $('<div style="height: 100px; width: 300px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 28px; color: #16A085"><span class="fa fa-list"></span></div>');
                },
                revert: 'invalid',
                appendTo: 'body',
                connectToSortable: el,
                stop: function(event, ui){
                    $(this).addClass('selected-item');
                    pageEmpty(el);
                    allEmpty(el);
                },
                start: function() {
                    var mainSelector = $(this).closest('.build-page');
                    mainSelector.find('.selected-item').removeClass('selected-item');
                }
            });
        });
    
    };
    
    var remakeSortable = function (el) {
        var mainSelector = el;
        el.sortable({
            revert: true,
            placeholder: "drop-hover",
            beforeStop: function(event, ui) {
                var itemSelector = mainSelector.find('.selected-item img'),
                    rowData = itemSelector.attr('data-rowdata');
                mainSelector.find('.selected-item').removeClass('selected-item');
                
                $(ui.item).removeAttr('style').addClass('row m-0').attr('data-src', itemSelector.attr('data-src'));
                $(ui.item).find('.fa').remove();

                var  frameUniqId = getUniqueId(1);
                var __html = '';
                if ($(ui.item).children().length == 0) {
                    __html = mainHtml(frameUniqId, rowData, itemSelector);
                } else {
                    __html = $(ui.item).children().clone();
                }
                
                ui.item.html(__html);
                var _btns = actionBtns();
                actionButtons = $(_btns);
    
                ui.item.find('.zoomer-cover').append(actionButtons);
            },
            stop: function(event, ui) {
                mainSelector.find('#pageList ul:visible li').each(function() {
                    $(this).find('.zoomer-cover > a').remove();
                });
            },
            over: function(event, ui) {
                mainSelector.find('#start').hide();
            }
        });
    };

    var makeSortable = function (el) {
        var mainSelector = el.closest('.build-page');
        el.sortable({
            revert: true,
            placeholder: "drop-hover",
            beforeStop: function(event, ui) {
                var itemSelector = mainSelector.find('.selected-item img'),
                    rowData = itemSelector.attr('data-rowdata');
                mainSelector.find('.selected-item').removeClass('selected-item');
                
                $(ui.item).removeAttr('style').addClass('row m-0').attr('data-src', itemSelector.attr('data-src'));
                $(ui.item).find('.fa').remove();

                var  frameUniqId = getUniqueId(1);
                var __html = '';
                if ($(ui.item).children().length == 0) {
                    __html = mainHtml(frameUniqId, rowData, itemSelector);
                } else {
                    __html = $(ui.item).children().clone();
                }
                
                ui.item.html(__html);
                var _btns = actionBtns();
                actionButtons = $(_btns);
    
                ui.item.find('.zoomer-cover').append(actionButtons);
            },
            stop: function(event, ui) {
                mainSelector.find('#pageList ul:visible li').each(function() {
                    $(this).find('.zoomer-cover > a').remove();
                });
            },
            over: function(event, ui) {
                mainSelector.find('#start').hide();
            }
        });
    };

    var removeSortable = function (el) {
        el.unbind('sort');
        el.sortable('destroy');
    };
    
    var removeDraggable = function (el) {
        $(el).draggable({ disabled: true });
    };

    var render = function (uniqId) {
        if (typeof window['graphJson' + uniqId] !== 'undefined') {
            var grapJson = window['graphJson' + uniqId];
            var layoutSelector = $('#layout-builder'+ uniqId),
                page = layoutSelector.find('ul#page' + uniqId);
            
            $.each(grapJson, function (key, rowData) {
                
                if (typeof rowData['html'] !== 'undefined' && rowData['html']) {
                    page.find('div[data-block-uniqid="'+ key +'"]').append(rowData['html']);
                }
                
                if (typeof rowData['widgetId'] !== 'undefined' && rowData['widgetId']) {
                    page.find('div[data-block-uniqid="'+ key +'"]').append('<iframe src="'+URL_APP + 'mdwidget/playWidgetStandart/' + rowData['widgetId'] +'/widget" scrolling="no" style="height: 100%; width: 100%;" frameborder="0"><iframe>');
                }

                switch (rowData['blockType']) {
                    case 'chart':
                        layoutSelector.find('div[data-block-uniqid="'+ key +'"]').append('<div id="itemDragin-'+key +'" class="row w-100 h-100"></div>').promise().done(function () {
                            if (typeof rowData['addintionalConfig'] !== 'undefined') {
                                var chartOption = JSON.parse(rowData['addintionalConfig']);
                                var chartDom = document.getElementById(chartOption['elemId']);
                                echarts.dispose(chartDom);
                                var myChart = echarts.init(chartDom);
                                chartOption && myChart.setOption(chartOption);
                                
                                setInterval(() => {
                                    myChart.resize();
                                }, 2000);
                            }
                        });
                        break;
                
                    default:
                        break;
                }
            });
        }
    };

    var selectWidget = function (uniqId, srcSection, trgSection) {
        var indicatorId = $('div[data-uniqid="'+ uniqId +'"]').find('input[name="indicatorId"]').val();
        var $trgSection = $(trgSection);
        if ($(srcSection).find('img[data-rowdata]').length < 1) {
            PNotify.removeAll();
            new PNotify({
                title: 'Санамж',
                text: 'Сонгосон <b>Section</b> шаардлагатай мэдээлэл олдсонгүй.',
                type: 'warning', 
                sticker: false
            });
            return false;
        }

        var rowData = JSON.parse($(srcSection).find('img[data-rowdata]').attr('data-rowdata'));
        var  frameUniqId = getUniqueId(1);
        $trgSection.attr('data-widgetid', rowData['id']);

        if ($trgSection.find('input[data-path="rowState"]').length > 0) {
            $trgSection.find('input[data-path="rowState"]').val('modified');
            $trgSection.find('textarea[data-path="trgindicatorid"]').val(rowData['id']);
            $trgSection.find('img').attr('src', $(srcSection).find('img[data-rowdata]').attr('src'));
        } else {
            var _html = '';
            _html += '<div class="indicatormap_data d-none">';
                _html += '<input type="hidden" data-path="rowState" name="rowState[]" value="added" />';
                _html += '<textarea data-path="srcindicatorid" name="param[pageindicatormap.srcindicatorid][][0]">'+ indicatorId +'</textarea>';
                _html += '<textarea data-path="trgindicatorid" name="param[pageindicatormap.trgindicatorid][][0]">'+ rowData['id'] +'</textarea>';
                _html += '<input type="hidden" data-path="semantictypeid" name="param[pageindicatormap.semantictypeid][][0]" value="69" />';
                if (window['indicatorMapKeys' + uniqId]) {
                    $.each(window['indicatorMapKeys' + uniqId], function (kIndex, kRow) {
                        if (kRow !== 'trgindicatorid' && kRow !== 'srcindicatorid'  && kRow !== 'semantictypeid') {
                            _html += '<textarea type="hidden" data-path="'+ kRow +'" name="param[pageindicatormap.'+ kRow +'][][0]"></textarea>';
                        }
                    })
                }
                /* _html += '<textarea data-path="ordernumber" name="param[pageindicatormap.ordernumber][][0]"></textarea>';
                _html += '<textarea data-path="id" name="param[pageindicatormap.id][][0]"></textarea>'; */
            _html += '</div>';
            _html += '<img src="'+$(srcSection).find('img[data-rowdata]').attr('src') + '" class="pull-left w-100"/>';
            $trgSection.addClass('iframe-section').empty().append(_html);
        }

        var _layoutBuilder = $trgSection.closest('.layout-builder-v0');
        _layoutBuilder.find('input[name="widgetId['+ $trgSection.attr('data-block-uniqid') +']"]').val(rowData['id']);
    };

    var mainHtml = function (uniqId, rowData, itemSelector) {
        var __html = '';
        switch (itemSelector.attr('data-src')) {
            case 'widget':
                html += '<div class="col-md-12 zoomer-cover iframe-section" data-block-uniqid="'+ uniqId +'">';
                    _html += '<div class="indicatormap_data d-none">';
                        _html += '<input type="hidden" data-path="rowState" name="rowState[]" value="modified" />';
                        _html += '<textarea data-path="srcindicatorid" name="param[pageindicatormap.srcindicatorid][][0]">'+ indicatorId +'</textarea>';
                        _html += '<textarea data-path="trgindicatorid" name="param[pageindicatormap.trgindicatorid][][0]">'+ rowData['id'] +'</textarea>';
                        if (window['indicatorMapKeys' + uniqId]) {
                            $.each(window['indicatorMapKeys' + uniqId], function (kIndex, kRow) {
                                if (kRow !== 'trgindicatorid' && kRow !== 'srcindicatorid'  && kRow !== 'semantictypeid') {
                                    _html += '<textarea type="hidden" data-path="'+ kRow +'" name="param[pageindicatormap.'+ kRow +'][][0]"></textarea>';
                                }
                            })
                        }
                    _html += '</div>';
                    html +=  '<img src="'+itemSelector.attr('src') + '" class="w-auto pull-left h-100 mx-auto" style="max-height: 330px;"/>';
                html+= '</div>';
                break;
            case 'col2':
                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                __html += '<div class="col standart-section zoomer-cover" data-position="2" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                break;
            case 'col3':
                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                __html += '<div class="col standart-section zoomer-cover" data-position="2" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                __html += '<div class="col standart-section zoomer-cover" data-position="3" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                break;
            case 'col4':
                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                __html += '<div class="col standart-section zoomer-cover" data-position="2" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                __html += '<div class="col standart-section zoomer-cover" data-position="3" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                __html += '<div class="col standart-section zoomer-cover" data-position="4" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                break;
            default:
                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'"></div>';
                break;
        }
        return __html;
    }

    var actionBtns = function () {
        var _btns = '<div class="action-btns position-absolute right-12 top-0">'
                /* _btns += '<button type="button" class="btn btn-secondary resetBlock" title="'+ plang.get('refresh_btn') +'" data-type="block"><i class="fa fa-refresh"></i></button>' */
                _btns += '<button type="button" class="btn btn-danger deleteBlock" title="'+ plang.get('delete_btn') +'" data-type="block"><span class="fa fa-trash"></span></button>'
                _btns += '<button type="button" class="btn btn-warning editBlock" title="'+ plang.get('edit_btn') +'" data-type="block"><i class="fa fa-edit"></i></button>'
                _btns += '<button type="button" class="btn btn-secondary copyBlock" title="'+ plang.get('copy_btn') +'" data-type="block"><i class="far fa-copy"></i></button>'
                /* _btns += '<button type="button" class="btn btn-secondary addRowBlock" title="'+ plang.get('addrow') +'" data-type="block"><i class="far fa-list"></i></button>' */
            _btns += '</div>'

        return _btns;
    }

    var callExtensions = function (uniqId) {

        Core.initFancybox($('#layout-builder'+ uniqId), uniqId);
        
        $.contextMenu({
            selector: '#layout-builder'+ uniqId +' .chooseWidget',
            callback: function(key, opt) {
                if (key === 'chooseWidget') {
                    var _selectedSection = $('#layout-builder'+ uniqId).find('.standart-section.section-edit');
                    if (_selectedSection.length < 1) {
                        PNotify.removeAll();
                        new PNotify({
                            title: 'Санамж',
                            text: 'Сонгосон <b>Section</b> олдсонгүй.',
                            type: 'warning', 
                            sticker: false
                        });
                        return false;
                    } else {
                        selectWidget(uniqId, opt.$trigger, _selectedSection);
                    }
                } 
            },
            items: {
                "chooseWidget": {name: plang.get('Сонгох'), icon: 'check'}
            }
        });
    };

    var buildHtmlByIndicators = function (kpiTypeIndicators, html, uniqId) {
        var defaultVal = '';
        $.each(kpiTypeIndicators, function (i, r) {
            defaultVal = (typeof r.DEFAULT_VALUE && r.DEFAULT_VALUE) ? r.DEFAULT_VALUE : '';
            if (defaultVal && typeof eval(defaultVal) == 'object')
                defaultVal = JSON.stringify(eval(defaultVal));

            if (typeof r.children !== 'undefined' && r.children) {
                html += buildHtmlByIndicators(r.children, '', uniqId);
            } else {
                switch (r.CODE) {
                    case 'sectionCol':
                        html += '<div class="form-group row">';
                            html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">'+ plang.get(r.CODE) +' :</label>';
                            html += '<div class="col-md-8">';
                                html += '<select class="form-control form-control-sm mt-0" name="sectionCol['+ blockUniqId +']" data-path="'+ r.CODE +'">';
                                    html += '<option value="">- Сонгох -</option>';
                                    for (var i = 1; i <= 12; i++) {
                                        html += '<option value="'+ i +'">col-'+ i +'</option>';
                                    }
                                html += '</select>';
                            html += '</div>';
                        html += '</div>';
                        break;
                    case 'series.data':
                    case 'json':
                        html += '<div class="form-group row">';
                            html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1" for="sectionCol">'+ plang.get(r.CODE) +' :</label>';
                            html += '<div class="col-md-8">';
                                html += '<div class="input-group">';
                                    html += '<textarea tabindex="" class="form-control form-control-sm mt-0 expression_editorInit" readonly="readonly" data-path="'+ r.CODE +'" data-isclear="0" style="height: 28px; overflow: hidden; resize: none;" draggable="false" rows="1" placeholder="">'+ defaultVal +'</textarea>';
                                    html += '<span class="input-group-append"><button class="btn grey-cascade" type="button" onclick="bpExpressionEditor(this);"><i class="far fa-code"></i></button></span> ';
                                html += '</div>';
                            html += '</div>';
                        html += '</div>';
                        break;
                    case 'html':
                        html += '<div class="form-group row">';
                            html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1" for="sectionCol">'+ plang.get(r.CODE) +' :</label>';
                            html += '<div class="col-md-8">';
                                html += '<div class="input-group">';
                                    html += '<textarea tabindex="" class="form-control form-control-sm mt-0 " readonly="readonly" data-path="'+ r.CODE +' data-isclear="0" style="height: 28px; overflow: hidden; resize: none;" draggable="false" rows="1" placeholder=""></textarea>';
                                    html += '<span class="input-group-append"><button class="btn grey-cascade" type="button" onclick="bpFieldTextEditorClickToEdit(this, \'1\', \'setHtml_'+ uniqId +'\');"><i class="far fa-code"></i></button></span> ';
                                html += '</div>';
                            html += '</div>';
                        html += '</div>';
                        break;
                    default:
                        html += '<div class="form-group row">';
                            html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">'+ plang.get(r.CODE) +' :</label>';
                            html += '<div class="col-md-8">';
                                html += '<div class="input-group">';
                                    html += '<input type="text" class="form-control form-control-sm mt-0" data-path="'+ r.CODE +'" />';
                                    html += '<span class="input-group-append">';
                                        html += '<button class="btn grey-cascade" type="button" onclick="clearKpiTypeAttr'+ uniqId +'(this);"><i class="far fa-trash"></i></button>';
                                    html += '</span> ';
                                html += '</div>';
                            html += '</div>';
                        html += '</div>';
                        break;
                }
            }
        });
        return html;
    };

    var renderIndicatorHtml = function (blockUniqId, kpiIndicatorData, uniqId, configEditing) {
        var __html = '<div class="item-config" data-item-cf="' + blockUniqId +'" style="display: none">';
            __html += '<input type="hidden" name="blockUniqId[]" value="'+ blockUniqId +'">';
            if (kpiIndicatorData) {
                __html += '<div class="card-group-control card-group-control-left">';
                    $.each(kpiIndicatorData, function (i, r) {
                        __html += '<div class="card rounded-top-0 p-0 mb-0 border-top-0 border-bottom-1 conf_'+ r.ID +'">';
                            __html += '<div class="card-header h-auto m-0 px-1 py-2 bg-root-color">';
                                __html += '<h6 class="card-title pull-left w-100">';
                                    __html += '<a class="text-white w-100 pull-left collapsed" data-toggle="collapse" href="#collapsible-'+ r.ID +'-group" aria-expanded="true">'+ plang.get(r.CODE + '_collapse') +'</a>';
                                __html += '</h6>';
                            __html += '</div>';
                            __html += '<div id="collapsible-'+ r.ID +'-group" class="collapse">';
                                __html += '<div class="card-body">';
                                    __html += '<div class="col-md-12">';
                                        if (typeof r.children !== 'undefined' && r.children) {
                                            __html += buildHtmlByIndicators(r.children, '', uniqId);
                                        } else {
                                            switch (r.CODE) {
                                                case 'sectionCol':
                                                    __html += '<div class="form-group row">';
                                                        __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">'+ plang.get(r.CODE) +' :</label>';
                                                        __html += '<div class="col-md-8">';
                                                            __html += '<select class="form-control form-control-sm" name="sectionCol['+ blockUniqId +']" data-path="'+ r.CODE +'">';
                                                                __html += '<option value="">- Сонгох -</option>';
                                                                for (var i = 1; i <= 12; i++) {
                                                                    __html += '<option value="'+ i +'">col-'+ i +'</option>';
                                                                }
                                                            __html += '</select>';
                                                        __html += '</div>';
                                                    __html += '</div>';
                                                    break;
                                                case 'html':
                                                    __html += '<div class="form-group row">';
                                                        __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">'+ plang.get(r.CODE) +' :</label>';
                                                        __html += '<div class="col-md-8">';
                                                            __html += '<div class="input-group">';
                                                                __html += '<textarea tabindex="" class="form-control form-control-sm " data-path="html" readonly="readonly" data-isclear="0" style="height: 28px; overflow: hidden; resize: none;" draggable="false" rows="1" placeholder=""></textarea>';
                                                                __html += '<span class="input-group-append"><button class="btn grey-cascade" type="button" onclick="bpFieldTextEditorClickToEdit(this, \'1\', \'setHtml_'+ uniqId +'\');"><i class="far fa-code"></i></button></span> ';
                                                            __html += '</div>';
                                                        __html += '</div>';
                                                    __html += '</div>';
                                                    break;
                                                default:
                                                    __html += '<div class="form-group row">';
                                                        __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">'+ plang.get(r.CODE) +' :</label>';
                                                        __html += '<div class="col-md-8">';
                                                            __html += '<div class="input-group">';
                                                                __html += '<input type="text" class="form-control form-control-sm" data-path="'+ r.CODE +'" />';
                                                                __html += '<span class="input-group-append">';
                                                                    __html += '<button class="btn grey-cascade" type="button" onclick="clearKpiTypeAttr'+ uniqId +'(this);"><i class="far fa-trash"></i></button>';
                                                                __html += '</span> ';
                                                            __html += '</div>';
                                                        __html += '</div>';
                                                    __html += '</div>';
                                                    break;
                                            }
                                        }
                                    __html += '</div>';
                                __html += '</div>';
                            __html += '</div>';
                        __html += '</div>';
                    });
                __html += '</div>';
            }
            __html += '<div class="card-group-control card-group-control-left">';
                __html += '<div class="card rounded-top-0 p-0 mb-0 border-top-0 border-bottom-1 conf_'+ blockUniqId +'">';
                    __html += '<div class="card-header h-auto m-0 px-1 py-2 bg-root-color">';
                        __html += '<h6 class="card-title pull-left w-100">';
                            __html += '<a class="text-white w-100 pull-left" data-toggle="collapse" href="#collapsible-'+ blockUniqId +'-group" aria-expanded="true">'+ plang.get('Appearance') +'</a>';
                        __html += '</h6>';
                    __html += '</div>';
                    __html += '<div id="collapsible-'+ blockUniqId +'-group" class="collapse show">';
                        __html += '<div class="card-body">';
                            $.each(configEditing, function (ckey, cRow) {
                                switch (cRow) {
                                    case 'section':
                                        __html += '<div class="col-md-12">';
                                            __html += '<div class="form-group row">';
                                                __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">Col [1~12] :</label>';
                                                __html += '<div class="col-md-8">';
                                                    __html += '<div class="input-group">';
                                                        __html += '<select class="form-control form-control-sm" name="sectionCol" data-path="sectionCol" id="sectionCol">';
                                                                __html += '<option value="">- Сонгох -</option>';
                                                                for (var i = 1; i <= 12; i++) {
                                                                    __html += '<option value="'+ i +'">col-'+ i +'</option>';
                                                                }
                                                            __html += '</select>';
                                                    __html += '</div>';
                                                __html += '</div>';
                                            __html += '</div>';
                                        __html += '</div>';
                                        break;
                                    case 'html':
                                        __html += '<div class="col-md-12">';
                                            __html += '<div class="form-group row">';
                                                __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">Source :</label>';
                                                __html += '<div class="col-md-8">';
                                                    __html += '<div class="input-group">';
                                                        __html += '<textarea tabindex="" class="form-control form-control-sm " data-path="html" readonly="readonly" data-isclear="0" style="height: 28px; overflow: hidden; resize: none;" draggable="false" rows="1" placeholder=""></textarea>';
                                                        __html += '<span class="input-group-append"><button class="btn grey-cascade" type="button" onclick="bpFieldTextEditorClickToEdit(this, \'1\', \'setHtml_'+ uniqId +'\');"><i class="far fa-code"></i></button></span> ';
                                                    __html += '</div>';
                                                __html += '</div>';
                                            __html += '</div>';
                                        __html += '</div>';
                                        break;
                                
                                    default:
                                        __html += '<div class="col-md-12">';
                                            __html += '<div class="form-group row">';
                                                __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1">'+ cRow +' :</label>';
                                                __html += '<div class="col-md-8">';
                                                    __html += '<div class="input-group">';
                                                        __html += '<input type="text" class="form-control form-control-sm" name="'+ cRow +'" data-path="'+ cRow +'" id="'+ cRow +'" value="" >'
                                                    __html += '</div>';
                                                __html += '</div>';
                                            __html += '</div>';
                                        __html += '</div>';
                                        break;
                                }
                            });
                            __html += '<div class="block-config-'+ blockUniqId +'"></div>';
                            __html += '<div class="form-group row -item-addinconfig">';
                                __html += '<label class="col-form-label col-md-4 text-right pr-0 pt-1" for="widgetId">Нэмэлт тохиргоо :</label>';
                                __html += '<div class="col-md-8">';
                                    __html += '<div class="input-group">';
                                        __html += '<textarea name="secNemgoo['+ blockUniqId +']" tabindex="" class="form-control form-control-sm expression_editorInit" data-path="secNemgoo" data-field-name="secNemgoo" data-isclear="0" style="height: 28px; overflow: hidden; resize: none;" draggable="false" rows="1" placeholder="Нэмэлт тохиргоо"></textarea>';
                                        __html += '<span class="input-group-append"><button class="btn grey-cascade" type="button" onclick="bpExpressionEditor(this);"><i class="far fa-code"></i></button></span> ';
                                    __html += '</div>';
                                __html += '</div>';
                            __html += '</div>';
                        __html += '</div>';
                    __html += '</div>';
                __html += '</div>';
            __html += '</div>';
        __html += '</div>';
        return __html;
    }

    return {
        init: function(uniqId) {
            main('#layout-builder'+ uniqId);
            pageEmpty('#layout-builder'+ uniqId);
            allEmpty('#layout-builder'+ uniqId);
            makeDraggable('#layout-builder'+ uniqId, '#layout-builder'+ uniqId +' #page' + uniqId);
            makeSortable($('#layout-builder'+ uniqId +' #pageList ul#page' + uniqId));
            render(uniqId);
            callExtensions(uniqId)
        },
        initPageEmpty: function (uniqId) {
            pageEmpty('#layout-builder'+ uniqId);
        }, 
        initAllEmpty: function (uniqId) {
            allEmpty('#layout-builder'+ uniqId);
        }, 
        initMakeDraggable: function (uniqId) {
            makeDraggable(uniqId);
        }, 
        initMakeSortable: function (uniqId) {
            makeSortable(uniqId);
        }, 
        initRemakeDraggable: function (uniqId) {
            remakeDraggable(uniqId);
        }, 
        initRemakeSortable: function (uniqId) {
            remakeSortable(uniqId);
        }, 
        initRemoveDraggable: function (uniqId) {
            removeDraggable(uniqId);
        }, 
        initRemoveSortable: function (uniqId) {
            removeSortable(uniqId);
        }, 
        initRender: function (uniqId) {
            render(uniqId);
        },
        selectWidget: function (uniqId, srcSection, trgSection) {
            selectWidget(uniqId, srcSection, trgSection);
        },
        renderIndicatorHtml: function (blockUniqId, kpiIndicatorData, uniqId, configEditing) {
            return renderIndicatorHtml(blockUniqId, kpiIndicatorData, uniqId, configEditing);
        }
    };
} ();