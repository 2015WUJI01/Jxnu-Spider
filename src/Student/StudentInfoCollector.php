<?php

namespace JxnuSpider\Student;

use QL\QueryList;

class StudentInfoCollector
{
    public static function getInfo($user_cookie)
    {
        $numbers = func_num_args();
        $user_data = [];
        $info_name_arr = [
            'infoPage' => [],
            'scorePage' => [],
            'phonePage' => [],
            'infoCheckPage' => []
        ];
        for ($i = 1; $i < $numbers; $i++) {
            $current_info_name = func_get_arg($i);
            switch ($current_info_name) {
                case 'name':
                case 'gender':
                case 'class_name':
                    $info_name_arr['infoCheckPage'][] = $current_info_name;
                    break;
                case 'major_name':
                    $info_name_arr['scorePage'][] = $current_info_name;
                    break;
                case 'phone':
                    $info_name_arr['phonePage'][] = $current_info_name;
                    break;
                default:
                    break;
            }
        }

        if (count($info_name_arr['infoCheckPage']) > 0) {
            $partial_user_info = self::getFromInfoCheckPage($user_cookie, $info_name_arr['infoCheckPage']);
            $user_data = array_merge($user_data, $partial_user_info);
        }
        if (count($info_name_arr['scorePage']) > 0) {
            $partial_user_info = self::getFromScorePage($user_cookie, $info_name_arr['scorePage']);
            $user_data = array_merge($user_data, $partial_user_info);
        }
        if (count($info_name_arr['phonePage']) > 0) {
            $partial_user_info = self::getFromPhonePage($user_cookie, $info_name_arr['phonePage']);
            $user_data = array_merge($user_data, $partial_user_info);
        }
        return $user_data;
    }

    /**
     * 学生信息校对页面
     * @param $user_cookie
     * @param $info_name_arr
     * @return array
     */
    private static function getFromInfoCheckPage($user_cookie, $info_name_arr)
    {
        $url = 'http://jwc.jxnu.edu.cn/MyControl/Student_InforCheck.aspx';

        $ql = QueryList::get($url, [], [
            'header' => [
                'Cookie' => $user_cookie
            ]
        ])->removeHead();
        $all_user_infos = [
            'uid' => $ql->find('#lblXH')->text(),
            'name' => $ql->find('#lblXM')->text(),
            'gender' => $ql->find('#lblXB')->text(),
            'class_name' => $ql->find('#lblBJ')->text(),
            // 身份证号
            // 民族
        ];
        $ql->destruct();

        $user_info = [];
        foreach ($info_name_arr as $info_name) {
            $user_info[$info_name] = $all_user_infos[$info_name];
        }
        return $user_info;
    }

    /**
     * 教务在线短信平台手机号码登记窗口
     * @param $user_cookie
     * @param $info_name_arr
     * @return array
     */
    private static function getFromPhonePage($user_cookie, $info_name_arr)
    {
        $ql = QueryList::get('http://jwc.jxnu.edu.cn/MyControl/Phone.aspx', [], [
            'header' => [
                'Cookie' => $user_cookie
            ]
        ])->removeHead();
        $all_user_infos = [
            //'class_name' => $ql->find('#lblInfor u:eq(0)')->text(),
            //'uid' => $ql->find('#lblInfor u:eq(1)')->text(),
            //'name' => $ql->find('#lblInfor u:eq(2)')->text(),
            'phone' => $ql->find('#lblInfor u:eq(3)')->text(),
        ];
        $ql->destruct();

        $user_info = [];
        foreach ($info_name_arr as $info_name) {
            $user_info[$info_name] = $all_user_infos[$info_name];
        }
        return $user_info;
    }

    /**
     * 课程成绩页
     * @param $user_cookie
     * @param $info_name_arr
     * @return array
     */
    private static function getFromScorePage($user_cookie, $info_name_arr)
    {
        $url = 'http://jwc.jxnu.edu.cn/MyControl/All_Display.aspx';
        $ql = QueryList::get($url, [
            'UserControl' => 'xfz_cj.ascx',
            'Action' => 'Personal'
        ], [
            'header' => [
                'Cookie' => $user_cookie
            ]
        ])->removeHead();
        $all_user_infos = [
            // 学号
            //'name' => $ql->find('#_ctl11_lblMsg u:eq(4)')->text(),
            'college_name' => $ql->find('#_ctl11_lblMsg u:eq(0)')->text(),
            'major_name' => $ql->find('#_ctl11_lblMsg u:eq(1)')->text(),
            //'class_name' => $ql->find('#_ctl11_lblMsg u:eq(2)')->text(),
            'total_credits' => $ql->find('#_ctl11_lblMsg u:eq(5)')->text(),
        ];
        $ql->destruct();

        $user_info = [];
        foreach ($info_name_arr as $info_name) {
            $user_info[$info_name] = $all_user_infos[$info_name];
        }
        return $user_info;
    }
}