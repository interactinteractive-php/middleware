var builderPageManagment = true;
$( window ).on('load', function() { 
    /* $('#loader').fadeOut(); */
});

var _oldIcon = new Array();
var Builder = function() {
    
    var main = function (el) {
        actionButtons = $('<div class="action-btns position-absolute right-12 top-0">'
                + '<button type="button" class="btn btn-warning editBlock" data-type="block"><i class="fa fa-edit"></i> '+ plang.get('edit_btn') +'</button><div class="clear-both"></div>'
                + '<button type="button" class="btn btn-danger deleteBlock" data-type="block"><span class="fa fa-trash"></span> '+ plang.get('delete_btn') +'</button><div class="clear-both"></div>'
                + '<button type="button" class="btn btn-secondary copyBlock" data-type="block"><i class="far fa-copy"></i> '+ plang.get('copy_btn') +'</button><div class="clear-both"></div>'
                + '<button type="button" class="btn btn-secondary addRowBlock" data-type="block"><i class="far fa-list"></i> '+ plang.get('addrow') +'</button><div class="clear-both"></div>'
            + '</div>');

        $(el).find('#pageList > ul').find('.standart-section > .action-btns').remove();
        $(el).find('#pageList > ul').find('.standart-section').append(actionButtons);
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
    
        $(mainId).find('#elements li').each(function() {
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
    
    var makeSortable = function (el) {
        var mainSelector = el.closest('.build-page');
        el.sortable({
            revert: true,
            placeholder: "drop-hover",
            beforeStop: function(event, ui) {
                var itemSelector = mainSelector.find('.selected-item > img'),
                    rowData = itemSelector.attr('data-rowdata');
                mainSelector.find('.selected-item').removeClass('selected-item');
                
                $(ui.item).removeAttr('style').addClass('row m-0');
                $(ui.item).find('.fa').remove();
    
                if (itemSelector.attr('data-type') === 'widget') {
                    var  rowData = JSON.parse(rowData);
                    ui.item.html('<div class="col-md-12 zoomer-cover iframe-section" style="min-height: 350px;"><iframe src="'+URL_APP + 'mdwidget/playWidgetStandart/' + rowData['CODE'] +'/widget" scrolling="no" style="height: 100%; width: 100%;" frameborder="0"><iframe></div>');
                    ui.item.find('iframe').uniqueId();
                    ui.item.find('iframe').on('load', function() {
                        var _this = $(this),
                            _section = _this.closest('.standart-section'),
                            _layoutBuilder = _this.closest('.layout-builder-v0');
    
                        _layoutBuilder.find('input[name="widgetId['+ _section.attr('data-block-uniqid') +']"]').val(rowData['ID']);
                    });
                    ui.item.find('.zoomer-cover a').remove();
    
                    actionButtons = $('<div class="action-btns position-absolute right-12 top-0">'
                        + '<button type="button" class="btn btn-danger deleteBlock" data-type="widget"><span class="fa fa-trash"></span> '+ plang.get('delete_btn') +'</button><div class="clear-both"></div>'
                        + '<button type="button" class="btn btn-secondary resetBlock" data-type="widget"><i class="fa fa-refresh"></i> '+ plang.get('refresh_btn') +'</button><div class="clear-both"></div>'
                    + '</div>');
    
                    ui.item.find('.zoomer-cover').append( actionButtons );
                } else {
                    var __html = '';
                    switch (itemSelector.attr('data-src')) {
                        case 'col2':
                            __html += '<div class="row w-100 m-0">';
                                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                                __html += '<div class="col standart-section zoomer-cover" data-position="2" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                            __html += '</div>';
                            break;
                        case 'col3':
                            __html += '<div class="row w-100 m-0">';
                                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                                __html += '<div class="col standart-section zoomer-cover" data-position="2" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                                __html += '<div class="col standart-section zoomer-cover" data-position="3" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                            __html += '</div>';
                            break;
                        case 'col4':
                            __html += '<div class="row w-100 m-0">';
                                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                                __html += '<div class="col standart-section zoomer-cover" data-position="2" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                                __html += '<div class="col standart-section zoomer-cover" data-position="3" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                                __html += '<div class="col standart-section zoomer-cover" data-position="4" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                            __html += '</div>';
                            
                            break;
                    
                        default:
                            __html += '<div class="row w-100 m-0">';
                                __html += '<div class="col standart-section zoomer-cover" data-position="1" data-block-uniqid="'+ getUniqueId(1) +'">';
                                __html += '</div>';
                            __html += '</div>';
                            break;
                    }
    
                    /* ui.item.html(__html); */
                    $(ui.item).append(__html).promise().done(function () {
                    });
                    //action button
                    
                    actionButtons = $('<div class="action-btns position-absolute right-12 top-0">'
                                        + '<button type="button" class="btn btn-warning editBlock" data-type="block"><i class="fa fa-edit"></i> '+ plang.get('edit_btn') +'</button><div class="clear-both"></div>'
                                        + '<button type="button" class="btn btn-danger deleteBlock" data-type="block"><span class="fa fa-trash"></span> '+ plang.get('delete_btn') +'</button><div class="clear-both"></div>'
                                        // + '<button type="button" class="btn btn-secondary resetBlock" data-type="block"><i class="fa fa-refresh"></i> '+ plang.get('refresh_btn') +'</button><div class="clear-both"></div>'
                                        + '<button type="button" class="btn btn-secondary copyBlock" data-type="block"><i class="far fa-copy"></i> '+ plang.get('copy_btn') +'</button><div class="clear-both"></div>'
                                        + '<button type="button" class="btn btn-secondary addRowBlock" data-type="block"><i class="far fa-list"></i> '+ plang.get('addrow') +'</button><div class="clear-both"></div>'
                                    + '</div>');
        
                    ui.item.find('.zoomer-cover').append( actionButtons );
                }
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

    return {
        init: function(uniqId) {
            main('#layout-builder'+ uniqId);
            pageEmpty('#layout-builder'+ uniqId);
            allEmpty('#layout-builder'+ uniqId);
            makeDraggable('#layout-builder'+ uniqId, '#layout-builder'+ uniqId +' #page' + uniqId);
            makeSortable($('#layout-builder'+ uniqId +' #pageList ul#page' + uniqId));
            render(uniqId);
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
        initRemoveDraggable: function (uniqId) {
            removeDraggable(uniqId);
        }, 
        initRemoveSortable: function (uniqId) {
            removeSortable(uniqId);
        }, 
        initRender: function (uniqId) {
            render(uniqId);
        }
    };
} ();