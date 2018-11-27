<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use App\Models\MyCash;

class CheckCash
{
    protected $id;
    protected $if_check;

    public function __construct($id)
    {
        $this->id = $id;
        $mycash = MyCash::find($this->id);
        $this->if_check = $mycash->if_check;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.check-user').on('click', function () {
    $.ajax({
        url: 'mycashes/check',
        type: 'POST',
        data: JSON.stringify({
          'id':$(this).data('id'),
          'if_check':$(this).data('check'),
          _token: LA.token,
        }),
        contentType: 'application/json',
        success: function (data) {
            swal({
                title: data.msg,
                type: 'success'
            }).then(function() {
                // 用户点击 swal 上的按钮时刷新页面
                location.reload();
            });
        }
      });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        if($this->if_check==0){
            return "<a style='margin-right: 10px;' class='btn btn-xs btn-success check-user' data-id='{$this->id}' data-check='1'>通过</a><a style='margin-right: 10px;' class='btn btn-xs btn-danger check-user' data-id='{$this->id}' data-check='2'>拒绝</a>";
        }else{
            return "<a></a>";
        }

    }

    public function __toString()
    {
        return $this->render();
    }
}