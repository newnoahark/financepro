    <dl class="list_dl">
        <dt class="list_dt">
            <span class="_after"></span>
            <p>选择器</p>
            <i class="list_dt_icon"></i>
        </dt>
        <dd class="list_dd">
            <ul>
                <li class="list_li">#id</li>
                <li class="list_li">element</li>
                <li class="list_li">.class</li>
                <li class="list_li">*</li>
            </ul>
        </dd>
        <dt class="list_dt">
            <span class="_after"></span>
            <p>属性</p>
            <i class="list_dt_icon"></i>
        </dt>
        <dd class="list_dd">
            <ul>
                <li class="list_li">attr</li>
                <li class="list_li">removeAttr</li>
                <li class="list_li">prop</li>
                <li class="list_li">removeProp</li>
                <li class="list_li">addClass</li>
                <li class="list_li">removeClass</li>
            </ul>
        </dd>
        <dt class="list_dt">
            <span class="_after"></span>
            <p>文档处理</p>
            <i class="list_dt_icon"></i>
        </dt>
        <dd class="list_dd">
            <ul>
                <li class="list_li">append</li>
                <li class="list_li">appendTo</li>
                <li class="list_li">prepend</li>
                <li class="list_li">prependTo</li>
                <li class="list_li">after</li>
                <li class="list_li">before</li>
            </ul>
        </dd>
        <dt class="list_dt">
            <span class="_after"></span>
            <p>事件</p>
            <i class="list_dt_icon"></i>
        </dt>
        <dd class="list_dd">
            <ul>
                <li class="list_li">ready</li>
                <li class="list_li">on</li>
                <li class="list_li">off</li>
                <li class="list_li">bind</li>
                <li class="list_li">one</li>
                <li class="list_li">trigger</li>
                <li class="list_li">hover</li>
                <li class="list_li">click</li>
                <li class="list_li">focus</li>
            </ul>
        </dd>
        <dt class="list_dt">
            <span class="_after"></span>
            <p>AJAX</p>
            <i class="list_dt_icon"></i>
        </dt>
        <dd class="list_dd">
            <ul>
                <li class="list_li">$.ajax</li>
                <li class="list_li">$.get</li>
                <li class="list_li">$.getJSON</li>
                <li class="list_li">$.getScript</li>
                <li class="list_li">$.post</li>
            </ul>
        </dd>
        <dt class="list_dt">
            <span class="_after"></span>
            <p>效果</p>
            <i class="list_dt_icon"></i>
        </dt>
        <dd class="list_dd">
            <ul>
                <li class="list_li">show</li>
                <li class="list_li">hide</li>
                <li class="list_li">toggle</li>
                <li class="list_li">slideDown</li>
                <li class="list_li">slideUp</li>
                <li class="list_li">slideToggle</li>
                <li class="list_li">fadeIn</li>
                <li class="list_li">fadeOut</li>
                <li class="list_li">fadeTo</li>
                <li class="list_li">fadeToggle</li>
                <li class="list_li">stop</li>
            </ul>
        </dd>
    </dl>
 <script type="text/javascript">
        $(".list_dt").on("click",function () {
            $('.list_dd').stop();
            $(this).siblings("dt").removeAttr("id");
            if($(this).attr("id")=="open"){
                $(this).removeAttr("id").siblings("dd").slideUp();
            }else{
                $(this).attr("id","open").next().slideDown().siblings("dd").slideUp();
            }
        });
</script>   
    
    