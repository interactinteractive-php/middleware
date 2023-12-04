<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');  ?>

<div class="page_<?php echo $this->uniqId ?> col-md-12">
<?php 
    if (issetParamArray($this->pageJson['page']['body'])) {
        $pageHtml = $pageCss = '';
        $pageCss .= '<style type="text/css"> .page_' . $this->uniqId . ' { ';
        
        $pageAttr = Mdwidget::renderShowFields(array($this->pageJson['page']['body']), $this->pageJson['page']['body']['widgetCode'], $this->uniqId);
        $pageHtml .= issetParam($pageAttr['html']);
        $pageCss .= issetParam($pageAttr['css']);

        $pageCss .= "} </style>";
        echo  $pageHtml . $pageCss;
        /* var_dump($this->pageJson['page']['body']);  */
    }

?>
</div>

<script type="text/javascript">
    $(function () {
        option = {
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'cross',
      crossStyle: {
        color: '#999'
      }
    }
  },
  color: [ '#516b91', '#39E0CF'],
  toolbox: {
    feature: {
      dataView: { show: true, readOnly: false },
      magicType: { show: true, type: ['line', 'bar'] },
      restore: { show: true },
      saveAsImage: { show: true }
    }
  },
  xAxis: [
    {
      type: 'category',
      data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      axisPointer: {
        type: 'shadow'
      }
    }
  ],
  yAxis: [
    {
      type: 'value',
      name: 'Precipitation',
      min: 0,
      max: 250,
      interval: 50,
      axisLabel: {
        formatter: '{value} ml'
      }
    },
    {
      type: 'value',
      name: 'Temperature',
      min: 0,
      max: 25,
      interval: 5,
      axisLabel: {
        formatter: '{value} °C'
      }
    }
  ],
  series: [
    {
      name: 'Evaporation',
      type: 'bar',
      tooltip: {
        valueFormatter: function (value) {
          return value + ' ml';
        }
      },
      data: [
        2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3
      ]
    },
    {
      name: 'Temperature',
      type: 'line',
      yAxisIndex: 1,
      tooltip: {
        valueFormatter: function (value) {
          return value + ' °C';
        }
      },
      data: [2.0, 2.2, 3.3, 4.5, 6.3, 10.2, 20.3, 23.4, 23.0, 16.5, 12.0, 6.2]
    }
  ]
};
console.log(JSON.stringify(option));
        return false;
        /*
        var chartDom = document.getElementById('widgetCode_column-1-1-2_position_3');
        var myChart = echarts.init(chartDom);
        var option;

        option = {
            tooltip: {
                trigger: 'item',
                show: false
            },

            grid: {
                show: false,
                top: 0,
                left: 0,
                right: 0,
                bottom: 0
            },
            legend: {
                show: true,
                bottom: 'bottom'
            },
            color: ['#009EF7', '#39E0CF', '#E6ECF6'],
            series: [ {
                type: 'pie',
                height: 480,
                top: -60,
                radius: ['40%', '70%'],
                startAngle: 180,
                label: {
                    show: true,
                    position: 'center',
                    fontSize: '32',
                    formatter : '72.5%'
                },
                itemStyle: {
                    borderRadius: 5,
                    borderColor: '#fff',
                    borderWidth: 0
                },
                data: [
                    { value: 1048, name: 'Шийдвэрлэлтийн хувь' },
                    { value: 735, name: 'Хугацаандаа шийдвэрлэсэн' },
                    { value: 580, name: '' },
                    {
                    value: 1048 + 735 + 580,
                    itemStyle: {
                        color: 'none',
                        decal: {
                        symbol: 'none'
                        }
                    },
                    label: {
                        show: false
                    }
                    }
                ]
                }
            ]
        };
        console.log(JSON.stringify(option));
        option && myChart.setOption(option);*/
    });
</script>