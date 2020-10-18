<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>接口文档</title>
    <link rel="stylesheet" href="{{ asset('static/source/styles.css') }}">

    <script src="{{ asset('static/source/jquery.min.js') }}"></script>
    <script src="{{ asset('static/source/layer/layer.js') }}"></script>

    <style>
        nav a {
            display: flex;
            height: 100%;
            align-items: center;
            padding: 0 15px;
        }

        nav a:hover {
            color: #fff;
        }

        nav li>ul {
            background: #2d3748;
            position: absolute;
            overflow: hidden;
            min-width: 160px;
            opacity: 0;
            visibility: hidden;
            transition: ease all 0.3s;
        }

        nav li>ul a {
            line-height: 40px;
        }

        nav li:hover>ul {
            visibility: visible;
            opacity: 1;
        }

        .target-fix {
            position: relative;
            top: -60px; // 偏移为nav被fixed元素的高度
        display: block;
            height: 0; //高度为0
        overflow: hidden;
        }
    </style>

</head>

<body class="bg-gray-200 font-mono">

<div>
    <nav class="fixed w-screen flex items-center justify-between bg-gray-800 text-gray-500 px-4 h-12">

        <div class="flex items-center h-full">
            <div class="text-sm">接口文档</div>
            <ul class="ml-4 flex items-stretch h-full">

                @foreach($data as $item)

                    <li>
                        <a href="">{{ $item['group_name'] }}</a>
                        <ul>
                            @foreach($item['api'] as $vv)
                                <li><a href="#{{ $vv['api_id'] }}">{{ $vv['name'] }}</a></li>
                            @endforeach

                        </ul>
                    </li>

                @endforeach

            </ul>
        </div>

        <div class="flex items-center">
            <div class="text-xs"> {{ $date }} Ver:{{ $ver }}</div>
        </div>

    </nav>
</div>

<main class="pt-12 px-10">

    @foreach($data as $item)

        @foreach($item['api'] as $vv)

            <a id="{{ $vv['api_id'] }}" class="target-fix"></a>
            <section class="leading-loose bg-white py-3 px-5 mt-2" >

                <div class="flex justify-between h-12 items-center">
                    <div class="flex items-center">
                        <div class="text-2xl">{{ $vv['name'] }}</div>
                        <div class=""><span class="text-xs bg-red-500 text-white rounded-md py-1 px-2 ml-2">{{ $vv['method'] }}</span> </div>
                    </div>
                    <div>
                        <span class="text-xl text-gray-800">{{ $vv['path'] }}</span>
                        <button class="px-2 py-1 text-xs font-bold bg-green-600 text-white rounded border-none outline-none no-underline mock-api" apiId="{{ $vv['api_id'] }}">模拟请求</button>
                    </div>
                </div>


                <div>
                    <span class="text-sm text-gray-700">更新日期：{{ $vv['date'] }}</span>
                </div>


                @if(sizeof($vv['u']) != 0)

                    <div>
                        <span class="text-lg border-l-4 border-blue-500 pl-2">Rest 参数</span>
                    </div>

                    <div class="mb-5">
                        <table class="table-fixed w-full border-2 border-gray-100">
                            <thead>
                            <tr>
                                <th class="w-2/12 px-4 py-2">参数</th>
                                <th class="w-6/12 px-4 py-2">说明</th>
                                <th class="w-2/12 px-4 py-2">必填</th>
                                <th class="w-2/12 px-4 py-2">类型</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($vv['u'] as $i)


                                <tr>
                                    <td class="border px-4 py-2">{{ $i['name'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['desc'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['is_must'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['type'] }}</td>
                                </tr>


                            @endforeach

                            </tbody>
                        </table>
                    </div>

                @endif

                @if(sizeof($vv['q']) != 0)

                    <div>
                        <span class="text-lg border-l-4 border-blue-500 pl-2">Query 请求参数</span>
                    </div>

                    <div class="mb-5">
                        <table class="table-fixed w-full border-2 border-gray-100">
                            <thead>
                            <tr>
                                <th class="w-2/12 px-4 py-2">参数</th>
                                <th class="w-6/12 px-4 py-2">说明</th>
                                <th class="w-2/12 px-4 py-2">必填</th>
                                <th class="w-2/12 px-4 py-2">类型</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($vv['q'] as $i)

                                <tr>
                                    <td class="border px-4 py-2">{{ $i['name'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['desc'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['is_must'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['type'] }}</td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                    </div>

                @endif

                @if(sizeof($vv['b']) != 0)

                    <div>
                        <span class="text-lg border-l-4 border-blue-500 pl-2">Body 请求参数</span>
                    </div>

                    <div class="mb-5">
                        <table class="table-fixed w-full border-2 border-gray-100">
                            <thead>
                            <tr>
                                <th class="w-2/12 px-4 py-2">参数</th>
                                <th class="w-6/12 px-4 py-2">说明</th>
                                <th class="w-2/12 px-4 py-2">必填</th>
                                <th class="w-2/12 px-4 py-2">类型</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($vv['b'] as $i)

                                <tr>
                                    <td class="border px-4 py-2">{{ $i['name'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['desc'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['is_must'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['type'] }}</td>
                                </tr>

                            @endforeach



                            </tbody>
                        </table>
                    </div>

                @endif

                @if(sizeof($vv['r']) != 0)

                    <div>
                        <span class="text-lg border-l-4 border-teal-500 pl-2">返回参数</span>
                    </div>

                    <div class="mb-5">
                        <table class="table-fixed w-full border-2 border-gray-100">
                            <thead>
                            <tr>
                                <th class="w-2/12 px-4 py-2">参数</th>
                                <th class="w-8/12 px-4 py-2">说明</th>
                                <th class="w-2/12 px-4 py-2">类型</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($vv['r'] as $i)

                                <tr>
                                    <td class="border px-4 py-2">{{ $i['name'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['desc'] }}</td>
                                    <td class="border px-4 py-2">{{ $i['type'] }}</td>
                                </tr>

                            @endforeach



                            </tbody>
                        </table>
                    </div>

                @endif


                <div>
                    <span class="text-sm border-l-4 border-red-500 pl-2">额外说明</span>

                    <div class="bg-gray-100 min-h-0 py-1 px-3 text-sm text-gray-800">
                        {{ $vv['introduction'] }}
                    </div>

                </div>

            </section>


        @endforeach

    @endforeach


</main>



<script>

    $(document).ready(function(){

        $('a[href*=#],area[href*=#]').click(function() {

            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var $target = $(this.hash);
                $target = $target.length && $target || $('[id=' + this.hash.slice(1) + ']');
                if ($target.length) {
                    var targetOffset = $target.offset().top;
                    $('html,body').animate({
                            scrollTop: targetOffset
                        },
                        400);
                    return false;
                }
            }
        });


        let url = "{{ route('docapi.show') }}";

        $('button.mock-api').click(function () {

            let apiId = $(this).attr("apiId");
            //iframe窗
            layer.open({
                type: 2,
                title: 'API调试工具',
                offset: 'rt',
                shadeClose: true,
                shade: 0.3,
                maxmin: false, //开启最大化最小化按钮
                area: ['50%', '100%'],
                content: url+"?api="+apiId,
            });


        });




    });


</script>

</body>

</html>
