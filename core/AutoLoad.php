<?php

/**
* 自动加载类
*/
class AutoLoad
{
    //以下根据官方给出psr-4自动加载实例改
    protected $prefixes = array();

    public function register()
    {
        //注册自动加载
        spl_autoload_register(array($this, 'loadClass'));
        $this->addNamespace('kicoe\Core', CORE_PATH.'library');     //框架核心库自动加载
        $this->addNamespace('app\controller', APP_PATH.'controller');   //控制器库自动加载
        $this->addNamespace('app\model',APP_PATH.'model');      //模型库自动加载
    }

    /**
     *添加命名空间
     * @param string $prefix 命名空间前缀
     * @param string $base_dir 对应实际类名
     * @return void
    */
    public function addNamespace($prefix, $base_dir)
    {
        $prefix = trim($prefix, '\\') . '\\';

        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }
        $this->prefixes[$prefix] = $base_dir;
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }
            $prefix = rtrim($prefix, '\\');
        }
        return false;
    }

    /**
     * 判断与加载文件
     *
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }
        $file = $this->prefixes[$prefix]
              . str_replace('\\', '/', $relative_class)
              . '.php';
        if ($this->requireFile($file)) {
            return $file;
        }
        return false;
    }

    /**
     * 检测文件是否存在
     *
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}

$core = new AutoLoad;    //自动加载类实例
$core->register();  //注册自动加载