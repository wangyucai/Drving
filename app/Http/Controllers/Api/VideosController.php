<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Transformers\VideoTransformer;

class VideosController extends Controller
{
    // 获取科目一安全学习视频列表
    public function index(Request  $request,Video $video)
    {
        $query = $video->query();
        $videos = $query->get();
        return $this->response->collection($videos, new VideoTransformer());
    }
}
