<?php
    $state = $page_name != 'edit' ? 'disabled' : '';
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">P.停權管理設定</h3>
            </div>
            <?php if (validation_errors()) { ?>
                <div class="alert alert-danger">
                    <button class="close" data-dismiss="alert" type="button">×</button>
                    <?= validation_errors(); ?>
                </div>
            <?php } ?>
            <!-- Horizontal Form -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 style="color: red">重新累計設定</h3>
                    <?php
                        if (isset($user_name)) {
                            echo $user_name;
                        }
                        if (isset($response)) {
                            echo "<script>alert('$response');</script>";
                        }
                    ?>
                </div>
                <?php
                    if (isset($query_start)) {
                        echo "<small id='queryMonthHelpInline' class=text-muted'>$query_start ~ $query_end</small>";
                    }
                ?>
                <form action="<?php echo base_url('/volunteer_manage/resume_edit') ?>" method="POST">
                    <input type="hidden" name="idNo" value="<?php echo $idNo ?>">
                    <input type="hidden" name="user_name" value="<?= isset($user_name)?$user_name:null ?>">
                    <input type="hidden" name="query_start" value="<?php echo $query_start ?>">
                    <input type="hidden" name="query_end" value="<?php echo $query_end ?>">
                <table id="example2" class="table table-bordered table-hover" style="text-align:center;vertical-align:middle">
                    <thead>
                    <tr>
                        <th style="text-align:center;">id</th>
                        <th style="text-align:center;">志工取消時間</th>
                        <th style="text-align:center;">類別</th>
                        <th style="text-align:center;">班期名稱</th>
                        <th style="text-align:center;">服務時間</th>
                        <th style="text-align:center;">重新累計原因</th>
                    </tr> 
                    </thead>
                    <tbody>
                    <?php
                        foreach($list as $item) {
                            echo '<tr>
                            <td style="text-align:center;">'.$item->id.'</td>
                            <td style="text-align:center;">'.$item->modifytime.'</td>
                            <td style="text-align:center;">'.$item->category.'</td>
                            <td style="text-align:left;">'.$item->course_name.'</td>
                            <td style="text-align:center;">'.$item->service_time.'</td>
                            <td style="text-align:center;">'.$item->description.'</td>';
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
                <div style="width: 100%;text-align: right;padding-right: 20px">
                        <button>儲存</button>                        
                    </div>
                </form>
            </div>
            <!-- /.box -->
<?php include('banuser_modal.php');?>
<script type="text/javascript">
    $(function () {
        $('#example2').DataTable({
            info: false,
            paging: false,
            searching: false,
            columnDefs: [{
                    target: 0,
                    visible: false,
                    searchable: false
                }, {
                targets: 5,
                render: function (data, type, row) {
                    if (row[5]) {
                        return '<input type="text" name="resume_' + row[0] + '" class="form-control" value="'+ row[5] +'">';
                    } else {
                        return '<input type="text" name="resume_' + row[0] + '" class="form-control" value="">';
                    }
                    
                }}
            ]
        });
    });
</script>