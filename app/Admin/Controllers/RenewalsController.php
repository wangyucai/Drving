<?php

namespace App\Admin\Controllers;

use App\Models\Renewal;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RenewalsController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('续费设置列表')
            ->body($this->grid());
    }


    public function show($id, Content $content)
    {
        return $content
            ->header('查看续费设置')
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑续费设置')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建续费设置')
            ->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new Renewal);

        $grid->id('Id');
        $grid->days('续费月数');
        $grid->money('续费金额');
        $grid->disableExport();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Renewal::findOrFail($id));

        $show->id('Id');
        $show->days('续费月数');
        $show->money('续费金额');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Renewal);
        $form->text('days', '续费月数')->rules('required');
        $form->text('money', '续费金额')->rules('required');

        return $form;
    }
}