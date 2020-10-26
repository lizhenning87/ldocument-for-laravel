<?php


namespace Zning\Apidocument\commands;


use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Closure;
use Illuminate\Support\Str;
use Zning\Apidocument\ControllerDocumentParser;

class ApiDocument extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zning:api-document {--d|debug : Debug输出}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Api文档生产';

    const CACHE_KEY_API = 'cache-api-document';

    protected $cache;
    protected $router;
    protected $fun = ['index'=>'列表 :value (详细)','store'=>'新建 :value','show'=>'单条查看 :value','update'=>'修改 :value','destroy'=>'删除 :value','list'=>'列表 :value (简短)'];

    /**
     * ApiDocument constructor.
     * @param $router
     */
    public function __construct(Router $router, CacheManager $cache)
    {
        parent::__construct();
        $this->router = $router;
        $this->cache = $cache;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->info('开始处理API');

        $this->cache->forget(self::CACHE_KEY_API);

        $routes = $this->getRoutes();
        $data = $this->getApis($routes);

        // 变更结构
        $group = [];
        foreach ($data as $key => $value)
        {
            $apiId = random_int(100000000,999999999);
            $value['api_id'] = $apiId;

            $groupName = $value['group'];
            $flag = false;
            foreach ($group as &$v)
            {
                if ($v['group_name'] == $groupName)
                {
                    $v['api'][] = $value;
                    $flag = true;
                }
            }

            if (!$flag)
            {
                $groupId = random_int(100000000,999999999);

                $group[] = [
                    'group_id' => $groupId,
                    'group_name' => $groupName,
                    'api' => [
                        $value
                    ],
                ];
            }

        }

        $this->cache->forever(self::CACHE_KEY_API, [
            'date' => Carbon::now()->toDateTimeString(),
            'document' => $group,
        ]);

        if ($this->option('debug'))
        {
            $this->info(json_encode($data));
        }

        $this->info('结束处理API');

    }

    protected function getApis($routes) {

        return collect($routes)->filter(function ($route) {

            return Str::startsWith($route['uri'], config('doc.only'));

        })->map(function ($route) {

            $explode = explode('@', $route['action']);

            if(array_key_exists(1, $explode))
            {
                $reflection = (new \ReflectionClass($explode[0]));
                $classDoc = $this->getDocComment($reflection);

                try {

                    $api = $reflection->getMethod($explode[1]);

                }catch (\Exception $exception) {
                    return [];
                }

                $apiDoc = $this->getDocComment($api);

                return [
                    'name'=>$this->getApiName(@$classDoc['group'],@$apiDoc['description'],$explode[1]),
                    'group'=>@$classDoc['group']?:'未分配',
                    'path'=>$route['uri'],
                    'method'=>$route['method'],
                    'return'=>$route['uri'],
                    'date' => array_key_exists('date', $apiDoc) ? $apiDoc['date'] : '',
                    'introduction' => array_key_exists('introduction', $apiDoc) ? $apiDoc['introduction'] : '',
                    'q'=>$this->getParam(array_key_exists('q',$apiDoc)?$apiDoc['q']:[]),
                    'u'=>$this->getParam(array_key_exists('u',$apiDoc)?$apiDoc['u']:[]),
                    'b'=>array_merge($this->getParam(array_key_exists('b',$apiDoc)?$apiDoc['b']:[]),$this->getRequest($api)),
                    'r'=>$this->getParam(array_key_exists('r',$apiDoc)?$apiDoc['r']:[]),
                ];

            }

            return [];

        })->filter()->toArray();

    }

    protected function getRequest($api) {

        foreach ($api->getParameters() as $value)
        {
            if ($value->getClass())
            {
                $class = $value->getClass()->getName();

                $request = new $class();

                if ($request instanceof FormRequest)
                {

                    $rules = $request->rules();
                    if (method_exists($request,'attributes')){
                        $this->columns = array_merge($this->columns,$request->attributes());
                    }
                    $data = [];
                    foreach ($rules as $key=>$vv){
                        $explode = explode('.',$key);
                        $explodeEnd = $explode[count($explode)-1];
                        $name = $key;

                        $is_must = array_search('required',$vv) === false?'N':'Y';
                        $desc = @$this->columns[$explodeEnd];
                        $data[] = compact('name','is_must','desc');

                    }
                    return $data;

                }

            }

        }

        return [];
    }


    protected function getParam($query) {

        $query = (array)$query;

        $data = [];

        foreach ($query as $value)
        {
            $value = array_values(array_filter(explode(' ', $value)));
            $data[] = [
                'name' => @$value[0],
                'type' => @$value[1],
                'is_must' => @$value[2]?:'N',
                'desc' => @$value[3],
                'example' => @$value[4],
            ];
        }

        return $data;
    }

    protected function getApiName($classDescription, $apiDescription, $funcName) {

        if ($apiDescription)
        {
            return $apiDescription;
        }

        $this->fun = array_merge($this->fun, []);

        if (array_key_exists($funcName, $this->fun))
        {
            return str_replace(':value', $classDescription, $this->fun[$funcName]);
        }

        return null;
    }

    protected function getDocComment($reflection) {
        return (new ControllerDocumentParser())->parse($reflection->getDocComment());
    }

    protected function getRoutes() {

        return collect($this->router->getRoutes())->map(function ($route) {
            return $this->getRouteInformation($route);
        })->filter()->all();
    }

    protected function getRouteInformation(Route $route) {

        return [
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => ltrim($route->getActionName(), '\\'),
            'middleware' => $this->getMiddleware($route),
        ];
    }

    protected function getMiddleware($route) {

        return collect($this->router->gatherRouteMiddleware($route))->map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        })->implode("\n");
    }

}
