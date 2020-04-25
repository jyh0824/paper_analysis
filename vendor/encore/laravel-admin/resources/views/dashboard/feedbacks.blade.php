<style>
    .ext-icon {
        color: rgba(0,0,0,0.5);
        margin-left: 10px;
    }
    .installed {
        color: #00a65a;
        margin-right: 10px;
    }
</style>
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">用户反馈列表</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <ul class="products-list product-list-in-box">

            @foreach($feedbacks as $feedback)
            <li class="item">
                <div class="product-img">
                    <i class="fa fa-angle-double-right fa-2x ext-icon"></i>
                </div>
                <div class="product-info">
                    <a href="/admin/auth/feedback" target="_self" class="product-title">
                        {{ $feedback->name }}
                    </a>
                    <label style="float: right; font-size: 17px;">
                    @if($feedback->status == 0)
                        <i class="fa fa-times" style='color: darkred;'></i>未处理
                    @else
                        <i class="fa fa-check" style='color: darkgreen;'></i>已处理
                    @endif
                    </label>
                </div>
            </li>
            @endforeach

            <!-- /.item -->
        </ul>
    </div>
    <!-- /.box-body -->
    <div class="box-footer text-center">
        <a href="/admin/auth/feedback" target="_self" class="uppercase">查看所有反馈</a>
    </div>
    <!-- /.box-footer -->
</div>