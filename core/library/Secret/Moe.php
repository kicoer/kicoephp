<?php
// 萌属性~

namespace kicoe\Core\Secret;

class Moe
{
    // 颜表情数组
    private static $emoticon = [
    	[
    		// 卖萌
    		'(>▽<)','( *￣▽￣)((≧︶≦*)','（*＾-＾*）','(≧∇≦)ﾉ','n(*≧▽≦*)n','ヾ(^▽^*)))','(～o￣3￣)～','（o´・ェ・｀o）','o(〃\'▽\'〃)o','つ﹏⊂'
    	],
    	[
    		// 不高兴
    		'Ｏ(≧口≦)Ｏ','ヽ(*。>Д<)o゜','(・∀・(・∀・(・∀・*)','(。・・)ノ','ε = = (づ′▽`)づ','→)╥﹏╥)','(*￣︿￣)','（╯‵□′）╯︵┴─┴','┴─┴︵╰（‵□′╰）','（＞人＜；）','(。﹏。*)','（ﾉ´д｀）','つ﹏⊂','( ´･･)ﾉ(._.`)'
    	]
    ];

    /**
     * 设置自己的颜表情数组,博客后台需要
     * 测试扩展
     * @param array $em_list 设置的表情数组
     */
    public static function set_em($em_list)
    {
        self::$emoticon = $em_list;
    }

    /**
     * 随机获取一个颜表情,exception需要
     * @param int $type 表情类型
     * @return string 随机表情
     */
    public static function em($type = false)
    {
        if ($type === false) {
            $yu = time()%count(self::$emoticon);
            return self::$emoticon[$yu][time()%count(self::$emoticon[$yu])];
        }
        if (!is_integer($type) || $type<0 || $type>=count(self::$emoticon)) {
            return '_(:з」∠)_';
        }
        return self::$emoticon[$type][time()%count(self::$emoticon[$type])];
    }
}
