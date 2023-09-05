<div class="row">
<div class="col-xs-12">
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">G.異動通知(錄取及取消)</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
    <form action="<?php echo base_url();?>change_log/" method="POST" id="sentq">
      <select class='form-control input-sm' style='width: auto;float: left;margin-top: 2px' name='year'>
        <option values='112' selected="selected">112</option>
        <option values='111'>111</option>
        <option values='110'>110</option>
        <option values='109'>109</option>
        <option values='108'>108</option>
        <option values='109'>107</option>
      </select>
      <p style='float: left;margin-top: 5px'>年</p>
      <!--select class='form-control input-sm' name='month' style='width: auto;float: left;margin-top: 2px'>
        <option values='1' selected="selected">1</option>
        <option values='2'>2</option>
        <option values='3'>3</option>
        <option values='4'>4</option>
        <option values='5'>5</option>
        <option values='6'>6</option>
        <option values='7'>7</option>
        <option values='8'>8</option>
        <option values='9'>9</option>
        <option values='10'>10</option>
        <option values='11'>11</option>
        <option values='12'>12</option>
      </select-->
      <select class="form-control input-sm" name="month" style='width: auto;float: left;margin-top: 2px'>
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
      <p style='float: left;margin-top: 5px'>月</p>&emsp;
      <label><input type="checkbox" name="forty8" <?=isset($forty8) && $forty8 ? 'checked' : ''; ?> >48小時內取消</label>&emsp;
      <button type="button" class='btn btn-success btn-flat' onclick="sentFun()">確認</button>
    </form>
      <table id="example2" class="table table-bordered table-hover" style="text-align:center;vertical-align:middle">
        <thead>
          <tr>
            <th style="text-align:center;">志工種類</th>
            <th style="text-align:center;">年度</th>
            <th style="text-align:center;">班期代碼</th>
            <th style="text-align:center;">期別</th>
            <th style="text-align:center;">班期名稱</th>
            <th style="text-align:center;">姓名</th>
            <th style="text-align:center;">上課日期</th>
            <th style="text-align:center;">時段</th>
            <th style="text-align:center;">操作</th>
            <th style="text-align:center;">重新累計原因</th>
            <th style="text-align:center;">異動時間</th>
           </tr> 
        </thead>
        <tbody>
        <?php 
          for($i=0;$i<count($list);$i++){
            echo '<tr>
                    <td style="vertical-align:middle">'.$list[$i]->category.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->year.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->class_no.'</td> 
                    <td style="vertical-align:middle">'.$list[$i]->term.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->course_name.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->firstname.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->course_date.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->type.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->action.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->description.'</td>
                    <td style="vertical-align:middle">'.$list[$i]->modifytime.'</td>
                  </tr>';
          }  
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

<script type="text/javascript">
  function sentFun(){
    var obj = document.getElementById('sentq');
    obj.submit();
  }
</script>
<script type="text/javascript">
  $(function () {
    $('#example2').DataTable({
      oLanguage:  {
              "sProcessing": "處理中...",
              "sLengthMenu": "<button type='button' class='btn btn-success btn-flat' onclick='sentFun()'>搜尋</button>",
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