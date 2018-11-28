<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">我的详细信息</h3>
    <div class="box-tools">
      <div class="btn-group pull-right" style="margin-right: 10px">
        <a href="{{ route('admin.trainer.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <th>用户名：</th>
        <td>{{ $user->username }}</td>
        <th>我的积分：</th>
        <td>{{ $user->my_points }}</td>
      </tr>
      <tr>
        <th>姓名：</th>
        <td>{{ $user->name }}</td>
        <th>手机号：</th>
        <td>{{ $user->phone }}</td>
      </tr>
      <tr>
        <th>身份证号：</th>
        <td>{{ $user->carno }}</td>
        <th>认证状态：</th>
        <td>{{ \App\Models\User::$trainerStatusMap[$user->if_check] }}</td>
      </tr>
      <tr>
        <th>车牌号：</th>
        <td>{{ $user->car_number }}</td>
        <th>学员报名地点：</th>
        <td>{{ $user->registration_site }}</td>
      </tr>
      <tr>
        <th>学员练车地点：</th>
        <td>{{ $user->trainingground_site }}</td>
        <th>全部约车时间段：</th>
        <td>{{ $user->all_time }}</td>
      </tr>
      <tr>
        <th>单次约车时间段：</th>
        <td>{{ $user->single_time }}</td>
        <th>练车次数：</th>
        <td>{{ $user->day_times }}</td>
      </tr>
      </tbody>
    </table>
  </div>
  <div class="box-header with-border">
    <h3 class="box-title">我的提现账号</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
       @foreach($cashes as $item)
        @if($item->type==1)
        <tr>
            <th>类型</th>
            <th>姓名</th>
            <th>账号</th>
        </tr>
        <tr>
            <td>支付宝</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->identity }}</td>
        </tr>
        @elseif($item->type==2)
        <tr>
            <th>类型</th>
            <th colspan="2">微信二维码</th>
        </tr>
        <tr>
            <td>微信</td>
            <td colspan="2"><img src="{{$item->wechat_code}}"" /></td>
        </tr>
        @endif
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="box-header with-border">
    <h3 class="box-title">我的积分流水</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      @foreach($mycashes as $item)
      <tr>
        <th>于 {{ $item->created_at->format('Y-m-d H:i:s') }} 申请的提现：{{ $item->points }}积分     @if($item->if_check==0)
                  正在审核中
                @elseif($item->if_check==1)
                  提现成功
                @elseif($item->if_check==2)
                  提现失败
                @elseif($item->if_check==3)
                  已退还
                @endif
        </th>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
