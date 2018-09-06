<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
error_reporting(0);
require 'querylist/phpQuery.php';
require 'querylist/QueryList.php';
use QL\QueryList;

class curlapi{
    public $url; //提交地址
    public $params; //登入的post数据
    public $cookies=""; //cookie
    public $referer=""; //http referer
    
    /*
        获取验证码
    */
    public function get_code(){
        $ch = curl_init($this -> url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        preg_match("/Set-Cookie:(.*);/siU", $output, $arr);
        $cookies = $arr[1];
        //cookies存SESSION
        session_start();
        $_SESSION['cookies'] = $cookies;
        //截取GIF二进制图片
        $explode = explode("GIF89a?",$output);
        return $explode = trim("GIF89a?".$explode[1]);
    }
    
    /*
        模拟登陆
    */
    public function login(){
        session_start();
        $ch=curl_init();

        $headers = array();
        $headers[] = 'X-Apple-Tz: 0';
        $headers[] = 'X-Apple-Store-Front: 143444,12';
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Accept-Encoding: gzip, deflate';
        $headers[] = 'Accept-Language: en-US,en;q=0.5';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        $headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        $headers[] = 'X-MicrosoftAjax: Delta=true';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result=curl_exec($ch);
        preg_match_all("/Set-Cookie:(.*);/siU", $result, $arr);
        $_SESSION['cookies'] = $arr[1][0];

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($result, 0, $headerSize);
            $body = substr($result, $headerSize);
        }

        curl_close($ch);
        return $body;
    }
    
