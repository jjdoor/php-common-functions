<?php
namespace Benjamin\Common;
/*
 * 数据库内结构如下所示
 *
id	parentid	label
51	31	电站布局
52	31	设备模拟
11	0	系统设置
12	11	管理员设置
13	11	角色&权限设置
14	11	角色&权限管理
15	11	菜单树管理
17	11	角色管理
18	0	设备设置
19	18	汇总器管理
20	18	优化器设置
21	0	数据查询
22	21	优化器数据
23	21	汇总器数据
24	21	电站信息查询
25	0	系统命令
26	25	控制命令
29	0	报警设置
30	29	报警规则设定
31	0	电站设置
32	31	电站用户配置
33	31	用户电站查询
34	21	第三方数据查询
35	21	第三方设备配置
36	21	系统记录查询
37	29	报警记录查询
39	0	固件管理
41	39	固件设置
42	0	Management Tools
43	42	Beebox/Beehive status
44	42	Device control
45	11	客户账户设置
46	21	电站查询
47	25	手动控制命令
48	25	多选升级命令
49	25	命令备忘录
50	25	优化器命令
 */
class Tree
{
    //静态变量
    static $son_mark = 'children';

    //设置静态变量
    public static function setSonMark($mark)
    {
        self::$son_mark = $mark;
    }

    /**
     * 输出树
     * @param $data
     * @param $parentId
     * @return array
     */
    static function buildTree($data, $parentId = 0) {
        $tree = [];
        foreach ($data as $item) {
            if ($item['parentid'] == $parentId) {
                $children = static::buildTree($data, $item['id']);
                if (!empty($children)) {
                    $item[self::$son_mark] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    //重构buildTree方法，返回数据需要增加子节点的深度
    static function buildTree2($data, $parentId = 0,$depth = 0) {
        $tree = [];
        foreach ($data as $item) {
            if ($item['parentid'] == $parentId) {
                $item['depth'] = $depth;
                $children = static::buildTree2($data, $item['id'],$depth+1);
                $children = array_map(function ($item) use ($depth){
                    $item['depth'] = $depth+1;
                    return $item;
                }, $children);
                if (!empty($children)) {
                    $item[self::$son_mark] = $children;
                    $item['isLeaf'] = false;
                }else{
                    $item['isLeaf'] = true;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /*
     * 递归重组节点信息为一维数组，并保存原来顺序
     */
    static function array_multi2single($array) {
        static $result_array = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                self::array_multi2single($value);
            } else
                $result_array[] = $value;
        }
        return $result_array;
    }

    /*
     * 递归重组节点信息为二维数组，并保存原来顺序
     */
    static function array_multi2singlearray($array) {
        static $result_array = array();
        foreach ($array as $value) {
            if (is_array($value) && count($value,1) > count($value)) {
                $son = $value[self::$son_mark];
                unset($value[self::$son_mark]);
                $result_array[] = $value;
                self::array_multi2singlearray($son);
            } else
                $result_array[] = $value;
        }
        return $result_array;
    }

    /**
     * 任意ids数组，获取包含ids的树
     * @param $tree buildTree使用后，获取的树
     * @param $ids id数组
     * @return array
     */
    public static function getTreeBranch($tree, $ids)
    {
        $branch = [];
        foreach ($tree as $k => $v) {
            if (in_array($v['id'], $ids)) {
//                $branch[] = $v;
                array_unshift($branch, $v);
//                break;
            } else {
                if (isset($v[self::$son_mark])) {
                    $is_exists = self::getTreeBranch($v[self::$son_mark], $ids);
                    if (!empty($is_exists)) {
                        $tmp = $v;
                        $tmp[self::$son_mark] = null;
                        $tmp[self::$son_mark] = $is_exists;

                        array_unshift($branch, $tmp);
//                        break;
                    }
                }
            }
        }
        return $branch;
    }
}
?>