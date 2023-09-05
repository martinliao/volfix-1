<?php
$id = isset($course->id)?$course->id:null;
$name = isset($course->name)?$course->name:null;
$apply_start = isset($course->apply_start)?$course->apply_start:null;
$apply_end = isset($course->apply_end)?$course->apply_end:null;
$note = isset($note)?$note:null;
?>

<div class="row">
<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <h1 class="box-title">
                停權設定
            </h1>
        </div>
        <div class="box-body">
            <div>
                <div id="main_div" class="special_scrollbar" style="overflow-x: auto;">
                <form action="<?php echo base_url('/volunteer_manage/apply_ban_save') ?>" method="POST">
                    
                    <table id="main_table" class="table table-bordered table-hover" width="100%">
                        <tr>
                            <td>
                                停權時間：起
                                <?php
                                    $input = new input_builder('date','apply_start',$apply_start);
                                    $input->set_style('display:inline-block;width:180px')
                                          ->print_html();
                                ?>
                                迄
                                <?php
                                    $input = new input_builder('date','apply_end',$apply_end);
                                    $input->set_style('display:inline-block;width:180px')
                                          ->print_html();
                                ?><br>
                            </td>
                        </tr>
                    </table>
                    <div style="width: 100%;text-align: right;padding-right: 20px">
                        <button>儲存</button>                        
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
