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


    $('button.mock-api').click(function () {


        //边缘弹出
        layer.open({
            type: 1
            ,offset: 'rb' //具体配置参考：offset参数项
            ,content: '<div style="padding: 20px 80px;">内容</div>'
            ,btn: '关闭全部'
            ,btnAlign: 'c' //按钮居中
            ,shade: 0 //不显示遮罩
            ,yes: function(){
                layer.closeAll();
            }
        });


    });




});
