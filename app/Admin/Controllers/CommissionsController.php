<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CommissionsController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('佣金设置列表')
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('查看佣金设置')
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑佣金设置')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建佣金设置')
            ->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new Commission);

        $grid->id('Id');
        $grid->type('佣金类型')->display(function ($value) {
            return $value =Commission::$typeMap[$value];
        });
        $grid->one_level('一级佣金');
        $grid->two_level('二级佣金');
        $grid->three_level('三级佣金');
        $grid->disableExport();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Commission::findOrFail($id));

        $show->id('Id');
        $show->type('佣金类型');
        $show->one_level('一级佣金');
        $show->two_level('二级佣金');
        $show->three_level('三级佣金');
        return $show;
    }

    protected function form()
    {
        $form = new Form(new Commission);
        $form->radio('type', '类型')->options(Commission::$typeMap)->rules('required');
        $form->decimal('one_level', '一级佣金')->default(0.00);
        $form->decimal('two_level', '二级佣金')->default(0.00);
        $form->decimal('three_level', '三级佣金')->default(0.00);
        return $form;
    }
}
