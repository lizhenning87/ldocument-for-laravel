<?php


namespace Zning\Apidocument\controller;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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


    public function api(Request $request)
    {

        $url = $request->input('zning-url', null);
        $method = $request->input('zning-method', null);
        $token = $request->input('zning-token', null);

        if (!$url || !$method)
        {
            return new Response(['message' => '缺少请求信息', 'error' => 1], 400);
        }

        $credentials = $request->except(['zning-url', 'zning-method']);

        //Log::debug($credentials);

        $query = [];
        $formParams = [];
        $files = [];

        foreach ($credentials as $key => $value)
        {
            if (str_starts_with($key, 'zning-u-'))
            {
                $k = str_replace('zning-u-', '', $key);
                $url = str_replace($k, $value, $url);
            }

            if (str_starts_with($key, 'zning-q-'))
            {
                $k = str_replace('zning-q-', '', $key);
                $query[$k] = $value;
            }

            if (str_starts_with($key, 'zning-b-s-'))
            {
                $k = str_replace('zning-b-s-', '', $key);
                $formParams[$k] = $value;
            }

            if (str_starts_with($key, 'zning-b-f-'))
            {
                $k = str_replace('zning-b-f-', '', $key);
                $f = $request->file($key);
                $files[] = [
                    'name'     => $k,
                    'contents' => file_get_contents($f->getRealPath()),
                    'filename' => $f->getFilename().'.'.$f->getClientOriginalExtension(),
                ];
            }

        }

        $client = new Client();
        $response = null;

        $header['Accept'] = 'application/json';

        if ($token)
        {
            $header['Authorization'] = 'Bearer '.$token;
        }

        try {

            $_url = url($url);

            if (sizeof($query) != 0)
            {
                $query = http_build_query($query);
                $_url = $_url.'?'.$query;
            }

            if (strtoupper($method) == 'POST')
            {

                $post = [];

                if (sizeof($files) != 0)
                {
                    //需要合并
                    if (sizeof($formParams) != 0)
                    {
                        foreach ($formParams as $key => $value)
                        {
                            $files[] = [
                                'name'     => $key,
                                'contents' => $value,
                            ];
                        }
                    }

                    $post['multipart'] = $files;

                }else
                {
                    if (sizeof($formParams) != 0)
                    {
                        $post['form_params'] = $formParams;
                    }
                }

                $post['headers'] = $header;

                //Log::debug($post);

                $response = $client->request('post', $_url, $post);

            }else
            {
                $response = $client->request('get', $_url, [
                    'headers' => $header,
                ]);
            }

            return new Response(json_decode($response->getBody()->getContents(), true), $response->getStatusCode());

        }catch (ClientException $exception) {

            $responseBody = $exception->getResponse()->getBody()->getContents();
            return new Response(json_decode($responseBody, true), $exception->getResponse()->getStatusCode());
        }



    }

}
