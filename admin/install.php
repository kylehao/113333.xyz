<?php
// ========================== �ļ�˵�� ==========================// 
// ���ļ�˵���������Զ���װ�ű�
// -------------------------------------------------------------// 
// С��������
// =============================================================// 
error_reporting(7);

// ���������� register_globals = off �Ļ����¹���
if ( function_exists('ini_get') ) {
	$onoff = ini_get('register_globals');
} else {
	$onoff = get_cfg_var('register_globals');
}
if ($onoff != 1) {
	@extract($_POST, EXTR_SKIP);
	@extract($_GET, EXTR_SKIP);
}

// ȥ��ת���ַ�
function stripslashes_array($array) {
	while (list($k,$v) = each($array)) {
		if (is_string($v)) {
			$array[$k] = stripslashes($v);
		} else if (is_array($v))  {
			$array[$k] = stripslashes_array($v);
		}
	}
	return $array;
}

// �ж� magic_quotes_gpc ״̬
if (get_magic_quotes_gpc()) {
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}

set_magic_quotes_runtime(0);

// ������һҳ����
function sa_exit($msg) {
	global $step;
	echo "<p>$msg</p>";
	echo "<p><a href=\"javascript:history.go(-1);\">������һҳ</a></p>";
	cpfooter();
	exit;
}

