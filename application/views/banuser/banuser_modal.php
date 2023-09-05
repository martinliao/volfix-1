<div id="ban_edit" class="modal fade" role="dialog" 
    aria-labelledby="availableRoomModalLabel" aria-hidden="true">
    <!-- modal-lg modal-dialog-centered -->
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="availableRoomModalLabel" class="modal-title">停權設定</h4>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="-1">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <form id="banuser" class="form-inline">
                <div class="modal-body">
                    <!-- <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" /> -->
                    <input type="hidden" id="category_name" name="category_name" value="" />
                    <input type="hidden" id="idNo" name="idNo" value="" />
                    <div class='form-group'>
                        <label>停權類別</label>
                        <div class="form-check form-check-inline">
                            <label><input type="checkbox" name="category[]" value="all" class="all vID" >全部</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="1" class="vID" data-name=班務>班務</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="2" class="vID" data-name=警衛>警衛</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="3" class="vID" data-name=圖書>圖書</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="4" class="vID" data-name=客服>客服</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="7" class="vID" data-name=行政>行政</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="8" class="vID" data-name=會計>會計</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="9" class="vID" data-name=人事>人事</label>&emsp;
                            <label><input type="checkbox" name="category[]" value="10" class="vID" data-name=VR>VR</label>&emsp;
                        </div>
                    </div>
                    <div class='form-group'>
                        <label>停權時間</label>
                        <table id="main_table" class="table table-bordered table-hover" width="100%">
                            <tr>
                                <td>
                                    起
                                    <?php
                                        $input = new input_builder('date', 'ban_start', $ban_start);
                                        $input->set_style('display:inline-block;width:180px')
                                            ->print_html();
                                    ?>
                                    迄
                                    <?php
                                        $input = new input_builder('date', 'ban_end', $ban_end);
                                        $input->set_style('display:inline-block;width:180px')
                                            ->print_html();
                                    ?><br>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <input type="submit" class="btn btn-primary" id="btn">
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.vID.all').on('click',function(){
        $('.vID:not(.all)').prop('checked',$(this).prop('checked'));
    });
    $(document).ready(function(){
        $("#banuser").submit(function(event){
            var postData = $('#banuser').serialize();
            $.post('<?php echo base_url('volunteer_manage/ban_edit') ?>', postData).done(function(response){
                var json = $.parseJSON(response);
                alert(json.msg);
                if(json.success) {
                    location.reload();
                }
            });
            return false;
        });
    });
    // function apply(calendarID){
    //     var post_data = {'calendarID':calendarID};
    //     $.post('<?php echo base_url('volunteer_apply/apply') ?>', post_data).done(function(response){
    //         var json = $.parseJSON(response);

    //         alert(json.msg);

    //         if(json.success)
    //         {
    //             location.reload();
    //         }
    //     });
    // }
</script>