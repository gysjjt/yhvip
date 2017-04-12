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
	$login = 'ncbln';//urlencode($_POST['login']);
	$passwd = 'bln0807'; //$_POST['passwd'];
	$rand = '123'; //$_POST['rand'];
	$params = "username=$login&password=$passwd&disksn=&rememberme=false&service=http%3A%2F%2Fqht.cloudvast.com%2Findex.do%3Bjsessionid%3DEA527163685A68DEC0B0E83BD3783CC5-n1.q2";
	$curl -> url = "http://login.cloudvast.com/login";
	$curl -> params = $params;
	$result = $curl -> login();
	if($result == '0' || $result == 0){
		echo 1;
	}else {
		echo "账号密码或者验证码错误";
	}
}else if($_GET['action'] == 'curlmember'){
	$shopname = isset($_REQUEST['shopname'])?$_REQUEST['shopname']:'会员';
	$data = array();

    //获取总数
	$curl -> url = "http://qht.cloudvast.com/member/getMember.do?start=0&limit=50&field=cardNumber&total=7406";
	$rs = $curl -> getMembersPage();
	$rs = json_decode($rs,true);
    $totals = isset($rs['total'])?$rs['total']:10;

    //总页数
    $pages = ceil($totals/50);
	for($i=0; $i<=$pages; $i++){
		$start = $i*50;
		$curl -> url = "http://qht.cloudvast.com/member/getMember.do?start=$start&limit=50&field=cardNumber&total=";
		$pagesData = $curl -> getMembersPage();
		$pagesData = json_decode($pagesData,true);
		foreach($pagesData['list'] as $v){
			$data[] = $v;
		}
	};
    if($data == '') {
        header('Location: index.php');
    }

	$curl -> downMembersCvs($data, $shopname);
}else if($_GET['action'] == 'curlpackage'){
    $shopname = isset($_REQUEST['shopname'])?$_REQUEST['shopname']:'会员';
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