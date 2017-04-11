<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
set_time_limit(0);
header("Content-Type: text/html;charset=utf-8");
include_once("curlapi.class.php");
$curl = new curlapi();
if($_GET['action'] == "code"){//获取验证码
	$curl -> url = "http://vip.minicon.net/validatecode.aspx";
	echo $curl -> get_code();
}else if($_GET['action'] == "login"){
	$login = urlencode($_POST['login']);
	$passwd = $_POST['passwd'];
	$rand = $_POST['rand'];
	//$params = "a=LoginIn&u=$login&p=$passwd&c=$rand&ts=0.6658900876500904&hi=";
	$curl -> url = "http://vip.minicon.net/ajaxapp/commonajaxquery.ashx?a=LoginIn&u=$login&p=$passwd&ts=0.6658900876500904&hi=";
	$curl -> params = '';
	$result = $curl -> login();
	if($result == '0' || $result == 0){
		echo 1;
	}else {
		echo "账号密码或者验证码错误";
	}
}else if($_GET['action'] == 'curlmember'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

    //获取总数
    $curl -> url = "http://vip.minicon.net/iframepage/apppage/member_list.aspx";
    $rs = $curl -> curl();
    preg_match('/共(.*)条记录/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;
	$totals = preg_replace("/\s\n\t/","",$totals);
	$totals = str_replace('&nbsp;','',$totals);
    //总页数
    $pages = ceil($totals/50);

	for($i=1; $i<=$pages; $i++){
		$params = "p=$i&birthBegin=&birthEnd=&czCountE=&czCountS=&czE=&czS=&gender=-1&invalidDate=0&jfE=&jfS=&keyword=&kkBegin=&kkEnd=&lxfBegin=&lxfEnd=&mctype=0&mtype=0&notxfDate=0&ostate=0&othkw=&qkE=&qkS=&sortPreField=&sortd=&sortf=&xfBegin=&xfCountE=&xfCountS=&xfE=&xfEnd=&xfS=&xfitem=0&yueE=&yueS=&zjE=&zjS=";
		$curl -> params = $params;
		$curl -> url = "http://vip.minicon.net/iframepage/apppage/member_list.aspx";
		$pagesData = $curl -> getMembersPage();

		$data .= $curl ->getMembersInfo($pagesData, $i);
	};

    if($data == '') {
        header('Location: index.php');
    }

	$curl -> downMembersCvs($data, $shopname);
}else if($_GET['action'] == 'curlpackage'){
    $shopname = $_REQUEST['shopname'];
    $data = '';

    //获取总数
    $curl -> url = "http://vip8.sentree.com.cn/shair/timesItem!initTreat.action?set=cash";
    $rs = $curl -> curl();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;

	//总页数
    $pages = ceil($totals/100);
    for($i=1; $i<=$pages; $i++){
        $params = "page.currNum=$i&page.rpp=100&set=cash&r=0.3421386775783387";
        $curl -> params = $params;
        $curl -> url = "http://vip8.sentree.com.cn/shair/timesItem!initTreat.action";
        $pagesData = $curl -> getPackagePage();
        $data .= $curl ->getPackageInfo($pagesData, $i);
    };
    if($data == '') {
        header('Location: index.php');
    }
    $curl -> downPackageCvs($data, $shopname);
}else if($_GET['action'] == 'curlstaff'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

	//获取员工数据
	$curl -> url = "http://vip8.sentree.com.cn/shair/employee!employeeInfo.action?set=manage&r=0.5704847458180489";
	$rs = $curl -> curl();

	$rsBlank = preg_replace("/\s\n\t/","",$rs);
	//$rsBlank = str_replace(' ', '', $rsBlank);
	preg_match_all("/table_fixed_head.*>(.*)<\/form>/isU", $rsBlank ,$tables);

    if(count($tables[0]) == 0) {
        header('Location: index.php');
    }
	$curl -> downStaffCvs($tables[1][0], $shopname);
}
?>