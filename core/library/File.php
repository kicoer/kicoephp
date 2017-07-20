<?php
// 文件操作类
namespace kicoe\Core;

use \kicoe\Core\Exception;
use \SplFileInfo;

class File extends SplFileInfo{

    // 上传文件信息
    private $up_info = [];
    // 验证信息
    private $file_veri = [];

    /**
     * SPL_FILE
     * @param string $filename 文件名
     */
    public function __construct($filename, $info = [])
    {
        parent::__construct($filename);
        $this->up_info = $info;
    }

    /**
     * 设置文件验证规则
     */
    public function veri($veri_info = [])
    {
        $this->file_veri = $veri_info;
    }

    /**
     * 文件上传验证函数
     * 随便写
     * @throws 自定义异常 - 有关文件上传错误
     */
    public function validate()
    {
        // 验证文件大小 k
        if (isset($this->file_veri['size']) && $this->up_info['size'] > $this->file_veri['size']*1000 ) {
            throw new Exception("上传文件超出 ", $this->file_veri['size'].'k');
        }
        // 验证文件类型
        if (isset($this->file_veri['type']) && ( is_string($this->file_veri['type'])?($this->up_info['type'] != $this->file_veri['type'] ):( !in_array($this->up_info['type'], $this->file_veri['type']) ) ) ) {
            throw new Exception("上传文件Mime ", '...');
        }
        // 验证文件后缀
        if (isset($this->file_veri['ext']) && (is_string($this->file_veri['ext'])?( $this->file_veri['ext'] != strtolower(pathinfo($this->up_info['name'], PATHINFO_EXTENSION)) ):(!in_array(strtolower(pathinfo($this->up_info['name'], PATHINFO_EXTENSION)), $this->file_veri['ext'] ))) ) {
            throw new Exception("上传文件后缀 ", '...');
        }       
    }

   /**
     * 检查目录是否可写
     * @param  string   $path    目录
     * @return boolean
     */
    protected function checkPath($path)
    {
        if (is_dir($path)) {
            return true;
        }

        if (mkdir($path, 0755, true)) {
            return true;
        } else {
            throw new Exception("上传文件，目录不可写", $path);
        }
    }

    /**
     * 拷贝函数
     * @param string $save 复制文件路径与名称
     * @return File object
     */
    public function CP($save)
    {
        // 上传$up_info里有个错误信息,最好判断下不为0
        if ($this->up_info !== []) {
            // 验证上传文件
            $this->validate();
            $this->checkPath(dirname($save));
            if (!move_uploaded_file($this->getRealPath(), $save)) {
                throw new Exception("上传文件, CP FAIL", $save);
            }
            // 返回移动后的文件实例
            return ( new self($save, $this->up_info) );
        }
    }

}