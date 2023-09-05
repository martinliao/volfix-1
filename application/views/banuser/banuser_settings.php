<?php
    $state = $page_name != 'edit' ? 'disabled' : '';
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">停權管理設定</h3>
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
                    <!-- <h3 class="box-title">個人資料</h3> -->
                    <?php if ($page_name == 'edit') { ?>
                        <h3 style="color: red">目前為修改模式</h3>
                    <?php } ?>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" role="form" method="post" action="<?=$link_save;?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">統計取消次數</label>
                            <div class="col-sm-10">
                            <select name="month_start">
                                <?php 
                                    for ($m=1; $m <= 12; $m++) { 
                                        echo '<option value="'.str_pad($m,2,'0',STR_PAD_LEFT).'" '.((date('m'))==$m?'selected':null).'>'.str_pad($m,2,'0',STR_PAD_LEFT).'</option>';
                                    }
                                ?>
                                </select>
                                月
                                現在月份: 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="idno" class="col-sm-2 control-label">身份證字號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="idno" name="idNo" placeholder="IDNO" value="<?= set_value('idNo', $form['idNo']); ?>" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="telphone" class="col-sm-2 control-label">電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="telphone" name="telphone" placeholder="Telphone" value="<?= set_value('telphone', $form['telphone']); ?>" <?=$state?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">電子信箱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email" name="email" placeholder="eMail" value="<?= set_value('email', $form['email']); ?>" <?=$state?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label">戶籍地址</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="<?= set_value('$address', $form['address']); ?>" <?=$state?>>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <?php if ($page_name == 'edit') { ?>
                            <button type="submit" class="btn btn-primary">確認</button>
                            <button type="submit" class="btn btn-default">取消</button>
                        <?php } else { ?>
                            <button type="submit" class="btn btn-primary">編修</button>
                        <?php } ?>
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
            <!-- /.box -->
            <script type="text/javascript">
  $(function () {
    $('#example2').dataTable({
      oLanguage:  {
              "sProcessing": "處理中...",
              //"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
              "sLengthMenu": 'Display <select>'+
      '<option value="10">10</option>'+
      '<option value="20">20</option>'+
      '<option value="30">30</option>'+
      '<option value="40">40</option>'+
      '<option value="50">50</option>'+
      '<option value="-1">All</option>'+
      '</select> records',
              "sZeroRecords": "<font color='red'>目前無您可管理的資料</font>",
              "sInfo": "目前記錄：_START_ 至 _END_, 總筆數：_TOTAL_",
              "sInfoEmpty": "無任何資料",
              "sInfoFiltered": "(過濾總筆數 _MAX_)",
              "sInfoPostFix": "",
              "sSearch": "搜尋",
              "sUrl": "",
              "oPaginate": {
                  "sFirst":    "首頁",
                  "sPrevious": "上頁",
                  "sNext":     "下頁",
                  "sLast":     "末頁"
              }
          },
        deferRender: true,
        searching: false,
        bSort: false,  
        select:{
            style:'single',
            blurable: true
        }
    });
  });
</script>