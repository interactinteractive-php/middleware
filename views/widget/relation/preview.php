<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.'); ?> 
<?php 
$tmparr = $hideTmparr = array();
$headerPathArray = $rowsArray = $position1GroupArray = array();
if (issetParamArray($this->relationComponentsConfigData['header'])) {
    $headerPathArray = $this->relationComponentsConfigData['header'];
    $headerArray = Arr::groupByArrayOnlyRow($headerPathArray, 'trg_indicator_path', false);
    
}

if (issetParamArray($this->relationComponentsConfigData['rows'])) {
    $rowsPathArray = $this->relationComponentsConfigData['rows'];
    $rowsArray = Arr::groupByArrayOnlyRow($rowsPathArray, 'trg_indicator_path', false);
    
    $position1PathArr = explode('.', $rowsArray['position-1']['src_indicator_path']);
    $position1GroupArray = Arr::groupByArrayOnlyRows($this->rowData[$position1PathArr['0']], $position1PathArr['1']);
    $index= $counter= 0;
    foreach ($position1GroupArray as $position1Key =>  $position1Grp) { 
        $position1Group = $position1Grp['0'];
        $tmparr = $hideTmparr = array();
        foreach ($position1Grp as $rKey => $rVal) {
            for ($c=1; $c<=10; $c++) {
                if (issetParam($rowsArray['position-' . $c]['src_indicator_path'])) {
                    $position1PathArr = explode('.', $rowsArray['position-' . $c]['src_indicator_path']);
                    if (issetParam($position1PathArr['1'])) {
                        $tmp['position' . $c] = issetParam($rVal[$position1PathArr['1']]);
                    }
                }
            }
            if (issetParam($rVal['TIME'])) {
                $parsed = date_parse($rVal['TIME']);
                $startTimeSecond = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];    
                $startTime = str_replace('$00:', '', '$'.$rVal['TIME']);


                $tmp['position-starttime'] = $startTimeSecond;
                $tmp['position-endtime'] = '';
            }
            array_push($tmparr, $tmp);
            array_push($hideTmparr, $rVal);
        }
    }
}

?>
<div class="wg-form-paper <?php echo $this->uniqId ?> " id="mv-checklist-render<?php echo $this->uniqId ?>">
    <div class="wg-form">
        <div class="card card-side no-border px-0">
            <div class="card-body">
                <div class="w-100 main-content" style="background-color: #F9F9F9">
                    <div class="mv-checklist-render-comment p-3">
                        <div class="w-100 text-center vid-component">
                            <?php 
                                $html = '';
                                $html .= '<a href="javascript:;" data-starttime="" data-endtime="" class="go-video-startendtime">';
                                    $html .= '<div class="detail_cart_slider_imagevideo" data-starttime="'. issetParam($tmparr['0']['position-starttime']) .'">';
                                        $html .= '<video width="100%" controls data-id="main_video_'. $this->uniqId .'">';
                                            $html .= '<source src="'. issetParam($tmparr['0']['position1']) .'" type="video/mp4">';
                                            $html .= 'Your browser does not support HTML5 video.';
                                        $html .= '</video>';
                                        $html .= '<button class="d-none" id="playVideo'. $this->uniqId .'" data-id="main_video_'. $this->uniqId .'">Play</button>';
                                        $html .= '<button class="d-none" id="pauseVideo'. $this->uniqId .'" data-id="main_video_'. $this->uniqId .'">Pause</button>';
                                    $html .= '</div>';
                                $html .= '</a>';
                                echo $html;
                            ?>
                        </div>
                        <div class="text-center overflow-auto d-flex col-component">
                            <?php foreach ($tmparr as $key => $row) { ?>
                                <div class="media-component">
                                    <!-- <a href="javascript:;" data-starttime="00:00:00" data-endtime="00:01:00" class="go-video-startendtime">
                                        <div class="detail_cart_slider_imagevideo">
                                            <canvas id="canvas_<?php echo $rk ?>" width="500" height="280"></canvas>
                                        </div>        
                                    </a> -->
                                    <?php echo '<div class="detail_cart_slider_imagevideo" style="height: 115px;" data-starttime="'. issetParam($row['position-starttime']) .'">'
                                                . '<canvas id="canvas_'. $key . '_' . $this->uniqId .'" width="125" class="d-none" height="125"></canvas>'
                                                . '<video width="100%" controls id="video_' . $key . '_' . $this->uniqId .'"  class="d-none" >'
                                                    . '<source src="'. issetParam($tmparr['0']['position1']) .'" type="video/mp4" data-id="' . $key . '_' . $this->uniqId .'" class="mx-auto videotoimg" height="110px"/>'
                                                . '</video>'
                                            . '</div>'; ?>
                                    <p class="m-0 text-left text-info"><?php echo checkDefaultVal($row['position2'], 'pos-2') ?></p>
                                    <p class="m-0 text-left"><?php echo checkDefaultVal($row['position3'], 'pos-3') ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo issetParam($this->uniqCss) ?>
<?php echo issetParam($this->uniqJs) ?>
<script type="text/javascript">
    $(function() {
        /* videoToImageFn<?php echo $this->uniqId ?>(); */
        $('#mv-checklist-render<?php echo $this->uniqId ?> .videotoimg').each(function (key, row) {
            videoToImageFn<?php echo $this->uniqId ?>(row)
        });
    });

    function videoToImageFn<?php echo $this->uniqId ?>(param) {
        var $self = $(param);
            dataId = $self.attr('data-id'),
            myvideo = document.getElementById('video_' + dataId);
            startTimer = $(param).closest('.detail_cart_slider_imagevideo').attr('data-starttime')
            myvideo.currentTime = startTimer
            ;
        
        setTimeout(function() {
            canvas = document.getElementById('canvas_' + dataId);
            console.log(canvas);
            canvas.getContext('2d').drawImage(myvideo, 0, 0, 125, 125);
            var img = canvas.toDataURL('image/png');			
            $(param).closest('.detail_cart_slider_imagevideo').css('background-image', "url('"+img+"')");
        }, 500);
    }
</script>