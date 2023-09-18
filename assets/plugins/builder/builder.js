var builderPageManagment = true;
$( window ).on('load', function() { 
    /* $('#loader').fadeOut(); */
});

function pageEmpty() {

    if ($('#pageList ul:visible > div').size() == 0 ) {
        $('#start').show();
        $('#frameWrapper').addClass('empty');
    } else {
        $('#start').hide();
        $('#frameWrapper').removeClass('empty');
    }

}

function allEmpty() {

    var allEmpty = false;

    if ($('#pageList li').size() == 0 ) {
        allEmpty = true;
    } else {
        allEmpty = false;
    }

    if( allEmpty ) {

        $('a.actionButtons').each(function(){
            $(this).addClass('disabled');
        });

        $('header .modes input').each(function(){
            $(this).prop('disabled', true).parent().addClass('disabled');
        });

    } else {

        $('header .modes input').each(function(){
            $(this).prop('disabled', false).parent().removeClass('disabled');
        });

        $('a.actionButtons').each(function(){
            $(this).removeClass('disabled');
        });

    }

}

function makeDraggable(theID) {

    $('#elements li').each(function() {
        $(this).draggable({
            helper: function() {
                return $('<div style="height: 100px; width: 300px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 28px; color: #16A085"><span class="fa fa-list"></span></div>');
            },
            revert: 'invalid',
            appendTo: 'body',
            connectToSortable: theID,
            stop: function(event, ui){
                $(this).addClass('selected-item');
                pageEmpty();
                allEmpty();
            },
            start: function() {
                var mainSelector = $(this).closest('.build-page');
                mainSelector.find('.selected-item').removeClass('selected-item');
            }
        });
    });

}

function makeSortable(el) {
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
            $('#pageList ul:visible li').each(function() {
                $(this).find('.zoomer-cover > a').remove();
            });
        },
        over: function(event, ui) {
            $('#start').hide();
        }
    });
}

var _oldIcon = new Array();

$(function() {

    makeDraggable( '#page1' )
    makeSortable($('#pageList ul#page1'));

    $('#menu #main').on('click', 'a:not(.actionButtons)', function(e) {
        e.preventDefault();
    });

})
