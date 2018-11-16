<?php

namespace App\Admin\Controllers;

use App\Models\Schedule;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SchedulesController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('时刻表列表')
            ->body($this->grid());
    }


    public function show($id, Content $content)
    {
        return $content
            ->header('查看时刻表')
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑时刻表')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建时刻表')
            ->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new Schedule);

        $grid->id('Id');
        $grid->time('时间段');
        $grid->disableExport();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Schedule::findOrFail($id));

        $show->id('Id');
        $show->time('时间段');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Schedule);

        $form->text('time', '时间段')->rules('required');

        return $form;
    }
}
