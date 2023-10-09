<?php
if ($this->filterData) {
    
    if (isset($this->isDashboard) && $this->isDashboard == 1) {
        $clickFnc = 'filterKpiIndicatorToggleValue';
    } else {
        
        if (isset($this->isChartList) && $this->isChartList) {
            $clickFnc = 'filterKpiIndicatorValueChartListLoad';
        } elseif (isset($this->uniqId) && $this->uniqId) {
            $clickFnc = 'filterKpiIndicatorValueChartLoad';
        } else {
            $clickFnc = 'filterKpiIndicatorValueGrid';
        }
    }
    
    if ($fncName = Input::post('fncName')) {
        $clickFnc = str_replace('(this);', '', $fncName);
    }
    
    $html = array();
    $model = new Mdform_model();
    
    $html[] = '<div class="text-uppercase font-weight-bold mt-0 mb-2 kpi-indicator-filter-title"><i class="far fa-search"></i> '.$this->lang->line('filter').'</div>';
        $html[] = '<div class="list-group" data-uniqid="'.$this->uniqId.'" data-indicatorid="'.$this->indicatorId.'">';
        
    foreach ($this->filterData as $columnName => $filter) {
        
        $rowConfig = $filter['config'];
        $labelName = Lang::line($rowConfig['labelName']);
        $defaultValue = issetParam($rowConfig['defaultValue']);
        
        if (isset($filter['rows'])) {
            
            $rows = $filter['rows'];
            
            $html[] = '<div data-filter-type="checkbox" data-filter-column="'.$columnName.'">';
            
            $html[] = '
                <a href="javascript:;" class="list-group-item font-weight-bold font-size-14 line-height-normal justify-content-between kpi-indicator-filter-collapse-btn" style="color: #000">
                    '.$labelName.' <i class="far fa-plus-square ml-1"></i>
                </a>';
            
            $html[] = '<div class="list-group-body d-none">';
            
                if (Mdstatement::$isKpiIndicator && $rowConfig['reportAggrFunc'] != '') {
                    
                    $html[] = '<a href="javascript:;" class="list-group-item list-group-item-action line-height-normal active" onclick="'.$clickFnc.'(this);" data-colname="'.$columnName.'_groupingSum" data-report-colname="'.$columnName.'" data-report-aggregate="'.$rowConfig['reportAggrFunc'].'">
                        <i class="font-size-16 mr7 fas fa-check-square"></i><span>Нэгтгэх</span>
                    </a>';
                }
            
                foreach ($rows as $row) {
                    
                    $recordCount = isset($row['RECORD_COUNT']) ? '<span class="font-weight-bold ml-auto">'.$row['RECORD_COUNT'].'</span>' : '';
                    $valueName   = $row['LABEL_NAME'];
                    $iconClass   = 'far fa-square';
                    $activeClass = '';
                    $valueMode   = '';
                    
                    if ($defaultValue != '' && $valueName == $defaultValue) {
                        $iconClass = 'fas fa-check-square';
                        $activeClass = ' active';
                    }
                    
                    if ($valueName == '') {
                        $valueName = 'Хоосон';
                        $valueMode = 'isnull';
                    }
                    
                    $html[] = '<a href="javascript:;" class="list-group-item list-group-item-action line-height-normal'.$activeClass.'" onclick="'.$clickFnc.'(this);" data-colname="'.$columnName.'">
                        <i class="'.$iconClass.' font-size-16 mr7"></i><span data-value-mode="'.$valueMode.'">'.$valueName.'</span>'.$recordCount.'
                    </a>';
                }
                
            $html[] = '</div>';
            
        } else {
            
            $showType = $rowConfig['showType'];
            
            $html[] = '<div data-filter-type="input" data-filter-column="'.$columnName.'">';
            
            $html[] = '
                <a href="javascript:;" class="list-group-item font-weight-bold font-size-14 line-height-normal justify-content-between kpi-indicator-filter-collapse-btn" style="color: #000">
                    '.$labelName.' <i class="far fa-minus-square ml-1"></i>
                </a>';
            
            $html[] = '<div class="list-group-body">';
                
                if ($rowConfig['namedParam']) {
                    
                    $html[] = '<div data-named-param="1" data-load-fnc="'.$clickFnc.'">';
                        $html[] = $model->kpiIndicatorControl($rowConfig['row']);
                    $html[] = '</div>';
                    
                } else {
                    
                    $html[] = '<div class="row" data-kpi-indicator-filter-between-input="'.$columnName.'">';

                    if ($showType == 'bigdecimal' || $showType == 'decimal') {

                        $html[] = '<div class="col-md-6 pr-1">
                                <div class="form-group mb-1">';

                                $html[] = Form::text(array(
                                    'class' => 'form-control bigdecimalInit', 
                                    'data-kpi-indicator-filter-between' => 'begin', 
                                    'data-load-fnc' => $clickFnc, 
                                    'placeholder' => 'min', 
                                    'value' => $defaultValue
                                ));

                        $html[] = '</div>
                            </div>    
                            <div class="col-md-6 pl-1">
                                <div class="form-group mb-1">';

                                $html[] = Form::text(array(
                                    'class' => 'form-control bigdecimalInit', 
                                    'data-kpi-indicator-filter-between' => 'end', 
                                    'data-load-fnc' => $clickFnc, 
                                    'placeholder' => 'max', 
                                    'value' => $defaultValue
                                ));

                        $html[] = '</div>
                            </div>';

                    } elseif ($showType == 'number' || $showType == 'long') {

                        $html[] = '<div class="col-md-6 pr-1">
                                <div class="form-group mb-1">';

                                $html[] = Form::text(array(
                                    'class' => 'form-control integerInit', 
                                    'data-kpi-indicator-filter-between' => 'begin', 
                                    'data-load-fnc' => $clickFnc, 
                                    'placeholder' => 'min', 
                                    'value' => $defaultValue
                                ));

                        $html[] = '</div>
                            </div>    
                            <div class="col-md-6 pl-1">
                                <div class="form-group mb-1">';

                                $html[] = Form::text(array(
                                    'class' => 'form-control integerInit', 
                                    'data-kpi-indicator-filter-between' => 'end', 
                                    'data-load-fnc' => $clickFnc, 
                                    'placeholder' => 'max', 
                                    'value' => $defaultValue
                                ));

                        $html[] = '</div>
                            </div>';

                    } elseif ($showType == 'date') {

                        $html[] = '<div class="col-md-6 pr-1">
                                <div class="form-group mb-1">';

                                $beginControl = Form::text(array(
                                    'class' => 'form-control dateInit', 
                                    'data-kpi-indicator-filter-between' => 'begin', 
                                    'data-load-fnc' => $clickFnc, 
                                    'placeholder' => 'min', 
                                    'value' => $defaultValue
                                ));

                                $html[] = html_tag('div', array(
                                        'class' => 'dateElement input-group'
                                    ), $beginControl . '<span class="input-group-btn"><button tabindex="-1" onclick="return false;" class="btn"><i class="fal fa-calendar"></i></button></span>', true
                                );

                        $html[] = '</div>
                            </div>    
                            <div class="col-md-6 pl-1">
                                <div class="form-group mb-1">';

                                $endControl = Form::text(array(
                                    'class' => 'form-control dateInit', 
                                    'data-kpi-indicator-filter-between' => 'end', 
                                    'data-load-fnc' => $clickFnc, 
                                    'placeholder' => 'max', 
                                    'value' => $defaultValue
                                ));

                                $html[] = html_tag('div', array(
                                        'class' => 'dateElement input-group'
                                    ), $endControl . '<span class="input-group-btn"><button tabindex="-1" onclick="return false;" class="btn"><i class="fal fa-calendar"></i></button></span>', true
                                );

                        $html[] = '</div>
                            </div>';
                    }

                    $html[] = '</div>';
                }
        
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
    }
    
    $html[] = '</div>';
    
    echo implode('', $html);
}
?>