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
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
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
                if (isset($v['children'])) {
                    $is_exists = self::getTreeBranch($v['children'], $ids);
                    if (!empty($is_exists)) {
                        array_unshift($branch, $v);
//                        break;
                    }
                }
            }
        }
        return $branch;
    }
}
?>