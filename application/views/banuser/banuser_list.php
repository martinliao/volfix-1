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
                    <!-- <h3 class="box-title">個人資料</h3> -->
                    <?php if ($page_name == 'edit') { ?>
                        <h3 style="color: red">目前為修改模式</h3>
                    <?php } ?>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form id="banuser_query" class="form-horizontal" role="form" method="post" action="<?=$link_save;?>">
                    <div class="box-body">
                        <div class="form-group form-inline">
                            <label for="name" class="col-sm-2 control-label">統計取消次數</label>
                            <div class="col-sm-10">
                                <select name="month_start">
                                    <?php
                                        $current = intval(date('m'));
                                        for ($m=1; $m <= $current; $m++) { 
                                            if (isset($defaultMonth)) {
                                                echo '<option value="'.str_pad($m,2,'0',STR_PAD_LEFT).'" '.($defaultMonth==$m?'selected':null).'>'.str_pad($m,2,'0',STR_PAD_LEFT).'</option>';
                                            }
                                            else {
                                                echo '<option value="'.str_pad($m,2,'0',STR_PAD_LEFT).'" '.((date('m'))==$m?'selected':null).'>'.str_pad($m,2,'0',STR_PAD_LEFT).'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                                月
                                <small id="monthHelpInline" class="text-muted">
                                (假設當前為5月,若設定3月,則統計日期區間將為3/1~5/31)
                                </small>
                                <!--現在月份: <?=date('m')?>-->
                            </div>
                        </div>
                        <div class="form-group form-inline">
                            <label for="name" class="col-sm-2 control-label">設定取消次數標準: </label>
                            <div class="col-sm-10">
                                <label for="cat1" class="control-label">班務</label>
                                <input type="text" class="form-control" id="cat1" name="category1" placeholder="班務" value="3" >
                                <label for="cat2" class="control-label">警衛</label>
                                <input type="text" class="form-control" id="cat2" name="category2" placeholder="警衛" value="2" >
                                <label for="cat3" class="control-label">圖書</label>
                                <input type="text" class="form-control" id="cat3" name="category3" placeholder="圖書" value="2" >
                                <label for="cat4" class="control-label">客服</label>
                                <input type="text" class="form-control" id="cat4" name="category4" placeholder="客服" value="3" >
                                <label for="cat5" class="control-label">行政</label>
                                <input type="text" class="form-control" id="cat7" name="category7" placeholder="行政" value="2" >
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class='btn btn-success btn-flat' >查詢</button>
                    </div>
                    <!-- /.box-footer -->
                </form>
                <table id="example2" class="table table-bordered table-hover" style="text-align:center;vertical-align:middle">
                    <thead>
                    <tr>
                        <th style="text-align:center;">姓名</th>
                        <th style="text-align:center;">身份證字號</th>
                        <th style="text-align:center;">志工種類</th>
                        <th style="text-align:center;">累計</th>
                        <th>Ban Gate</th>
                        <th style="text-align:center;">已停權類別及期限</th>
                        <th style="text-align:center;">設定</th>
                    </tr> 
                    </thead>
                    <tbody>
                    <?php
                    if (isset($query_start)) {
                        echo "<small id='queryMonthHelpInline' class=text-muted'>$query_start ~ $query_end</small>";
                    }
                    if (!empty($list) ) {
                        for($i=0;$i<count($list);$i++){
                            echo '<tr class="bg-danger">
                            <td style="text-align:center;">'.$list[$i]->firstname.'</td>
                            <td style="text-align:center;">'.$list[$i]->idNo.'</td>
                            <td style="text-align:center;">'.$list[$i]->category.'</td>';
                            /*if ($list[$i]->cancels >= 2 ) {
                                echo '<td style="text-align:center;color:red;"><strong>'.$list[$i]->cancels.'</strong></td>';
                            } else {
                                echo '<td style="text-align:center;">'.$list[$i]->cancels.'</td>';
                            }/** */
                            echo '<td style="text-align:center;">'.$list[$i]->cancels.'</td>';
                            echo '<td>'.$list[$i]->ban_gate.'</td>';
                            if (isset($list[$i]->ban_start)) {
                                echo '<td style="text-align:left;">'. $list[$i]->category_name . ': '. $list[$i]->ban_start . '~' . $list[$i]->ban_end .'</td>';
                                echo '<td></td>';
                            }
                            else {
                                echo '<td></td>';
                                echo '<td></td>';
                            }
                            /*if (isset($list[$i]->ban_start)) {
                                echo '<td style="text-align:left;">'. $list[$i]->category_name . ': '. $list[$i]->ban_start . '~' . $list[$i]->ban_end .'</td>';
                                echo '<td>
                                    <div class="btn-dataset">
                                        <a href="#" onclick="remove_data(\''.base_url().'volunteer_manage/evaluation_leader_user_remove/\',{\'userID\':\''.$list[$i]->idNo.'\'})" title="re-calc" class="btn btn-default btn-xs">
                                            <i class="fa fa-trash fa-trash"></i>&nbsp;&nbsp;重新累計
                                        </a>
                                    </div>
                                </td>';
                            } else {
                                echo '<td></td>';
                                if ($list[$i]->cancels >= 2 ) {
                                    echo '<td>
                                        <div class="btn-dataset">
                                            <!--button class="btn btn-primary btn-sm" data-target="#ban_edit" data-toggle="modal" data-category='. $list[$i]->category .'>停權</button-->
                                            <!--button class="btn btn-primary btn-sm openban" data-target="#ban_edit" data-category='. $list[$i]->category . ' data-idNo='. $list[$i]->idNo . '>
                                            停權</button-->
                                            <!--a href="' . base_url() . 'volunteer_manage/ban_edit?userID='.$list[$i]->idNo . '" title="ban" class="btn btn-default btn-xs">
                                                <i class="fa fa-trash fa-trash"></i>&nbsp;&nbsp;停權
                                            </a-->
                                        </div>
                                    </td>';
                                } else {
                                    echo '<td></td>';
                                }
                            }/** */
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- /.box -->
<?php include('banuser_modal.php');?>
<script type="text/javascript">
    $(function () {
        var banTimes = <?= $ban_times?>;
        $('#example2').DataTable({
            columnDefs: [{
                targets: 3,
                render: function (data, type, row) {
                    if (row[3] >= 2) {
                        return '<td style="text-align:center;"><strong><font color="red">' + row[3] + '</font></strong></td>';
                    }
                    else {
                        return '<td style="text-align:center;">' + row[3] + '</td>';
                    }
                }
                }, {
                    target: 4,
                    visible: false,
                    searchable: false
                }, {
                targets: 6,
                render: function (data, type, row) {
                    var _returnButtons= '';
                    if (row[5]) {
                        _returnButtons = '<div class="btn-dataset"><button type="button" onclick="resume(\'' + row[1] + '\', \'' + row[2] + '\')" value="0" class="btn btn-warning btn-sm" data-target="#ban_edit" data-idNo=' + row[1] + ' >重新累計</button></div>';
                        //_returnButtons = '<div class="btn-dataset"><a type="button" href="' + 'www.click-ap.com' + '">重新累計</button></div>';
                    } else {
                        if (row[3] >= row[4] ) { //if (row[3] >= banTimes[2] ) {
                            _returnButtons += '<div class="btn-dataset"><button type="button" onclick="banedit(\'' + row[1] + '\', \'' + row[2] + '\')" value="0" class="btn btn-danger btn-sm" data-target="#ban_edit" data-idNo=' + row[1] + ' >停權</button></div>';
                        }
                    }
                    //return '<div class="btn-group"> <button type="button" onclick="set_value(' + item.ID + ')" value="0" class="btn btn-warning" data-toggle="modal" data-target="#myModal">停權</button></div>'
                    return _returnButtons;
                }}
            ],
            rowGroup: {
                dataSrc: 0
            },
        });
    });
    $(document).ready(function() {
        $('.openban').on('click', function(e) {
            $('#ban_edit').modal('show');
            var _defaultCategory = $(this).attr('data-category');
            $('#ban_edit #category_name').val(_defaultCategory);
            $('.vID:not(.all)').each(function () {
                if (_defaultCategory == $(this).attr("data-name")) {
                    $(this).prop( "checked", true );
                } else {
                    $(this).prop( "checked", false );
                }
            });
            $('#ban_edit #idNo').val($(this).attr('data-idNo'));
        });
    });
    function banedit(idNo, _defaultCategory) {
        $('#ban_edit').modal('show');
        //var _defaultCategory = $(this).attr('data-category');
        $('#ban_edit #category_name').val(_defaultCategory);
        $('.vID:not(.all)').each(function () {
            if (_defaultCategory == $(this).attr("data-name")) {
                $(this).prop( "checked", true );
            } else {
                $(this).prop( "checked", false );
            }
        });
        //$('#ban_edit #idNo').val($(this).attr('data-idNo'));
        $('#ban_edit #idNo').val(idNo);
    }
    function resume(idNo, _defaultCategory) {
        var _queryMonth = $('#banuser_query').find('select[name="month_start"]').val()
        // var formData = new FormData(document.querySelector('form'))
        // $('banuser_query').serialize()
        //var parameter = $(this).val();
        window.location = "<?=base_url();?>banned/ban_resume?idNo=" + idNo + "&month=" + _queryMonth;
    }
</script>