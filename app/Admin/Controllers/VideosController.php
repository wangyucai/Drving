<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VideosController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('科目一安全学习列表')
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('查看科目一安全学习视频')
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑科目一安全学习视频')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建科目一安全学习视频')
            ->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new Video);

        $grid->id('Id');
        $grid->title('视频标题');
        $grid->path('视频地址');
        $grid->disableExport();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Video::findOrFail($id));

        $show->id('Id');
        $show->title('视频标题');
        $show->path('视频地址');
        return $show;
    }

    protected function form()
    {
        $form = new Form(new Video);
        $form->text('title', '视频标题')->rules('required');
        $form->file('path', '科目一安全学习')->rules('required');
        return $form;
    }
}
