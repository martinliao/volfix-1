<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>志工管理系統</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/plugins/datatables/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/artdialog/css/ui-dialog.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/plugins/select2/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/fileinput/css/fileinput.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/jquery.tagsinput/jquery.tagsinput.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <!-- awesomplete -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/css/awesomplete.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery 2.1.3 -->
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/jQueryUI/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/arttemplate/template-native.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/bootbox.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/toastr/toastr.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/fastclick/fastclick.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/autosize/autosize.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/momentjs/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/momentjs/locales.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>resource/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/fullcalendar/fullcalendar.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/fullcalendar/lang-all.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
    
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/dist/js/app.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/sammy/lib/min/sammy-latest.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/server.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/fileinput/js/fileinput.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/jquery.tagsinput/jquery.tagsinput.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/awesomplete.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>resource/table2excel/jquery.table2excel.min.js"></script>
</head>

<body>

    <div class="container" style="width:1080px;">
        <div class="row">
            <div class="col-sm-12">
                <br>
                <br>
                <h3 class="text-center">臺北市政府公務人員訓練處</h3>
                <div class="row">
                    <div class="col-sm-6" style="float: left;width:50%;">
                        <h4 class="text-center">
                            <?php echo $year-1911; ?>
                            年
                            
                            <?php echo htmlspecialchars($start_month,ENT_HTML5|ENT_QUOTES).'-'.htmlspecialchars($end_month,ENT_HTML5|ENT_QUOTES);?>
                            
                            月份志工餐點與交通補助清冊
                        </h4>
                    </div>
                    <div class="col-sm-6" style="float: right;width:50%;">
                        <h4 class="text-center">
                            中華民國
                            <?php echo $year-1911; ?>
                            年
                            <?php echo date('m') ?>
                            月
                            <?php echo date('d') ?>
                            日
                        </h4>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
            <br>


            <div class="row">
                <div class="col-xs-12">
                    
                    <table class="table table-bordered" id=table2excel >
                        <tr>
                            <th rowspan="2" style="width: 100px;">姓名</th>
                            <th colspan="<?=htmlspecialchars($row_span,ENT_HTML5|ENT_QUOTES)?>">班次</th>
                            <th rowspan="2" style="width: 120px;">金額</th>
                            <th rowspan="2" style="width: 80px;">總計</th>
                            <th rowspan="2" style="width:120px;">簽章</th>
                            <th rowspan="2" style="width:100px;">身分證<br>統一編號</th>
                            <th rowspan="2" style="width:250px;">戶籍地址</th>
                        </tr>
                        <tr>
                            <?php for($j=$start_month;$j<=$end_month;$j++){ ?>
                                <th><?=htmlspecialchars($j,ENT_HTML5|ENT_QUOTES)?> 月</th>
                            <?php } ?>
                            
                            <th>小計</th>
                        </tr>
                            <?php foreach ($getData as $key => $data): ?>
                                <?php $total = array();?>
                                <?php $total_show = '';?>
                                <?php $price_show = '';?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($data['firstname'],ENT_HTML5|ENT_QUOTES) ?>
                                    </td>
                                    <?php for($j=$start_month;$j<=$end_month;$j++){ ?>
                                        <?php $show = '';?>
                                        <?php if(isset($data['category'][$j]) && isset($data['count'][$j])){ ?>
                                            <?php foreach ($data['category'][$j] as $key2 => $value): ?>
                                                <?php if(isset($total[$key2])){ ?>
                                                    <?php $total[$key2]['value'] += $data['count'][$j][$key2]; ?>
                                                <?php } else { ?>
                                                    <?php $total[$key2]['name'] = $value; ?>
                                                    <?php $total[$key2]['value'] = $data['count'][$j][$key2]; ?>
                                                <?php } ?>
                                                <?php $show .= $value.'：'.$data['count'][$j][$key2].'<br>'; ?>
                                            <?php endforeach ?>
                                            <td>
                                                <?=$show?>
                                            </td>
                                        <?php } else { ?>
                                            <td>
                                            </td>
                                        <?php } ?>
                                    <?php } ?>
                                    
                                    <?php foreach ($total as $key3 => $value): ?>
                                        <?php $total_show .= $value['name'].'：'.$value['value'].'<br>'; ?>
                                        <?php $price_show .= $value['name'].'：'.number_format($value['value']*120).'元'.'<br>'; ?>
                                    <?php endforeach ?>
                                    <td>
                                        <?=$total_show?>
                                    </td>
                                    <td>
                                        <?=$price_show?>
                                    </td>
                                    <td><?php echo htmlspecialchars(number_format($data['amount']),ENT_HTML5|ENT_QUOTES).'元' ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($data['signature'],ENT_HTML5|ENT_QUOTES) ?>" style="width: 100px;">
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($data['idNo'],ENT_HTML5|ENT_QUOTES) ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($data['address'],ENT_HTML5|ENT_QUOTES) ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php foreach ($userData as $key => $data): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($data['name'],ENT_HTML5|ENT_QUOTES) ?>
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($data['signature'],ENT_HTML5|ENT_QUOTES) ?>" style="width: 100px;">
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($data['idNo'],ENT_HTML5|ENT_QUOTES) ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($data['address'],ENT_HTML5|ENT_QUOTES) ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <tr>
                            <td colspan="6">總計金額</td>
                            <td colspan="4"><?php echo htmlspecialchars(number_format($total_amount),ENT_HTML5|ENT_QUOTES).'元' ?></td>
                        </tr>

                    </table>

                    <h5>
                        註:本補助次數計算以每班次連續服務3小時為1次，每次120元（含餐點與交通補助）。
                    </h5>



                </div>
            </div>


        </div>


    </div>


    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("#table2excel").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Worksheet Name",
                filename: "SomeFile", //do not include extension
                fileext: ".xls" // file extension
            }); 
        });
    </script>



</body>
</html>











