<?php

return [

'paths' => ['/','*','/*','/login','/register'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://3.35.133.157'], // 요청을 허용할 출처 추가
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,


];
