<form method="post">
    <table class="table table-border table-hover">
        <thead>
            <tr>
                <th class="font-weight-bold" style="width: 20px;">№</th>
                <th class="font-weight-bold" style="width: 170px;">Баганын нэр /src/</th>
                <th class="font-weight-bold" style="width: 120px;">Төрөл /src/</th>
                <th class="font-weight-bold" style="width: 180px;">Нэр /src/</th>
                <th class="font-weight-bold">Trg</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($this->srcFields as $k => $row) {
            ?>
            <tr>
                <td><?php echo ++$k; ?>.</td>
                <td>
                    <?php 
                    echo ($row['IS_UNIQUE'] == '1' ? '🔑 ' : '');
                    echo $row['COLUMN_NAME']; 
                    echo Form::hidden(array('name' => 'mapId[]', 'value' => $row['ID']));
                    echo Form::hidden(array('name' => 'srcId[]', 'value' => $row['SRC_MAP_ID'].'|'.$row['COLUMN_NAME']));
                    ?>
                </th>
                <td><?php echo $row['SHOW_TYPE']; ?></td>
                <td><?php echo $row['LABEL_NAME']; ?></td>
                <td>
                    <?php 
                    $trgComboTmp = $this->trgCombo;
                    $trgComboTmp = str_replace('<option value="'.$row['TRG_MAP_ID'].'">', '<option value="'.$row['TRG_MAP_ID'].'" selected>', $trgComboTmp);
                    echo $trgComboTmp; 
                    ?>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <?php
    echo Form::hidden(array('name' => 'indicatorId', 'value' => $this->indicatorId)); 
    echo Form::hidden(array('name' => 'mainIndicatorId', 'value' => $this->mainIndicatorId)); 
    ?>
</form>