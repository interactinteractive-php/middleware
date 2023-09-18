<form id="createmvstructurefromfile-form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group row">
                <?php 
                echo Form::label(array(
                    'text' => 'Файл /txt/',  
                    'class' => 'col-form-label col-md-3 text-right pr0 pt5', 
                    'required' => 'required'
                )); 
                ?>
                <div class="col-md-9">
                    <?php 
                    //application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, text/csv, application/csv, 
                    //xls, xlsx, csv,
                    echo Form::file(array(
                        'class' => 'form-control form-control-sm fileInit', 
                        'name' => 'importStructureFile', 
                        'required' => 'required', 
                        'data-valid-extension' => 'txt', 
                        'accept' => 'text/comma-separated-values, text/plain'
                    ));
                    ?> 
                </div>
            </div>
        </div>  
        <div class="col-md-12">
            
            <fieldset class="collapsible border-fieldset mt-2 mb-3">
                <legend>File format</legend>
                <div class="row">
                    <div class="col-md-4">
                        
                        <div class="form-group row">
                            <?php 
                            $headerCheckBox = Form::checkbox(array('class' => 'form-control form-control-sm', 'id' => 'headerCheckBox', 'value' => '1', 'saved_val' => '1'));
                            echo Form::label(array(
                                'text' => $headerCheckBox . ' Header',  
                                'class' => 'col-form-label col-md-3 text-right pr0 pt5', 
                                'for' => 'headerCheckBox'
                            )); 
                            ?>
                            <div class="col-md-7">
                                <?php 
                                echo Form::select(array(
                                    'class' => 'form-control form-control-sm', 
                                    'name' => 'headerSkip', 
                                    'data' => array(
                                        array(
                                            'id' => 'before_skip', 
                                            'name' => 'Before skip'
                                        ), 
                                        array(
                                            'id' => 'after_skip', 
                                            'name' => 'After skip'
                                        )
                                    ), 
                                    'op_value' => 'id', 
                                    'op_text' => 'name', 
                                    'value' => 'before_skip', 
                                    'text' => 'notext'
                                ));
                                ?> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <?php 
                            echo Form::label(array(
                                'text' => 'Skip rows',  
                                'class' => 'col-form-label col-md-2 text-right pr0 pt5'
                            )); 
                            ?>
                            <div class="col-md-4">
                                <?php 
                                echo Form::text(array(
                                    'class' => 'form-control form-control-sm longInit', 
                                    'name' => 'skipRows',
                                    'value' => 0
                                ));
                                ?> 
                            </div>
                        </div>
                    </div>
                </div>    
                
                <div class="row mt10">
                    <div class="col-md-4">
                        
                        <div class="form-group row">
                            <?php 
                            echo Form::label(array(
                                'text' => 'Delimiter',  
                                'class' => 'col-form-label col-md-3 text-right pr0 pt5'
                            )); 
                            ?>
                            <div class="col-md-7">
                                <?php 
                                echo Form::select(array(
                                    'class' => 'form-control form-control-sm', 
                                    'name' => 'delimiter', 
                                    'data' => array(
                                        array(
                                            'id' => ','
                                        ), 
                                        array(
                                            'id' => '|'
                                        ), 
                                        array(
                                            'id' => ';'
                                        ), 
                                        array(
                                            'id' => ':'
                                        ), 
                                        array(
                                            'id' => 'tab'
                                        )
                                    ), 
                                    'op_value' => 'id', 
                                    'op_text' => 'id', 
                                    'value' => '|', 
                                    'text' => 'notext'
                                ));
                                ?> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <?php 
                            echo Form::label(array(
                                'text' => 'Preview row limit',  
                                'class' => 'col-form-label col-md-2 text-right pr0 pt5'
                            )); 
                            ?>
                            <div class="col-md-4">
                                <?php 
                                echo Form::text(array(
                                    'class' => 'form-control form-control-sm longInit', 
                                    'name' => 'previewRowLimit',
                                    'value' => 100
                                ));
                                ?> 
                            </div>
                        </div>
                    </div>
                </div>    
            </fieldset>
            
            <fieldset class="collapsible border-fieldset mt-2 mb-3">
                <legend>File contents</legend>
                <div id="mv-importfile-preview-rows" class="bp-overflow-xy-auto" style="max-height: 500px">
                </div>
            </fieldset>
        </div>
    </div>     
