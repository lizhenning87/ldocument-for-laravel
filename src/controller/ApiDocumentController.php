<?php


namespace Zning\Apidocument\controller;

use Illuminate\Support\Facades\Cache;
use Zning\Apidocument\commands\ApiDocument;
use Illuminate\Routing\Controller as BaseController;

class ApiDocumentController extends BaseController
{

    public function index() {

        $data = Cache::get(ApiDocument::CACHE_KEY_API, null);

        if (!$data)
        {
            return '文章暂未初始化';
        }

        $group = $data['document'];
        $date = $data['date'];

        return view('doc::index', [
            'data' => $group,
            'date' => $date,
            'ver' => config('doc.v'),
        ]);

    }


}
