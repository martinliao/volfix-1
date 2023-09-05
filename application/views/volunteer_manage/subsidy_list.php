<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">J.志工補助清冊</h3>
            </div>
            <div class="box-body">  

            <form action="<?php echo base_url();?>download/subsidy_detail" method="post" id="sentq" target="_blank">
                <input type="hidden" id="downloadtype" name="downloadtype" value="pdf">
                <select class='form-control input-sm' style='width: auto;float: left;margin-top: 2px;margin-right: 10px;' name='year'>
                    <?php
                        $y = date('Y')-1911;
                        for($i=$y;$i>=108;$i--){
                            echo '<option values="'.$i.'" '.($year==$i?'selected="selected"':null).'>'.$i.'</option>';
                        }
                    ?>
                </select>
                <p style='float: left;margin-top: 5px;margin-right: 10px;'>年</p>
                <select class='form-control input-sm' name='season' style='width: auto;float: left;margin-top: 2px;margin-right: 10px;'>
                    <option value="">請選擇季</option>
                    <option value="1">第 1 季</option>
                    <option value="2">第 2 季</option>
                    <option value="3">第 3 季</option>
                    <option value="4">第 4 季</option>
                </select>
                <p style='float: left;margin-top: 5px;margin-right: 10px;'>月份：</p>
                <select class='form-control input-sm' name='start_month' style='width: auto;float: left;margin-top: 2px;margin-right: 10px;'>
                    <option value="">請選擇</option>
                    <option value="1">1月</option>
                    <option value="2">2月</option>
                    <option value="3">3月</option>
                    <option value="4">4月</option>
                    <option value="5">5月</option>
                    <option value="6">6月</option>
                    <option value="7">7月</option>
                    <option value="8">8月</option>
                    <option value="9">9月</option>
                    <option value="10">10月</option>
                    <option value="11">11月</option>
                    <option value="12">12月</option>
                </select>
                <p style='float: left;margin-top: 5px;margin-right: 10px;'>到</p>
                <select class='form-control input-sm' name='end_month' style='width: auto;float: left;margin-top: 2px;margin-right: 10px;'>
                    <option value="">請選擇</option>
                    <option value="1">1月</option>
                    <option value="2">2月</option>
                    <option value="3">3月</option>
                    <option value="4">4月</option>
                    <option value="5">5月</option>
                    <option value="6">6月</option>
                    <option value="7">7月</option>
                    <option value="8">8月</option>
                    <option value="9">9月</option>
                    <option value="10">10月</option>
                    <option value="11">11月</option>
                    <option value="12">12月</option>
                </select>
                
                <button type="submit" class="btn btn-primary" value="pdf">
                    下載PDF檔
                </button>

                <button type="submit" class="btn btn-primary" value="excel">
                    下載Excel檔
                </button>
                
                <br>
                <br>
                <div>
                <label><input type="checkbox" name="category[]" value="all" class="all vID">全部</label>&emsp;
                <label><input type="checkbox" name="category[]" value="1" class="vID">班務</label>&emsp;
                <?php
                    $categoryList = array();
                    $categoryList[1]['name'] = '班務';
                    $categoryList[1]['total_hours'] = 0;
                    for($i=0;$i<count($category);$i++) { 
                        $categoryList[$category[$i]->id]['name'] = $category[$i]->name;
                        $categoryList[$category[$i]->id]['total_hours'] = 0;
                    //   $tmp_checked = in_array($category[$i]->id,$query_category)?'checked':'';
                        echo '<label><input type="checkbox" name="category[]" value="'.$category[$i]->id.'" class="vID">'.$category[$i]->name.'</label>&emsp;';
                    }
                ?>
                <br>
                <br>
                姓名：<input type="text" class="awesomplete" data-minchars="1" id="firstname" name="firstname" value=""></input>
                &emsp;清冊顯示:&emsp;<label><input type="checkbox" name="show_type[]" value='all' class="vID">全部顯示</label>&emsp;
                <label><input type="checkbox" name="show_type[]" value=2 class="vID">僅顯示有刷完二卡</label>&emsp;
                </div>
                
            </form>

        </div>
    </div>
</div>

<script>
    $('.vID.all').on('click',function(){
            $('.vID:not(.all)').prop('checked',$(this).prop('checked'));
    });
    $("#sentq button").click(function(ev){
        ev.preventDefault();
        $('input[name=downloadtype]').val($(this).attr("value"));
        $("#sentq").submit();
    });
</script>