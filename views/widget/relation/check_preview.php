<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.'); ?> 
<?php 

$headerPathArray = $rowsArray = $position2GroupArray = array();
if (issetParamArray($this->relationComponentsConfigData['header'])) {
    $headerPathArray = $this->relationComponentsConfigData['header'];
    $headerArray = Arr::groupByArrayOnlyRow($headerPathArray, 'trg_indicator_path', false);
    
}

if (issetParamArray($this->relationComponentsConfigData['rows'])) {
    $rowsPathArray = $this->relationComponentsConfigData['rows'];
    $rowsArray = Arr::groupByArrayOnlyRow($rowsPathArray, 'trg_indicator_path', false);
}

?>
<div class="wg-form-paper <?php echo $this->uniqId ?> " id="mv-checklist-render<?php echo $this->uniqId ?>">
    <div class="wg-form">
        <div class="card card-side no-border px-0">
            <div class="card-body">
                <div class="d-flex justify-content-center px-2 ">
                    <img style="height: 30px;float: left;" class="mr-auto" src="assets/custom/img/new_veritech_black_logo.png">
                    <p class="mb-0 mt-1 ml-2 headerTitle mr-auto d-none">Сургалт</p>
                </div>
                <!-- <ul class="nav nav-tabs nav-tabs-bottom mt-3">
                    <li class="nav-item"><a href="#tab-info-section-<?php echo $this->uniqId ?>" class="nav-link active" data-toggle="tab">Хүсэлт </a></li>
                    <li class="nav-item"><a href="#tab-question-section<?php echo $this->uniqId ?>" class="nav-link active" data-toggle="tab">Шалгалт</a></li>
                </ul> -->
                <div class="tab-content">
                    <div class="tab-pane fade" id="tab-info-section-<?php echo $this->uniqId ?>">
                        <div class="kpi-ind-tmplt-section padding-content" id="kpi-1704772363816112" data-process-id="169987129260032" data-bp-uniq-id="1704772363816112">
                            <div class="row m-0">
                                <?php for ($i = 1; $i <= sizeOf($headerArray)/2; $i++) { ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo issetParam($headerArray['position-'. $i .'-label']['default_value']) ?>:</label>
                                            <input type="text" readonly class="form-control" value="<?php echo is_array(issetParam($this->rowData[$headerArray['position-'. $i]['src_indicator_path']])) ? '' : issetParam($this->rowData[$headerArray['position-'. $i]['src_indicator_path']]); ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade active show" id="tab-question-section<?php echo $this->uniqId ?>">
                        <div class="d-flex">
                            <div class="sidebar sidebar-light sidebar-secondary sidebar-expand-md pr-2" style="width:280px">
                                <div class="sidebar-content">
                                    <div class="card">
                                        <div class="card-body mv-checklist-menu" >
                                            <ul class="nav nav-sidebar" data-nav-type="accordion">
                                                <?php 
                                                if (issetParam($rowsArray['position-1'])) {
                                                    $position1PathArr = explode('.', $rowsArray['position-1']['src_indicator_path']);
                                                    
                                                    if (issetParamArray($this->rowData[$position1PathArr['0']])) {
                                                        
                                                        $position1GroupArray = Arr::groupByArrayOnlyRows($this->rowData[$position1PathArr['0']], $position1PathArr['1']);
                                                        foreach ($position1GroupArray as $position1Key => $position1Group) {
                                                        ?>
                                                        <li class="nav-item nav-item-submenu nav-group-sub-mv-opened">
                                                            <a href="javascript:;" class="nav-link mv_checklist_02_groupname"><?php echo $position1Key; ?></a>
                                                            <?php 
                                                            
                                                            $position2PathArr = explode('.', $rowsArray['position-2']['src_indicator_path']);
                                                            $position2GroupArray = Arr::groupByArrayOnlyRows($position1Group, $position2PathArr['1'], false);
                                                            $index= 1;

                                                            foreach ($position2GroupArray as $position2Key =>  $position2Group) { 
                                                                $tmp = array();
                                                                $positionGroup2 = issetParamArray($position2Group[0]);
                                                                for ($c=3; $c<=10; $c++) {
                                                                    if (issetParam($rowsArray['position-' . $c]['src_indicator_path'])) {
                                                                        $position2PathArr = explode('.', $rowsArray['position-' . $c]['src_indicator_path']);
                                                                        if (issetParam($position2PathArr[1]))
                                                                            $tmp['position' . $c] = issetParam($positionGroup2[$position2PathArr[1]]);
                                                                    }
                                                                }

                                                                $rowJson = htmlentities(json_encode($tmp), ENT_QUOTES, 'UTF-8');
                                                                ?>
                                                                <ul class="nav nav-group-sub">
                                                                    <li class="nav-item checklistmenu-item">
                                                                        <a href="javascript:;" class="mv_checklist_02_sub nav-link" onclick="checkMenuFnc<?php echo $this->uniqId ?>(this)" data-uniqid="<?php echo $this->uniqId; ?>" data-json="<?php echo $rowJson; ?>" data-iscomment="1" data-stepid="<?php echo $this->uniqId . '_' . $index++; ?>">
                                                                            <i class="far fa-square"></i>
                                                                            <span class="pt1"><?php echo $position2Key; ?></span>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            <?php } ?>
                                                        </li>
                                                        <?php 
                                                        }
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 main-content" style="background-color: #F9F9F9">
                                <div class="mv-checklist-render-comment p-3">
                                    <div class="w-100 text-center">
                                        <img src="middleware/assets/img/process/background/watch.png" />
                                    </div>
                                </div>
                            </div>
                        </div>          
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">

    div[aria-describedby="dialog-widgetrender-<?php echo $this->mainIndicatorId ?>"] {
        .wg-form-paper {
            background-image: url('middleware/assets/img/process/background/back.png');
            background-repeat: no-repeat;
            background-position: top center;
            background-attachment: fixed;
            background-color: #ededed;
            padding-top: 11px;
            background-size: cover;

            .wg-form {
                position: relative;
                width: 1000px;
                min-height: calc(100vh - 126px);
                margin-left: auto;
                margin-right: auto;
                .card-side  {
                    margin-top: 10px;
                    padding: 20px;
                    box-shadow: 0px 2px 6px 0 rgba(0,0,0,.5);
                    background: #FFF;
                    border-radius: 20px;
    
                    .nav-link.active {
                        color: #699BF7 !important;
                        font-size: 18px;
                    }
                    
                    .nav-link {
                        font-size: 18px;
                        font-weight: 500;
                        line-height: 21px;
                        letter-spacing: 0px;
                        text-align: left;
                        padding: 20px;
                    }
    
                    .nav-tabs-bottom .nav-link.active:before {
                        background-color: #699BF7 !important;
                    }
    
                    .headerTitle {
                        font-size: 24px;
                        font-weight: 700;
                        line-height: 28px;
                        letter-spacing: 0px;
                        text-align: left;
                    }
    
                    .nav-tabs-bottom {
                        box-shadow: 0px 5px 10px 0px #0000001A;
                    }
    
                    .bp-btn-save {
                        padding: 12px 15px 10px 15px;
                        border-radius: 20px;
                        gap: 5px;
                        background: #468CE2;
                        font-size: 14px;
                        line-height: 16px;
                        letter-spacing: 0em;
                        text-align: center;
                    }
                    
                    .form-control {
                        border-radius: 20px;
                        border: 0.5px;
                        padding: 11px 20px;
                        font-size: 16px;
                        font-weight: 600;
                        line-height: 24px;
                        letter-spacing: 0px;
                        text-align: left;
                        height: 46px !important;
                        color: #585858;
                    }
    
                    .form-label {
                        font-size: 16px;
                        font-weight: 400;
                        line-height: 24px;
                        letter-spacing: 0px;
                        text-align: left;
                    }
    
                    .padding-content {
                        padding: 24px 11px 0 11px;
                    }
    
                    .mv-checklist-menu {
                        overflow-y: auto;
                        overflow-x: hidden;

                        .nav-group-sub-mv-opened .nav-group-sub {
                            display: block;
                        }
                        .nav-sidebar .nav-item:not(.nav-item-header):last-child {
                            padding-bottom: 0 !important;
                        }
                        .nav-item-submenu.nav-group-sub-mv-opened>.nav-link:after {
                            -webkit-transform: rotate(90deg);
                            transform: rotate(90deg);
                        }
                        .nav-group-sub .nav-link {
                            padding-left: 20px;
                        }
                        .nav-item-submenu>.nav-link.mv_checklist_02_groupname:after {
                            margin-top: -6px;
                        }
                        .nav-link.mv_checklist_02_groupname {
                            font-size: 13px;
                            color: #333 !important;
                            font-weight: bold !important;
                            padding-top: 5px;
                            padding-bottom: 5px;
                            text-transform: none !important;
                        }    
                        .nav-link.mv_checklist_02_sub {
                            padding-top: 2px;
                            padding-bottom: 2px;
                            font-size: 12px;
                        }    
                        .nav-link.mv_checklist_02_sub i {
                            color: #1B84FF !important;
                            margin-top: 2px;
                            font-size: 18px;    
                            margin-right: 13px;
                        }    
                    }
                    
                    .main-content {
                        background-color: #F9F9F9;
                        overflow-y: auto;
                        overflow-x: hidden;
                    }

                    .mv-checklist-render-comment {

                        .question-txt {
                            font-size: 28px;
                            font-weight: 500;
                            line-height: 32px;
                            letter-spacing: 0px;
                            text-align: center;
                            color: #3C3C3C;
                            margin-top: 100px;
                            margin-bottom: 20px;
                        }

                        .comment-txt {
                            font-size: 18px;
                            font-weight: 500;
                            line-height: 21px;
                            letter-spacing: 0px;
                            text-align: center;
                            color: #67748E;
                            margin-bottom: 20px;
                            width: 440px;
                            margin: 0 auto;
                        }
                        
                        .answer-txt {
                            font-size: 12px;
                            font-weight: 400;
                            line-height: 14px;
                            letter-spacing: 0px;
                            text-align: left !important;
                            padding: 15px 25px 15px 25px !important;
                            border-radius: 50px !important;
                            gap: 10px !important;
                            margin-bottom: 15px;
                            background-color: #FFF !important;
                            border-color: #FFF;
                            color: #585858 !important;
                        }
                        .answer-txt:hover {
                            color: #FFF !important;
                            background: linear-gradient(90deg, #468CE2 0%, rgba(70, 140, 226, 0.52) 100%);
                        }
                        
                    }
                }

                .position-timer {
                    margin-left: 10px;
                    margin-top: 10%;

                    .card-body {
                        width: 300px;
                        height: 140px;
                        padding: 10px;
                        border-radius: 20px;
                        border: none;
                        box-shadow: 0px 5px 10px 0px #0000001A;
                        
                        .timer {

                            font-size: 38px;
                            font-weight: 400;
                            line-height: 38px;
                            letter-spacing: 0px;
                            text-align: center;
                            color: #585858;

                            .num {
                                font-size: 38px;
                                font-weight: 600;
                                line-height: 38px;
                                letter-spacing: 0px;
                                text-align: center;
                                color: #699BF7;
                            }
                            .txt {
                                font-size: 12px;
                                font-weight: 400;
                                line-height: 14px;
                                letter-spacing: 0px;
                                text-align: center;
                                color: #585858;
                            }
                            
                            .all {
                                display: grid;
                                padding: 0 10px;
                            }
                        }
                    }
                }
            }
        }

        #dialog-widgetrender-<?php echo $this->mainIndicatorId ?> {
            padding-left: 0;
            padding-right: 0;
        }

        .ui-dialog-titlebar {
            border: none;
        }
    }
    
</style>

<script type="text/javascript">
    var $checkList_<?php echo $this->uniqId; ?> = $('#mv-checklist-render<?php echo $this->uniqId ?>');
    var $checkListMenu_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.mv-checklist-menu');
    var $checkListContent_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.main-content');

    $(function() { 
    
        $checkListMenu_<?php echo $this->uniqId; ?>.height($(window).height() - $checkListMenu_<?php echo $this->uniqId; ?>.offset().top - 390);
        $checkListMenu_<?php echo $this->uniqId; ?>.find('a[data-stepid="<?php echo $this->uniqId . '_1'; ?>"]').trigger('click');
        $checkListMenu_<?php echo $this->uniqId; ?>.find('a[data-stepid="<?php echo $this->uniqId . '_1'; ?>"]').addClass('active');
        /* $checkListContent_<?php echo $this->uniqId; ?>.height($(window).height() - $checkListMenu_<?php echo $this->uniqId; ?>.offset().top - 390); */
        
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
            
            if (typeof rowJson !== 'object') {
                var jsonObj = JSON.parse(html_entity_decode(rowJson, 'ENT_QUOTES'));
            } else {
                var jsonObj = rowJson;
            }

        });

    });

    function videoToImageFn_<?php echo $this->uniqId; ?>(param) {
        
        var myvideo = document.getElementById('bp-video-id');
        var $self = $('.go-video-startendtime');        
            /* myvideo.currentTime = $self.attr('data-starttime'); */
            myvideo.currentTime = 0;

        setTimeout(function() {
            /* var canvas = document.getElementById('canvas_'+param); */
            var canvas = document.getElementById('canvas');
            canvas.getContext('2d').drawImage(myvideo, 0, 0, 500, 280);
            var img = canvas.toDataURL('image/png');			
            $('.detail_cart_slider_imagevideo').css('background-image', "url('"+img+"')");
            dc++;
            
            if (param == dtlRowCount) {            
                clearInterval(videoToImageCheckInterval);
                myvideo.currentTime = 0;
                $('#bp-video-id').show().parent().find('h4').remove();
            }                
        }, 500);               
    }

    function playVideoInit_<?php echo $this->uniqId; ?> (contentId, uniqId) {
        var myvideo = document.getElementById('bp-video-id');
        myvideo.controls = false;
        
        $('#playVideo' + uniqId).click(function(e){
            myvideo.play();
            videoTimeToSaveInterval = setInterval(function () {
                $.ajax({
                    type: "post",
                    url: "mdcontentui/contentVisitorLog",
                    data: { recordId: contentId, duration: myvideo.currentTime },
                    success: function (data) {                    
                    }
                });
            }, 5000);              
            e.preventDefault();
        });        
        
        $('#pauseVideo' + uniqId).click(function(e){
            myvideo.pause();
            clearInterval(videoTimeToSaveInterval);
            e.preventDefault();
        });        
        
        $.ajax({
            type: "post",
            url: "mdcontentui/contentDurationVisitorLog",
            data: { recordId: contentId },
            success: function (data) {                  
                myvideo.currentTime = data;
            }
        });    
          
    }
    function startSubject_<?php echo $this->uniqId ?>(element, filePath, contentId, uniqId) {
        var $this = $(element),
            $parent = $this.closest('.<?php echo $this->uniqId ?>');
        var html = '';
        
        html = '';
        html += '<a href="javascript:;" data-starttime="" data-endtime="" class="go-video-startendtime">';
            html += '<div class="detail_cart_slider_imagevideo">';
                
                html += '<video width="960" height="500" controls id="bp-video-id" data-id="'+ contentId +'">';
                    html += '<source src="'+ filePath +'" type="video/mp4">';
                    html += 'Your browser does not support HTML5 video.';
                html += '</video>';
                    html += '<button class=""id="playVideo'+ uniqId +'" data-id="'+ contentId +'">Play</button>';
                    html += '<button class=""id="pauseVideo'+ uniqId +'" data-id="'+ contentId +'">Pause</button>';
                html += '<br>';
                // html += '<canvas id="canvas" width="500" height="280"></canvas>';
            html += '</div>';
        html += '</a>';
        var $dialogName = 'dialog-preview-' + uniqId;
        if (!$("#" + $dialogName).length) {
            $('<div id="' + $dialogName + '"></div>').appendTo('body');
        }
        
        $("#" + $dialogName).empty().append(html).promise().done(function () {
            playVideoInit_<?php echo $this->uniqId; ?>(contentId, uniqId);
        });
        $("#" + $dialogName).dialog({
            cache: false,
            resizable: false,
            bgiframe: true,
            autoOpen: false,
            title: 'Сургалт үзэх',
            width: 1000,
            height: "auto",
            modal: true,
            close: function() {
                if (typeof videoTimeToSaveInterval !== 'undefined')
                    clearInterval(videoTimeToSaveInterval);
                $("#" + $dialogName).empty().dialog('close');
            },
            buttons: [
                {
                    text: plang.get('close_btn'),
                    class: 'btn blue-madison btn-sm',
                    click: function() {
                        if (typeof videoTimeToSaveInterval !== 'undefined')
                            clearInterval(videoTimeToSaveInterval);
                        $("#" + $dialogName).dialog('close');
                    }
                }
            ]
        });
        $("#" + $dialogName).dialog('open');
    } 

    function nextQuestion_<?php echo $this->uniqId ?>(element) {
        var $this = $(element),
            $parent = $this.closest('.<?php echo $this->uniqId ?>');
    } 

    $('a[href="#tab-question-section<?php echo $this->uniqId ?>"]').on('shown.bs.tab', function(e) {
        $('.mv_checklist_02_sub').first().trigger('click');
        /* $('.position-timer').show(); */
    });

    $('a[href="#tab-info-section-<?php echo $this->uniqId ?>"]').on('shown.bs.tab', function(e) {
        /* $('.position-timer').hide(); */
    });

    function checkMenuFnc<?php echo $this->uniqId ?>(element) {
        var _this = $(element),
            stepKey = _this.attr('data-stepid'),
            rowJson = JSON.parse(_this.attr('data-json')),
            $parentSelector = _this.closest('#mv-checklist-render<?php echo $this->uniqId ?>');
        $parentSelector.find('.main-content > .mv-checklist-render-comment').hide();
        
        if ($parentSelector.find('.main-content > .mv-checklist-render-comment[data-stepkey="'+ stepKey +'"]').length < 1) {
            var _html = '';
            _html += '<div class="mv-checklist-render-comment p-3" data-stepkey="'+ stepKey +'">';
                _html += '<p class="question-txt">'+ rowJson['position3'] +'</p>';
                _html += '<p class="comment-txt mb-4">'+ rowJson['position4'] +'</p>';
                _html += '<div class="w-100 text-center">';
                    _html += '<button type="button" class="btn btn-sm btn-circle btn-success bp-btn-save" onclick="startSubject_<?php echo $this->uniqId ?>(this, \''+ rowJson['position5'] +'\', \''+ rowJson['position6'] +'\', \'<?php echo getUID() ?>\')">';
                        _html += 'Сургалт үзэх';
                    _html += '</button>';
                _html += '</div>';
                _html += '<div class="w-100 text-center">';
                    _html += '<img src="middleware/assets/img/process/background/watch.png" />';
                _html += '</div>';
            _html += '</div>';
            $parentSelector.find('.main-content').append(_html);
        } else {
            $parentSelector.find('.main-content > .mv-checklist-render-comment[data-stepkey="'+ stepKey +'"]').show();
        }
        
    };
</script>