function cpheader(){
?>
<HTML>
<HEAD>
<TITLE>Sarticle��װ�ű�</TITLE>
<META http-equiv=Content-Type content="text/html; charset=gb2312">
<STYLE type=text/css>
BODY {BACKGROUND-COLOR: #EEEEEE; COLOR: #3F3849; line-height: 18px;	font-family: "Verdana", "Tahoma", "����";	FONT-SIZE: 12px;}
TD{FONT-FAMILY: "Verdana", "Tahoma", "����"; FONT-SIZE: 12px; line-height: 18px;}
A:active {COLOR: #000000 ;}
A:visited {COLOR: #000000 ;text-decoration: none}
A:hover {COLOR: #3D4F7A;text-decoration: underline}
A:link {COLOR: #000000;text-decoration: none}
</STYLE>
</HEAD>
<body leftmargin="0" topmargin="20" marginwidth="0" marginheight="0" style="table-layout:fixed; word-break:break-all">
<TABLE width=760 border=0 align=center cellPadding=5 cellSpacing=5>
  <TBODY>
    <TR> 
      <TD width="100%"><div align="center"><b>Sarticle��װ�ű�</b></div>
        <hr noshade></TD>
    </TR>
    <TR>
      <TD>
<?
}

function cpfooter(){
?>
</TD>
  </TR>
    <TR>
      <TD align=right>
      <hr align="center" noshade>
      <center>
        <b>Copyright &copy; 2004 Security Angel Team[S4T] All Rights Reserved.</b>
      </center></TD>
    </TR>
  </TBODY>
</TABLE>
</BODY></HTML>
<?php
}

cpheader();

if (empty($step)) {
	$step = "1";
    require "config.php";
	echo "<p><b>�������ݿ�</b></p>\n";
	echo "<p><form action=\"install.php\"  method=\"post\"></p>\n";
	echo "<p><input type=\"hidden\" name=\"step\" value=\"".($step+1)."\"></p>\n";
    echo "<p>��������ַ: <input type=\"text\" value=\"$servername\" name=\"servername\"></p>\n";
    echo "<p>���ݿ���: <input type=\"text\" value=\"$dbname\" name=\"dbname\"></p>\n";
    echo "<p>���ݿ��û���: <input type=\"text\" value=\"$dbusername\" name=\"dbusername\"></p>\n";
    echo "<p>���ݿ��û�����: <input type=\"password\" value=\"\" name=\"dbpassword\"></p>\n";
    echo "<p>���ݱ�ǰ׺: <input type=\"text\" value=\"$db_prefix\" name=\"db_prefix\"></p>\n";
	echo "<p>ɾ����־���ܳ�: <input type=\"text\" value=\"\" name=\"dellog_pass\"></p>\n";
	echo "<p>Ҫɾ����̨�Ĺ�����¼�Լ���½��¼������������ܳ�.</p>";
    echo "<p><input type=\"submit\" name=\"next\" value=\"��һ��\"></p>\n";
}

// step three
if($step==2){

   if(trim($dbname)=="" or trim($servername)=="" or trim($dbusername)=="" or trim($dellog_pass=="")){
      sa_exit("�뷵�ز�ȷ������ѡ�������ȷ��д");
   }
   $file = "./config.php";

   if (file_exists($file)){
      @chmod ($file, 0777);
   }

   $fp = fopen($file,w);
   $filecontent = "<?php

/********** ���ݿ���������IP **********/
\$servername = '$servername';

/********** ���ݿ��û��� **********/
\$dbusername = '$dbusername';

/********** ���ݿ����� **********/
\$dbpassword = '$dbpassword';

/********** ���ݿ����ӷ�ʽ **********/
\$usepconnect = '1';

/********** ���ݿ��� **********/
\$dbname = '$dbname';

/********** ���ݱ�ǰ׺ **********/
\$db_prefix = '$db_prefix';

/********** ɾ����־���ܳ� **********/
\$dellog_pass = '$dellog_pass';

?>";
   fwrite($fp,$filecontent,strlen($filecontent));
   fclose($fp);


   $link = mysql_connect($servername,$dbusername,$dbpassword);
   if ($link) {
       echo "<p>���ݿ���������ӳɹ�</p>";
       if (mysql_select_db($dbname)) {
           echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=1\">��һ��(ɾ���Ѵ��ڵı�)</a></p>";
           echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=0\">��һ��(��ɾ���Ѵ��ڵı�)</a></p>";
       } else {
           echo "<p>�����Դ������ݿ� $dbname</p>";

           if (mysql_create_db($dbname)) {
               echo "<p>���ݿⴴ���ɹ�</p>";
               echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=1\">��һ��(ɾ���Ѵ��ڵı�)</a></p>";
               echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=0\">��һ��(��ɾ���Ѵ��ڵı�)</a></p>";
           } else {
               echo "<p>���ݿⴴ��ʧ��</p>";
               echo "<p><a href=\"./install.php?step=".($step-1)."\">������һ��</a></p>";
           }
       }

   } else {
       echo "<p>���ݿ����������ʧ��</p>";
   }
   mysql_close($link);
}




if($step>=3){

   require "config.php";
   require "class/mysql.php";

   $DB = new DB_MySQL;

   $DB->servername=$servername;
   $DB->dbname=$dbname;
   $DB->dbusername=$dbusername;
   $DB->dbpassword=$dbpassword;

   $DB->connect();
   $DB->selectdb();

}

if($step==3){

   $mysql_data = "

DROP TABLE IF EXISTS `".$db_prefix."adminlog`;
CREATE TABLE `".$db_prefix."adminlog` (
  `adminlogid` int(15) NOT NULL auto_increment,
  `action` varchar(50) NOT NULL default '',
  `script` varchar(255) NOT NULL default '',
  `date` varchar(10) NOT NULL default '',
  `ipaddress` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`adminlogid`)
) ;


DROP TABLE IF EXISTS `".$db_prefix."article`;
CREATE TABLE `".$db_prefix."article` (
  `articleid` int(15) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `sortid` int(11) NOT NULL default '0',
  `title` varchar(120) NOT NULL default '',
  `author` varchar(20) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `source` varchar(100) NOT NULL default '',
  `addtime` varchar(10) NOT NULL default '',
  `content` text NOT NULL,
  `comment` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `iscommend` int(11) NOT NULL default '0',
  `isparseurl` int(11) NOT NULL default '1',
  `ishtml` int(11) NOT NULL default '0',
  `visible` int(11) NOT NULL default '1',
  PRIMARY KEY  (`articleid`)
) ;

DROP TABLE IF EXISTS `".$db_prefix."loginlog`;
CREATE TABLE `".$db_prefix."loginlog` (
  `loginlogid` int(15) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `date` varchar(10) NOT NULL default '',
  `ipaddress` varchar(16) NOT NULL default '',
  `result` int(11) NOT NULL default '0',
  PRIMARY KEY  (`loginlogid`)
) ;

DROP TABLE IF EXISTS `".$db_prefix."setting`;
CREATE TABLE `".$db_prefix."setting` (
  `settingid` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `value` mediumtext NOT NULL,
  `type` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`settingid`)
) ;

INSERT INTO `".$db_prefix."setting` VALUES (1, '����ϵͳ����', '��������ҳ���������ʾ������', 'title', '��������', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (2, '����ϵͳ��ַ', '����ϵͳ�������ϵĵ�ַ', 'url', 'http://www.ssfun.com', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (3, 'ǰ̨ģ��', '', 'template', 'default', 'templates');
INSERT INTO `".$db_prefix."setting` VALUES (4, 'ÿҳ��ʾ����������', '', 'articlenum', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (5, '���·ֶ�������ʾ', '', 'colnum', '2', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (6, 'ÿҳ��ʾ����������', '', 'commentnum', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (7, '����������ʾ���ٸ����', '', 'searchnum', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (8, '�Ƿ񿪷����۹���', '', 'iscomment', '1', 'yesno');
INSERT INTO `".$db_prefix."setting` VALUES (9, '�ύ����ʱ����', '���Է�ֹ���˹�ˮ,��λ��,0Ϊ������', 'post_time', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (10, 'HTMLҳ���Ŀ¼', '', 'htmldir', 'html', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (11, '�������Ŀ¼', '', 'attachdir', 'attachments', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (12, '��������󸽼���С', '�ϴ��ĸ���������ֽ�������Ϊ0���������ơ�<br>1 KB = 1024 �ֽ� 1 MB = 1048576 �ֽ�', 'maxattachsize', '1048576', 'integer');

    

DROP TABLE IF EXISTS `".$db_prefix."sort`;
CREATE TABLE `".$db_prefix."sort` (
  `sortid` int(15) NOT NULL auto_increment,
  `parentid` int(15) NOT NULL default '0',
  `sortname` varchar(20) NOT NULL default '',
  `sortdir` varchar(20) NOT NULL default '',
  `count` int(15) NOT NULL default '0',
  `displayorder` int(15) NOT NULL default '0',
  PRIMARY KEY  (`sortid`)
) ;



DROP TABLE IF EXISTS `".$db_prefix."user`;
CREATE TABLE `".$db_prefix."user` (
  `userid` int(15) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`userid`)
) ;

";
	
	echo "<p><b>���ڽ������ݱ�......</b></p>";
	echo "<p><form action=\"install.php\"  method=\"post\"></p>";
	echo "<p><input type=\"hidden\" name=\"step\" value=\"".($step+1)."\"></p>";
    $a_query = explode(";",$mysql_data);
    while (list(,$query) = each($a_query)) {
           $query = trim($query);
           if ($query) {
               if (strstr($query,'CREATE TABLE')) {
                   ereg('CREATE TABLE ([^ ]*)',$query,$regs);
                   if ($delete_existing) {
                       $DB->query("DROP TABLE IF EXISTS $regs[1]");
                   }
				   echo "<p>���ڽ�����: ".$regs[1]." ���� ";
				   $DB->query($query);
					if ($query)
					{
						echo "�ɹ�</p>\n";
					} else {
						echo "ʧ��</p>\n";
					}
               } else {
                   $DB->query($query);
               }

           }
    }
	echo "<p><input type=\"submit\" name=\"next\" value=\"��һ��\"></p>";
}

if ($step==4) {

	echo "<p><b>���ӹ���Ա�ʺ�</b></p>";
	echo "<p><form action=\"install.php\"  method=\"post\"></p>";
	echo "<p><input type=\"hidden\" name=\"step\" value=\"".($step+1)."\"></p>";
    echo "<p>�û���: <input type=\"text\" value=\"\" name=\"username\"></p>";
    echo "<p>�û�����: <input type=\"password\" value=\"\" name=\"password\"></p>";
    echo "<p>ȷ���û�����: <input type=\"password\" value=\"\" name=\"password2\"></p>";
    echo "<p><input type=\"submit\" name=\"next\" value=\"��һ��\"></p>";
}

if ($step==5) {

    if (trim($username)=="" OR trim($password)=="") {
        sa_exit("�뷵�ز���������ѡ��");
    }
	if(strlen($_POST['password']) < 6)
	{
		sa_exit("���볤�Ȳ���С��6λ","javascript:history.go(-1);");
	}
    if ($password!=$password2) {
        sa_exit("������������벻��ͬ,�뷵����������");
    }
    $DB->query("INSERT INTO ".$db_prefix."user (username,password)
                       VALUES ('".htmlspecialchars(trim($_POST[username]))."','".md5(trim($_POST[password]))."')");


    echo "<p>��װ���,��ɾ����װ�ļ� install.php,���ⱻ���˶�������.</p>";
    echo "<p>��л��ʹ�ñ�����.</p>";
    echo "<p><a href=\"index.php\">��½�������</a></p>";

}
cpfooter();
?>