    /*
        curl模拟采集数据
    */
    public function curl(){
        session_start();
        $ch=curl_init();

        $headers = array();
        $headers[] = 'X-Apple-Tz: 0';
        $headers[] = 'X-Apple-Store-Front: 143444,12';
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Accept-Encoding: gzip, deflate';
        $headers[] = 'Accept-Language: en-US,en;q=0.5';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        $headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        $headers[] = 'X-MicrosoftAjax: Delta=true';

        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch, CURLOPT_REFERER,$this -> referer);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*
    curl模拟采集数据，会员数据
    */
    public function getMembersPage(){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*
    curl模拟采集数据，会员一些详细数据
    */
    public function getMembersInfos(){
        session_start();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**分析会员数据
     * @param $rs
     * @param $page
     * @return mixed|string
     */
    public function getMembersInfo($rs, $page){
        $rsBlank = preg_replace("/\s\n\t/","",$rs);
        $rsBlank = str_replace('&nbsp;','',$rsBlank);
        //$rsBlank = str_replace(' ', '', $rsBlank);
        preg_match_all("/datalist.*>(.*)<\/table>/isU", $rsBlank ,$tables);

        if(isset($tables[1][0])) {
            return preg_replace("/<tr class=\"category\">.*<\/tr>/isU", '', $tables[1][0]);
            /*if($page>1) {
                return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
            } else {
                return $tables[1][0];
            }*/
        } else {
            return '';
        }
        return $tables[1][0];
    }

    /**
     * 获取会员信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downMembersCvs($data,$shopname){
        foreach ($data as $k => &$item) {
            if(count($item) > 12) {
                foreach ($item as &$v1) {
                    //$v1 = strip_tags($v1);;
                    $v1 = preg_replace("/\s\n\t/","",$v1);
                    $v1 = str_replace(' ', '', $v1);
                    $v1 = trim(str_replace(PHP_EOL, '', $v1));
                    $v1 = str_replace('&nbsp;','',$v1);
                }

                $other = $item;
                //卡号
                $newdata[$k][0] = "\t".$other['cardnumber']; //卡号
                $newdata[$k][1] = $other['name']; //姓名
                $newdata[$k][2] = $other['mobilephone']; //手机号
                $newdata[$k][3] = $other['sex'] == 2?'女':'男'; //性别

                //卡类型
                $newdata[$k][4] = $other['levelname']; //卡类型

                $newdata[$k][5] = 10; //折扣

                //卡金余额信息,
                $newdata[$k][6] = $other['balanceofcash']; //卡金余额
                $newdata[$k][12] = $other['owedamount']; //欠款
                $newdata[$k][7] = $other['totalCash']; //充值总额
                $newdata[$k][9] = 0; //消费总额
                $newdata[$k][10] = $other['balanceofbonus']; //赠送金
                $newdata[$k][8] = $other['totalconsumedtimes']; //消费次数
                $newdata[$k][11] = $other['balanceofpoints']; //积分
                $newdata[$k][13] = date('Y-m-d', $other['createtime']/1000); //开卡时间


                $newdata[$k][14] = ''; //最后消费时间
                if($other['lastconsumedate'] != ''){
                    $other['lastconsumedate'] = str_replace('T', '', $other['lastconsumedate']);
                    $other['lastconsumedate'] = str_replace('Z', '', $other['lastconsumedate']);
                    $newdata[$k][14] = date('Y-m-d H:i:s', strtotime($other['lastconsumedate'])); //最后消费时间
                }
                $newdata[$k][15] = ''; //生日
                if($other['birthday'] != ''){
                    $newdata[$k][15] = date('Y-m-d', $other['birthday']); //生日
                }
                $newdata[$k][16] = $other['remark']; //会员备注

                $newdata[$k][17] = ''; //会员详情地址
                ksort($newdata[$k]);
            }
        }
        //导出CVS
        $cvsstr = "卡号(必填[唯一]),姓名(必填),手机号(必填[唯一]),性别(必填[“0”代表男，“1”代表女]),卡类型(必填[系统编号]),折扣(必填),卡金余额(必填),充值总额,消费次数,消费总额,赠送金,积分,欠款,开卡时间(格式：YYYY-mm-dd),最后消费时间(格式：YYYY-mm-dd),生日(格式：YYYY-mm-dd),会员备注\n";
        $filename = $shopname.'_会员信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);

        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 19) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }

    /*
    curl模拟采集数据，会员套餐数据
    */
    public function getPackagePage(){
        session_start();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     *获取套餐页面数据
     */
    public function getPackageInfo($rs, $page){
        $rsBlank = preg_replace("/\s\n\t/","",$rs);
        //$rsBlank = str_replace(' ', '', $rsBlank);
        preg_match_all("/table-responsive.*>(.*)<\/form>/isU", $rsBlank ,$tables);
        if(isset($tables[1][0])) {
            if($page>1) {
                return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
            } else {
                return $tables[1][0];
            }
        } else {
            return '';
        }
        return $tables[1][0];
    }

    /**
     * 获取会员套餐信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downPackageCvs($data,$shopname){
        $rules = array(
            //采集tr中的纯文本内容
            'other' => array('tr','html'),
        );
        $newdata = array();
        foreach($data as $k2=>$v2) {
            $newA[0] = ''; //手机号
            $newA[1] = "\t".$v2['cardNumber']; //卡号
            $newA[2] = $v2['memberName']; //姓名
            $newA[3] = $v2['levelName']; //卡名称
            $newA[4] = '次数充值卡'; //卡类型

            $newA[5] = '';//项目编号
            $newA[6] = $v2['poolName'];//项目名称
            $newA[7] = $v2['rechargedTimes'];//总次数
            $newA[8] = $v2['remainedTimes'];//剩余次数
            $newA[9] = $v2['perCost']; //单次消费金额
            $newA[10] = $v2['remainedTimes']*$v2['perCost']; //剩余金额
            $newA[11] = '2200-12-01';//失效日期

            $newA[12] = $v2['remainedTimes'];//总剩余次数
            $newA[13] = $v2['remainedTimes']*$v2['perCost']; //总剩余金额
            $newA[14] = '';
            $newdata[] = $newA;
        }

        //导出CVS
        $cvsstr = "手机号,卡号,姓名,卡名称,卡类型,项目编号,项目名称,总次数,剩余次数,单次消费金额,剩余金额,失效日期,总剩余次数,总剩余金额\n";
        $filename = $shopname.'_会员套餐信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);
        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //时间转换
                if($k == 5 || $k == 19) {
                    //$v1 = strtotime($v1);
                }
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 14) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }

    /**
     * 获取员工信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downStaffCvs($html,$shopname){
        $rules = array(
            //采集tr中的纯文本内容
            'other' => array('tr','html'),
        );
        $newdata = array();
        $data = QueryList::Query($html, $rules)->data;
        foreach ($data as $k=>&$item) {
            $other = explode('</td>', $item['other']);
            if(count($other) > 8) {
                //unset($other[0]);//去掉第一空白项
                $item['other'] = $other;
                foreach ($other as $k1 => &$v1) {
                    $v1 = strip_tags($v1);;
                    $v1 = preg_replace("/\s\n\t/","",$v1);
                    $v1 = str_replace(' ', '', $v1);
                    $v1= trim(str_replace(PHP_EOL, '', $v1));
                }

                $date1 = substr($other[11], 0, 3).' '.substr($other[11], 3, 3).' '.substr($other[11], 19, 4);
                $date1 = date('Y-m-d', strtotime($date1));
                $newdata[$k][0] = "\t".$other[1];
                $newdata[$k][1] = $other[2];
                $newdata[$k][2] = $other[3];
                $newdata[$k][3] = preg_match('/男/', $other[4])?0:1;
                $newdata[$k][4] = $other[9];
                $newdata[$k][5] = str_replace('阴', '', $other[10]);
                $newdata[$k][5] = str_replace('阳', '', $newdata[$k][5]);
                $newdata[$k][5] = str_replace('"', '', $newdata[$k][5]);
                $newdata[$k][6] = $date1;
                $newdata[$k][7] = $other[8];
                $newdata[$k][8] = '';

                //日期格式含有1900，设置为空
                if(preg_match("/1900/isU", $newdata[$k][5])) {
                    $newdata[$k][5] = '';
                }
            }
        }
        unset($newdata[count($newdata)]);
        unset($newdata[count($newdata)]);

        //导出CVS
        $cvsstr = "编号(必填[唯一]),姓名(必填),级别(必填),性别,手机号码,生日,入职时间,身份证号,银行账号\n";
        $filename = $shopname.'_员工信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);

        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 8) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }
}

?>