</form> 

<script type="text/javascript">
var $form = $('#createmvstructurefromfile-form');
var $formDialog = $('#dialog-mvrows-createstructure');
var mvReader = new FileReader();
    
$(function() {
    
    $form.on('change', 'input[name="importStructureFile"]', function() {
        
        var $fileInput = $(this);
        var file = $fileInput[0].files[0];

        mvReader.readAsText(file, 'UTF-8');

        mvReader.onload = function () {
            mvFileToHtmlTable();
        };

        mvReader.onerror = function (err) {
            var errorMsg = err.target.error.message;

            if (errorMsg.indexOf('file could not be read') !== -1 || errorMsg.indexOf('ERR_UPLOAD_FILE_CHANGED') !== -1) {

                $fileInput.val('');

                PNotify.removeAll();
                new PNotify({
                    title: 'Info',
                    text: 'Таны сонгосон файл дээр өөрчлөлт орсон тул та файлаа дахин сонгоно уу.',
                    type: 'info',
                    sticker: false, 
                    delay: 1000000000, 
                    addclass: 'pnotify-center'
                });
                Core.unblockUI();
            }
        };
    });
    
    $form.on('change', 'select[name="delimiter"], input[name="previewRowLimit"], input#headerCheckBox, input[name="skipRows"]', function() {
        mvFileToHtmlTable();
    });
});    

function mvFileToHtmlTable() {
    Core.blockUI({message: 'Loading...', boxed: true});
            
    setTimeout(function() {
        
        var rowsData = (mvReader.result).split("\r\n");
        var rowsLength = rowsData.length;

        if (rowsLength > 0) {

            var delimiter = $form.find('select[name="delimiter"]').val(), 
                previewRowLimit = Number($form.find('input[name="previewRowLimit"]').val()),
                skipRows = Number($form.find('input[name="skipRows"]').val()),
                isHeader = $form.find('#headerCheckBox').is(':checked'), 
                rowData, n = 1, htmlTbl = [], firstKey = 0;
                
            if (delimiter == 'tab') {
                delimiter = "　";
            }    
            
            if (skipRows > 0) {
                rowsData = rowsData.slice(skipRows);
            }
            
            var firstRow = (rowsData[firstKey]).split(delimiter);

            htmlTbl.push('<table class="table table-bordered table-hover">');
                htmlTbl.push('<thead>');
                    htmlTbl.push('<tr class="table-border-solid">');
                
                    if (isHeader) {
                        
                        for (var f in firstRow) {
                            htmlTbl.push('<th>'+firstRow[f]+'</th>');
                        }
                        
                        delete rowsData[firstKey];
                        
                    } else {
                        for (var f in firstRow) {
                            htmlTbl.push('<th>Column'+(Number(f) + 1)+'</th>');
                        }
                    }
                
                    htmlTbl.push('</tr>');
                htmlTbl.push('</thead>');

                htmlTbl.push('<tbody>');

                for (var r in rowsData) {
                    rowData = (rowsData[r]).split(delimiter);

                    htmlTbl.push('<tr>');
                        for (var v in rowData) {
                            htmlTbl.push('<td>'+rowData[v]+'</td>');
                        }
                    htmlTbl.push('</tr>');

                    if (n == previewRowLimit) {
                        break;
                    }
                    n++;
                }

                htmlTbl.push('</tbody>');
            htmlTbl.push('</table>');

            $form.find('#mv-importfile-preview-rows').empty().append(htmlTbl.join(''));

            $formDialog.dialog('option', 'position', {my: 'center', at: 'center', of: window});
        }

        Core.unblockUI();
    }, 100);
}
</script>