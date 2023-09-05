<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">H.設定報名階段名額</h3>
            </div>
            <div class="box-body">  

                <table class="table table-bordered">
                    <tr>
                        <td style="width: 200px;">分類</td>
                        <td>
                            <select id="category" class="form-control">
                                <option value="1" <?=(isset($detail[0]['category']) && $detail[0]['category'] == '1')?'selected':'' ?>>班務</option>
                                <option value="2" <?=(isset($detail[0]['category']) && $detail[0]['category'] == '2')?'selected':'' ?>>警衛</option>
                                <option value="3" <?=(isset($detail[0]['category']) && $detail[0]['category'] == '3')?'selected':'' ?>>圖書</option>
                                <option value="4" <?=(isset($detail[0]['category']) && $detail[0]['category'] == '4')?'selected':'' ?>>客服</option>
                                <option value="7" <?=(isset($detail[0]['category']) && $detail[0]['category'] == '7')?'selected':'' ?>>行政</option>
                                <!-- <option value="8">會計</option>
                                <option value="9">人事</option> -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;">報名_開始時間</td>
                        <td>
                            <?php if(isset($detail[0]['startTime'])){ ?>
                                <input type="datetime-local" id="startTime" class="form-control" value="<?php echo htmlspecialchars(date("Y-m-d",strtotime($detail[0]['startTime']." 1 month")), ENT_HTML5|ENT_QUOTES).'T'.htmlspecialchars(date("H:i",strtotime($detail[0]['startTime'])), ENT_HTML5|ENT_QUOTES) ?>">
                            <?php } else { ?>
                                <input type="datetime-local" id="startTime" class="form-control" value="<?php echo date('Y-m-d') ?>T10:00">
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;">報名_結束時間</td>
                        <td>
                            <?php if(isset($detail[0]['endTime'])){ ?>
                                <input type="datetime-local" id="endTime" class="form-control" value="<?php echo htmlspecialchars(date("Y-m-d",strtotime($detail[0]['endTime']." 1 month")), ENT_HTML5|ENT_QUOTES).'T'.htmlspecialchars(date("H:i",strtotime($detail[0]['endTime'])), ENT_HTML5|ENT_QUOTES) ?>">
                            <?php } else { ?>
                                <input type="datetime-local" id="endTime" class="form-control" value="<?php echo date('Y-m-d') ?>T23:00">
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="width: 200px;">課程_開始時間</td>
                        <td>
                            <?php if(isset($detail[0]['reg_startTime'])){ ?>
                                <input type="datetime-local" id="reg_startTime" class="form-control" value="<?php echo htmlspecialchars(date("Y-m-d",strtotime($detail[0]['reg_startTime']." 1 month")), ENT_HTML5|ENT_QUOTES).'T'.htmlspecialchars(date("H:i",strtotime($detail[0]['reg_startTime'])), ENT_HTML5|ENT_QUOTES) ?>">
                            <?php } else { ?>
                                <input type="datetime-local" id="reg_startTime" class="form-control" value="<?php echo date('Y-m-d',strtotime(date('Y-m-01',strtotime('1 month')))) ?>T00:00">
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;">課程_結束時間</td>
                        <td>
                            <?php if(isset($detail[0]['reg_endTime'])){ ?>
                                <input type="datetime-local" id="reg_endTime" class="form-control" value="<?php echo htmlspecialchars(date("Y-m-d",strtotime($detail[0]['reg_endTime']." 1 month")), ENT_HTML5|ENT_QUOTES).'T'.htmlspecialchars(date("H:i",strtotime($detail[0]['reg_endTime'])), ENT_HTML5|ENT_QUOTES) ?>">
                            <?php } else { ?>
                                <input type="datetime-local" id="reg_endTime" class="form-control" value="<?php echo date('Y-m-d',strtotime(date('Y-m-01',strtotime('2 month')).'-1 day')) ?>T23:00">
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;">報名上限</td>
                        <td>
                            <?php if(isset($detail[0]['sum'])){ ?>
                                <input type="number" id="sum" class="form-control" value="<?php echo htmlspecialchars($detail[0]['sum'], ENT_HTML5|ENT_QUOTES) ?>">
                            <?php } else { ?>
                                <input type="number" id="sum" class="form-control" value="4">
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        
                        <td colspan="2">
                        <?php if(isset($detail[0]['first']) == '1'){ ?>
                            <input type="checkbox" name="first" id="first" value="1" style="margin-left:20%" checked>
                        <?php } else { ?>
                            <input type="checkbox" name="first" id="first" value="1" style="margin-left:20%">
                        <?php } ?>
                        <label>是否為第一階段</label>
                        <form action="<?=htmlspecialchars($save_url, ENT_HTML5|ENT_QUOTES)?>" method="post" style="display:inline">
                            <label style="margin-left:25%">編號</label>
                            <input type="number" name="seq_number" value="">
                            <button type="submit" class="btn btn-primary">帶入為下個月</button>
                        </form>
                            <button id="ajax-add" class="btn btn-primary" style="float: right;">
                                新增限制
                            </button>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#ajax-add').on('click', function(event) {
    event.preventDefault();
    /* Act on the event */
    var startTime = $('#startTime').val()+':00' ;
    var endTime = $('#endTime').val()+':00' ;
    var sum = $('#sum').val() ;
    
    if($('#first').is(':checked')){
        var first = 1;
    } else {
        var first = 0;
    }

    // make
    var insertData = {
        category        : $('#category').val()                  ,
        startTime       :  startTime                            ,
        endTime         :  endTime                              ,
        reg_startTime   :  $('#reg_startTime').val()+':00'      ,
        reg_endTime     :  $('#reg_endTime').val()+':00'        ,
        sum             :  sum          ,
        first           : first,
    } ;

    // ajax
    $.ajax({
        url: '../volunteer_manage/ajax_insert_report_stage',
        type: 'POST',
        dataType: 'json',
        data: insertData,
    })
    .done(function(msg) {
        if (msg.code=='100') {
            alert('新增完成！');
            location.reload() ;
        } else if (msg.code=='101') {
            if (confirm(msg.message)) {
                insertData['confirmed'] = true;
                $.post( "../volunteer_manage/ajax_insert_report_stage", insertData)
                .done(function(response){
                    var data = $.parseJSON(response);
                    if (data.code=='100') {
                        alert('新增完成！');
                        location.reload() ;
                    } else {
                        alert('新增失敗，請稍後再試！');            
                    }
                });
            }
        } else {
            alert('新增失敗，請稍後再試！');
        }
    })
    .fail(function() {
        alert('新增失敗，請稍後再試！');
    })
    .always(function() {
        console.log("complete");
    });
});
</script>


<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">  

                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>編號</th>
                            <th>分類</th>
                            <th>報名_開始時間</th>
                            <th>報名_結束時間</th>
                            <th>課程_開始時間</th>
                            <th>課程_結束時間</th>
                            <th>報名上限</th>
                            <th>第一階段</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $stageList as $data ) : ?>
                        <tr>
                            <td>
                                <?php echo $data->no ?>
                            </td>
                            <td>
                                <?php
                                    switch( $data->category ) {
                                        case '1': echo "班務" ; break ;
                                        case '2': echo "警衛" ; break ;
                                        case '3': echo "圖書" ; break ;
                                        case '4': echo "客服" ; break ;
                                        case '7': echo "行政" ; break ;
                                        case '8': echo "會計" ; break ;
                                        case '9': echo "人事" ; break ;
                                    } ;
                                ?>
                            </td>
                            <td>
                                <?php echo $data->startTime ?>
                            </td>
                            <td>
                                <?php echo $data->endTime ?>
                            </td>
                            <td>
                                <?php echo $data->reg_startTime ?>
                            </td>
                            <td>
                                <?php echo $data->reg_endTime ?>
                            </td>
                            <td>
                                <?php echo $data->sum ?>
                            </td>
                            <td><?=($data->first==1)?'是':'否'?></td>
                            <td>
                                <!-- <button class="btn btn-danger ajax-det" no="<?php echo $data->no ?>">
                                    刪除
                                </button> -->
                            </td>
                        </tr>
                    <?php endforeach ; ?>
                </table>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#example2').DataTable({
        info: false,
        paging: false,
        searching: false,
        order: [[0, 'desc']],
        columnDefs: [{
            target: 0,
            searchable: false
        }, {
            target: 8,
            render: function (data, type, row) {
                return '<div class="btn-group"> <button type="button" onclick="ajaxDet(' + row[0] + ');" value="0" class="btn btn-warning" data-toggle="modal" data-target="#myModal">刪除</button></div>'
            },
            orderable: false
        },
        { bSortable: false, targets: [7,8] }
        ]
    });
});
//$('.ajax-det').on('click', function(event) {
    function ajaxDet(no) {
        //event.preventDefault();
        /* Act on the event */
        //var no = $(this).attr('no') ;
        // make
        var insertData = {
            no   :  no    ,
        } ;
        if ( confirm('確定刪除此限制？') ) {
            // ajax
            $.ajax({
                url: '../volunteer_manage/ajax_delete_report_stage',
                type: 'POST',
                dataType: 'json',
                data: insertData,
            })
            .done(function(msg) {
                if (msg.code=='100') {
                    alert('刪除完成！');
                    location.reload() ;
                } else {
                    alert('刪除失敗，請稍後再試！');
                }
            })
            .fail(function() {
                alert('刪除失敗，請稍後再試！');
            })
            .always(function() {
                console.log("complete");
            });
        }
    }//);
</script>