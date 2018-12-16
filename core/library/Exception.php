<?php
// 自定义异常类

namespace kicoe\Core;

class Exception extends \Exception
{

    private $ex_tpl_start = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Exception</title>
        <meta name="robots" content="noindex,nofollow" />
    ';

    // custom error page html head
    protected $ex_tpl_head = '
        <link rel="stylesheet" type="text/css" href="/static/css/main.css">
        <style>
            .er { width:400px;margin:60px auto; }
            .er div span{ display:block; padding:15px 10px; font-size:14px; color:#FF4040; box-shadow: 0px 1px 0px #ddd;letter-spacing: 1px; }
            .er div p{ padding:5px 12px; }
            em{ font-family:"MS Gothic"; display:block;text-align:center;margin-bottom:16px; }
        </style>
    ';

    private $ex_tpl_end ='
        </head>
        <body>
        <div class="er">
            <div class="type"> <span> type </span> <p>%s</p> </div>
            <div class="info"> <span> info </span> <p>%s</p> </div>
            <div class="file"> <span> file </span> <p>%s</p> </div>
            <div class="line"> <span> line </span> <p>%s</p> </div>
        </div>
        </body>
        </html>
    ';

    // 报错类型
    protected $ex_type;

    /**
     * 和原来相比新增一个报错类型
     * 主要构造参数有
     * @param string $file 报错文件名
     * @param int $line 报错行数
     * @param string message 报错信息
     */
    public function __construct(
        $type = null, 
        $message = null, 
        $file = null, 
        $line = null, 
        $code = 0, 
        Exception $previous = null
    )
    {
        $this->ex_type = $type;
        $this->file = $file;
        $this->line = $line;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 用于显示错误信息
     */
    public function show()
    {
        header("HTTP/1.1 500 $this->message");

        Response::getInstance()->status('500')->send(
            $this->ex_tpl_start.$this->ex_tpl_head.sprintf(
                $this->ex_tpl_end,
                $this->ex_type,
                $this->message,
                substr($this->file, strlen(substr(CORE_PATH, 0, -6))),
                $this->line
            )
        );
    }

}
