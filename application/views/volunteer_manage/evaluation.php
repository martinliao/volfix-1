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
          "aoColumnDefs" : [ {
            "bSortable" : false,
            "aTargets" : [ "no-sort" ]
        } ],
        "lengthMenu": [ [10, 50], [10, 50] ],
  
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
            <h3 class="box-title">L.績效考核</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div>
                <form id="post_form" method="POST" action="">
                <input type="hidden" id="batch" name="batch" value="">
                <select class='form-control input-sm' style='width: auto;float: left;margin-top: 2px' id="year" name='year'>
                    <?php
                        $y = date('Y')-1911;
                        if(empty($query_year)){
                            $query_year = $y;
                        }
                            echo '<option values="'.($y+1).'">'.($y+1).'</option>';
                        for($i=0;$i<5;$i++){
                            echo '<option values="'.($y-$i).'" '.($query_year==$y-$i?'selected="selected"':null).'>'.($y-$i).'</option>';

                        }
                    ?>
                    </select>
                    <p style='float: left;margin-top: 5px'>年</p>
                    <select class='form-control input-sm' id="helf" name='helf' style='width: auto;float: left;margin-top: 2px'>
                        <option value='1' <?=($query_helf==1)?'selected':''?>>上</option>
                        <option value='2' <?=($query_helf==2)?'selected':''?>>下</option>
                    </select>
                    <p style='float: left;margin-top: 5px'>半年</p>
                    <label for="status">考核表填寫:</label>
                    <select class='form-control input-sm' id="status" name='status' style='width: auto;float: left;margin-top: 2px'>
                        <option value='all' <?=($status=='all')?'selected':''?>>所有名單</option>
                        <option value='0' <?=($status=='0')?'selected':''?>>應填</option>
                        <option value='1' <?=($status=='1')?'selected':''?>>未填</option>
                    </select>
                    <br>
                    <br>
                    姓名：<input type="text" class="awesomplete" data-minchars="1" id="firstname" name="firstname" value="<?=$query_name?>"></input>
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
                    <br>
                    <button type="button" onclick="search()" class='btn btn-success btn-flat' >查詢</button>
                    <a href="<?=htmlspecialchars($setup_url,ENT_HTML5|ENT_QUOTES)?>" target="_blank"><button type="button" class='btn btn-success btn-flat' >可填寫時間設定</button></a>
                    <button type="button" class='btn btn-success btn-flat' onclick="downloadAllFun()" >下載總表(整批下載)</button>
                </form>
                <?php if(isset($info) && !empty($info)){ ?>
                
                <button type="button" class='btn btn-success btn-flat' style="float:right;margin-left:5px" onclick="againFun(2)">設為<br>不同意再任</button>
                <br>
                <br>
                <br>
                <button type="button" class='btn btn-success btn-flat' style="float:right;width:96px" onclick="againFun(1)">設為<br> 同意再任</button>
                
                
                <table id="example2" class="table table-bordered table-hover" style="text-align:center;vertical-align:middle;font-size:20px">
                    <thead>
                      <tr>
                        <th style="text-align:center;padding-right:0px" class="no-sort">年度</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort">上下半年</th>
                        <th style="text-align:center;padding-right:0px">志工類別</th>
                        <th style="text-align:center;padding-right:0px">姓名</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort">志工自評分</th>
                        <?php if(!empty($leader)){ ?>
                            <th style="text-align:center;padding-right:0px" class="no-sort">承辦評分</th>
                        <?php } else { ?>
                            <th style="text-align:center;padding-right:0px" class="no-sort"><button type="button" class='btn btn-success btn-flat' style="width:96px;" onclick="batchConfirmFun()">批次陳核</button><br>承辦評分</th>
                        <?php } ?>
                        <th style="text-align:center;padding-right:0px" class="no-sort">組長評分</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort">填寫狀態</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort">開放性意見</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort">加權<br>總分</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort">考核<br>等第</th>
                        <th style="text-align:center;padding-right:0px" class="no-sort"><input type="checkbox" id="agree_all" class="again all">機關同意<br>再任否</th>
                       </tr> 
                    </thead>
                    <tbody>
                    <?php
                        for($i=0;$i<count($info);$i++){
                            echo '<tr>
                                  <td style="vertical-align:middle">'.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).'</td>
                                  <td style="vertical-align:middle">'.htmlspecialchars($helf_name,ENT_HTML5|ENT_QUOTES).'</td>
                                  <td style="vertical-align:middle">'.htmlspecialchars($info[$i]['category_name'],ENT_HTML5|ENT_QUOTES).'</td>
                                  <td style="vertical-align:middle">'.htmlspecialchars($info[$i]['user_name'],ENT_HTML5|ENT_QUOTES).'</td>';

                            echo '<td style="vertical-align:middle">工作表現：'.htmlspecialchars($info[$i]['top_grade'],ENT_HTML5|ENT_QUOTES).'<br>服務態度：'.htmlspecialchars($info[$i]['bottom_grade'],ENT_HTML5|ENT_QUOTES).'<br><button type="button" class="btn btn-success btn-flat" onclick="backFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this,6'.')">退回</button></td>';
                            
                            if(in_array($info[$i]['category_id'], $leader) && $info[$i]['undertaker_status']=='1'){
                                echo '<td style="vertical-align:middle">工作表現：'.htmlspecialchars($info[$i]['undertaker_top_grade'],ENT_HTML5|ENT_QUOTES).'<br>服務態度：'.htmlspecialchars($info[$i]['undertaker_bottom_grade'],ENT_HTML5|ENT_QUOTES).'<br><button type="button" class="btn btn-success btn-flat" onclick="backFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this,7'.')">退回</button></td>';
                            } else if(in_array($info[$i]['category_id'], $leader) && $info[$i]['undertaker_status']=='0'){
                                echo '<td style="vertical-align:middle">工作表現：<br>服務態度：</td>';
                            } else if(!empty($leader) && !in_array($info[$i]['category_id'], $leader)){
                                echo '<td style="vertical-align:middle">工作表現：'.htmlspecialchars($info[$i]['undertaker_top_grade'],ENT_HTML5|ENT_QUOTES).'<br>服務態度：'.htmlspecialchars($info[$i]['undertaker_bottom_grade'],ENT_HTML5|ENT_QUOTES).'</td>';
                            } else if(empty($leader) && $info[$i]['undertaker_status']=='0'){
                                $show_undertaker_top_grade = !empty($info[$i]['undertaker_top_grade'])?htmlspecialchars($info[$i]['undertaker_top_grade'],ENT_HTML5|ENT_QUOTES):htmlspecialchars($info[$i]['top_grade'],ENT_HTML5|ENT_QUOTES);
                                $show_undertaker_bottom_grade = !empty($info[$i]['undertaker_bottom_grade'])?htmlspecialchars($info[$i]['undertaker_bottom_grade'],ENT_HTML5|ENT_QUOTES):htmlspecialchars($info[$i]['bottom_grade'],ENT_HTML5|ENT_QUOTES);
                                echo '<td style="vertical-align:middle">工作表現：<input type="number" value="'.$show_undertaker_top_grade.'" style="width:70px" min="0" max="70" onblur="saveFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this.value,1,this'.')"><br>服務態度：<input type="number" id="undertaker_bottom_grade" name="undertaker_bottom_grade" value="'.$show_undertaker_bottom_grade.'" style="width:70px" min="0" max="30" onblur="saveFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this.value,2,this'.')"><br><button type="button" class="btn btn-success btn-flat" onclick="confirmFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this,5'.')">送出陳核</button></td>';
                            } else if(empty($leader) && $info[$i]['undertaker_status']=='1'){
                                echo '<td style="vertical-align:middle">工作表現：'.htmlspecialchars($info[$i]['undertaker_top_grade'],ENT_HTML5|ENT_QUOTES).'<br>服務態度：'.htmlspecialchars($info[$i]['undertaker_bottom_grade'],ENT_HTML5|ENT_QUOTES).'</td>';
                            } else {
                                echo '<td style="vertical-align:middle"></td>';
                            }

                            if(in_array($info[$i]['category_id'], $leader) && $info[$i]['undertaker_status']=='1'){
                                $show_leader_top_grade = !empty($info[$i]['leader_top_grade'])?htmlspecialchars($info[$i]['le ader_top_grade'],ENT_HTML5|ENT_QUOTES):htmlspecialchars($info[$i]['undertaker_top_grade'],ENT_HTML5|ENT_QUOTES);
                                $show_leader_bottom_grade = !empty($info[$i]['leader_bottom_grade'])?htmlspecialchars($info[$i]['leader_bottom_grade'],ENT_HTML5|ENT_QUOTES):htmlspecialchars($info[$i]['undertaker_bottom_grade'],ENT_HTML5|ENT_QUOTES);
                                echo '<td style="vertical-align:middle">工作表現：<input type="number" value="'.$show_leader_top_grade.'" style="width:70px" min="0" max="70" onblur="saveFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this.value,3,this'.')"><br>服務態度：<input type="number" id="leader_bottom_grade" name="leader_bottom_grade" value="'.$show_leader_bottom_grade.'" style="width:70px"  min="0" max="30" onblur="saveFun('.htmlspecialchars($query_year,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($query_helf,ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['category_id'],ENT_HTML5|ENT_QUOTES).','.htmlspecialchars($info[$i]['uid'],ENT_HTML5|ENT_QUOTES).',this.value,4,this'.')"></td>';
                            } else {
                                echo '<td style="vertical-align:middle">工作表現：'.htmlspecialchars($info[$i]['leader_top_grade'],ENT_HTML5|ENT_QUOTES).'<br>服務態度：'.htmlspecialchars($info[$i]['leader_bottom_grade'],ENT_HTML5|ENT_QUOTES).'</td>';
                            }
                            
                            if(intval($info[$i]['seid']) > 0){
                                echo '<td style="vertical-align:middle"><button type="button" class="btn btn-success btn-flat" onclick="downloadFun('.htmlspecialchars(intval($info[$i]['seid']),ENT_HTML5|ENT_QUOTES).')">下載</button><br><button type="button" class="btn btn-success btn-flat" onclick="downloadAllYearFun('.htmlspecialchars(intval($info[$i]['seid']),ENT_HTML5|ENT_QUOTES).')">下載全部年度</button></td>';
                            } else {
                                echo '<td style="vertical-align:middle"></td>';
                            }

                            if( isset($info[$i]['selfcomment']) && !empty($info[$i]['selfcomment'])) {
                                echo "<td>
                                <button type='button' class='btn btn-lg btn-danger' data-toggle='popover' title='開放性意見' data-content='".$info[$i]['selfcomment']."'>已填寫</button>
                                </td>";
                            } else {
                                echo "<td>未填寫</td>";
                            }

                            $self_grade = 0;
                            if(!empty($info[$i]['top_grade']) && !empty($info[$i]['bottom_grade'])){
                                $self_grade = ($info[$i]['top_grade']+$info[$i]['bottom_grade'])*0.2;
                                $self_grade = $self_grade;
                            }

                            $undertaker_grade = 0;
                            if(!empty($info[$i]['undertaker_top_grade']) && !empty($info[$i]['undertaker_bottom_grade'])){
                                $undertaker_grade = ($info[$i]['undertaker_top_grade']+$info[$i]['undertaker_bottom_grade'])*0.4;
                                $undertaker_grade = $undertaker_grade;
                            }

                            $leader_grade = 0;
                            if(!empty($info[$i]['leader_top_grade']) && !empty($info[$i]['leader_bottom_grade'])){
                                $leader_grade = ($info[$i]['leader_top_grade']+$info[$i]['leader_bottom_grade'])*0.4;
                                $leader_grade = $leader_grade;
                            }

                            $total_grade = 0;
                            if(isset($self_grade) && $self_grade > 0){
                                $total_grade += $self_grade;
                            }

                            if(isset($undertaker_grade) && $undertaker_grade > 0){
                                $total_grade += $undertaker_grade;
                            }

                            if(isset($leader_grade) && $leader_grade > 0){
                                $total_grade += $leader_grade;
                            }

                            $total_grade = round($total_grade);
                            $id_key = 'total_'.$query_year.'_'.$query_helf.'_'.$info[$i]['category_id'].'_'.$info[$i]['uid'];
                            echo '<td style="vertical-align:middle" id="'.$id_key.'">'.$total_grade.'</td>';
                            
                            if($total_grade == 0){
                                $rank = '';
                            } else if($total_grade >= 90){
                                $rank = '特優';
                            } else if($total_grade >= 80 && $total_grade < 90){
                                $rank = '優等';
                            }  else if($total_grade >= 70 && $total_grade < 80){
                                $rank = '適任';
                            }  else if($total_grade >= 60 && $total_grade < 70){
                                $rank = '待觀察';
                            } else if($total_grade < 60){
                                $rank = '不適任';
                            } 
                            
                            $rank_key = 'rank_'.$query_year.'_'.$query_helf.'_'.$info[$i]['category_id'].'_'.$info[$i]['uid'];
                            echo '<td id="'.$rank_key.'" style="vertical-align:middle">'.$rank.'</td>';

                            if(intval($info[$i]['seid']) > 0){
                                $again_key = 'again_'.$query_year.'_'.$query_helf.'_'.$info[$i]['category_id'].'_'.$info[$i]['uid'];
                                if($info[$i]['again'] == '1'){
                                    echo '<td id="'.$again_key.'" style="vertical-align:middle"><input type="checkbox" name="agree[]" value="'.htmlspecialchars(intval($info[$i]['seid']),ENT_HTML5|ENT_QUOTES).'" class="again">同意再任</td>';
                                } else if($info[$i]['again'] == '2') {
                                    echo '<td id="'.$again_key.'" style="vertical-align:middle"><input type="checkbox" name="agree[]" value="'.htmlspecialchars(intval($info[$i]['seid']),ENT_HTML5|ENT_QUOTES).'" class="again">不同意再任</td>';
                                } else {
                                    echo '<td id="'.$again_key.'" style="vertical-align:middle"><input type="checkbox" name="agree[]" value="'.htmlspecialchars(intval($info[$i]['seid']),ENT_HTML5|ENT_QUOTES).'" class="again"></td>';
                                }
                            } else {
                                echo '<td style="vertical-align:middle"></td>';
                            }
                            
                            echo  '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
                <?php if(empty($leader)){ ?>
                <center>
                    <button type="button" class='btn btn-success btn-flat' style="width:96px" onclick="batchConfirmFun()">批次陳核</button>
                </center>
                <?php } ?>
                <?php } ?>
                <form id="downloadForm" method="POST" action="<?=htmlspecialchars($download_url,ENT_HTML5|ENT_QUOTES)?>">
                    <input type="hidden" id="seid" name="seid" value="">
                </form>
                <form id="downloadAllYearForm" method="POST" action="<?=htmlspecialchars($downloadAllYear_url,ENT_HTML5|ENT_QUOTES)?>">
                    <input type="hidden" id="seidAllYear" name="seidAllYear" value="">
                </form>
                <form id="downloadAllForm" method="POST" action="<?=htmlspecialchars($downloadAll_url,ENT_HTML5|ENT_QUOTES)?>">
                    <input type="hidden" id="AllYear" name="AllYear" value="">
                    <input type="hidden" id="AllHelf" name="AllHelf" value="">
                    <input type="hidden" id="AllFirstname" name="AllFirstname" value="">
                    <input type="hidden" id="AllCategory[]" name="AllCategory[]" value="">
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    var firstname = new Awesomplete(document.getElementById('firstname'));

    $('#firstname').on('keyup', function() {
      $.ajax({
          url: 'https://elearning.taipei/eda/getVolunteerList.php?key=' + this.value,
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
        });
        if(all)
            $('.vID.all').prop('checked',true);
        else
            $('.vID.all').prop('checked',false);            
    });
    $('.vID.all').on('click',function(){
            $('.vID:not(.all)').prop('checked',$(this).prop('checked'));
    });

    $('.again:not(.all)').on('change',function(){
        var all = true;
        $('.again:not(.all)').each(function(){
            all = all && ($(this).prop('checked'));
        });
        if(all)
            $('.again.all').prop('checked',true);
        else
            $('.again.all').prop('checked',false);            
    });
    $('.again.all').on('click',function(){
            $('.again:not(.all)').prop('checked',$(this).prop('checked'));
    });

    function search(){
        $('#post_form').attr('action','<?php echo base_url('/evaluation') ?>');
        $('#post_form').submit();
    }

    function saveFun(year,helf,category,uid,grade,type,obj){
        if(category == 1 || category == 3){
            if((type == 1 || type == 3) && grade > 70){
                alert('滿分為70分，送出失敗！');

                setTimeout(function(){
                    obj.focus();
                },0);
                
                return false;
            } else if((type == 2 || type == 4) && grade > 30){
                alert('滿分為30分，送出失敗！');

                setTimeout(function(){
                    obj.focus();
                },0);

                return false;
            } else {
                var insertData = {
                    year:year,
                    helf:helf,
                    category:category,
                    uid:uid,
                    grade:grade,
                    type:type
                } ;

                $.ajax({
                    url: 'https://elearning.taipei/eda/manage/evaluation/save',
                    type: 'POST',
                    dataType: 'json',
                    data: insertData,
                })
                .done(function(msg) {
                    if(type == 1 || type == 2 || type == 3 || type == 4){
                        var id_key = 'total_'+year.toString()+'_'+helf.toString()+'_'+category.toString()+'_'+uid.toString();
                        var rank_key = 'rank_'+year.toString()+'_'+helf.toString()+'_'+category.toString()+'_'+uid.toString();
                        document.getElementById(id_key).innerText = msg['code'];
                        document.getElementById(rank_key).innerText = msg['rank'];

                        if(type == 4){
                            var again_key = 'again_'+year.toString()+'_'+helf.toString()+'_'+category.toString()+'_'+uid.toString();
                            document.getElementById(again_key).innerHTML = '<input type="checkbox" name="agree[]" value="'+msg['seid']+'" class="again">'+msg['again'];
                        }
                    }
                })
                .fail(function() {
                    
                })
                .always(function() {
                    console.log("complete");
                });
            }
        } else if(category == 2 || category == 4){
            if((type == 1 || type == 3) && grade > 50){
                alert('滿分為50分，送出失敗！');

                setTimeout(function(){
                    obj.focus();
                },0);
                
                return false;
            } else if((type == 2 || type == 4) && grade > 50){
                alert('滿分為50分，送出失敗！');

                setTimeout(function(){
                    obj.focus();
                },0);

                return false;
            } else {
                var insertData = {
                    year:year,
                    helf:helf,
                    category:category,
                    uid:uid,
                    grade:grade,
                    type:type
                } ;

                $.ajax({
                    url: 'https://elearning.taipei/eda/manage/evaluation/save',
                    type: 'POST',
                    dataType: 'json',
                    data: insertData,
                })
                .done(function(msg) {
                    if(type == 1 || type == 2 || type == 3 || type == 4){
                        var id_key = 'total_'+year.toString()+'_'+helf.toString()+'_'+category.toString()+'_'+uid.toString();
                        var rank_key = 'rank_'+year.toString()+'_'+helf.toString()+'_'+category.toString()+'_'+uid.toString();
                        document.getElementById(id_key).innerText = msg['code'];
                        document.getElementById(rank_key).innerText = msg['rank'];

                        if(type == 4){
                            var again_key = 'again_'+year.toString()+'_'+helf.toString()+'_'+category.toString()+'_'+uid.toString();
                            document.getElementById(again_key).innerHTML = '<input type="checkbox" name="agree[]" value="'+msg['seid']+'" class="again">'+msg['again'];
                        }
                    }
                })
                .fail(function() {
                    
                })
                .always(function() {
                    console.log("complete");
                });
            }
        }
    }

    function confirmFun(year,helf,category,uid,obj,type){
        if(confirm('是否確認送出陳核')){
            var insertData = {
                year:year,
                helf:helf,
                category:category,
                uid:uid,
                grade:1,
                type:type
            } ;

            $.ajax({
                url: 'https://elearning.taipei/eda/manage/evaluation/save',
                type: 'POST',
                dataType: 'json',
                data: insertData,
            })
            .done(function(msg) {
                obj.disabled='disabled';
                location.reload()
            })
            .fail(function() {
                
            })
            .always(function() {
                console.log("complete");
            });
        }
    }

    function backFun(year,helf,category,uid,obj,type){
        if(confirm('是否確認退回')){
            var insertData = {
                year:year,
                helf:helf,
                category:category,
                uid:uid,
                grade:1,
                type:type
            } ;

            $.ajax({
                url: 'https://elearning.taipei/eda/manage/evaluation/save',
                type: 'POST',
                dataType: 'json',
                data: insertData,
            })
            .done(function(msg) {
                obj.disabled='disabled';
                location.reload()
            })
            .fail(function() {
                
            })
            .always(function() {
                console.log("complete");
            });
        }
    }

    function downloadFun(id){
        document.getElementById('seid').value = id;
        var obj = document.getElementById('downloadForm');
        obj.submit();
    }

    function downloadAllYearFun(id){
        document.getElementById('seidAllYear').value = id;
        var obj = document.getElementById('downloadAllYearForm');
        obj.submit();
    }

    function downloadAllFun(){
        document.getElementById('AllYear').value = document.getElementById('year').value;
        document.getElementById('AllHelf').value = document.getElementById('helf').value;
        document.getElementById('AllFirstname').value = document.getElementById('firstname').value;

        // var xxxVal = new Array();
        // $('input[name="category[]"]:checkbox:checked').each(function(i) {
        //     // document.getElementById('AllCategory['+i+']').value = this.value;
        //     console.log(this.value);
        // });

        var obj = document.getElementById('downloadAllForm');
        obj.submit();
    }

    function againFun(id){
        var list = new Array();
        $('input[name="agree[]"]:checkbox:checked').each(function(i) {
            list[i] = this.value;
        });

        if(list.length === 0){
            alert('須至少勾選一個');
            return false;
        } else {
            $.ajax({
                url: 'https://elearning.taipei/eda/manage/evaluation/againSave',
                type: 'POST',
                dataType: 'json',
                data: {list: list, id: id},
            })
            .done(function(msg) {
                if (msg.code=='100') {
                    alert('設定完成！');
                    location.reload();
                }
            })
            .fail(function() {
                
            })
            .always(function() {
                console.log("complete");
            });
        }
    }

    function batchConfirmFun(){
        if(confirm('是否確認批次送出陳核')){
            document.getElementById('batch').value = 'batch';
            var obj = document.getElementById('post_form');
            obj.submit();
        }

        return false;
    }
   
    $(':input[type="number"]').keydown(function (e) {

        if (e.keyCode == 13 /*Enter*/) {

            // focus next input elements
            $(':input[type="number"]:visible:enabled:eq(' + ($(':input[type="number"]:visible:enabled').index(this) + 1) + ')').focus();
            e.preventDefault();
        }

    });
    
</script>