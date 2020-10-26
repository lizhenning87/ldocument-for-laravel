<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>接口文档</title>
    <link rel="stylesheet" href="{{ asset('static/source/styles.css') }}">

    <script src="{{ asset('static/source/jquery.min.js') }}"></script>
    <script src="{{ asset('static/source/layer/layer.js') }}"></script>



</head>

<body class="bg-white font-mono">

<div>
    <form class="px-3" id="tf">

        <input type="hidden" name="zning-url" value="{{ $data['path'] }}">
        <input type="hidden" name="zning-method" value="{{ $data['method'] }}">

        <div class="flex justify-between h-12 items-center">
            <div class="flex items-center">
                <div class="text-lg">{{ $data['name'] }}</div>
                <div class=""><span class="text-sm bg-red-500 text-white rounded-md py-1 px-2 ml-2">{{ $data['method'] }}</span> </div>
            </div>
            <div>
                <span class="text-sm text-gray-800">{{ $data['path'] }}</span>
            </div>
        </div>


        <div>
            <span class="text-sm border-l-4 border-blue-500 pl-2">Header 参数</span>
        </div>

        <div class="mb-5">
            <table class="table-fixed w-full border-2 border-gray-100">
                <thead>
                <tr>
                    <th class="w-2/12 px-4 py-2 text-sm">参数</th>
                    <th class="w-10/12 px-4 py-2 text-sm">内容</th>

                </tr>
                </thead>
                <tbody>

                <tr>
                    <td class="border px-4 py-2 text-sm">TOKEN</td>
                    <td class="border px-4 py-2 text-sm">
                        <input class="block w-full border rounded py-2 px-3 text-sm text-gray-700" type="text" autocomplete="off" name="zning-token" placeholder="仅在需要token时提交即可"/>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        @if(sizeof($data['u']) != 0)

            <div>
                <span class="text-sm border-l-4 border-blue-500 pl-2">Rest 参数</span>
            </div>

            <div class="mb-5">
                <table class="table-fixed w-full border-2 border-gray-100">
                    <thead>
                    <tr>
                        <th class="w-2/12 px-4 py-2 text-sm">参数</th>
                        <th class="w-10/12 px-4 py-2 text-sm">内容</th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach($data['u'] as $i)


                        <tr>
                            <td class="border px-4 py-2 text-sm">{{ $i['name'] }}</td>
                            <td class="border px-4 py-2 text-sm">
                                <input class="block w-full border rounded py-2 px-3 text-sm text-gray-700" type="text" autocomplete="off" name="zning-u-{{ $i['name'] }}"/>
                            </td>

                        </tr>


                    @endforeach

                    </tbody>
                </table>
            </div>

        @endif

        @if(sizeof($data['q']) != 0)

            <div>
                <span class="text-sm border-l-4 border-blue-500 pl-2">Query 请求参数</span>
            </div>

            <div class="mb-5">
                <table class="table-fixed w-full border-2 border-gray-100">
                    <thead>
                    <tr>
                        <th class="w-2/12 px-4 py-2 text-sm">参数</th>
                        <th class="w-10/12 px-4 py-2 text-sm">内容</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($data['q'] as $i)

                        <tr>
                            <td class="border px-4 py-2  text-sm">{{ $i['name'] }}</td>
                            <td class="border px-4 py-2 text-sm">
                                <input class="block w-full border rounded py-2 px-3 text-sm text-gray-700" type="text" autocomplete="off" name="zning-q-{{ $i['name'] }}"/>
                            </td>
                        </tr>

                    @endforeach

                    </tbody>
                </table>
            </div>

        @endif

        @if(sizeof($data['b']) != 0)

            <div>
                <span class="text-sm border-l-4 border-blue-500 pl-2">Body 请求参数</span>
            </div>

            <div class="mb-5">
                <table class="table-fixed w-full border-2 border-gray-100">
                    <thead>
                    <tr>
                        <th class="w-2/12 px-4 py-2  text-sm">参数</th>
                        <th class="w-10/12 px-4 py-2 text-sm">内容</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($data['b'] as $i)

                        <tr>
                            <td class="border px-4 py-2  text-sm">{{ $i['name'] }}</td>
                            <td class="border px-4 py-2 text-sm">

                                @if($i['type'] == '[file]')

                                    <input class="block w-full border rounded py-2 px-3 text-sm text-gray-700" type="file" autocomplete="off" name="zning-b-f-{{ $i['name'] }}"/>
                                @else
                                    <input class="block w-full border rounded py-2 px-3 text-sm text-gray-700" type="text" autocomplete="off" name="zning-b-s-{{ $i['name'] }}"/>
                                @endif


                            </td>
                        </tr>



                    @endforeach



                    </tbody>
                </table>
            </div>

        @endif

        <input type="button" value="提交" onclick="doApi();" class="mb-2 px-4 py-1 text-sm font-bold rounded no-underline hover:shadow-md bg-green-600 text-white" />

    </form>
</div>

<div class="border bg-gray-100 p-2 mx-2">

    <pre id="json_result">

    </pre>

</div>

<script>

    function doApi() {

        var form = new FormData(document.getElementById("tf"));
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('docapi.api') }}",
            type: "post",
            data: form,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log(data);

                let result = JSON.stringify(data, null, 2);
                document.getElementById('json_result').innerText= result;
            },
            error: function(e) {
                console.log(e);

                let result = JSON.stringify(e['responseJSON'], null, 2);
                document.getElementById('json_result').innerText= result;
            }
        });

    }

</script>

</body>

</html>
