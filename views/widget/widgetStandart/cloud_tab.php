<?php 
    $renderAtom = new Mdwidget(); 
    $uid = getUID(); ?>
<div class="card-body cloud_tab<?php echo $uid ?> position-relative">
    <div class="position-absolute" style="top: 0; right: 0;">
        <a class="btn blue rounded-xl" href="javascript:;" onclick="mvNormalRelationRender(this, '2008', '190189444', {methodIndicatorId: '190189654', structureIndicatorId: '190189444'});" style="border-radius: 100px;"><i class="far fa-plus-circle" style="color:"></i> АПП ҮҮСГЭХ</a>        
    </div>     
    <ul class="nav nav-pills nav-pills<?php echo $uid ?>">
        <li class="nav-item active mr-1">
            <a href="#basic-1-tab<?php echo $uid ?>" class="nav-link active" data-layoutid="<?php echo '17091131749959' ?>" data-toggle="tab">Сүүлд хөгжүүлсэн</a>
        </li>
        <li class="nav-item">
            <a href="#basic-2-tab<?php echo $uid ?>" class="nav-link" data-layoutid="<?php echo '17091131945649' ?>" data-toggle="tab">Нээлттэй апп</a>
        </li>
    </ul>
    <div class="tab-content tab-content<?php echo $uid ?>">
        <div class="tab-pane fade show active" id="basic-1-tab<?php echo $uid ?>"></div>
        <div class="tab-pane fade" id="basic-2-tab<?php echo $uid ?>"></div>
    </div>
</div>
<style type="text/css">
    .cloud_tab<?php echo $uid ?> {

        .nav-pills<?php echo $uid ?> {
            .nav-link {
                padding: 10px 20px 10px 20px !important;
                border-radius: 10px !important;
                background: transparent;
                color: #333;
            }
            
            .nav-item {
                border-bottom: 3px solid transparent !important;
                padding-bottom: 10px;
            }
    
            .nav-item.active,
            .nav-item:hover {
                border-bottom: 3px solid #2F81E5 !important;
            }
            
            .nav-link.active {
                gap: 10px !important;
                background: #FFF !important;
                color: #2F81E5 !important;
            }
        }

        .tab-content<?php echo $uid ?> {
            min-height: 500px;
        }

    }
</style>

<script type="text/javascript">

    var cloud_tab<?php echo $uid ?> = $('.cloud_tab<?php echo $uid ?>'); 
    $('body').on('click', '.nav-pills<?php echo $uid ?> .nav-link', function () {
        var _this = $(this),
            _layoutId = _this.attr('data-layoutid'),
            _selectorContent = _this.attr('href'),
            _navPills = _this.closest('.nav-pills');

        _navPills.find('.nav-item').removeClass('active');
        _this.parent().addClass('active');

        if ($(_selectorContent).children().length > 0) {
        } else {

            $.ajax({
                type: 'post',
                url: 'mdlayout/v2/' + _layoutId,
                dataType: 'json',
                beforeSend: function() {
                    Core.blockUI({
                        target: $(_selectorContent),
                        boxed: true, 
                        message: 'Loading...'
                    });
                },
                success: function(data) {
                    
                    if (data.hasOwnProperty('status') && data.status == 'error') {
                        PNotify.removeAll();
                        new PNotify({
                            title: data.status,
                            text: data.message,
                            type: data.status,
                            addclass: pnotifyPosition,
                            sticker: false
                        });
                        return;
                    }
                    $(_selectorContent).empty().append(data.Html).promise().done(function () {
                        Core.unblockUI(_selectorContent);
                    });
                },
                error: function() {
                    alert('Error');
                }
            }).done(function() {
                Core.initAjax($(_selectorContent));
            });
        }

    });

    $(function() {
        cloud_tab<?php echo $uid ?>.find('.nav-link:eq(0)').trigger('click');
    });

</script>