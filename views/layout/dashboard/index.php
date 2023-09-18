<?php
    $colorData = array('#ff7e79','#ff8e51','#fec244','#39e0d0','#48c6f3','#6373ed');
?>
<style type="text/css">
    /* .header-tab, */
    #app_tab_governmentdashboardv1 .approve-requests,
    #app_tab_governmentdashboardv1 .employee-unitdashboard {
        display: none;
    }
</style>

<link rel="stylesheet" href="middleware/assets/css/intranet/style.css">
<link rel="stylesheet" href="assets/custom/addon/plugins/owl-carousel/owl.carousel.css">
<script src="assets/custom/addon/plugins/owl-carousel/owl.carousel.min.js"></script>
<script src="assets/custom/addon/plugins/owl-carousel/jquery.easing.min.js"></script>
<script src="assets/custom/addon/plugins/owl-carousel/easypiechart.js"></script>
<script src="views/government/meeting/include/feather.min.js"></script>

<div class="app_dashboard dashboard<?php echo $this->uniqId ?>">
    <div class="page-content">
        <div class="content-wrapper">
            <div class="">
                <div class="card card-hover card-profile-sidebar card-event-calendar">
                <div class="row">
                    <div class="col-md-6">
                        <h3 style="margin-left: 8px;margin-top: 6px;"><?php echo Lang::line('HR_DASHBOARD_1'); ?></h3>
                        <?php 
                        if (issetParam($this->layoutPositionArr['pos_8'])) { ?>
                            <a href="<?php echo $this->layoutPositionArr['pos_8'][0]['link']; ?>" target="_blank"><div class="p-2" style="background: #ffffff; border-left: 1px solid #f7f8ff;">
                            <div style="height: 400px;background-image: url(<?php echo URL.$this->layoutPositionArr['pos_8'][0]['profilephoto']; ?>);background-repeat:no-repeat;background-position: center center;background-size: cover;">
                            </div>
                            <h2 style="
                                position: absolute;
                                top: 50%;
                                width: 96%;
                                padding: 4px 8px;
                                background: rgba(255, 255, 255, 0.75);
                                color: black;                                  
                                text-align: center;                              
                            "><?php echo $this->layoutPositionArr['pos_8'][0]['activityname']; ?></h2>
                                </div>
                            </a>
                        <?php } else { ?>
                            <div class="p-2" style="background: #ffffff; border-left: 1px solid #f7f8ff;">
                            <div style="height: 400px;background-repeat:no-repeat;background-position: center center;background-size: cover;">
                            </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-hover card-project-three border-0 pt10 box-shadow-none work<?php echo $this->uniqId ?>">
                            <h3><?php echo Lang::line('HR_DASHBOARD_2'); ?></h3>
                            <?php if (issetParam($this->layoutPositionArr['pos_0'])) {
                                foreach ($this->layoutPositionArr['pos_0'] as $key => $row) {
                                    $rowJson = htmlentities(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <div class="event-box khantask event-box<?php echo $this->uniqId ?>" data-row="<?php echo $rowJson ?>" data-id="<?php echo $row['id'] ?>" data-startdate="<?php echo $row['startdate'] ?>" data-wfmstatuscode="<?php echo $row['rowcolor'] ?>">
                                        <div class="mr15" style="min-width:70px;float:left;">
                                            <div class="calendar-date project-data-group">
                                                <div class="d-flex flex-column">
                                                    <h3 class="text-black d-flex align-items-center">
                                                        <i data-feather="calendar" class="svg-14 mr-1 text-muted"></i>
                                                        <span><?php echo Date::formatter($row['startdate']) ?></span>
                                                    </h3>
                                                    <h3 class="text-black d-flex align-items-center">
                                                        <i data-feather="calendar" class="svg-14 mr-1 text-muted"></i>
                                                        <span class="text-danger"><?php echo Date::formatter($row['enddate']) ?></span>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="row d-flex align-items-center justify-content-between w-100">
                                                <div class="mdata w-100">
                                                    <div class="taskdesc">
                                                        <div><?php echo $row['location'] ?></div>
                                                        <div class="text-muted"><?php echo $row['activityname'] ?></div>
                                                    </div>
                                                    <div class="d-flex justify-content-end ">
                                                        <h3 class="text-danger mb-0"><button onclick="updateEvent(this ,'<?php echo $row['id'];?>')"  data-row="<?php echo $rowJson ?>" class="btn btn-sm btn-primary btn-rounded text-white" style="background-color: <?php //echo $row['wfmstatuscolor'] ? $row['wfmstatuscolor'] : '#3F51B5' ?>"><?php echo $row['purpose'] ?></button></h3>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                            <?php }
                            } ?>
                            <div style="display: inline-block">
                                <div id="calendar2-<?php echo $this->uniqId; ?>" class="hrmtimelog-calendar2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class=" newslider">
                <h3 class="card-title mg-b-0 " style="color: #000 !important;"><?php echo Lang::line('HR_DASHBOARD_4'); ?></h3>
                <?php if (issetParam($this->layoutPositionArr['pos_12'])) { ?>
                    <div class="owl-carousel news-post">
                    <?php 
                        foreach ($this->layoutPositionArr['pos_12'] as $key => $row) { 
                            $rowJson = htmlentities(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                        <div class="w-100">
                            <div class="card mb-2">
                            <?php 
                                if ($row['picture']) { 
                                    $img = $row['picture'];
                                }else{
                                    $img =  URL.'assets/custom/img/noimage.png';
                                }
                               
                            ?>


                                <?php if (file_exists($img)) { ?>
                                    <div class="post-feature" style="background-image: url('<?php echo URL.$img; ?>');"> </div>
                                <?php } else { ?>
                                    <div class="post-feature" style=""></div>
                                <?php } ?>
                                <div class="card-body">
                                <a href="javascript:void(0);" onclick="drilldownLink_news<?php echo $this->uniqId ?>(this)"  data-row ="<?php echo $rowJson; ?>"><h4 class="card-title"><?php echo $row['title'] ?></h4>
                                    <p class="card-text"><?php echo (mb_substr(Str::htmltotext(Str::cleanOut($row['dim1'])), 0, 95)); ?> ... </p></a>
                                    <!-- <a class="btn btn-primary">Button</a> -->
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="card card-hover card-prompt">
                        <div class="card-header bg-transparent">
                            <h6 class="card-title mg-b-0 text-danger" style="color: #CC0000 !important;">Мэдээлэл бүртгэгдээгүй байна</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled media-list mg-b-0"></ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
       
        <div style="width: 400px;" class="px-2 ">
            <div class="card card-hover card-profile-sidebar card-weather">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mg-b-0">Цаг агаар</h6>
                    <nav class="nav nav-card-icon">
                        
                        <a href="javascript:void(0);"><i data-feather="refresh-ccw"></i></a>
                    </nav>
                </div>
                <div class="p-3">
                    <?php if ($this->weatherData) { ?>
                        <div class="d-flex align-items-center justify-content-between row">
                            <div class="col">
                                <h2 class="tx-primary mb-0 font-family-Oswald"><?php echo issetParam($this->weatherData[0]['temperatureDay']) ? $this->weatherData[0]['temperatureDay'] : '' ?>°C</h2>
                                <p class="mb-0">Өнөөдөр<br><?php echo issetParam($this->weatherData[0]['date']) ? $this->weatherData[0]['date'] : ''  ?></p>
                            </div>
                            <div class="col">
                                <img src="<?php echo issetParam($this->weatherData[0]['filepath']) ? $this->weatherData[0]['filepath'] : 'assets/custom/img/app_dashboard/weather.png'; ?>" class="img-fluid" onerror="onUserImageError(this);">
                            </div>
                        </div>
                        <div class="profile-info mt-3 row">
                            <div class="col-4">
                            <h5 class="tx-primary"><?php echo issetParam($this->weatherData[1]['temperatureDay']) ? $this->weatherData[1]['temperatureDay'] : ''  ?>°C</h5>
                            <p class="text-nowrap"><?php echo issetParam($this->weatherData[1]['date']) ? Date::format('l', $this->weatherData[1]['date'], true) : ''  ?></p>
                            </div>
                            <div class="col-4">
                            <h5 class="tx-primary"><?php echo issetParam($this->weatherData[2]['temperatureDay']) ? $this->weatherData[2]['temperatureDay'] : ''  ?>°C</h5>
                            <p class="text-nowrap"><?php echo issetParam($this->weatherData[2]['date']) ? Date::format('l', $this->weatherData[2]['date'], true) : ''  ?></p>
                            </div>
                            <div class="col-4">
                            <h5 class="tx-primary"><?php echo issetParam($this->weatherData[3]['temperatureDay']) ? $this->weatherData[2]['temperatureDay'] : ''  ?>°C</h5>
                            <p class="text-nowrap"><?php echo issetParam($this->weatherData[3]['date']) ? Date::format('l', $this->weatherData[3]['date'], true) : ''  ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card card-hover card-connection-one">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mg-b-0"><?php echo Lang::line('HR_DASHBOARD_3'); ?></h6>
                    <nav class="nav nav-card-icon">
                        
                        <a href="javascript:void(0);"><i data-feather="refresh-ccw"></i></a>
                    </nav>
                </div>
                <ul class="list-group list-group-flush newslist<?php echo $this->uniqId ?>">
                    <?php if (issetParam($this->layoutPositionArr['pos_7'])) {
                        foreach ($this->layoutPositionArr['pos_7'] as $key => $row) { 
                        $rowJson = htmlentities(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                        <li class="list-group-item align-items-center" data-id="<?php echo $row['id'] ?>">
                            <div class="avatar">
                                <?php if (!empty($row['physicalpath']) && file_exists($row['physicalpath'])) { ?>
                                    <img src="<?php echo $row['physicalpath'] ?>" class="rounded-circle" onerror="onUserImageError(this);" alt="">
                                    <?php } else {
                                        $colorData = array('#5556fd','#22d273','#6f42c1','#dc3545','#38c4fa','#fd7e14');
                                    ?>
                                    <span class="avatar-initial rounded-circle" style="background: <?php echo $colorData[rand(0, 5)]; ?>;">
                                        <?php echo Str::utf8_substr(Str::sanitize($row['description']), 0, 1); ?>
                                    </span>
                                <?php } ?>
                            </div>
                            <div class="list-body">
                            <a href="javascript:void(0);" onclick="drilldownLink_news2<?php echo $this->uniqId ?>(this)"  data-row ="<?php echo $rowJson; ?>"><h5 class="link" style="line-height: 1"><?php echo $row['departmentname'] ?></h5></a>
                                <p class="person-name mb-0"><?php echo $row['description'] ?></p>
                                <p class="person-location"><?php echo $row['positionname'] ?></p>
                                <p class="person-location"><?php echo $row['enddate'] ?></p>
                                <!-- <p class="person-location"><?php echo $row['startdate'] ?></p> -->
                            </div>
                        </li>
                    <?php }} ?>
                </ul>
            </div>
         
        </div>
    </div>
</div>

<style type="text/css">
    /* @import url("https://fonts.googleapis.com/css?family=Oswald:300,400,500;"); */
    .font-family-Oswald {
        font-family: "Oswald", sans-serif !important;
    }
    .app_dashboard .drill-chart .chart-label:hover,
    .app_dashboard .drill-chart .percent:hover {
        cursor: pointer;
    }
    .app_dashboard .app_dashboard {
        font-size: 12px;
    }
    .app_dashboard .small,
    .app_dashboard small {
        font-size: 11px;
    }
    .app_dashboard svg {
        width: 16px;
        height: 16px;
    }
    .app_dashboard .text-blue {
        color: #4175e1;
    }
    .app_dashboard .marker {
        position: absolute;
        font-size: 10px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        letter-spacing: 0.5px;
        padding: 2px 10px;
        left: 10px;
        top: 10px;
        text-transform: uppercase;
    }
    .app_dashboard .card .bg-transparent {
        border-bottom: inherit;
    }
    .app_dashboard .card {
        border-radius: 0;
        border-color: rgba(230, 231, 239, 0.85);
        box-shadow: 6px 11px 41px -28px #a99de7;
        transition: all 0.25s;
        margin-bottom: 10px;
    }
    .app_dashboard .card.card-dark {
        color: rgba(255, 255, 255, 0.7);
    }
    .app_dashboard .card.card-dark .card-title {
        color: #fff;
    }
    .app_dashboard .card.card-dark a {
        color: inherit;
    }
    .app_dashboard .card.card-dark a:hover,
    .app_dashboard .card.card-dark a:focus {
        color: #fff;
    }
    .app_dashboard .card-header {
        padding: 10px 15px;
    }
    .app_dashboard .card-header .nav-card-icon {
        line-height: 0;
    }.card-link {
        color: #373857;
        text-decoration: underline;
    }
    .app_dashboard .card-link:hover,
    .app_dashboard .card-link:focus {
        color: #06072d;
    }
    .app_dashboard .card-title {
        color: #06072d;
        line-height: 1;
    }
    .app_dashboard .avatar {
        position: relative;
        width: 30px;
        height: 30px;
        border: 0;
    }
    .app_dashboard .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .app_dashboard .avatar-initial {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #8392a5;
        color: #fff;
        font-size: 16px;
        font-family: Arial;
        font-weight: bold;
        padding-top: 1px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .app_dashboard .avatar-offline::after,
    .app_dashboard .avatar-online::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 4px;
        width: 6px;
        height: 6px;
        border-radius: 100%;
        box-shadow: 0 0 0 1.5px #fff;
    }
    .app_dashboard .avatar-offline::after {
        background-color: #c0ccda;
    }
    .app_dashboard .avatar-online::after {
        background-color: #28a745;
    }
    .app_dashboard .avatar-xxs {
        width: 20px;
        height: 20px;
    }
    .app_dashboard .avatar-xxs .avatar-initial {
        font-size: 8px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        font-weight: 400;
    }
    .app_dashboard .avatar-xxs.avatar-offline::after,
    .app_dashboard .avatar-xxs.avatar-online::after {
        width: 4px;
        height: 4px;
        right: 0;
        box-shadow: 0 0 0 1px #fff;
    }
    .app_dashboard .avatar-xs {
        width: 22px;
        height: 22px;
    }
    .app_dashboard .avatar-xs .avatar-initial {
        font-size: 10px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        font-weight: 500;
    }
    .app_dashboard .avatar-xs.avatar-offline::after,
    .app_dashboard .avatar-xs.avatar-online::after {
        width: 4px;
        height: 4px;
    }
    .app_dashboard .avatar-sm {
        width: 32px;
        height: 32px;
    }
    .app_dashboard .avatar-sm .avatar-initial {
        font-size: 16px;
    }
    .app_dashboard .avatar-sm.avatar-offline::after,
    .app_dashboard .avatar-sm.avatar-online::after {
        width: 5px;
        height: 5px;
        box-shadow: 0 0 0 1.2px #fff;
    }
    .app_dashboard .avatar-md {
        width: 48px;
        height: 48px;
    }
    .app_dashboard .avatar-md .avatar-initial {
        font-size: 18px;
    }
    .app_dashboard .avatar-md.avatar-offline::after,
    .app_dashboard .avatar-md.avatar-online::after {
        width: 7px;
        height: 7px;
        right: 3px;
        bottom: 1px;
    }
    .app_dashboard .avatar-lg {
        width: 64px;
        height: 64px;
    }
    .app_dashboard .avatar-lg .avatar-initial {
        font-size: 24px;
    }
    .app_dashboard .avatar-lg.avatar-offline::after,
    .app_dashboard .avatar-lg.avatar-online::after {
        width: 10px;
        height: 10px;
        bottom: 3px;
        right: 4px;
    }
    .app_dashboard .avatar-xl {
        width: 72px;
        height: 72px;
    }
    .app_dashboard .avatar-xl .avatar-initial {
        font-size: 30px;
    }
    .app_dashboard .avatar-xl.avatar-offline::after,
    .app_dashboard .avatar-xl.avatar-online::after {
        width: 11px;
        height: 11px;
        bottom: 4px;
        right: 5px;
        box-shadow: 0 0 0 2.5px #fff;
    }
    .app_dashboard .avatar-xxl {
        width: 100px;
        height: 100px;
    }
    .app_dashboard .avatar-xxl .avatar-initial {
        font-size: 45px;
    }
    .app_dashboard .avatar-xxl.avatar-offline::after,
    .app_dashboard .avatar-xxl.avatar-online::after {
        width: 10px;
        height: 10px;
        bottom: 6px;
        right: 8px;
        box-shadow: 0 0 0 3px #fff;
    }
    .app_dashboard .avatar-group {
        display: flex;
    }
    .app_dashboard .avatar-group .avatar {
        position: relative;
        flex-shrink: 0;
    }
    .app_dashboard .avatar-group .avatar img {
        position: absolute;
        top: 0;
        left: 0;
    }
    .app_dashboard .avatar-group .avatar + .avatar {
        margin-left: -15px;
    }
    .app_dashboard .avatar-group .avatar + .avatar.avatar-sm {
        margin-left: -10px;
    }
    .app_dashboard .avatar-group .avatar + .avatar.avatar-xs {
        margin-left: -5px;
    }
    .app_dashboard .avatar-group .avatar + .avatar.avatar-xxs {
        margin-left: -4px;
    }
    .app_dashboard .avatar-group .avatar img,
    .avatar-group .avatar-initial {
        box-shadow: 0 0 0 1px #fff;
    }
    .app_dashboard .avatar-group-more {
        margin-left: 5px;
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .avatar-add {
        width: 20px;
        height: 20px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        border: 1.2px solid #919fac;
        margin-left: 5px;
        color: #637382;
        opacity: .7;
    }
    .app_dashboard .avatar-add:hover,
    .app_dashboard .avatar-add:focus {
        border-color: #5556fd;
        background-color: rgba(85, 86, 253, 0.2);
        color: #5556fd;
        opacity: 1;
    }
    .app_dashboard .avatar-add svg {
        stroke-width: 2.5px;
    }
    .app_dashboard .card-hover {
        transition: all 0.25s;
    }
    .app_dashboard .card-invoice .card-body h6 {
        color: #06072d;
    }
    .app_dashboard .card-invoice .card-body p {
        color: #575d78;
    }
    .app_dashboard .card-pricing {
        height: 100%;
        border-color: rgba(224, 225, 235, 0.85);
        transition: all 0.25s;
    }
    .app_dashboard .card-pricing:hover,
    .app_dashboard .card-pricing:focus {
        border-color: rgba(214, 216, 229, 0.85);
        box-shadow: 0 4px 12px 0 rgba(131, 146, 165, 0.15), 2px 2px 5px 0 rgba(60, 70, 83, 0.04);
    }
    .app_dashboard .card-pricing:not([class*="bg-"]) .card-header,
    .card-pricing:not([class*="bg-"]) .card-footer {
        background-color: transparent;
    }
    .app_dashboard .card-pricing .card-header {
        padding: 20px;
    }
    .app_dashboard .card-pricing .card-header h4 {
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 300;
        color: #5556fd;
    }
    .app_dashboard .card-pricing .card-header p {
        margin-bottom: 0;
        color: #575d78;
    }
    .app_dashboard .card-pricing .card-header svg {
        width: 40px;
        height: 40px;
        margin-bottom: 10px;
        stroke-width: 1px;
        color: #5556fd;
        fill: rgba(85, 86, 253, 0.15);
    }
    .app_dashboard .card-pricing .card-body {
        padding: 20px;
        color: #575d78;
    }
    .app_dashboard .card-pricing .card-body svg {
        width: 16px;
        height: 16px;
        stroke-width: 4px;
        margin-right: 10px;
    }
    .app_dashboard .card-pricing .card-body svg.feather-check {
        color: #20c997;
    }
    .app_dashboard .card-pricing .card-body li {
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-pricing .card-body li + li {
        margin-top: 5px;
    }
    .app_dashboard .card-pricing .card-footer {
        border-top: 0;
        padding: 20px;
    }
    .app_dashboard .card-pricing-premium {
        background-color: #f4f4ff;
        background-image: linear-gradient(to bottom, #fff 0%, #f4f4ff 100%);
        background-repeat: repeat-x;
    }
    .app_dashboard .card-pricing-premium,
    .app_dashboard .card-pricing-premium:hover,
    .app_dashboard .card-pricing-premium:focus {
        border-color: #5556fd;
        box-shadow: 0 4px 12px 0 rgba(131, 146, 165, 0.15), 2px 2px 5px 0 rgba(60, 70, 83, 0.04);
    }
    .app_dashboard .card-analytics-one .card-body .row > div:first-child > label {
        position: relative;
        margin-top: -5px;
        display: block;
    }
    .app_dashboard .card-analytics-one .content-label {
        margin-bottom: 5px;
    }
    .app_dashboard .card-analytics-one .card-value {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        font-size: 20px;
        color: #06072d;
    }
    .app_dashboard .card-analytics-one .card-value-sub {
        font-size: 10px;
        font-family: "Oswald", sans-serif;
        display: flex;
        align-items: baseline;
        margin-left: 5px;
    }
    .app_dashboard .card-analytics-one .card-value-sub i {
        font-size: 16px;
        line-height: 0;
        position: relative;
        top: 1px;
        margin-right: 2px;
    }
    .app_dashboard .card-analytics-one .card-value-sub i::before {
        line-height: 0;
    }
    .app_dashboard .card-analytics-one .progress-value {
        max-width: 150px;
        height: 3px;
        margin-bottom: 5px;
    }
    .app_dashboard .card-analytics-one .card-value-desc {
        font-size: 10px;
        color: rgba(87, 93, 120, 0.9);
        margin-bottom: 0;
    }
    .app_dashboard .card-analytics-one .chart-wrapper {
        height: 160px;
    }
    .app_dashboard .card-analytics-one .flot-chart {
        height: 100%;
    }
    .app_dashboard .card-analytics-one .flot-chart .flot-y-axis .flot-tick-label {
        font-size: 8px;
        font-weight: 500;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        color: #373857;
    }
    .app_dashboard .card-analytics-one .flot-chart .flot-x-axis .flot-tick-label {
        font-size: 8px;
        font-weight: 700;
        color: rgba(55, 56, 87, 0.6);
        font-family: "Helvetica Neue", Helvetica, sans-serif;
    }
    .app_dashboard .card-analytics-one .card-footer {
        padding: 15px;
    }
    .app_dashboard .card-analytics-one .card-footer .row > div {
        flex-grow: 0;
    }
    .app_dashboard .card-analytics-one .card-footer .content-label {
        letter-spacing: normal;
        color: #575d78;
        margin-bottom: 0;
        white-space: nowrap;
    }
    .app_dashboard .card-analytics-one .card-footer .card-value-sub {
        font-size: 9px;
        font-weight: 400;
        margin-left: 2px;
    }
    .app_dashboard .card-analytics-one .card-footer .card-value-sub .icon {
        font-size: 10px;
        top: 0;
    }
    .app_dashboard .card-analytics-one .card-footer h4 {
        margin-bottom: 0;
    }
    .app_dashboard .card-analytics-two .card-header {
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-analytics-two .card-body {
        padding: 0;
    }
    .app_dashboard .list-group-item {
        border-top: 1px solid #eaebf1;
    }
    .app_dashboard .list-group-flush {
        padding: 0;
        max-height: 524px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .app_dashboard .card-analytics-two .list-group-item {
        box-shadow: none;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-analytics-two .list-group-icon {
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
        border-radius: 100%;
    }
    .app_dashboard .card-analytics-two .list-body {
        flex: 1;
        margin-left: 10px;
    }
    .app_dashboard .card-analytics-two .list-body h6 {
        font-size: 12px;
        margin-bottom: 2px;
    }
    .app_dashboard .card-analytics-two .list-body span {
        display: block;
        color: rgba(55, 56, 87, 0.6);
        font-size: 10px;
    }
    .app_dashboard .card-analytics-two .list-visit {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        width: 40px;
        margin-left: 10px;
        text-align: right;
        font-size: 12px;
    }
    .app_dashboard .card-analytics-two .list-rate {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        width: 50px;
        margin-left: 10px;
        text-align: right;
        font-size: 12px;
    }
    .app_dashboard .card-analytics-two .list-rate .icon {
        font-size: 14px;
        margin-right: 1px;
    }
    .app_dashboard .card-chart-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px 0;
    }
    .app_dashboard .card-chart-one .card-header .nav {
        margin-right: -5px;
    }
    .app_dashboard .card-chart-one .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-chart-one .card-footer {
        padding: 12px 15px;
    }
    .app_dashboard .card-chart-one .card-footer h4 {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        margin-bottom: 2px;
    }
    .app_dashboard .card-chart-one .card-value {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        font-size: 26px;
        color: #06072d;
        margin-bottom: 5px;
    }
    .app_dashboard .card-chart-one .card-value span {
        font-weight: 400;
        font-family: "Roboto Mono", monospace;
        color: rgba(6, 7, 45, 0.85);
        letter-spacing: normal;
    }
    .app_dashboard .card-chart-one .flot-chart {
        width: 100px;
        height: 70px;
    }
    .app_dashboard .card-chart-one .flot-chart-two {
        width: 100%;
        height: 160px;
    }
    .app_dashboard .card-chart-one .flot-chart-two .flot-y-axis .flot-tick-label {
        color: rgba(55, 56, 87, 0.6);
        font-size: 9px;
        font-family: sans-serif;
    }
    .app_dashboard .card-chart-one .flot-chart-two .flot-x-axis .flot-tick-label {
        font-size: 10px;
    }
    .app_dashboard .card-chart-one.bg-primary {
        border-width: 0;
    }
    .app_dashboard .card-chart-one.bg-primary .card-header,
    .card-chart-one.bg-primary .card-title,
    .card-chart-one.bg-primary .card-body {
        color: #fff;
    }
    .app_dashboard .card-chart-one.bg-primary .chart-legend label {
        color: rgba(255, 255, 255, 0.5);
    }
    .app_dashboard .card-chart-one.bg-primary .flot-chart-two .flot-y-axis .flot-tick-label {
        color: rgba(255, 255, 255, 0.5);
    }
    .app_dashboard .card-chart-one.bg-primary .flot-chart-two .flot-x-axis .flot-tick-label {
        color: #fff;
    }
    .app_dashboard .card-chart-one.bg-primary .card-footer {
        border: 1px solid rgba(230, 231, 239, 0.85);
        border-top-width: 0;
    }
    .app_dashboard .card-chart-one.bg-primary:hover .card-footer,
    .app_dashboard .card-chart-one.bg-primary:focus .card-footer {
        border-color: rgba(218, 219, 231, 0.85);
    }
    .app_dashboard .card-chart-two {
        position: relative;
    }
    .app_dashboard .card-chart-two .card-header {
        position: absolute;
        padding: 0 15px;
        top: 15px;
        left: 0;
        right: 0;
        z-index: 10;
        background-image: linear-gradient(to bottom, #fff 0%, rgba(255, 255, 255, 0) 100%);
        background-repeat: repeat-x;
    }
    .app_dashboard .card-chart-two .card-value {
        font-size: 34px;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        line-height: 1;
        margin-bottom: 20px;
        letter-spacing: -.5px;
    }
    .app_dashboard .card-chart-two .card-value span {
        font-size: 16px;
        font-weight: 300;
        color: rgba(6, 7, 45, 0.4);
        letter-spacing: -.5px;
        text-transform: uppercase;
        margin-left: 2px;
    }
    .app_dashboard .card-chart-two .card-value-label {
        font-size: 12px;
        font-weight: 700;
        font-family: "Fira Sans", sans-serif;
        text-transform: uppercase;
        letter-spacing: .2px;
        color: #06072d;
        margin-bottom: 2px;
    }
    .app_dashboard .card-chart-two .nav-card-icon {
        position: absolute;
        top: 10px;
        right: 14px;
        z-index: 100;
    }
    .app_dashboard .card-chart-two .card-body {
        padding: 100px 0 0;
        position: relative;
    }
    .app_dashboard .card-chart-two .chart-wrapper {
        position: relative;
        overflow: hidden;
    }
    .app_dashboard .card-chart-two .flot-chart {
        height: 280px;
        margin-left: -8px;
        margin-right: -8px;
        margin-bottom: -14px;
    }
    .app_dashboard .card-chart-two .flot-chart .flot-x-axis .flot-tick-label {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        font-size: 9px;
        font-weight: 600;
        color: rgba(30, 26, 112, 0.4);
        transform: translateY(-22px);
    }
    .app_dashboard .card-chart-three .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-chart-three .card-chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .app_dashboard .card-chart-three .card-chart-header .btn,
    .app_dashboard .card-chart-three .card-chart-header .ui-datepicker-buttonpane button,
    .app_dashboard .ui-datepicker-buttonpane .card-chart-three .card-chart-header button,
    .app_dashboard .card-chart-three .card-chart-header .sp-container button,
    .app_dashboard .sp-container .card-chart-three .card-chart-header button {
        text-transform: uppercase;
        font-size: 9px;
        font-weight: 600;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        padding: 5px 10px;
        letter-spacing: .5px;
    }
    .app_dashboard .card-chart-three .card-chart-header .btn.active,
    .app_dashboard .card-chart-three .card-chart-header .ui-datepicker-buttonpane button.active,
    .app_dashboard .ui-datepicker-buttonpane .card-chart-three .card-chart-header button.active,
    .app_dashboard .card-chart-three .card-chart-header .sp-container button.active,
    .app_dashboard .sp-container .card-chart-three .card-chart-header button.active {
        background-color: #d9dfe7;
        border-color: #d0d7e1;
        color: #373857;
    }
    .app_dashboard .card-chart-three .chart-legend label {
        display: block;
    }
    .app_dashboard .card-chart-three .chart-legend label + label {
        margin-left: 0; }
    .app_dashboard .card-chart-three .chart-wrapper {
        height: 200px;
    }
    .app_dashboard .card-chart-four .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-chart-four .chart-wrapper {
        height: 250px;
    }
    .app_dashboard .card-campaign-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-social-one .chart-icon,
    .card-campaign-one .chart-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 100%;
    }
    .app_dashboard .card-social-one .chart-icon {
        width: 40px;
        height: 40px;
        margin-bottom: 0;
    }
    .app_dashboard .card-campaign-one .chart-icon svg {
        width: 30px;
        height: 30px;
        stroke-width: 1.5px;
    }
    .app_dashboard .card-social-one .chart-icon.brand-01,
    .card-campaign-one .chart-icon.brand-01 {
        color: #3c3dfd;
        background-color: rgba(85, 86, 253, 0.16);
    }
    .app_dashboard .card-campaign-one .chart-icon.brand-01 svg {
        fill: rgba(85, 86, 253, 0.2);
    }
    .app_dashboard .card-social-one .chart-icon.brand-02,
    .card-campaign-one .chart-icon.brand-02 {
        color: #26d0cc;
        background-color: rgba(75, 222, 219, 0.3);
    }
    .app_dashboard .card-social-one .chart-icon.brand-02 svg,
    .card-campaign-one .chart-icon.brand-02 svg {
        fill: rgba(75, 222, 219, 0.2);
    }
    .app_dashboard .card-social-one .chart-icon.brand-pink,
    .card-campaign-one .chart-icon.brand-pink {
        color: #e83e8c;
        background-color: rgba(232, 62, 140, 0.3);
    }
    .app_dashboard .card-social-one .chart-icon.brand-pink svg,
    .card-campaign-one .chart-icon.brand-pink svg {
        fill: rgba(232, 62, 140, 0.2);
    }
    .app_dashboard .card-social-one .chart-icon.brand-green,
    .card-campaign-one .chart-icon.brand-green {
        color: #20c997;
        background-color: rgba(32, 201, 151, 0.3);
    }
    .app_dashboard .card-social-one .chart-icon.brand-green svg,
    .card-campaign-one .chart-icon.brand-green svg {
        fill: rgba(32, 201, 151, 0.2);
    }
    .app_dashboard .card-social-one .chart-icon.brand-orange,
    .card-campaign-one .chart-icon.brand-orange {
        color: #fd7e14;
        background-color: rgba(253, 126, 20, 0.3);
    }
    .app_dashboard .card-social-one .chart-icon.brand-orange svg,
    .app_dashboard .card-campaign-one .chart-icon.brand-orange svg {
        fill: rgba(253, 126, 20, 0.2);
    }
    .app_dashboard .card-campaign-one .chart-value {
        font-size: 22px;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        margin-bottom: 10px;
    }
    .app_dashboard .card-campaign-one .chart-label {
        font-weight: 500;
        font-size: 13px;
        margin-bottom: 0;
        line-height: normal;
    }
    .app_dashboard .card-deal .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-deal .avatar-xs {
        width: 26px;
        height: 26px;
    }
    .app_dashboard .card-deal .table thead > tr th,
    .app_dashboard .card-deal .table thead > tr td,
    .card-deal .table tbody > tr th,
    .card-deal .table tbody > tr td {
        white-space: nowrap;
    }
    .app_dashboard .card-deal .table thead > tr th:first-child,
    .app_dashboard .card-deal .table thead > tr td:first-child,
    .card-deal .table tbody > tr th:first-child,
    .card-deal .table tbody > tr td:first-child {
        padding-left: 20px;
    }
    .app_dashboard .card-deal .table thead > tr th:last-child,
    .app_dashboard .card-deal .table thead > tr td:last-child,
    .card-deal .table tbody > tr th:last-child,
    .card-deal .table tbody > tr td:last-child {
        padding-right: 20px;
    }
    .app_dashboard .card-deal .table thead > tr {
        background-color: #f9f9fd;
    }
    .app_dashboard .card-deal .table thead > tr th,
    .app_dashboard .card-deal .table thead > tr td {
        font-size: 12px;
        text-align: right;
    }
    .app_dashboard .card-deal .table thead > tr th:first-child,
    .app_dashboard .card-deal .table thead > tr td:first-child {
        text-align: left;
    }
    .app_dashboard .card-deal .table tbody > tr th,
    .app_dashboard .card-deal .table tbody > tr td {
        text-align: right;
        font-size: 13px;
        color: #575d78;
        vertical-align: middle;
    }
    .app_dashboard .card-deal .table tbody > tr th:first-child,
    .app_dashboard .card-deal .table tbody > tr td:first-child {
        text-align: left;
        color: #373857;
    }
    .app_dashboard .card-deal .avatar,
    .card-transactions .avatar {
        flex-shrink: 0;
    }
    .app_dashboard .card-deal .card-footer,
    .card-transactions .card-footer {
        display: flex;
        align-items: center;
        padding: 12px 20px;
    }
    .app_dashboard .card-deal .card-footer a,
    .app_dashboard .card-transactions .card-footer a {
        display: block;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-deal .card-footer a .icon,
    .card-transactions .card-footer a .icon {
        font-size: 9px;
        margin-top: 1px;
    }
    .app_dashboard .card-deal .card-footer a .icon:first-of-type,
    .card-transactions .card-footer a .icon:first-of-type {
        margin-left: 5px;
    }
    .app_dashboard .card-transactions .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-transactions .avatar svg {
        width: 12px;
        height: 12px;
        stroke-width: 2.5px; }
    .app_dashboard 
    .card-transactions .list-group-item {
        border-style: dashed;
        padding: 12px 15px;
        display: flex;
    }
    .app_dashboard .card-transactions .list-group-item h6 {
        font-weight: 600;
        margin-bottom: 3px;
        font-size: 12px;
    }
    .app_dashboard .card-transactions .list-group-item h6,
    .card-transactions .list-group-item p {
        display: block;
        text-transform: lowercase;
        line-height: normal;
    }
    .app_dashboard .card-transactions .list-group-item h6::first-letter,
    .card-transactions .list-group-item p::first-letter {
        text-transform: uppercase;
    }
    .app_dashboard .card-transactions .list-group-item:last-child {
        border-bottom-width: 0;
        margin-bottom: 0;
    }
    .app_dashboard .card-connection-one .person-name a {
        margin-bottom: 2px;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
    }
    .app_dashboard .card-transactions .list-group-item small {
        display: flex;
        align-items: center;
        color: rgba(55, 56, 87, 0.6);
        line-height: normal;
    }
    .app_dashboard .card-transactions .card-footer {
        justify-content: center;
    }
    .app_dashboard .card-sale-location .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-sale-location .card-header .nav {
        line-height: 0;
        margin-right: -5px;
    }
    .app_dashboard .card-sale-location .card-body {
        padding: 15px;
        display: flex;
        flex-direction: column;
    }
    .app_dashboard .card-sale-location .vmap-wrapper {
        height: 150px;
    }
    .app_dashboard .card-sale-location .vmap {
        width: 100%;
        height: 100%;
    }
    .app_dashboard .card-sale-location .list-group {
        margin-bottom: 10px;
    }
    .app_dashboard .card-sale-location .list-group-item {
        padding: 8px 0;
        display: flex;
        align-items: center;
        box-shadow: none;
        font-size: 0.8125rem;
    }
    .app_dashboard .card-sale-location .list-group-item span:first-child {
        width: 8px;
        height: 8px;
        margin-right: 5px;
        opacity: .7;
    }
    .app_dashboard .card-sale-location .list-group-item span:last-child {
        display: block;
        margin-left: auto;
        color: #06072d;
    }
    .app_dashboard .card-total-sales .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-total-sales .total-sales-info {
        display: flex;
    }
    .app_dashboard .card-total-sales .total-sales-info > div + div {
        margin-left: 15px;
    }
    .app_dashboard .card-total-sales .total-sales-info label {
        margin-bottom: 5px;
        font-size: 9px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        font-weight: 500;
        letter-spacing: .5px;
        color: #575d78;
        text-transform: uppercase;
    }
    .app_dashboard .card-total-sales .total-sales-info h5 {
        font-family: "Oswald", sans-serif;
        color: #06072d;
        font-size: 14px;
        font-weight: 400;
    }
    .app_dashboard .card-total-sales .total-sales-info h5 span {
        font-size: 11px;
        font-weight: 400;
        color: #575d78;
        text-transform: uppercase;
        margin-left: 2px;
    }
    .app_dashboard .card-total-sales .flot-wrapper {
        position: relative;
    }
    .app_dashboard .card-total-sales .flot-wrapper .chart-legend {
        background-color: #fff;
        padding: 0 5px;
        position: absolute;
        top: 0;
        right: 0;
        z-index: 5;
    }
    .app_dashboard .card-total-sales .flot-chart {
        height: 225px;
    }
    .app_dashboard .card-total-sales .flot-chart .flot-y-axis .flot-tick-label {
        font-size: 9px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        letter-spacing: .2px;
        color: #575d78;
    }
    .app_dashboard .card-total-sales .flot-chart .flot-x-axis .flot-tick-label {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 500;
        color: #575d78;
        transform: translateY(-5px);
    }
    .app_dashboard .card-chart-five .row > div:first-child {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .app_dashboard .card-chart-five .flot-wrapper {
        width: 100%;
        height: 100px;
        position: relative;
        margin-bottom: -2px;
        margin-right: -5px;
    }
    .app_dashboard .card-chart-five .flot-chart {
        width: 100%;
        height: 100%;
    }
    .app_dashboard .card-chart-five .flot-chart .flot-y-axis .flot-tick-label {
        color: rgba(55, 56, 87, 0.6);
        font-size: 8px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
    }
    .app_dashboard .card-chart-five .flot-chart .flot-x-axis .flot-tick-label {
        color: #575d78;
        font-size: 8px;
        font-weight: 500;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        text-transform: uppercase;
    }
    .app_dashboard .card-profile-visits {
        position: relative;
        padding: 15px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-profile-visits .flot-wrapper {
        position: relative;
        margin: -5px;
    }
    .app_dashboard .card-profile-visits .flot-chart {
        width: 120px;
        height: 60px;
    }
    .app_dashboard .card-customer-score .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-customer-score .card-value {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        font-size: 40px;
        color: #06072d;
        letter-spacing: -1px;
    }
    .app_dashboard .card-customer-score .card-value small {
        font-size: 16px;
        font-weight: 400;
        font-family: "Roboto", sans-serif;
    }
    .app_dashboard .card-customer-score .table-card {
        width: 100%;
    }
    .app_dashboard .card-customer-score .table-card tbody > tr + tr {
        border-top: 1px dashed rgba(221, 222, 233, 0.85);
    }
    .app_dashboard .card-customer-score .table-card tbody > tr td {
        font-size: 0.8125rem;
        padding: 6px 0;
    }
    .app_dashboard .card-customer-score .table-card tbody > tr td:first-child div {
        width: 10px;
        height: 10px;
        border-width: 2.5px;
        border-style: solid;
    }
    .app_dashboard .card-customer-score .table-card tbody > tr td:nth-child(3) {
        font-weight: 500;
    }
    .app_dashboard .card-customer-score .table-card tbody > tr td:nth-child(4) {
        color: #575d78;
    }
    .app_dashboard .card-customer-score .table-card tbody > tr td:nth-child(3), .card-customer-score .table-card tbody > tr td:nth-child(4) {
        font-family: "Helvetica Neue", Helvetica, sans-serif;
    }
    .app_dashboard .card-customer-score .table-card tbody > tr:last-child td {
        padding-bottom: 0;
    }
    .app_dashboard .card-cash-flow .card-value {
        color: #06072d;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        font-size: 18px;
        margin-bottom: 2px;
    }
    .app_dashboard .card-cash-flow .card-value small {
        font-size: 12px;
        font-weight: 300;
        color: rgba(55, 56, 87, 0.6);
        text-transform: uppercase;
        margin-left: 2px;
    }
    .app_dashboard .card-cash-flow .flot-chart {
        height: 180px;
    }
    .app_dashboard .card-cash-flow .flot-chart .flot-y-axis .flot-tick-label {
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        font-size: 9px;
        font-weight: 500;
        color: #575d78;
    }
    .app_dashboard .card-cash-flow .flot-chart .flot-x-axis .flot-tick-label {
        font-size: 9px;
        font-weight: 500;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-pie-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-pie-one .chart-wrapper {
        height: 180px;
    }
    .app_dashboard .card-pie-one .card-body h4 {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
    }
    .app_dashboard .card-pie-one .card-body h4 small {
        font-size: 14px;
        font-weight: 300;
        letter-spacing: normal;
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-pie-one .progress {
        height: 3px;
    }
    .app_dashboard .card-pie-two .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        padding-bottom: 16px;
    }
    .app_dashboard .card-pie-two .card-body {
        padding-top: 15px;
        padding-bottom: 16px;
    }
    .app_dashboard .card-pie-two .card-value {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        margin-bottom: 3px;
    }
    .app_dashboard .card-pie-two .card-value span {
        font-weight: 300;
        font-size: 20px;
        color: rgba(55, 56, 87, 0.6);
        margin-right: 2px;
    }
    .app_dashboard .card-pie-two .list-unstyled li {
        padding: 5px 0;
    }
    .app_dashboard .card-pie-two .list-unstyled li:first-child {
        padding-top: 0;
    }
    .app_dashboard .card-pie-two .list-unstyled li:last-child {
        padding-bottom: 0;
    }
    .app_dashboard .card-pie-two .list-unstyled li h6 {
        font-weight: 400;
    }
    .app_dashboard .card-pie-two .list-unstyled li h6 span {
        width: 6px;
        height: 6px;
        display: inline-block;
        border-radius: 100%;
        margin-bottom: 2px;
    }
    .app_dashboard .card-pie-two .chart-wrapper {
        height: 140px;
    }
    .app_dashboard .card-pie-two .list-group {
        margin-bottom: 13px;
    }
    .app_dashboard .card-pie-two .list-group-item {
        padding: 8px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: none;
    }
    .app_dashboard .card-pie-two .list-group-item h6 {
        margin-bottom: 3px;
        font-size: 13px;
        color: #373857;
    }
    .app_dashboard .card-pie-two .list-group-item span {
        display: block;
        font-size: 11px;
    }
    .app_dashboard .card-pie-two .list-group-item .badge {
        font-size: 9px;
        font-weight: 400;
        padding: 1px 4px 2px;
    }
    .app_dashboard .card-pie-two .list-group-item > div:first-child h6 {
        font-weight: 500;
        margin-bottom: 2px;
    }
    .app_dashboard .card-pie-two .list-group-item > div:first-child span {
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-pie-two .list-group-item > div:last-child h6 {
        font-family: "Helvetica Neue", Helvetica, sans-serif;
    }
    .app_dashboard .card-overall-rating .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
    }
    .app_dashboard .card-overall-rating .stars {
        color: #c4cdd6;
        font-size: 15px;
        line-height: 1.4;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-overall-rating .stars .icon + .icon {
        margin-left: 1px;
    }
    .app_dashboard .card-overall-rating .stars .active {
        color: #fd7e14;
    }
    .app_dashboard .card-overall-rating .card-value {
        line-height: 1;
        margin-bottom: 0;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
    }
    .app_dashboard .card-overall-rating .list-group-item {
        padding: 3px 0;
        display: flex;
        align-items: center;
        font-size: 12px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
    }
    .app_dashboard .card-overall-rating .list-group-item > div:first-child {
        width: 30px;
        font-weight: 500;
    }
    .app_dashboard .card-overall-rating .list-group-item .stars {
        font-size: 13px;
    }
    .app_dashboard .card-overall-rating .list-group-item > div:nth-child(3) {
        font-weight: 500;
        width: 40px;
        margin-left: auto;
        text-align: right;
    }
    .app_dashboard .card-overall-rating .list-group-item > div:last-child {
        width: 40px;
        text-align: right;
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-overall-rating .review-item .stars {
        font-size: 13px;
    }
    .app_dashboard .card-task-one .card-body {
        padding: 20px;
    }
    .app_dashboard .card-task-one .card-title {
        font-size: 13px;
        font-weight: 500;
        color: #06072d;
        margin-bottom: 10px;
    }.card-social-one .card-value,
    .card-task-one .card-value {
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        font-size: 32px;
        display: flex;
        align-items: baseline;
        letter-spacing: -1px;
        margin-bottom: 0;
    }
    .app_dashboard .card-social-one .card-value span,
    .card-task-one .card-value span {
        font-size: 13px;
        letter-spacing: normal;
        display: flex;
        align-items: baseline;
        margin-left: 2px;
    }
    .app_dashboard .card-task-one .card-value .icon {
        font-size: 15px;
        font-weight: bold;
        margin-right: 2px;
    }
    .app_dashboard .card-task-one .chart-wrapper {
        width: 80px;
        position: relative;
    }
    .app_dashboard .card-task-one .flot-chart {
        height: 40px;
        margin-right: -5px;
        margin-left: -5px;
    }
    .app_dashboard .card-task-one .card-desc {
        font-size: 10px;
        color: #919fac;
        margin-bottom: 0;
    }
    .app_dashboard .project-logo {
        width: 48px;
        height: 48px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .app_dashboard .project-date-end {
        font-size: 10px;
        font-weight: 400;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        background-color: #eaeaf8;
        color: rgba(55, 56, 87, 0.6);
        padding: 4px 8px 5px;
        width: 125px;
        text-align: center;
    }
    .app_dashboard .project-data-group {
        display: flex;
        align-items: center;
        margin-bottom: 0 !important;
    }
    .app_dashboard .project-data-group,
    .event-box {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .khantask .mdata .btn-primary{
        max-width: 220px !important;
        width: inherit;
    }
    .khantask .mdata {
        border-left: 3px solid #ff9800;
    }

    .khantask .taskdesc{
        border:none !important;
    }
    .app_dashboard .project-data-group:last-child,
    .event-box:last-child {
        margin-bottom: 0;
    }
    .app_dashboard .project-data-group > div + div {

    }
    .app_dashboard .vertical-seperator {
        width: 3px;
        min-width: 3px;
        margin-left: 15px;
        margin-right: 15px;
        display: list-item;
        min-height: 35px;
    }
    .app_dashboard .card-event-calendar .btn-primary {
        width: 100%;
        background:  var(--root-color1)!important;
    }
    .app_dashboard .card-event-calendar .taskdesc {
        /* width: 260px; */
        border-left: 3px solid #ff9800;
        padding-left: 15px;
        margin-left: 15px;
        padding-top: 5px;
        padding-bottom: 5px;
    }
    .app_dashboard .calendar-date.project-data-group svg {
        color: #c0c0c0 !important;
    }
    .app_dashboard .card-event-calendar .taskdesc.app-req {
        border-color: var(--root-color1);
    }
    .app_dashboard .yellownews<?php echo $this->uniqId ?> {
        height: 166px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .app_dashboard .event-tab-content {
        height: 473px;
        overflow-y: auto;
        padding-bottom: 20px;
        overflow-x: hidden;
    }
    .app_dashboard .event-tab-content.without-three-tab {
        height: 529px;
    }
    .app_dashboard .eventj-icon {
        position: relative;
        /* margin-left: 5px;
        bottom: -15px; */
    }
    .app_dashboard .event-tab-footer {
        background: #d8ddff;
        padding: 15px;
    }
    .app_dashboard .project-data-group h3,
    .app_dashboard .event-date-time {
        font-size: 14px;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #331cbf;
        margin-bottom: 3px;
    }
    .app_dashboard .event-date-time {
        font-size: 12px;
    }
    .app_dashboard .project-data-group h3 span {
        display: inline-block;
        letter-spacing: normal;
    }
    .app_dashboard .project-data-group label {
        font-size: 12px;
        color: #575d78;
        margin-bottom: 0;
    }
    .app_dashboard .table-card tbody > tr td:first-child div {
        width: 10px;
        height: 10px;
        border-width: 2.5px;
        border-style: solid;
    }
    .app_dashboard .card-progress {
        display: flex;
        align-items: center;
        margin-bottom: 17px;
    }
    .app_dashboard .card-progress .content-label {
        font-size: 9px;
        font-weight: 500;
        color: #919fac;
    }
    .app_dashboard .card-progress .progress {
        height: 3px;
        flex: 1;
        margin: 0 5px;
    }
    .app_dashboard .card-project-pink {
        background-color: #fef9fb;
        border-color: rgba(232, 62, 140, 0.5);
    }
    .app_dashboard .card-project-pink {
        border: 1px solid #ffc8e1 !important;
    }
    .app_dashboard .card-project-pink.card-hover:hover,
    .app_dashboard .card-project-pink.card-hover:focus {
        border-color: rgba(232, 62, 140, 0.8);
    }
    .app_dashboard .card-project-pink .content-label {
        color: rgba(232, 62, 140, 0.75);
        font-weight: 500;
    }.card-project-pink .card-title a {
        color: #06072d;
        line-height: normal;
    }
    .app_dashboard .card-project-pink .card-title a:hover,
    .app_dashboard .card-project-pink .card-title a:focus {
        color: #e83e8c;
    }
    .app_dashboard .card-project-pink .progress {
        background-color: rgba(232, 62, 140, 0.1);
    }
    .app_dashboard .card-project-pink .avatar img,
    .card-project-pink .avatar-initial {
        box-shadow: 0 0 0 1px #fef9fb;
    }
    .app_dashboard .card-project-pink .avatar-add {
        border-color: #e83e8c;
        color: #e83e8c;
        background-color: rgba(232, 62, 140, 0.1);
    }
    .app_dashboard .card-project-pink .avatar-add:hover,
    .app_dashboard .card-project-pink .avatar-add:focus {
        border-color: #e83e8c;
        background-color: rgba(232, 62, 140, 0.3);
        color: #e83e8c;
    }
    .app_dashboard .card-project-green {
        box-shadow: none;
        border-color: #aeeecc;
        background-color: #f6fdf9;
    }
    .app_dashboard .card-project-green .poll_bottom_btn .btn {
        background-color: #aeeecc !important;
        border: 0;
        color: rgba(0,0,0,0.5) !important;
    }
    .app_dashboard .card-three-col .three-card-col {
        border-right: 2px dotted #e0e0e0;
    }
    .app_dashboard .card-three-col .three-card-col:last-child {
        border-right: 0;
    }
    .app_dashboard .card-project-green .avatar img,
    .card-project-green .avatar-initial {
        box-shadow: none;
    }
    .app_dashboard .card-todo .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-todo .list-group-item {
        padding: 8px 15px 15px;
        box-shadow: none;
    }
    .app_dashboard .card-todo .list-group-header {
        display: flex;
        align-items: center;
        margin-bottom: 2px;
    }
    .app_dashboard .card-todo .list-group-header span {
        display: block;
        width: 15px;
        height: 3px;
    }
    .app_dashboard .card-todo .list-group-header span + span {
        margin-left: 3px;
    }
    .app_dashboard .card-todo .list-group-header a {
        display: block;
        line-height: 1;
    }
    .app_dashboard .card-todo .todo-date {
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        font-size: 11px;
        color: rgba(55, 56, 87, 0.6);
        margin-bottom: 15px;
    }
    .app_dashboard .card-todo .progress {
        background-color: #dddee9;
    }
    .app_dashboard .card-todo .badge {
        font-size: 9px;
        font-weight: 400;
    }
    .app_dashboard .card-todo .card-footer {
        padding: 15px;
    }
    .app_dashboard .card-todo .card-footer .btn,
    .app_dashboard .card-todo .card-footer .ui-datepicker-buttonpane button,
    .app_dashboard .ui-datepicker-buttonpane .card-todo .card-footer button,
    .app_dashboard .card-todo .card-footer .sp-container button,
    .app_dashboard .sp-container .card-todo .card-footer button {
        border-style: dashed;
        border-color: #c4cdd6;
        font-size: 12px;
        font-weight: 500;
        transition: none;
    }
    .app_dashboard .card-todo .card-footer .btn:hover,
    .app_dashboard .card-todo .card-footer .ui-datepicker-buttonpane button:hover,
    .app_dashboard .ui-datepicker-buttonpane .card-todo .card-footer button:hover,
    .app_dashboard .card-todo .card-footer .sp-container button:hover,
    .app_dashboard .sp-container .card-todo .card-footer button:hover,
    .app_dashboard .card-todo .card-footer .btn:focus,
    .app_dashboard .card-todo .card-footer .ui-datepicker-buttonpane button:focus,
    .app_dashboard .ui-datepicker-buttonpane .card-todo .card-footer button:focus,
    .app_dashboard .card-todo .card-footer .sp-container button:focus,
    .app_dashboard .sp-container .card-todo .card-footer button:focus {
        background-color: rgba(85, 86, 253, 0.1);
        border-color: #5556fd;
        border-style: solid;
        color: #5556fd;
    }
    .app_dashboard .card-projects .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-projects .list-group-item {
        padding: 18px 20px 20px;
        position: relative;
    }
    .app_dashboard .card-projects .list-group-item .media {
        margin-bottom: 10px;
    }
    .app_dashboard .card-projects .list-group-item .project-desc {
        color: #575d78;
        font-size: 13px;
        margin-bottom: 16px;
    }
    .app_dashboard .card-projects .list-group-item .nav {
        position: absolute;
        top: 10px;
        right: 12px;
    }
    .app_dashboard .card-projects .list-group-item h5 {
        font-size: 16px;
    }
    .app_dashboard .card-projects .project-deadline {
        display: inline-block;
        color: rgba(55, 56, 87, 0.6);
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        font-size: 11px;
        background-color: #eeeef9;
        padding: 3px 8px;
    }
    .app_dashboard .card-active-projects .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-active-projects .nav {
        font-size: 13px;
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-active-projects .nav a {
        color: #575d78;
    }
    .app_dashboard .card-active-projects .nav a:hover,
    .app_dashboard .card-active-projects .nav a:focus {
        color: #373857;
    }
    .app_dashboard .card-active-projects .nav a strong {
        font-weight: 500;
    }
    .app_dashboard .card-active-projects .list-group-item {
        display: flex;
        flex-wrap: wrap;
        padding: 15px;
    }
    .app_dashboard .card-active-projects .list-group-item:nth-child(2) {
        border-top-color: transparent;
    }
    .app_dashboard .card-active-projects .list-group-item:nth-child(odd) {
        border-right: 1px solid rgba(230, 231, 239, 0.85); }
    .app_dashboard
    .card-active-projects .list-group-item > div:last-child {
        order: 1;
        width: 10%;
        text-align: right;
    }
    .app_dashboard .card-active-projects .media {
        order: 0;
        width: 90%;
        flex-direction: column;
    }
    .app_dashboard .card-active-projects .project-img {
        width: 80px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .app_dashboard .card-active-projects .project-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
    }
    .app_dashboard .card-active-projects .project-title {
        font-size: 16px;
        margin-bottom: 5px;
    }
    .app_dashboard .card-active-projects .project-title a {
        color: #06072d;
    }
    .app_dashboard .card-active-projects .project-title a:hover,
    .app_dashboard .card-active-projects .project-title a:focus {
        color: #06072d;
    }
    .app_dashboard .card-active-projects .progress-wrapper {
        order: 2;
        width: 100%;
    }
    .app_dashboard .card-active-projects .progress-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 11px;
    }
    .app_dashboard .card-active-projects .progress-label span:first-child {
        color: #575d78;
    }
    .app_dashboard .card-active-projects .progress-label span:last-child {
        color: #373857;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        font-size: 10px;
        font-weight: 600;
    }
    .app_dashboard .card-active-projects .card-progress {
        margin-bottom: 18px;
    }
    .app_dashboard .card-active-projects .progress {
        height: 5px;
        margin-bottom: 15px;
        background-color: #e1e3ec;
    }
    .app_dashboard .card-active-projects .avatar-group {
        order: 4;
        margin-left: auto;
    }
    .app_dashboard .card-active-projects .project-date-end {
        order: 3;
    }
    .app_dashboard .card-project-two .card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 20px 20px 0;
        border-bottom: 0;
    }
    .app_dashboard .card-project-two .card-header h6 {
        color: #06072d;
    }
    .app_dashboard .card-project-two .card-header span {
        font-size: 12px;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        color: #637382;
        display: block;
    }
    .app_dashboard .card-project-two .card-header .nav {
        align-items: center;
        font-size: 12px;
    }
    .app_dashboard .card-project-two .card-header .nav a {
        color: #373857;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        font-weight: 500;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-project-two .card-header .nav a + a {
        margin-left: 10px;
    }
    .app_dashboard .card-project-two .card-header .nav svg {
        margin-right: 4px;
        color: rgba(55, 56, 87, 0.6);
        fill: none;
    }
    .app_dashboard .card-project-two .card-progress {
        align-items: center;
        margin-bottom: 0;
    }
    .app_dashboard .card-project-two .card-progress .progress {
        height: 5px;
        background-color: rgba(230, 231, 239, 0.85);
    }
    .app_dashboard .card-project-two .card-progress .content-label:last-child {
        color: #575d78;
        font-weight: 700;
        letter-spacing: normal;
    }
    .app_dashboard .card-project-two .card-footer {
        padding: 0 20px 20px;
        border-top-width: 0;
        display: flex;
        align-items: stretch;
    }
    .app_dashboard .card-project-two .card-footer .badge {
        font-weight: 400;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        padding: 7px 8px;
    }
    .app_dashboard .card-project-two .project-date-end {
        width: auto;
        margin-left: 4px;
    }
    .app_dashboard .card-project-two .avatar-group {
        margin-left: auto;
        align-self: center;
    }
    .app_dashboard .card-project-three .content-label {
        margin-bottom: 10px;
    }
    .app_dashboard .card-project-three .project-title {
        font-size: 18px;
    }
    .app_dashboard .card-project-three .project-title a {
        color: #06072d;
    }
    .app_dashboard .card-project-three .project-title a:hover,
    .app_dashboard .card-project-three .project-title a:focus {
        color: #373857;
    }
    .app_dashboard .card-calendar-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-calendar-one .ui-datepicker {
        background-color: transparent;
        padding: 0;
        border-width: 0;
        max-width: none;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-header .ui-datepicker-prev,
    .card-calendar-one .ui-datepicker .ui-datepicker-header .ui-datepicker-next {
        top: -1px;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-title {
        font-family: "Oswald", sans-serif;
        letter-spacing: .5px;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-title .ui-datepicker-month {
        font-weight: normal;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-title .ui-datepicker-year {
        font-weight: 500;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-calendar {
        width: 100%;
        height: 200px;
        font-family: "Oswald", sans-serif;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-calendar th {
        font-size: 10px;
        font-weight: 500;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-calendar td {
        background-color: transparent;
        border-color: transparent;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-calendar td a {
        display: block;
        width: 30px;
        height: 30px;
        white-space: nowrap;
        text-align: center;
        border-radius: 100%;
    }
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-calendar td a:hover,
    .app_dashboard .card-calendar-one .ui-datepicker .ui-datepicker-calendar td a:focus {
        background-color: #e6e7ef;
    }
    .app_dashboard .card-calendar-one ul li {
        padding: 15px 0 17px;
    }
    .app_dashboard .card-calendar-one ul li + li {
        border-top: 1px dashed rgba(230, 231, 239, 0.85);
    }
    .app_dashboard .card-calendar-one ul li:first-child {
        padding-top: 0;
    }
    .app_dashboard .card-calendar-one ul li:last-child {
        padding-bottom: 0;
    }
    .app_dashboard .card-calendar-one ul h6 a {
        color: #373857;
    }
    .app_dashboard .card-calendar-one ul h6 a:hover,
    .app_dashboard .card-calendar-one ul h6 a:focus {
        color: #06072d;
    }
    .app_dashboard .card-calendar-one ul h6 span {
        width: 7px;
        height: 7px;
        display: inline-block;
        margin-left: 2px;
        margin-bottom: 1px;
        border-radius: 100%;
    }
    .app_dashboard .card-event-one .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .app_dashboard .card-event-one .event-logo {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100%;
    }
    .app_dashboard .card-event-one .event-title {
        font-size: 16px;
        font-weight: 500;
    }
    .app_dashboard .card-event-one .event-title a {
        color: #06072d;
    }
    .app_dashboard .card-event-one .event-title a:hover,
    .app_dashboard .card-event-one .event-title a:focus {
        color: #373857;
    }
    .app_dashboard .card-event-one .event-desc {
        font-size: 13px;
        color: #575d78;
        text-align: center;
        margin-bottom: 17px;
    }
    .app_dashboard .card-contact-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-contact-one .card-body {
        padding: 15px 10px 15px 15px;
    }
    .app_dashboard .card-contact-one .list-group-item {
        margin-right: 3px;
        padding: 12px 0;
        box-shadow: none;
        display: flex;
    }
    .app_dashboard .card-contact-one .list-group-item:first-child {
        padding-top: 0;
    }
    .app_dashboard .card-contact-one .list-group-item:last-child {
        padding-bottom: 0;
    }
    .app_dashboard .card-contact-one .list-group-item .nav {
        align-items: center;
        align-self: center;
    }
    .app_dashboard .card-contact-one .list-group-item .nav a {
        color: #919fac;
        margin-left: 5px;
    }
    .app_dashboard .card-contact-one .list-group-item .nav a:hover,
    .app_dashboard .card-contact-one .list-group-item .nav a:focus {
        color: #06072d;
    }
    .app_dashboard .card-contact-one .list-group-item .nav a:first-child {
        margin-left: 0;
    }
    .app_dashboard .card-contact-one .list-group-item .nav a:last-child {
        margin-left: 2px;
    }
    .app_dashboard .card-contact-one .list-body {
        align-self: center;
        flex: 1;
        margin-left: 10px;
    }
    .app_dashboard .card-contact-one .list-body h6 {
        color: #06072d;
        font-size: 13px;
        margin-bottom: 2px;
    }
    .app_dashboard .card-contact-one .list-body p {
        font-size: 11px;
        font-weight: 300;
        font-family: "Oswald", sans-serif;
        margin-bottom: 0;
        color: #575d78;
    }
    .app_dashboard .card-contact-one .card-footer {
        padding: 12px 15px;
        font-size: 12px;
        text-align: center;
    }
    .app_dashboard .card-prompt .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-prompt .card-body {
        padding: 16px 15px;
    }
    .app_dashboard .card-prompt .media + .media {
        margin-top: 10px;
    }
    .app_dashboard .card-prompt .media-left {
        font-family: "Oswald", sans-serif;
        text-align: center;
        width: 16px;
        white-space: nowrap;
    }
    .app_dashboard .card-prompt .media-left label {
        display: block;
        margin-bottom: 0;
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 400;
        color: #575d78;
        text-align: center;
    }
    .app_dashboard .card-prompt .media-left p {
        text-align: center;
        font-size: 15px;
        font-family: "Oswald", sans-serif;
        margin-bottom: 0;
        line-height: 1;
    }
    .app_dashboard .card-prompt .media-body {
        margin-left: 10px;
        padding: 5px 10px;
        border-left: 3px solid transparent;
    }
    .app_dashboard .card-prompt .event-time {
        font-size: 12px;
        /* font-family: "Oswald", sans-serif; */
        margin-bottom: 0;
        font-weight: bold;
    }
    .app_dashboard .card-prompt .event-title {
        margin-bottom: 0;
    }
    .app_dashboard .card-prompt .event-desc {
        font-size: 11px;
        margin-top: 10px;
        margin-bottom: 0;
        color: rgba(6, 7, 45, 0.7);
    }
    .app_dashboard .card-prompt .event-panel-green {
        border-left-color: #22d273;
        background-color: rgba(34, 210, 115, 0.1);
    }
    .app_dashboard .card-prompt .event-panel-green .event-time {
        color: rgba(13, 78, 43, 0.7);
    }
    .app_dashboard .card-prompt .event-panel-green .event-title {
        color: #1ba65b;
    }
    .app_dashboard .card-prompt .event-panel-pink {
        border-left-color: #e83e8c;
        background-color: rgba(232, 62, 140, 0.1);
    }
    .app_dashboard .card-prompt .event-panel-pink .event-time {
        color: rgba(126, 15, 66, 0.7);
    }
    .app_dashboard .card-prompt .event-panel-pink .event-title {
        color: #d91a72;
    }
    .app_dashboard .card-prompt .event-panel-primary {
        border-left-color: #5556fd;
        background-color: rgba(85, 86, 253, 0.1);
    }
    .app_dashboard .card-prompt .event-panel-primary .event-time {
        color: rgba(1, 1, 82, 0.7);
    }
    .app_dashboard .card-prompt .event-panel-primary .event-time:hover {
        cursor: pointer;
    }
    .app_dashboard .card-prompt .event-panel-primary .event-title {
        color: #5556fd;
    }
    .app_dashboard .card-prompt .event-panel-orange {
        border-left-color: #fd7e14;
        background-color: rgba(253, 126, 20, 0.1);
    }
    .app_dashboard .card-prompt .event-panel-orange .event-time {
        color: rgba(18, 8, 0, 0.7);
    }
    .app_dashboard .card-prompt .event-panel-orange .event-title {
        color: #fd7e14;
    }
    .app_dashboard .card-prompt .card-footer {
        padding: 12px 15px;
        font-size: 12px;
        text-align: center;
    }
    .app_dashboard .card-notification-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-notification-one .card-body {
        padding: 0;
    }
    .app_dashboard .card-notification-one .list-group-item {
        padding: 15px 15px 16px;
        box-shadow: none;
        display: flex;
    }
    .app_dashboard .card-notification-one .list-group-item .nav {
        margin-right: -3px;
    }
    .app_dashboard .card-notification-one .list-group-item .nav a {
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-notification-one .list-group-item .nav a:hover,
    .app_dashboard .card-notification-one .list-group-item .nav a:focus {
        color: #373857;
    }
    .app_dashboard .card-notification-one .list-group-item .nav a + a {
        margin-left: 2px;
    }
    .app_dashboard .card-notification-one .list-group-item .nav a svg {
        width: 14px;
        height: 14px;
    }
    .app_dashboard .card-notification-one .list-body {
        line-height: 1.48;
        font-size: 13px;
        flex: 1;
        margin-left: 12px;
        margin-right: 10px;
        color: #575d78;
    }
    .app_dashboard .card-notification-one .list-body strong {
        color: #06072d;
    }
    .app_dashboard .card-notification-one .list-body span {
        color: rgba(55, 56, 87, 0.6);
        font-size: 11px;
    }
    .app_dashboard .card-notification-one .card-footer {
        font-size: 12px;
        text-align: center;
    }
    .app_dashboard .card-profile-sidebar .card-header.with-border-bottom {
        border-bottom-width: 1px;
    }
    .app_dashboard .card-profile-sidebar.card-forum .card-header {
        border-bottom: 1px solid #eaeaf2;
    }
    .app_dashboard .card-profile-sidebar .media {
        margin-bottom: 10px;
    }
    .app_dashboard .card-profile-sidebar .profile-info {
        display: flex;
    }
    .app_dashboard .card-profile-sidebar .profile-info > div + div {
        margin-left: 0;
        position: relative;
    }
    .app_dashboard .card-profile-sidebar .profile-info h5 {
        font-size: 18px;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        margin-bottom: 2px;
    }
    .app_dashboard .card-profile-sidebar .profile-info p {
        font-weight: 500;
        line-height: normal;
        color: #575d78;
        margin-bottom: 0;
    }
    .app_dashboard .card-social-one .card-body {
        padding-top: 10px;
    }
    .app_dashboard .card-social-one .card-info {
        position: absolute;
        top: 5px;
        right: 10px;
        color: #c4cdd6;
    }
    .app_dashboard .card-social-one .card-info:hover,
    .app_dashboard .card-social-one .card-info:focus {
        color: #454f5b;
    }
    .app_dashboard .card-social-one .card-value {
        font-family: "Oswald", sans-serif;
        font-size: 22px;
        color: #555;
        font-weight: 400;
        margin-bottom: 0;
        letter-spacing: -1px;
        margin-top: -5px;
    }
    .app_dashboard .card-social-one .card-value span {
        font-weight: 300;
    }
    .app_dashboard .card-social-one .chart-wrapper {
        position: relative;
        width: 60px;
        height: 35px;
        margin-right: -7px;
    }
    .app_dashboard .card-social-one .flot-chart {
        width: 100%;
        height: 40px;
    }
    .app_dashboard .card-social-one .card-title {
        font-size: 12px;
        font-weight: 700;
        font-family: "Fira Sans", sans-serif;
        text-transform: uppercase;
        line-height: normal;
        letter-spacing: .5px;
        margin-bottom: 0;
    }
    .app_dashboard .card-social-one .card-desc {
        font-size: 11px;
        margin-bottom: 0;
        color: #637382;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .app_dashboard .card .card-header h6 {
        font-family: "Oswald", sans-serif;
    }
    .app_dashboard .card-social-two .card-header {
        background-color: transparent;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 15px;
        padding-left: 15px;
        padding-right: 15px;
        padding-bottom: 0;
        border-bottom-width: 0;
    }
    .app_dashboard .card-social-two .card-body {
        position: relative;
        overflow: hidden;
    }
    .app_dashboard .card-social-two .chart-wrapper {
        position: relative;
    }
    .app_dashboard .card-social-two .chart-wrapper .overlay-body {
        bottom: auto;
        z-index: 5;
        background-image: linear-gradient(to bottom, #fff 0%, rgba(255, 255, 255, 0) 100%);
        background-repeat: repeat-x;
    }
    .app_dashboard .card-social-two .card-value {
        color: #06072d;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        margin-bottom: 3px;
        margin-right: 5px;
    }
    .app_dashboard .card-social-two .card-percent {
        font-family: "Oswald", sans-serif;
        font-weight: 300;
    }
    .app_dashboard .card-social-two .flot-chart {
        height: 100px;
        margin-right: -20px;
        margin-left: -20px;
    }
    .app_dashboard .card-social-two .flot-chart .flot-x-axis .flot-tick-label {
        font-size: 8px;
        font-weight: 600;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        text-transform: uppercase;
        color: #637382;
    }
    .app_dashboard .card-social-two .flot-chart.chart-tick-blue .flot-x-axis .flot-tick-label {
        color: #06a3e0;
    }
    .app_dashboard .card-social-two .flot-chart.chart-tick-green .flot-x-axis .flot-tick-label {
        color: #17904f;
    }
    .app_dashboard .card-connection-one .card-header {
        background-color: transparent;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .app_dashboard .card-connection-one .card-body {
        padding: 0;
    }
    .app_dashboard .card-connection-one .card-title span {
        font-size: 13px;
        font-weight: 700;
        font-family: "Lato", sans-serif;
        color: rgba(55, 56, 87, 0.6);
        letter-spacing: -.5px;
        padding-left: 5px;
    }
    .app_dashboard .card-connection-one .list-group-item {
        box-shadow: none;
        display: flex;
        border-color: rgba(230, 231, 239, 0.85);
    }
    .app_dashboard .card-connection-one .list-body {
        margin-left: 12px;
        margin-right: 10px;
        flex: 1;
    }
    .app_dashboard .card-connection-one .person-name {
        font-weight: 500;
        margin-bottom: 2px;
        line-height: normal;
    }
    .app_dashboard .card-connection-one .person-name a {
        color: #06072d;
    }
    .app_dashboard .card-connection-one .person-name a:hover,
    .app_dashboard .card-connection-one .person-name a:focus {
        color: #575d78;
    }
    .app_dashboard .card-connection-one .person-location {
        font-size: 11px;
        color: rgba(55, 56, 87, 0.6);
        margin-bottom: 0;
        line-height: normal;
    }
    .app_dashboard .card-connection-one .person-more {
        align-self: center;
        margin-right: -5px;
        color: #c4cdd6;
    }
    .app_dashboard .card-connection-one .person-more:hover,
    .app_dashboard .card-connection-one .person-more:focus {
        color: #373857;
    }
    .app_dashboard .card-blog-one {
        position: relative;
    }
    .app_dashboard .card-blog-one .card-body {
        padding: 15px;
    }
    .app_dashboard .card-blog-one .card-img-wrapper {
        position: relative;
        margin: -1px -1px 0;
    }
    .app_dashboard .card-blog-one .card-img {
        height: 121px;
        object-fit: cover;
    }
    .app_dashboard .card-body {
        padding: 15px;
    }
    .app_dashboard .card-blog-one .badge {
        font-weight: normal;
        font-size: 8px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 5px 7px;
        margin-bottom: 18px;
    }
    .app_dashboard .card-blog-one .card-title {
        font-size: 14px;
        line-height: normal;
        /* margin-bottom: 15px; */
    }
    .app_dashboard .card-blog-one .card-title a {
        color: #06072d;
        line-height: normal;
    }
    .app_dashboard .card-blog-one .card-title a:hover,
    .app_dashboard .card-blog-one .card-title a:focus {
        color: #373857;
    }
    .app_dashboard .card-blog-one .card-desc {
        font-size: 13px;
        margin-bottom: 0;
        color: #637382;
        text-align: justify;
        line-height: 18px;
    }
    .app_dashboard .card-footer {
        background-color: transparent;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        font-size: 11px;
        color: #AAA;
    }
    .app_dashboard .card-footer a > .fa-heart-o {
        color: #999999;
    }
    .app_dashboard .card-footer a > .fa-heart {
        color: #CC0000;
    }
    .app_dashboard .card-three-col .row .col-4 .media-body > h6,
    .app_dashboard .card-three-col .row .col-4 .profile-info h5 > a > span,
    .app_dashboard .card-three-col .row .col-4 .profile-info p {
        /* color: #b10e59 !important; */
    }
    .app_dashboard .card-three-col .row .col-4:first-child .media-body > h6,
    .app_dashboard .card-three-col .row .col-4:first-child .profile-info h5 > a > span,
    .app_dashboard .card-three-col .row .col-4:first-child .profile-info p {
        /* color: #4175e1 !important; */
    }
    .app_dashboard .card-three-col .row .col-4:last-child .media-body > h6,
    .app_dashboard .card-three-col .row .col-4:last-child .profile-info h5 > a > span,
    .app_dashboard .card-three-col .row .col-4:last-child .profile-info p {
        /* color: #13aa59 !important; */
    }
    .app_dashboard .card-blog-one .card-footer a {
        font-family: "Lato", sans-serif;
        color: #637382;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-blog-one .card-footer a:hover,
    .app_dashboard .card-blog-one .card-footer a:focus {
        color: #5556fd;
    }
    .app_dashboard .card-blog-one .card-footer a + a {
        margin-right: 15px;
    }
    .app_dashboard .card-blog-one .card-footer svg {
        margin-right: 5px;
    }
    .app_dashboard .card-post-one .card-header {
        background-color: transparent;
        padding-top: 15px;
        padding-left: 15px;
        padding-bottom: 6px;
        border-bottom-width: 0;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-post-one .header-body {
        margin-left: 10px;
    }
    .app_dashboard .card-post-one .header-body h6 {
        color: #06072d;
        margin-bottom: 3px;
        font-size: 13px;
    }
    .app_dashboard .card-post-one .header-body p {
        margin-bottom: 0;
        font-size: 12px;
        color: #919fac;
    }
    .app_dashboard .card-post-one .card-body p {
        color: #575d78;
        font-size: 13px;
        margin-bottom: 0;
    }
    .app_dashboard .card-post-one .card-footer {
        background-color: transparent;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-size: 11px;
        font-family: "Lato", sans-serif;
    }
    .app_dashboard .card-post-one .card-footer a {
        color: #637382;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-post-one .card-footer a:hover,
    .app_dashboard .card-post-one .card-footer a:focus {
        color: #5556fd;
    }
    .app_dashboard .card-post-one .card-footer a:hover svg,
    .app_dashboard .card-post-one .card-footer a:focus svg {
        fill: rgba(85, 86, 253, 0.2);
    }
    .app_dashboard .card-post-one .card-footer a + a {
        margin-left: 20px;
    }
    .app_dashboard .card-post-one .card-footer svg {
        margin-right: 5px;
    }
    .app_dashboard .card-stories-one .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
    }
    .app_dashboard .card-stories-one .list-group-item {
        padding: 13px 15px;
        box-shadow: none;
        display: flex;
        border-color: rgba(230, 231, 239, 0.85);
    }
    .app_dashboard .card-stories-one .list-body {
        flex: 1;
        margin-left: 15px;
    }
    .app_dashboard .card-stories-one .list-title {
        font-size: .8125rem;
        line-height: 1.42;
        margin-bottom: 10px;
    }
    .app_dashboard .card-stories-one .list-title a {
        color: #06072d;
    }
    .app_dashboard .card-stories-one .list-title a:hover,
    .app_dashboard .card-stories-one .list-title a:focus {
        color: #373857;
    }
    .app_dashboard .card-stories-one .list-footer {
        display: flex;
        align-items: center;
        font-size: 11px;
        font-family: "Lato", sans-serif;
    }
    .app_dashboard .card-stories-one .list-footer a {
        color: #637382;
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-stories-one .list-footer a:hover,
    .app_dashboard .card-stories-one .list-footer a:focus {
        color: #5556fd;
    }
    .app_dashboard .card-stories-one .list-footer a:hover svg,
    .app_dashboard .card-stories-one .list-footer a:focus svg {
        fill: rgba(85, 86, 253, 0.2);
    }
    .app_dashboard .card-stories-one .list-footer a + a {
        margin-left: 20px;
    }
    .app_dashboard .card-stories-one .list-footer svg {
        margin-right: 5px;
    }
    .app_dashboard .card-profile-one .card-body {
        padding: 15px;
    }
    .app_dashboard .card-profile-one .avatar {
        flex-shrink: 0;
    }
    .app_dashboard .card-profile-one .media {
        display: block;
    }
    .app_dashboard .card-profile-one .media-body {
        margin-top: 20px;
    }
    .app_dashboard .card-profile-one .card-title {
        margin-bottom: 15px;
        color: #06072d;
    }
    .app_dashboard .card-profile-one .card-desc {
        font-size: 13px;
        color: #575d78;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    .app_dashboard .card-profile-one .media-footer {
        display: flex;
        align-items: center;
    }
    .app_dashboard .card-profile-one .media-footer > div + div {
        margin-left: 20px;
        padding-left: 20px;
        border-left: 1px solid rgba(33, 43, 53, 0.08);
    }
    .app_dashboard .card-profile-one .media-footer h6 {
        font-size: 20px;
        font-family: "Oswald", sans-serif;
        font-weight: 400;
        color: #06072d;
        margin-bottom: 0;
    }
    .app_dashboard .card-profile-one .media-footer label {
        margin-bottom: 0;
        font-size: 10px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #575d78;
    }
    .app_dashboard .card-profile-one.card-dark {
        border-width: 0;
    }
    .app_dashboard .card-profile-two {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: #575d78;
    }
    .app_dashboard .card-profile-two .card-body {
        padding: 15px;
    }
    .app_dashboard .card-profile-two .avatar {
        margin-bottom: 15px;
    }
    .app_dashboard .card-profile-two .card-title {
        font-size: 16px;
    }
    .app_dashboard .card-profile-two .card-desc {
        font-size: 13px;
        margin-bottom: 0;
        line-height: 1.45;
    }
    .app_dashboard .card-profile-two .card-footer {
        padding-bottom: 15px;
        padding-left: 15px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .app_dashboard .card-profile-two .card-footer a {
        line-height: .5;
        color: rgba(55, 56, 87, 0.6);
    }
    .app_dashboard .card-profile-two .card-footer a:hover,
    .app_dashboard .card-profile-two .card-footer a:focus {
        color: #5556fd;
    }
    .app_dashboard .card-profile-two .card-footer a:hover svg,
    .app_dashboard .card-profile-two .card-footer a:focus svg {
        fill: rgba(85, 86, 253, 0.2);
    }
    .app_dashboard .card-profile-two .card-footer a + a {
        margin-left: 6px;
    }
    .app_dashboard .card-profile-two.card-dark {
        background-image: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.2) 100%);
        background-repeat: repeat-x;
    }
    .app_dashboard .card-profile-two.card-dark .card-title {
        font-weight: 400;
    }
    .app_dashboard .card-profile-two.card-dark .card-footer a {
        color: inherit;
    }
    .app_dashboard .card-profile-two.card-dark .card-footer a:hover,
    .app_dashboard .card-profile-two.card-dark .card-footer a:focus {
        color: #fff;
    }
    .app_dashboard .card-profile-three .card-body {
        padding: 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .app_dashboard .card-profile-three .profile-name {
        font-size: 20px;
        margin-bottom: 5px;
    }
    .app_dashboard .card-profile-three .profile-name a {
        color: #06072d;
    }
    .app_dashboard .card-profile-three .profile-name a:hover,
    .app_dashboard .card-profile-three .profile-name a:focus {
        color: #373857;
    }
    .app_dashboard .card-profile-three .profile-position {
        font-size: 13px;
        color: rgba(55, 56, 87, 0.6);
        margin-bottom: 18px;
    }
    .app_dashboard .card-profile-three .profile-bio {
        text-align: center;
        font-size: 12px;
        color: #575d78;
        margin-bottom: 0;
    }
    .app_dashboard .card-profile-three .nav {
        justify-content: center;
    }
    .app_dashboard .card-profile-three .badge {
        padding: 5px 8px;
        font-size: 9px;
        font-weight: 400;
        font-family: "Roboto", sans-serif;
        text-transform: uppercase;
        margin-bottom: 4px;
        border-radius: 10px;
    }
    .app_dashboard .card-profile-three .badge + .badge {
        margin-left: 4px;
    }
    .app_dashboard .card-profile-three .badge-warning:hover,
    .app_dashboard .card-profile-three .badge-warning:focus {
        color: #fff;
        background-color: #fd7e14;
    }
    .app_dashboard .card-profile-three .card-footer {
        padding: 0;
        background-color: transparent;
        text-align: center;
        font-size: 13px;
    }
    .app_dashboard .card-profile-three .card-footer a {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 13px 10px;
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 500;
        font-family: "Helvetica Neue", Helvetica, sans-serif;
        color: #575d78;
        letter-spacing: .5px;
    }
    .app_dashboard .card-profile-three .card-footer a:hover,
    .app_dashboard .card-profile-three .card-footer a:focus {
        color: #5556fd;
    }
    .app_dashboard .card-profile-three .card-footer a:hover svg,
    .app_dashboard .card-profile-three .card-footer a:focus svg {
        fill: rgba(85, 86, 253, 0.4);
    }
    .app_dashboard .card-profile-three .card-footer svg {
        margin-right: 5px;
        stroke-width: 2.5px;
        fill: rgba(6, 7, 45, 0.15);
    }
    /* .app_dashboard .nav-tabs.calendar-event > li > a, */
    .calendar-date {
        font-family: "Oswald", sans-serif;
    }
    .app_dashboard .nav-tabs.calendar-sub-tab {
        background: #efeffb;
        border-radius: 100px;
    }
    .app_dashboard .nav-tabs.calendar-sub-tab .nav-link {
        background-color: transparent;
        margin-bottom: 0;
        border: 0;
    }
    .app_dashboard .nav-tabs.calendar-sub-tab .nav-link,
    .app_dashboard .nav-tabs.calendar-event .nav-link {
        font-weight: bold;
    }
    .app_dashboard .nav-tabs.calendar-sub-tab .nav-link.active {
        background-color: var(--root-color1);
        color: #FFF;
    }
    .app_dashboard .gallery > a > img {
        margin: 4px 2px;
        width: 110px;
        height: auto;
    }
    .app_dashboard .nav.nav-card-icon {
        display: none;
    }
    .app_dashboard .nav-line {
        border-bottom: 2px solid rgba(230, 231, 239, 0.85);
    }
    .app_dashboard .nav-line .nav-link.active {
        position: relative;
    }
    .app_dashboard .nav-line .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #373857;
    }
    .app_dashboard .nav-line .nav-item + .nav-item {
        margin-left: 25px;
    }
    .app_dashboard .nav-line .nav-link {
        padding: 8px 0;
    }
    .app_dashboard .nav-link {
        display: block;
        color: #575d78;
        padding: 0.5rem 1rem;
    }
    .app_dashboard .carousel-control-next,
    .app_dashboard .carousel-control-prev {
        width: 5%;
    }
    .app_dashboard #carouselSlide2 .carousel-control-next,
    .app_dashboard #carouselSlide2 .carousel-control-prev {
        height: 50%;
    }
    .app_dashboard #carouselSlide2 .carousel-indicators li {
        background-color: #999;
    }
    .app_dashboard #carouselSlide2 .carousel-indicators {
        margin-bottom: 20px;
    }
    .app_dashboard #carouselSlideNew .marker {
        bottom: 10px;
        top: inherit;
        right: 10px;
        left: inherit;
        font-size: 12px;
    }
    .app_dashboard .card-prompt,
    .app_dashboard .card-blog-one,
    .app_dashboard .card-campaign-one,
    .app_dashboard .card-weather {
        height: 238px;
        overflow: hidden;
    }
    .app_dashboard #carouselSlide2 .carousel-indicators {
        display: none;
    }    
    .app_dashboard .card-event-calendar {
        height: 480px;
    }
    .app_dashboard .card-forum,
    .app_dashboard .card-poll {
        height: 350px;
    }
    .app_dashboard .forum-body-text:empty,
    .app_dashboard #carouselSlideNew .marker:empty {
        display: none;
    }
    .app_dashboard .intime,
    .app_dashboard .outtime,
    .app_dashboard .cleantime {
        font-size: 16px;
        font-family: "Oswald";
    }
    .app_dashboard .intime sup {
        color: #CC0000 !important;
    }
    .app_dashboard .outtime sup {
        color: #11b35b !important;
    }
    .calendar-<?php echo $this->uniqId ?> {
        border: 0;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-icon-left-single-arrow:after,
    .calendar-<?php echo $this->uniqId ?> .fc-icon-right-single-arrow:after {
        font-size: 26px;
        top: 0;
        color: var(--root-color1);
    }
    .calendar-<?php echo $this->uniqId ?> .fc-toolbar h2 {
        font-size: 18px;
        line-height: normal;
        font-family: "Oswald";
    }
    .calendar-<?php echo $this->uniqId ?> .fc-toolbar.fc-header-toolbar {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-icon-left-single-arrow:after {
        content: "\e9c8";
    }
    .calendar-<?php echo $this->uniqId ?> .fc-icon-right-single-arrow:after {
        content: "\e9cb";
    }
    .calendar-<?php echo $this->uniqId ?> .fc-toolbar.fc-header-toolbar button,
    .calendar-<?php echo $this->uniqId ?> .fc-toolbar.fc-header-toolbar .fc-button-group button {
        background: transparent !important;
        border-color: transparent !important;
        color: rgba(0, 0, 0, 0.5) !important;
        box-shadow: none;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-next-button.fc-button {
        padding-right: 0;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-head-container > .fc-widget-header > table {
        padding: 0 15px;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table .fc-head tr:first-child > td,
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table .fc-head tr:first-child > th {
        background: transparent;
        padding: 0;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-view-container {
        background-color: transparent;
        border: 0;
        font-family: "Oswald";
    }
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table .fc-head tr:first-child > th {
        padding: 10px 6px;
        text-align: center;
        border-left: 0;
        border-color: #e0e0e0;
        color: rgba(0,0,0,0.6);
        font-weight: normal;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-ltr .fc-basic-view .fc-day-top .fc-day-number {
        float: none;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table tr:last-child td:(.fc-event-container) {
        /* background: #000; */
    }
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table tr:last-child td {
        border: 0;
        text-align: center;
        /* height: 80px; */
        padding: 8px 15px;
        vertical-align: middle;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table tr:last-child td.fc-sat,
    .calendar-<?php echo $this->uniqId ?> .fc-view-container .fc-view > table tr:last-child td.fc-sun {
        background: #efeffb;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-event-container {
        display: none;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-event-container > span {
        font-weight: 300;
        color: #555;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-icon {
        height: auto;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-toolbar.fc-header-toolbar .fc-today-button {
        background: var(--root-color1) !important;
        color: #FFF !important;
        text-shadow: none;
        font-weight: bold;
        border-radius: 100px;
        padding: 0 34px;
        height: 30px;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-toolbar .fc-right {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
    .calendar-<?php echo $this->uniqId ?> .fc-unthemed .fc-widget-content .fc-bg td.fc-today {
        /*background: #d8ddff;*/
        background: none;
        /* border-radius: 100px; */
    }
    
    .calendar-<?php echo $this->uniqId ?> .fc-unthemed .fc-widget-content .fc-content-skeleton td.fc-today .fc-day-number {
        border: 3px solid var(--root-color1);
        border-radius: 30px;
        padding: 3px 7px;
    }
    .calendar-<?php echo $this->uniqId ?> .eventj-icon > i.fa-clock-o,
    .calendar-<?php echo $this->uniqId ?> .eventj-icon > i.fa-briefcase {
        /* color: #a2aeff !important; */
    }
    .calendar-<?php echo $this->uniqId ?> .eventj-icon > i.fa-briefcase {
        margin-left: 5px;
    }
    .dashboard<?php echo $this->uniqId ?> .spinner {
        width: initial !important;
        height: initial !important;
    }
    .dashboard<?php echo $this->uniqId ?> .chart1<?php echo $this->uniqId ?>,
    .dashboard<?php echo $this->uniqId ?> .chart2<?php echo $this->uniqId ?>,
    .dashboard<?php echo $this->uniqId ?> .chart3<?php echo $this->uniqId ?> {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 80px;
        margin-bottom: 15px;
    }
    .dashboard<?php echo $this->uniqId ?>  .chart1<?php echo $this->uniqId ?> canvas,
    .dashboard<?php echo $this->uniqId ?>  .chart2<?php echo $this->uniqId ?> canvas,
    .dashboard<?php echo $this->uniqId ?>  .chart3<?php echo $this->uniqId ?> canvas {
        position: absolute;
        width: 90px !important;
        height: auto !important;
    }
    .dashboard<?php echo $this->uniqId ?>  .chart1<?php echo $this->uniqId ?> > .percent,
    .dashboard<?php echo $this->uniqId ?>  .chart2<?php echo $this->uniqId ?> > .percent,
    .dashboard<?php echo $this->uniqId ?>  .chart3<?php echo $this->uniqId ?> > .percent {
        font-size: 22px;
        font-family: "Oswald";
    }
    @media (max-width: 1280px) {
        .app_dashboard .card-profile-sidebar .profile-info p {
            padding-right: 15px;
        }
        .app_dashboard .gallery > a > img {
            width: 100px;
        }
        .dashboard<?php echo $this->uniqId ?> .chart1<?php echo $this->uniqId ?>,
        .dashboard<?php echo $this->uniqId ?> .chart2<?php echo $this->uniqId ?>,
        .dashboard<?php echo $this->uniqId ?> .chart3<?php echo $this->uniqId ?> {
            height: 60px;
        }
        .dashboard<?php echo $this->uniqId ?>  .chart1<?php echo $this->uniqId ?> canvas,
        .dashboard<?php echo $this->uniqId ?>  .chart2<?php echo $this->uniqId ?> canvas,
        .dashboard<?php echo $this->uniqId ?>  .chart3<?php echo $this->uniqId ?> canvas {
            width: 70px !important;
            height: auto !important;
        }
        .app_dashboard #carouselSlide2 .carousel-indicators {
            display: none;
        }
    }
    @media (max-width: 1440px) {
        .dashboard<?php echo $this->uniqId ?> .chart1<?php echo $this->uniqId ?>,
        .dashboard<?php echo $this->uniqId ?> .chart2<?php echo $this->uniqId ?>,
        .dashboard<?php echo $this->uniqId ?> .chart3<?php echo $this->uniqId ?> {
            height: 70px;
        }
        .dashboard<?php echo $this->uniqId ?>  .chart1<?php echo $this->uniqId ?> canvas,
        .dashboard<?php echo $this->uniqId ?>  .chart2<?php echo $this->uniqId ?> canvas,
        .dashboard<?php echo $this->uniqId ?>  .chart3<?php echo $this->uniqId ?> canvas {
            width: 80px !important;
            height: auto !important;
        }
    }
    .qtip-taskdatacard {
        padding-left: 10px;
        padding-right: 10px;
        font-size: 12px;
    }
    
    .dashboard<?php echo $this->uniqId ?> .event-box:hover {
        cursor: pointer;
    }
    
    .dashboard<?php echo $this->uniqId ?> .carousel-control-next-icon,
    .dashboard<?php echo $this->uniqId ?> .carousel-control-prev-icon {
        filter: invert(48%) sepia(13%) saturate(3207%) hue-rotate(130deg) brightness(0%) contrast(80%);
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .owl-nav button{
        font-size: 36px;
        width: 20px;
        outline: none;
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .owl-nav{
        position: absolute;
        top: -50px;
        right: 0;
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .card-img-top{
        max-height: 250px
    }
    .dashboard<?php echo $this->uniqId ?> .newslider{
        padding: 10px;
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .news-post .post-feature{
        height: 180px;
        background-size: contain;
        background-position:center;
        background-repeat: no-repeat;
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .card-body .card-text{
        min-height: 50px;
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .card-body a:hover{
        opacity: .7;
     }
    .dashboard<?php echo $this->uniqId ?> .newslider .card-body a{ 
        color: #000;
    }
    .dashboard<?php echo $this->uniqId ?> .newslider .news-post{}

    .hrmtimelog-calendar.fc .fc-row .fc-content-skeleton td.fc-day-top {
        height: 30px;
    }
    .hrmtimelog-calendar.fc .fc-row .fc-content-skeleton td.fc-event-container {
        height: 60px;
        padding: 0 5px;
    }
    
    .hrmtimelog-calendar .fc-hrm-time-log-btn {
        position: absolute; 
        bottom: 0;
        padding: 5px;
    }
    .hrmtimelog-calendar .fc-hrm-time-log-startdate {
        padding: 1px 5px;
        border-radius: 4px;
        background-color:#199ec7;
        font-weight: 600;
        color: #fff;
    }
    .hrmtimelog-calendar .fc-hrm-time-log-date.start{
        background-color: <?php echo $this->startBgColor; ?>;
    }
    .hrmtimelog-calendar .fc-hrm-time-log-date {
        padding: 1px 5px;
        border-radius: 4px;
        background-color: <?php echo $this->endBgColor; ?>;
        font-weight: 600;
        color: #fff;
        margin-top: 2px;
    }
    .hrmtimelog-calendar .fc-hrm-time-log-enddate {
        margin-top: 3px;
        padding: 1px 5px;
        border-radius: 4px;
        background-color: #e54b1b;
        font-weight: 600;
        color: #fff;
    }
    .hrmtimelog-calendar .fc-hrm-time-log-date i, 
    .hrmtimelog-calendar .fc-hrm-time-log-startdate i, 
    .hrmtimelog-calendar .fc-hrm-time-log-enddate i {
        font-weight: normal;
        width: 15px;
    }
    .hrmtimelog-calendar .fc-row.panel-default {
        border: 0 !important;
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        -ms-border-radius: 0;
        -o-border-radius: 0;
        border-radius: 0;
    }
    .hrmtimelog-calendar .fc-body .fc-row.panel-default {
        height: 200px !important;
    }
    .hrmtimelog-calendar .fc-hrm-time-duration {
        height: 100px;
    }
    .hrmtimelog-calendar .fc-hrm-time-duration table {
        width: 100%;
        margin-top: 10px;
    }
    .hrmtimelog-calendar .fc-hrm-time-duration table > tbody > tr > td:nth-child(1n+2) {
        width: 50px;
        text-align: right;
        padding: 0;
    }
    .hrmtimelog-calendar .fc-hrm-time-log-parent {
        height: 78px;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr-done {
        overflow: hidden;
        white-space: normal;
        height: 14px;
        padding: 1px 5px;
        border-radius: 4px;
        background-color: green;
        color: #fff;
        margin-top: 2px;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        font-size: 11px;
        line-height: 13px;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr-cancel {
        overflow: hidden;
        white-space: normal;
        height: 14px;
        padding: 1px 5px;
        border-radius: 4px;
        background-color: #F3565D;
        color: #fff;
        margin-top: 2px;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        font-size: 11px;
        line-height: 13px;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr {
        overflow: hidden;
        height: 14px;
        padding: 1px 5px;
        border-radius: 4px;
        background-color: #666;
        color: #fff;
        margin-top: 2px;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        font-size: 11px;
        line-height: 13px;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr:hover {
        background-color: #a0a0a0;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr-done:hover {
        background-color: #4a964a;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr-cancel:hover {
        background-color: #f76f74;
    }
    .hrmtimelog-calendar .fc-hrm-time-descr-empty {
        height: 22px;
    }
    .hrmtimelog-calendar .fc-hrm-time-duration table > tbody > tr > td > a:hover {
        text-decoration: underline;
    }
    .hrmtimelog-calendar .fc-left-request {
        float: left;
        margin-left: 10px;
    }
    .hrmtimelog-calendar .fc-day-header {
        padding: 6px 0;
        background-color: #00c5dc;
        color: #fff;
    }
    .hrmtimelog-cal-parent .table th {
        padding: 8px 11px;
        background-color: #00c5dc;
        color: #fff;
        font-size: 12px;
        width: 175px;
        text-align: center;
    }
    .hrmtimelog-cal-parent .table td {
        padding: 7px 12px;
    }
    .hrmtimelog-cal-sidebar thead > tr > th {
        color: #287fb5;
        font-size: 15px;
        font-weight: 700;
    }
    .hrmtimelog-cal-sidebar > tbody > tr > td {
        border-top: 0;
        border-bottom: 0;
        padding: 7px 4px;
        font-weight: 600;
    }
    #load-news-dataview-<?php echo $this->uniqId; ?> .meta-toolbar {
        background-color: #00c5dc !important;
        background: none;
        min-height: 26px;
        height: 39px;
        padding-bottom: 3px;
        margin-left: -10px;
        margin-right: -10px;
    }
    .hrmtimelog-calendar .fc-day.fc-sat, 
    .hrmtimelog-calendar .fc-day.fc-sun {  
        background-color: #fffacb; 
    }
    .timerequist.tab-content{
        max-height: 400px;
        overflow-x: hidden;
    }
    
</style>


<script type="text/javascript">

   // feather.replace();
    
    var $data_<?php echo $this->uniqId ?> = [];
    var doubleClick_<?php echo $this->uniqId; ?> = false;
    var today<?php echo $this->uniqId; ?> = false;
    var $mainSelector<?php echo $this->uniqId; ?> = $('.dashboard<?php echo $this->uniqId ?>');
    
    $(document).ready(function() {
    });
    
    $('.news-post').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        autoplayTimeout:5000,
        autoplay:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:4
            }
        }
    });

    function apiChatSendMessagev1<?php echo $this->uniqId ?>(userId, text, type) {
        apiChatSendMessage(userId, text, type);
        PNotify.removeAll();
        new PNotify({
            title: 'Success',
            text: 'Амжилттай илгээгдлээ',
            type: 'success', 
            sticker: false
        });
    }
    
    function changeDateTimeFormat<?php echo $this->uniqId; ?>(date) {
        var yyyy = date.getFullYear();
        var MM = date.getMonth() + 1;
        var dd = date.getDate();

        if (MM < 10) {
            MM = '0' + MM
        }
        if (dd < 10) {
            dd = '0' + dd
        }
        /*date = MM + "/" + dd + "/" + yyyy;        */
        return yyyy + "-" + MM + "-" + dd;
    }


    function hrmTimesheetEditProcess<?php echo $this->uniqId ?>(elem, startDate, id) {
        var $dialogName = 'dialog-hrm-timesheet-bp';
        if (!$('#' + $dialogName).length) {
            $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo('body');
        }
        var $dialog = $('#' + $dialogName);
        var fillDataParams = '';

        if (id) {
            fillDataParams = 'id='+id+'&defaultGetPf=1';
        } else {
            fillDataParams = 'startDate='+startDate+'&endDate='+startDate;
        }

        $.ajax({
            type: 'post',
            url: 'mdwebservice/callMethodByMeta',
            data: {
                metaDataId: '1548836365306', 
                isDialog: true, 
                isSystemMeta: false, 
                fillDataParams: fillDataParams,  
                callerType: 'hrmTimesheet', 
                openParams: '{"callerType":"hrmTimesheet"}'
            },
            dataType: 'json',
            beforeSend: function () {
                Core.blockUI({
                    message: 'Loading...', 
                    boxed: true
                });
            },
            success: function (data) {

                $dialog.empty().append(data.Html);

                var $processForm = $('#wsForm', '#' + $dialogName), 
                    processUniqId = $processForm.parent().attr('data-bp-uniq-id');

                var buttons = [
                    {text: data.run_btn, class: 'btn green-meadow btn-sm bp-btn-save', click: function (e) {
                        if (window['processBeforeSave_'+processUniqId]($(e.target))) {     

                            $processForm.validate({ 
                                ignore: '', 
                                highlight: function(element) {
                                    $(element).addClass('error');
                                    $(element).parent().addClass('error');
                                    if ($processForm.find("div.tab-pane:hidden:has(.error)").length) {
                                        $processForm.find("div.tab-pane:hidden:has(.error)").each(function(index, tab){
                                            var tabId = $(tab).attr('id');
                                            $processForm.find('a[href="#'+tabId+'"]').tab('show');
                                        });
                                    }
                                },
                                unhighlight: function(element) {
                                    $(element).removeClass('error');
                                    $(element).parent().removeClass('error');
                                },
                                errorPlacement: function(){} 
                            });

                            var isValidPattern = initBusinessProcessMaskEvent($processForm);

                            if ($processForm.valid() && isValidPattern.length === 0) {
                                $processForm.ajaxSubmit({
                                    type: 'post',
                                    url: 'mdwebservice/runProcess',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        Core.blockUI({
                                            boxed: true, 
                                            message: 'Түр хүлээнэ үү'
                                        });
                                    },
                                    success: function (responseData) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: responseData.status,
                                            text: responseData.message,
                                            type: responseData.status, 
                                            sticker: false
                                        });

                                        if (responseData.status === 'success') {
                                            $('#calendar-<?php echo $this->uniqId; ?>').fullCalendar('refetchEvents');
                                            $dialog.dialog('close');
                                            
                                            var $html = '';
                                            var $responseData = responseData.resultData;
                                            var $startDate = ($responseData['startdate']).substr(0, 10) + ' ' +($responseData['starttime']).substr(11, 5);
                                            var $endDate = ($responseData['enddate']).substr(0, 10)+ ' ' + ($responseData['endtime']).substr(11, 5);
                                            
                                            $html += '<div class="d-flex flex-column justify-content-center mr-3" style="width:120px;">';
                                                $html += '<span class="font-size-14">&nbsp;'+ $startDate +'&nbsp;<br>&nbsp;'+ $endDate +'</span>';
                                            $html += '</div>';
                                            $html += '<div style="width:140px;">';
                                                $html += '<h5 class="mb-0 font-weight-normal font-size-14 no-border">'+ $responseData['booktypename'] +'</h5>';
                                            $html += '</div>';
                                            $html += '<div style="width:525px;">';
                                                $html += '<h5 class="mb-0 font-weight-normal font-size-14 ml10 no-border">'+ $responseData['description'] +'</h5>';
                                            $html += '</div>';
                                            
                                            $(elem).empty().append($html);
                                        } 
                                        Core.unblockUI();
                                    },
                                    error: function () {
                                        alert("Error");
                                    }
                                });
                            }
                        }    
                    }},
                    {text: data.close_btn, class: 'btn blue-madison btn-sm', click: function () {
                        $dialog.dialog('close');
                    }}
                ];

                var dialogWidth = data.dialogWidth, dialogHeight = data.dialogHeight;

                if (data.isDialogSize === 'auto') {
                    dialogWidth = 1200;
                    dialogHeight = 'auto';
                }

                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.Title,
                    width: dialogWidth,
                    height: dialogHeight,
                    modal: true,
                    closeOnEscape: (typeof isCloseOnEscape == 'undefined' ? true : isCloseOnEscape), 
                    close: function () {
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: buttons
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left",
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                if (data.dialogSize === 'fullscreen') {
                    $dialog.dialogExtend("maximize");
                }
                $dialog.dialog('open');
            },
            error: function () {
                alert("Error");
            }
        }).done(function () {
            Core.initBPAjax($dialog);
            Core.unblockUI();
        });
    }
    
    function drilldownLink_news2<?php echo $this->uniqId; ?> (element) {
        var row = JSON.parse($(element).attr('data-row'));
       
        gridDrillDownLink(element, 'inOpenJobList', 'metagroup', '1', '',  '1577260423821868', '', '1577260423821868', '', true, true)
    }
    function drilldownLink_news<?php echo $this->uniqId; ?> (element) {
        var row = JSON.parse($(element).attr('data-row'));
        var $dialogName = 'dialog-news-'+row.id;
        $html = '';
        var $dialog = $('#' + $dialogName);
        $html += '<div class="feature0-img" style="width:100%;">';
                $html += '<div class = "f-image mb20 d-flex justify-content-center" ><img src="'+URL_APP+''+row.picture+'"/ style="max-width:100%"></div>'
                $html += '<span class="font-size-11">'+ row.booktypename +'</span>';
                $html += '<h3>'+ row.title +'</h3>';
                $html += '<p class="font-size-13 desc">'+ html_entity_decode(row.dim1, 'ENT_QUOTES') +'</p>';
            $html += '</div>';
      

        if (!$('#' + $dialogName).length) {
            $('<div id="' + $dialogName + '" class="display-none">'+ $html +'</div>').appendTo('body');
        }

        $("#" + $dialogName).empty().append($html);
            $("#" + $dialogName).dialog({
                cache: false,
                resizable: true,
                bgiframe: true,
                autoOpen: false,
                title: 'Мэдээлэл',
                width: 720,
                minWidth: 750,
                height: "auto",
                modal: true,
                close: function() {
                    $("#" + $dialogName).empty().dialog('destroy').remove();
                },
                buttons: [{
                    text: 'Хаах',
                    class: 'btn btn-sm blue-madison',
                    click: function() {
                        $("#" + $dialogName).dialog('close');
                    }
                }]
            }).dialogExtend({
                "closable": true,
                "maximizable": true,
                "minimizable": true,
                "collapsable": true,
                "dblclick": "maximize",
                "minimizeLocation": "left",
                "icons": {
                    "close": "ui-icon-circle-close",
                    "maximize": "ui-icon-extlink",
                    "minimize": "ui-icon-minus",
                    "collapse": "ui-icon-triangle-1-s",
                    "restore": "ui-icon-newwin"
                }
            });
            $("#" + $dialogName).dialog('open');
            $("#" + $dialogName).dialogExtend("restore");
            Core.unblockUI();
         
        //gridDrillDownLink(element, 'HR_NEWS_LIST', 'metagroup', '1', '',  '1548219753368', '','', '', true, true)
    }
    
    function blockContent_<?php echo $this->uniqId; ?>(mainSelector) {
        $(mainSelector).block({
            message: '<i class="icon-spinner4 spinner"></i>',
            centerX: 0,
            centerY: 0,
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                width: 16,
                top: '15px',
                left: '',
                right: '15px',
                border: 0,
                padding: 0,
                backgroundColor: 'transparent'
            }
        });
    }
    function updateEvento(elem, id) {
        var processId = '1585648529412';
        var $dialogName = 'dialog-businessprocess-' + processId;
        if (!$('#' + $dialogName).length) {
            $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo('body');
        }
        var $dialog = $('#' + $dialogName);
        var fillDataParams = 'id='+id+'';

        $.ajax({
            type: 'post',
            url: 'mdwebservice/callMethodByMeta',
            data: {
                metaDataId: processId, 
                isDialog: true, 
                isSystemMeta: false, 
                fillDataParams: fillDataParams
            },
            dataType: 'json',
            beforeSend: function () {
                /*
                Core.blockUI({
                    message: 'Loading...', 
                    boxed: true
                });*/
            },
            success: function (data) {

                $dialog.empty().append(data.Html);

                var $processForm = $('#wsForm', '#' + $dialogName), 
                    processUniqId = $processForm.parent().attr('data-bp-uniq-id');

                var buttons = [
                    {text: data.run_btn, class: 'btn green-meadow btn-sm bp-btn-save', click: function (e) {
                        if (window['processBeforeSave_'+processUniqId]($(e.target))) {     

                            $processForm.validate({ 
                                ignore: '', 
                                highlight: function(element) {
                                    $(element).addClass('error');
                                    $(element).parent().addClass('error');
                                    if ($processForm.find("div.tab-pane:hidden:has(.error)").length) {
                                        $processForm.find("div.tab-pane:hidden:has(.error)").each(function(index, tab){
                                            var tabId = $(tab).attr('id');
                                            $processForm.find('a[href="#'+tabId+'"]').tab('show');
                                        });
                                    }
                                },
                                unhighlight: function(element) {
                                    $(element).removeClass('error');
                                    $(element).parent().removeClass('error');
                                },
                                errorPlacement: function(){} 
                            });

                            var isValidPattern = initBusinessProcessMaskEvent($processForm);

                            if ($processForm.valid() && isValidPattern.length === 0) {
                                $processForm.ajaxSubmit({
                                    type: 'post',
                                    url: 'mdwebservice/runProcess',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        /*
                                        Core.blockUI({
                                            boxed: true, 
                                            message: 'Түр хүлээнэ үү'
                                        });*/
                                    },
                                    success: function (responseData) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: responseData.status,
                                            text: responseData.message,
                                            type: responseData.status, 
                                            sticker: false
                                        });

                                        if (responseData.status === 'success') {
                                            $(elem).closest('.fc-hrm-time-duration').prepend('<div class="fc-hrm-time-descr fc-hrm-time-wfmstatus" title="'+responseData.resultData.description+'">'+responseData.resultData.description+'</div>');
                                            $dialog.dialog('close');
                                            $('#subject_'+sid).trigger('click');
                                        } 
                                        Core.unblockUI();
                                    },
                                    error: function () {
                                        alert("Error");
                                    }
                                });
                            }
                        }    
                    }},
                    {text: data.close_btn, class: 'btn blue-madison btn-sm', click: function () {
                        $dialog.dialog('close');
                    }}
                ];
                var dialogWidth = data.dialogWidth, dialogHeight = data.dialogHeight;

                if (data.isDialogSize === 'auto') {
                    dialogWidth = 1200;
                    dialogHeight = 'auto';
                }          

                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.Title,
                    width: dialogWidth,
                    height: dialogHeight,
                    modal: false,
                    closeOnEscape: (typeof isCloseOnEscape == 'undefined' ? true : isCloseOnEscape), 
                    close: function () {
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: buttons
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left",
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                if (data.dialogSize === 'fullscreen') {
                    $dialog.dialogExtend("maximize");
                }

                setTimeout(function() {
                    $dialog.dialog('open');
                    Core.unblockUI();
                }, 1);
            },
            error: function () {
                alert("Error");
            }
        }).done(function () {
            Core.initBPAjax($dialog);
        });
    }

    function updateEvent(elem, did) {
        var datarow = JSON.parse($(elem).attr('data-row'));
        console.log(datarow);
        var processId = '1585648529412';
            var $dialogName = 'dialog-businessprocess-' + processId;
            if (!$('#' + $dialogName).length) {
                $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo('body');
            }

            var $dialog = $('#' + $dialogName);

            $.ajax({
                type: 'post',
                url: 'mdwebservice/callMethodByMeta',
                data: {
                    metaDataId: processId,
                    isDialog: true,
                    dmMetaDataId: 1585127775592,
                    isSystemMeta: false,
                    oneSelectedRow: datarow
                },
                dataType: 'json',
                beforeSend: function () {
                    Core.blockUI({
                        message: 'Loading...',
                        boxed: true
                    });
                },
                success: function (data) {

                    $dialog.empty().append(data.Html);

                    var $processForm = $('#wsForm', '#' + $dialogName),
                            processUniqId = $processForm.parent().attr('data-bp-uniq-id');

                    var buttons = [
                        {text: data.run_btn, class: 'btn green-meadow btn-sm bp-btn-save', click: function (e) {
                                if (window['processBeforeSave_' + processUniqId]($(e.target))) {

                                    $processForm.validate({
                                        ignore: '',
                                        highlight: function (element) {
                                            $(element).addClass('error');
                                            $(element).parent().addClass('error');
                                            if ($processForm.find("div.tab-pane:hidden:has(.error)").length) {
                                                $processForm.find("div.tab-pane:hidden:has(.error)").each(function (index, tab) {
                                                    var tabId = $(tab).attr('id');
                                                    $processForm.find('a[href="#' + tabId + '"]').tab('show');
                                                });
                                            }
                                        },
                                        unhighlight: function (element) {
                                            $(element).removeClass('error');
                                            $(element).parent().removeClass('error');
                                        },
                                        errorPlacement: function () {}
                                    });

                                    var isValidPattern = initBusinessProcessMaskEvent($processForm);

                                    if ($processForm.valid() && isValidPattern.length === 0) {
                                        $processForm.ajaxSubmit({
                                            type: 'post',
                                            url: 'mdwebservice/runProcess',
                                            dataType: 'json',
                                            beforeSend: function () {
                                                Core.blockUI({
                                                    boxed: true,
                                                    message: 'Түр хүлээнэ үү'
                                                });
                                            },
                                            success: function (responseData) {
                                                PNotify.removeAll();
                                                new PNotify({
                                                    title: responseData.status,
                                                    text: responseData.message,
                                                    type: responseData.status,
                                                    sticker: false
                                                });

                                                if (responseData.status === 'success') {
                                                    $(elem).closest('.fc-hrm-time-duration').prepend('<div class="fc-hrm-time-descr fc-hrm-time-wfmstatus" title="' + responseData.resultData.description + '">' + responseData.resultData.description + '</div>');
                                                    $dialog.dialog('close');
                                                    reload_<?php echo $this->uniqId ?>();
                                                }
                                                Core.unblockUI();
                                            },
                                            error: function () {
                                                alert("Error");
                                            }
                                        });
                                    }
                                }
                            }},
                        {text: data.close_btn, class: 'btn blue-madison btn-sm', click: function () {
                                $dialog.dialog('close');
                            }}
                    ];
                    var dialogWidth = data.dialogWidth,
                            dialogHeight = data.dialogHeight;

                    if (data.isDialogSize === 'auto') {
                        dialogWidth = 1200;
                        dialogHeight = 'auto';
                    }

                    $dialog.dialog({
                        cache: false,
                        resizable: true,
                        bgiframe: true,
                        autoOpen: false,
                        title: data.Title,
                        width: 500,
                        height: dialogHeight,
                        modal: true,
                        closeOnEscape: (typeof isCloseOnEscape == 'undefined' ? true : isCloseOnEscape),
                        close: function () {
                            $dialog.empty().dialog('destroy').remove();
                        },
                        buttons: buttons
                    }).dialogExtend({
                        "closable": true,
                        "maximizable": true,
                        "minimizable": true,
                        "collapsable": true,
                        "dblclick": "maximize",
                        "minimizeLocation": "left",
                        "icons": {
                            "close": "ui-icon-circle-close",
                            "maximize": "ui-icon-extlink",
                            "minimize": "ui-icon-minus",
                            "collapse": "ui-icon-triangle-1-s",
                            "restore": "ui-icon-newwin"
                        }
                    });
                    if (data.dialogSize === 'fullscreen') {
                        $dialog.dialogExtend("maximize");
                    }

                    setTimeout(function () {
                        $dialog.dialog('open');
                        Core.unblockUI();
                    }, 1);

                },
                error: function () {
                    alert("Error");
                }
            }).done(function () {
                Core.initBPAjax($dialog);
            });
        }
    
    
    
</script>