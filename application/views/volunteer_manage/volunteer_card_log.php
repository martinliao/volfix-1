<script type="text/javascript">
  $(function () {
    $('#example2').dataTable({
      oLanguage:  {
              "sProcessing": "處理中...",
              "sLengthMenu": "顯示 _MENU_ 筆記錄",
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
        "lengthMenu": [ [20, 100], [20, 100] ],
  
        deferRender: true,
        searching: false,
        bSort: true,  
        select:{
            style:'single',
            blurable: true
        }
    });
  });
</script>

<div class="row">
<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">F.志工刷卡紀錄管理</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div>
                <form id="post_form" method="POST" action="">
                    刷卡時間：起
                    <?php
                      $input = new input_builder('date','start_date',$start_date);
                      $input->set_style('display:inline-block;width:180px')
                            ->print_html();
                    ?>
                    迄
                    <?php
                      $input = new input_builder('date','end_date',$end_date);
                      $input->set_style('display:inline-block;width:180px')
                            ->print_html();
                    ?>
                    <button type="button" class='btn btn-success btn-flat' onclick="setToday()">設定今天</button>
                    <button type="button" class='btn btn-success btn-flat' onclick="search()">確認</button>
                    <button type="button" class='btn btn-success btn-flat' onclick="exportFun()">匯出</button>
                    <br>
                    <br>
                    <div class="form-inline">
                    姓名：<input type="text" class="awesomplete" data-minchars="1" id="firstname" name="firstname" value="<?=$name?>"></input>
                    <p class="form-control-static">* 查無此人, 請先至"C.志工選員"進行志工報名作業.</p>
                    </div>
                    <br>
                    <br>
                    <div>
                    <label><input type="checkbox" name="category[]" value="all" class="all vID" <?=in_array('all',$query_category)?'checked':''?> >全部</label>&emsp;
                    <label><input type="checkbox" name="category[]" value="1" class="vID" <?=in_array('1',$query_category)?'checked':''?>>班務</label>&emsp;
                    <?php
                        $categoryList = array();
                        $categoryList[1]['name'] = '班務';
                        $categoryList[1]['total_hours'] = 0;
                        for($i=0;$i<count($category);$i++) { 
                          $categoryList[$category[$i]->id]['name'] = $category[$i]->name;
                          $categoryList[$category[$i]->id]['total_hours'] = 0;
                          $tmp_checked = in_array($category[$i]->id,$query_category)?'checked':'';
                          echo '<label><input type="checkbox" name="category[]" value="'.$category[$i]->id.'" class="vID"'.$tmp_checked.'>'.$category[$i]->name.'</label>&emsp;';
                        }
                    ?>
                    </div>
                </form>
                <?php if(isset($info) && !empty($info)){ ?>

                <table id="example2" class="table table-bordered table-hover" style="text-align:center;vertical-align:middle">
                    <thead>
                      <tr>
                        <th style="text-align:center;">姓名</th>
                        <th style="text-align:center;">刷卡日期</th>
                        <th style="text-align:center;">簽到時間</th>
                        <th style="text-align:center;">簽退時間</th>
                        <th style="text-align:center;">當日報名類別時數</th>
                        <th style="text-align:center;">當日報名總時數</th>
                        <th style="text-align:center;">刷卡紀錄</th>
                        <th style="text-align:center;">補登</th>
                        <th style="text-align:center;">實際服勤時數</th>
                        <th style="text-align:center;">當日班次</th>
                       </tr> 
                    </thead>
                    <tbody>
                    <?php
                        for($i=0;$i<count($info);$i++){
                          $sign_time_background = '';
                          $sign_log_background = '';
                          if(count($info[$i]['sign_time']) > 1){
                            $sign_out_time = $info[$i]['sign_time'][count($info[$i]['sign_time'])-1];
                          } else {
                            $sign_out_time = '';
                          }

                          $category_hours = '';
                          if(isset($info[$i]['category'][1])){
                            $categoryList[$info[$i]['category'][1]['category_id']]['total_hours'] += $info[$i]['category'][1]['hours'];
                            $category_hours .= $info[$i]['category'][1]['name'].$info[$i]['category'][1]['hours'];
                            if(count($info[$i]['category']) == 2){
                              $category_hours .= '<br>';
                            }
                          }

                          if(isset($info[$i]['category'][2])){
                            $categoryList[$info[$i]['category'][2]['category_id']]['total_hours'] += $info[$i]['category'][2]['hours'];
                            $category_hours .= $info[$i]['category'][2]['name'].$info[$i]['category'][2]['hours'];
                          }
                          
                          $sign_log_list = implode('<br>', $info[$i]['sign_time']);

                          if(count($info[$i]['sign_time']) > 1){
                            $tmp_first_sign_time = str_replace('<font style="color:red">(補)</font>',"",$info[$i]['sign_time'][0]);
                            $tmp_last_sign_time = str_replace('<font style="color:red">(補)</font>',"",$info[$i]['sign_time'][count($info[$i]['sign_time'])-1]);

                            $true_hours = (strtotime($tmp_last_sign_time) - strtotime($tmp_first_sign_time))/3600;
                      
                            if(round($true_hours) > $true_hours){
                              $true_hours = floor($true_hours)+0.5;
                            } else if(round($true_hours) < $true_hours){
                              $true_hours = floor($true_hours);
                            }

                            if($true_hours > 8){
                              $true_hours = 8;
                            }
                          } else {
                            $true_hours = 0;
                            $info[$i]['sign_time'][0] .= '<br>刷卡異常！';
                            $info[$i]['total_hours'] = '<font color="red">'.$info[$i]['total_hours'].'</font>';
                            $sign_time_background = ';background-color:red';
                            $sign_log_background = ';background-color:red';
                          }

                          $class_times = floor($true_hours/3);

                          if($class_times > count($info[$i]['category'])){
                            $class_times = 1;
                          }
                          // print_r($true_hours);
                          //     die('1');
                          echo '<tr>
                                  <td style="vertical-align:middle">'.$info[$i]['name'].'</td>
                                  <td style="vertical-align:middle">'.$info[$i]['sign_date'].'</td>
                                  <td style="vertical-align:middle'.$sign_time_background.'">'.$info[$i]['sign_time'][0].'</td>
                                  <td style="vertical-align:middle">'.$sign_out_time.'</td>
                                  <td style="vertical-align:middle">'.$category_hours.'</td>
                                  <td style="vertical-align:middle">'.$info[$i]['total_hours'].'</td>
                                  <td style="vertical-align:middle'.$sign_log_background.'">'.$sign_log_list.'</td>
                                  <td style="vertical-align:middle"><button type="button" class="btn btn-success btn-flat" onclick="signFun('.$info[$i]['uid'].",'".$info[$i]['sign_date']."'".')">補登</button></td>
                                  <td style="vertical-align:middle">'.$true_hours.'</td>
                                  <td style="vertical-align:middle">'.$class_times.'</td>';
                          echo  '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
                <?php

                    $total_true_hours = '';
                    $total_class_times = '';
                    $categoryList = array_values($categoryList);
                    
                    for($i=0;$i<count($categoryList);$i++){
                      $total_true_hours .= $categoryList[$i]['name'].$categoryList[$i]['total_hours'].'小時';
                      $total_class_times .= $categoryList[$i]['name'].floor($categoryList[$i]['total_hours']/3).'班次';;

                      if(count($categoryList) == ($i+1)){
                        $total_true_hours .= '。';
                        $total_class_times .= '。';
                      } else {
                        $total_true_hours .= '/';
                        $total_class_times .= '/';
                      }
                    }

                    echo '<p style="font-size:18px;font-weight:bolder">以上各類別實際服勤總時數：'.$total_true_hours.'</p>';
                    echo '<p style="font-size:18px;font-weight:bolder">以上各類別實際服勤總班次：'.$total_class_times.'</p>';
                  

                ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
</div>

<script>
    var firstname = new Awesomplete(document.getElementById('firstname'));

    $('#firstname').on('keyup', function() {
      $.ajax({
          //url: 'https://elearning.taipei/eda/getVolunteerList.php?key=' + this.value,
          url: '<?=$eda_url?>/getVolunteerList.php?key=' + this.value,
          type: 'GET',
          dataType: 'json'
        })
        .done(function(data) {
          var list = [];
          for(var i=0;i<data.length;i++){
            list.push(data[i]);
          }
    
          firstname.list = list;
        });
    });

    $('.vID:not(.all)').on('change',function(){
        var all = true;
        $('.vID:not(.all)').each(function(){
            all = all && ($(this).prop('checked'));
            console.log($(this));
            console.log($(this).prop('checked'));
        });
        if(all)
            $('.vID.all').prop('checked',true);
        else
            $('.vID.all').prop('checked',false);            
    });
    $('.vID.all').on('click',function(){
            $('.vID:not(.all)').prop('checked',$(this).prop('checked'));
    });
    function search(){
        $('#post_form').attr('action','<?php echo base_url('/Volunteer_card_log') ?>');
        $('#post_form').submit();
    }

    function signFun(id,sign_date){
        window.open('<?php echo base_url('/Volunteer_card_log/sign') ?>'+'/'+id+'/'+sign_date, 'sign', config='height=350,width=500');
    }

    function setToday(){
        var Today=new Date();
        var month = ((Today.getMonth()+1) < 10 ? '0' : '') + (Today.getMonth()+1);
        var day = ((Today.getDate()+1) < 10 ? '0' : '') + (Today.getDate());

        document.getElementById('start_date').value = Today.getFullYear()+ "-" + month + "-" + day;
        document.getElementById('end_date').value = Today.getFullYear()+ "-" + month + "-" + day;
    }

    function exportFun(){
      var obj = document.getElementById('post_form');
      obj.action = '/eda/manage/Volunteer_card_log/export';
      obj.submit();
    }
</script>