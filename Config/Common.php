<?php

/**
 * File: Common.php
 *
 * @author mingl
 */

namespace Config;

/**
 * Class Common.
 */
class Common
{

    // 公众号配置
    public static $weixinInfo = array(
        'app_id' => 'wx6b96dfce3a805628',
        'app_secret' => '1fd7a09af0add72d8933c790202520ad',
        'token' => 'aaa',
        'encoding_aes_key' => 'zZKcBtEbrifCnd4z4ayOKa4FGWcNbYyJb6IdxxgoWvO',
        'is_safe_mode' => true
    );
    // 小程序配置
    public static $miniprogram = array(
        'app_id' => 'xx',
        'app_secret' => 'oo'
    );
    // 消息发送的签名key
    public static $messageSignKey = "mde1131axxx@#Ade";
    // 用户扫描带场景值的二维码时的回复（默认回复图文消息，可以自行调整数组格式支持其他类型的消息）
    public static $subscribeOrScanSenceResponse = array(
        'activity_actor' => array(
            'title' => '吴彦祖',
            'desc' => '中国香港男演员',
            'pic_url' => 'http://xj.cnr.cn/2014xjfw/2014xjfwws/20150213/W020150213627208180677.jpg',
            'url' => 'https://weibo.com/p/1006052345544584'
        )
    );
    // 笑话
    public static $joke = array(
        "刚才室友穿着内裤去隔壁宿舍说事，十分钟过后，人没回来，内裤被送回来了",
        "在对不起三个字里加哪两个字最忧伤，神回复：对三，要不起",
        "今天喝酸奶时舔盖，被朋友看到了，他说：“大哥你这么有钱，喝酸奶还舔盖？”我急中生智： “你懂什么，我只吃盖上这一点。”说完把酸奶扔了。",
        "50岁大妈短裙丝袜乘地铁遭猥琐男摸臀，热心乘客见义勇为被大妈怒斥坏了好事",
        "女友错将风油精当成润滑油，小王新婚之夜竟在北京市第三人民医院度过",
        "女孩为晕倒老人做人工呼吸，围观老大爷竟集体假摔",
        "色狼抱住女路人欲施爆,机智女孩十秒完成卸妆,将流氓成功吓跑",
        "文学界最新研究成果，捕蛇者说原作者并非柳宗元而是法海",
        "主人遭遇车祸倒在马路旁，爱心宠物犬不离不弃守在旁边保护主人不允许外人接近，最终延误救治时间不治身亡。",
        "17名学生约校外青年放学后操场殴斗，被班主任知道，班主任立刻打电话给校长并拿出五十元赌校外青年赢",
        "店家WIFI密码竟是meiyoumima，青年小王多次连接失败后将老板打伤",
        "男子午睡讲梦话说出银行卡密码，同事盗取6900元后发现密码竟是自己老婆生日",
        "青年小王面对痛经女友认真说到女人天生就是用来疼的",
        "青年作家儿童浴场离奇溺亡成惊天迷案，浴场负责人辩称水深明明只有一米四",
        "90岁高龄老妪爱上30岁大叔痛苦不已，写信求助知心姐姐询问自己究竟算是大叔控还是正太控",
        "失明夫妻婚后同时进行复明手术，获得光明后，对视一笑在病房当即签订离婚协议",
        "孕妇公交车上突然晕倒，最美售票员联合数十名爱心乘客奋力施救，终唤醒孕妇令其补票",
        "一男童惨遭食人魔毒手，丧尽天良的凶手竟忘记放葱。",
        "我怕我女朋友出轨，就把她气给放了.",
        "天空惊现一烧柴油的UFO，宇宙无敌大将军金正恩驾驶一核动力拖拉机将其击落",
        "老大让我埋好地雷后用脚猛踩几下说是为了土地平整不易发现 老大就是老大、真细心",
        "你骂我，肯定是因为你不了解我。。。。。。。。。因为那些了解我的人，都想打我",
        "昨天看到我朋友被十个人打，我冲上去一下就解决了一半，5个人打他，5个人打我",
        "北冥有鱼，其名为鲲；鲲之大，一锅炖不下；化而为鸟，其名为鹏；鹏之大，需要两个烧烤架 ；一个秘制，一个微辣",
        "孩子睡觉老是踢被子,幸好被我及时发现打断了腿，否则肯定感冒.",
        "笨鸟先飞，笨猪先肥。",
        "小时候我以为自己长大后可以拯救这个世界，等我长大后才发现整个世界都拯救不了我......",
        "周末大家说要举行放鸽子大赛，最后就我一个人去了。",
        "世界上有许多你意想不到的事，比如，你以为我要举个例子。",
        "领导说你行不行也行，领导说你不行行也不行",
        "不是所有牛奶都叫特仑苏，不是每支球队都叫特能输，中国足球队专注输球30年，一直在模仿，从未被超越。我们不进球，我们只是足球的搬运工"
    );
    // 菜单配置
    public static $menu = array(
        '发现新大陆' =>
        array(
            'subs' =>
            array(
                '猎奇' =>
                array(
                    'type' => 'click',
                    'key' => 'curiosity',
                ),
                '今日运势' => array(
                    'type' => 'click',
                    'key' => 'luck'
                ),
                '找个妹子' =>
                array(
                    'type' => 'click',
                    'key' => 'girl_friend',
                ),
            ),
        ),
        '源码' =>
        array(
            'type' => 'view',
            'url' => 'https://github.com/supostar/weixin',
        ),
        'BAT' =>
        array(
            'subs' =>
            array(
                '百度' =>
                array(
                    'type' => 'view',
                    'url' => 'http://www.baidu.com',
                ),
                '天猫' =>
                array(
                    'type' => 'view',
                    'url' => 'http://www.tmall.com',
                ),
                '微信' =>
                array(
                    'type' => 'miniprogram',
                    'pagepath' => 'page/weixin/index',
                    'url' => 'http://mp.weixin.qq.com',
                ),
            ),
        ),
    );
    // 模板消息配置
    public static $templateMessageConfig = array(
        'activity' =>
        array(
            'template_id' => 'PjIXf2q_dC-KwAZX8EuBldeudC2-TEkghFvjGlm44yU',
            'key' => 'first,coupon,expDate,remark',
            'color' => '#FF0000,#FF9900,#173177,#FF0000',
        ),
        'overdue' =>
        array(
            'template_id' => 'u4VQjTTgQd_TmqZpmLwE0jsj6LPNyt8LC2GQNfRZSu4',
            'key' => 'first,name,expDate,remark',
            'color' => '#FF0000,#000000,#000000,#173177',
        ),
    );
    // 永久二维码场景参数配置
    public static $qRLimitSceneInfo = array(
        'scene_str' =>
        array(
            0 => 'activity_actor',
        ),
        'scene_id' =>
        array(
        ),
    );

}
