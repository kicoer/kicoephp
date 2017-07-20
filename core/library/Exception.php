<?php
namespace kicoe\Core;
// 自定义异常类

class Exception extends \Exception
{
    // 错误信息模板
    protected $Exception_tpl_start = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title> _(:з」∠)_</title>
        <meta name="robots" content="noindex,nofollow" />
        <style>
            *{ font-family: "Microsoft Yahei"; }
            .er { width:400px;margin:40px auto; }
            .er div span{ display:block; padding:3px 10px; font-size:13px; color:#FF4040; box-shadow: 0px 1px 0px #ddd;letter-spacing: 1px; }
            .er div p{ padding:5px 10px; }
        </style>
        </head>
        <body>
        ';
    protected $Exception_tpl ='
        <div class="er">
            <div class="Erro"> <span> 错误类型 </span> <p>%s</p> </div>
            <div class="Exce"> <span> 异常信息 </span> <p>%s</p> </div>
            <div class="file"> <span> 引发文件 </span> <p>%s</p> </div>
            <div class="line"> <span> 报错行 </span> <p>%s</p> </div>
        </div>
        </body>
        </html>
    ';

    // 报错类型
    protected $Exc_type;

    /**
     * 和原来相比新增一个报错类型
     * 主要构造参数有
     * @param string $file 报错文件名
     * @param int $line 报错行数
     * @param message 报错信息
     */
    public function __construct($type = null, $message = null, $file = null, $line = null, $code = 0, Exception $previous = null)
    {
        $this->Exc_type = $type;
        if ($file != null) {
            $this->file = $file;
        }
        if ($line != null) {
            $this->line = $line;
        }
        parent::__construct($message, $code, $previous);
    }

    // 用于显示错误的页面信息
    public function show()
    {
        header("HTTP/1.1 500 $this->message");
        echo $this->Exception_tpl_start.sprintf($this->Exception_tpl, $this->Exc_type, $this->message, $this->file, $this->line);
    }

}