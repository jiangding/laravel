<link rel="stylesheet" href="{{ URL::asset('/') }}src/dist/css/bootstrapValidator.min.css"/>
<script type="text/javascript" src="{{ URL::asset('/') }}src/dist/js/bootstrapValidator.min.js"></script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">修改邮费</h4>
</div>
<div class="modal-body">
    <form id="userForm" class="form-horizontal" role="form">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{ $id }}">
        <div class="form-group">
            <label for="email" class="col-md-2 control-label">京东</label>
            <div class="col-md-5">
                <input class="form-control" name="jd" value="{{ $postage['JD'] }}">
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-md-2 control-label">天猫</label>
            <div class="col-md-5">
                <input  class="form-control" name="tmall" value="{{ $postage['TMALL'] }}">
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-md-2 control-label">一号店</label>
            <div class="col-md-5">
                <input  class="form-control" name="yhd" value="{{ $postage['YHD'] }}">
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
    <button type="button" class="btn btn-primary" id="submit">修改</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#submit').click(function() {
            //开启验证
            $('#userForm').data('bootstrapValidator').validate();
            if(!$('#userForm').data('bootstrapValidator').isValid()){
                return ;
            }else{
                var $form = $('#userForm');
                console.log($form.serialize());
                // ajax 提交表单
                $.ajax({
                    type: "POST",
                    url: '{{ url('/admin/detail/postage_update') }}',
                    dataType: 'json',
                    cache: false,
                    data:  $form.serialize(),
                    success: function(result) {
                        console.log(result);
                        if (result.status == 0) {
                            window.location.reload();
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                    }
                });

            }
        });
        $('#userForm').bootstrapValidator({
            fields: {
                jd: {
                    validators: {
                        numeric: {
                            message: '请输入数字~',
                        }
                    }
                },
                tmall: {
                    validators: {
                        numeric: {
                            message: '请输入数字~',
                        }
                    }
                },
                yhd: {
                    validators: {
                        numeric: {
                            message: '请输入数字~',
                        }
                    }
                },

            }
        });
    });
</script>


