<?php


namespace Zning\Apidocument\controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Zning\Apidocument\commands\ApiDocument;
use Illuminate\Routing\Controller as BaseController;

class ApiDocumentController extends BaseController
{

    public function index() {

        $data = Cache::get(ApiDocument::CACHE_KEY_API, null);

        if (!$data)
        {
            return '接口文档暂未初始化';
        }

        $group = $data['document'];
        $date = $data['date'];

        return view('doc::index', [
            'data' => $group,
            'date' => $date,
            'ver' => config('doc.v'),
        ]);

    }


    public function show(Request $request) {

        $apiId =  $request->input('api');

        $data = Cache::get(ApiDocument::CACHE_KEY_API, null);

        if (!$data)
        {
            return '接口文档暂未初始化';
        }

        $api = null;
        foreach ($data['document'] as $item)
        {
            if (is_array($item) && key_exists('api', $item))
            {
                foreach ($item['api'] as $vv)
                {
                    if ($vv['api_id'] == $apiId)
                    {
                        $api = $vv;
                        break;
                    }
                }
            }

        }

        if (!$api)
        {
            return '没有找到接口信息';
        }

        $url = config('doc.path').'/'.$api['path'];
        $isPost = $api['method'] == 'post';

        return view('doc::show', [
            'data' => $api,
            'url' => $url,
            'post' => $isPost,
        ]);

    }


    public function api(Request $request) {


        return new Response(['message' => '测试', 'error' => 0, 'data'=>[]], 400);

    }

}
