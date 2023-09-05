<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">N.類別承辦人</h3>
            </div><!-- /.box-header -->
            <form method="POST" action="<?php echo base_url('volunteer_manage/manage_admin_add') ?>">
                <div class="box-body">
                    <div>
                        <label><input type="checkbox" name="category[]" value="all" class="all vID" <?= in_array('all', $query_category) ? 'checked' : '' ?>>全部</label>&emsp;
                        <label><input type="checkbox" name="category[]" value="1" class="vID" <?= in_array('1', $query_category) ? 'checked' : '' ?>>班務</label>&emsp;
                        <?php
                        $categoryList = array();
                        $categoryList[1]['name'] = '班務';
                        $categoryList[1]['total_hours'] = 0;
                        for ($i = 0; $i < count($category); $i++) {
                            $categoryList[$category[$i]->id]['name'] = $category[$i]->name;
                            $categoryList[$category[$i]->id]['total_hours'] = 0;
                            $tmp_checked = in_array($category[$i]->id, $query_category) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="category[]" value="' . $category[$i]->id . '" class="vID"' . $tmp_checked . '>' . $category[$i]->name . '</label>&emsp;';
                        }
                        ?>
                    </div>
                    <table id="example2" class="table table-bordered table-hover" style="text-align:center;vertical-align:middle">
                        <thead>
                            <tr>
                                <td colspan="3" style="text-align:right;">
                                    <div>
                                        姓名：
                                        <?php
                                        $input = new input_builder('select2', 'userID');
                                        $input->set_option(array('_disabled' => '請選擇') + $user_list)
                                            ->set_style('display:inline-block;width:200px')
                                            ->print_html();
                                        ?>
                                        <button type="submit" class="btn">加入</button>
            </form>
        </div>
        </td>
        </tr>
        <tr>
            <th style="text-align:center;">序號</th>
            <th style="text-align:center;">姓名</th>
            <th style="text-align:center;">類別</th>
            <th style="text-align:center;">設定</th>
        </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($users as $each) {
                echo '
                <tr>
                  <td style="text-align:center;">' . ++$i . '</td>
                  <td style="text-align:center;">' . $each->name . '</td>
                  <td style="text-align:left;">' . $each->category_list . '</td>
                  <td>
                    <div class="btn-dataset">
                      <a href="#" onclick="remove_data(\'' . base_url() . 'volunteer_manage/manage_admin_remove/\',{\'userID\':\'' . $each->id . '\'})" title="edit" class="btn btn-default btn-xs"><i class="fa fa-trash fa-trash"></i>&nbsp;&nbsp;刪除</a>
                    </div>
                  </td>
                 </tr> 
              ';
            }
            ?>
        </tbody>
        </table>
    </div>
</div>
</div>
</div>



<script type="text/javascript">
    $(function() {
        $('#user_list').DataTable({
            info: false,
            paging: false,
            searching: true,
            oLanguage: {
                "sProcessing": "處理中...",
                "sInfoEmpty": "無任何資料",
                "sSearch": "搜尋",
            },
        });
        $('.dataTables_filter').addClass('pull-left');
    });
    function remove_data(url,data){
        $.post(url,data,function(response){
        json = $.parseJSON(response);
        alert(json.msg);
        location.reload();
        });
    }
</script>