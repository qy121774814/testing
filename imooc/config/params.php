<?php

return [
    'title' => '一猿工作室',
    'desc' => '一猿工作室测试用',


    "domain" => [
//         'www' => 'http://book.imooc.test/',
//         'm' => 'http://book.imooc.test/m',
//         'web' => 'http://book.imooc.test/web',

//        'www' => 'http://13631262490.tunnel.echomod.cn',
//        'm' => 'http://13631262490.tunnel.echomod.cn/m',
//        'web' => 'http://13631262490.tunnel.echomod.cn/web',

//
        'www' => 'http://book.zw',
        'm' => 'http://book.zw/m',
        'web' => 'http://book.zw/web',
    ],
    "upload" => [
         'avatar' => '/uploads/avatar',
         'brand' => '/uploads/brand',
         'book' => '/uploads/book',
    ],
    'weixin' => [
        'appid' => 'wx3141670ec9299e16',
        'sk' => 'aebbef853d3ea7ab3efcc2235b214a26',
        'token' => '6f0b7f8e78',
        'aeskey' => 'ahTBcAYPjdIh3vzNLaJbxhExODR1ErHq2DpPK3wZEe6',
        'pay' => [
            'key' => '根据实际情况填写',
            'mch_id' => '根据实际情况填写',
            'notify_url' => [
                'm' => '/pay/callback'
            ]
        ]
    ]

];
