<?php
include_once("../includes/db.php");
// 允许上传的图片后缀
$allowedExts = array("gif", "jpeg", "jpg", "png");
$fname = explode(".", $_FILES["cover"]["name"]);
$extension = end($fname);     // 获取文件后缀名
if ((($_FILES["cover"]["type"] == "image/gif") || ($_FILES["cover"]["type"] == "image/jpeg") || ($_FILES["cover"]["type"] == "image/jpg") || ($_FILES["cover"]["type"] == "image/pjpeg")
|| ($_FILES["cover"]["type"] == "image/x-png")|| ($_FILES["cover"]["type"] == "image/png"))&& in_array($extension, $allowedExts)){
	if ($_FILES["cover"]["error"] > 0){
		echo "错误：: " . $_FILES["cover"]["error"] . "<br>";
	}else{
		// 判断当期目录下的 upload 目录是否存在该文件
		// 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
		if (file_exists("../upload/" . $_FILES["cover"]["name"])){
			unlink("../upload/".$_FILES["cover"]["name"]);
		}else{
			// 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
			move_uploaded_file($_FILES["cover"]["tmp_name"], "../upload/" . $_FILES["cover"]["name"]);
		}
	}
}else{
	echo "非法的封面文件格式";
	die();
}

// 允许上传的音频后缀
$allowedExts = array("mp3", "ogg", "wav");
$fname = explode(".", $_FILES["audio"]["name"]);
$extension = end($fname);     // 获取文件后缀名
if ((($_FILES["audio"]["type"] == "audio/ogg") || ($_FILES["audio"]["type"] == "audio/mpeg") || ($_FILES["audio"]["type"] == "audio/mp3") || ($_FILES["audio"]["type"] == "audio/x-wav"))&& in_array($extension, $allowedExts)){
	if ($_FILES["audio"]["error"] > 0){
		echo "错误：: " . $_FILES["audio"]["error"] . "<br>";
	}else{
		// 判断当期目录下的 upload 目录是否存在该文件
		// 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
		if (file_exists("../upload/" . $_FILES["audio"]["name"])){
			unlink("../upload/".$_FILES["audio"]["name"]);
		}else{
			// 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
			move_uploaded_file($_FILES["audio"]["tmp_name"], "../upload/" . $_FILES["audio"]["name"]);
		}
	}
}else{
	echo "非法的音频文件格式";
	die();
}

$pic_detail = getimagesize("../upload/".$_FILES["cover"]["name"]);
if(!($pic_detail[0] == 256 && $pic_detail[1] == 256)){
	print("封面分辨率不是256x256！请转换后再来");
	print_r($pic_detail);
	die();
}
switch($pic_detail['mime']){
	case "image/png": $pic = imagecreatefrompng("../upload/".$_FILES["cover"]["name"]);break;
	case "image/gif": $pic = imagecreatefromgif("../upload/".$_FILES["cover"]["name"]);break;
	case "image/jpeg": $pic = imagecreatefromjpeg("../upload/".$_FILES["cover"]["name"]);break;
	case "image/jpg": $pic = imagecreatefromjpeg("../upload/".$_FILES["cover"]["name"]);break;
	case "image/pjpeg": $pic = imagecreatefromjpeg("../upload/".$_FILES["cover"]["name"]);break;
	case "image/x-png": $pic = imagecreatefrompng("../upload/".$_FILES["cover"]["name"]);break;
	default: print("不支持的封面文件格式！");die();
}
imagepng($pic, "../upload/output.png");

/*生成title by ieb*/
$firstsize = 22;
$secondsize = 11;

$w = 460;
$y = 28;
$y2 = 50;

$font = "./MTL34e.ttf"; 
$first = $_POST['name'];
$second = $_POST['subtitle'];

$im = imagecreatetruecolor(460, 54);
//$im = imagecreatefrompng("t_cd_11_03.png");
imagesavealpha($im, true);
imagealphablending($im, true);
$back = imagecolorallocatealpha($im, 255, 255, 255, 127);
$black = imagecolorallocatealpha($im, 0,0,0, 0);
$white = imagecolorallocatealpha($im, 255, 255, 255, 0);
$lime = imagecolorallocate($im, 204, 255, 51);
imagefill($im, 0, 0, $back);



$lst = imagettfbbox($firstsize, 0, $font, $first);

$lst2 = imagettfbbox($secondsize, 0, $font, $second);

imagettftext($im, $firstsize , 0, ($w - ($lst[2] - $lst[0])) / 2, $y, $white, $font, $first);
imagettftext($im, $secondsize , 0, ($w - ($lst2[2] - $lst2[0])) / 2, $y2, $white, $font, $second);

imagepng($im, "../upload/title.png");
imagedestroy($im);

$pic = null;
unlink("../upload/".$_FILES["cover"]["name"]);
exec("ffmpeg -i ../upload/".$_FILES["audio"]["name"]." -ar 32000 -ab 192k ../upload/audio.mp3 2>&1", $output, $return_val);
exec("ffmpeg -i ../upload/".$_FILES["audio"]["name"]." -ar 32000 -ab 192k ../upload/audio.ogg 2>&1", $output, $return_val);
if(!file_exists("../upload/audio.mp3")){
	print("音频转换出错！");
	var_dump($output);
	die();
}
unlink("../upload/".$_FILES["audio"]["name"]);
unlink("../upload/output.png");

$proj_name = "custom_".$_POST['proj_name'];
if(!preg_match("/^[a-zA-Z0-9_]+$/", $_POST['proj_name'])){
	print("非法的项目名称！");
	die();
}
exec("mirainohana ../upload/tx.texb lives/".$proj_name."/cover:0:0:256:256 lives/".$_POST['proj_name']."/title:0:257:500:54 ../upload/tx.texb");
exec("itsudemo -r lives/".$_POST['proj_name']."/cover:cover.png ../upload/tx.texb");
exec("itsudemo -r lives/".$_POST['proj_name']."/title:title.png ../upload/tx.texb");
exec("itsudemo -g ../upload/tx.texb");
if(!file_exists("../upload/tx.texb")){
	print("texb打包出错！");
	die();
}

$notes = fopen("../upload/notes.json", "w");
fwrite($notes, $_POST['notes']);
fclose($notes);

$live_json = json_decode('{
	"name": "キラキラだとか夢だとか ～Sing Girls～ [by tofutofuto]",
	"attribute_icon_id": 2,
	"stage_level": 10,
	"notes_speed": 0.8,
	"live_icon_asset": "lives\/Kirakiradadoka_LLP656214\/cover.png",
	"title_asset": "lives\/Kirakiradadoka_LLP656214\/title.png",
	"notes_setting_asset": "lives\/Kirakiradadoka_LLP656214\/m_0656214.json",
	"sound_asset": "lives\/Kirakiradadoka_LLP656214\/music_m_0656214.mp3",
	"asset_background_id": 10,
	"dangerous": false,
	"is_random": false,
	"use_quad_point": false,
	"is_not_for_marathon_ranking": false,
	"live_clear_on_last_note": true,
	"pl_auto_calculate_combo_rank": true,
	"c_rank_score": 357482,
	"b_rank_score": 408551,
	"a_rank_score": 459620,
	"s_rank_score": 497921,
	"c_rank_combo": 0,
	"b_rank_combo": 0,
	"a_rank_combo": 0,
	"s_rank_combo": 0,
	"c_rank_complete": 15,
	"b_rank_complete": 30,
	"a_rank_complete": 60,
	"s_rank_complete": 120
}', true);//模板
$live_json['name'] = $_POST['name'];
$live_json['attribute_icon_id'] = (int)$_POST['attribute'];
$live_json['stage_level'] = (int)$_POST['level'];
$live_json['live_icon_asset'] = "lives/".$_POST['proj_name']."/cover.png";
$live_json['title_asset'] = "lives/".$_POST['proj_name']."/title.png";
$live_json['notes_setting_asset'] = "lives/".$_POST['proj_name']."/notes.json";
$live_json['sound_asset'] = "lives/".$_POST['proj_name']."/audio.mp3";
$live_json['asset_background_id'] = (int)$_POST['bg'];

include_once('../includes/live.php');
$total = calcScore(60500, json_decode($_POST['notes'], true));
$live_json['s_rank_score'] = floor($total*0.975);
$live_json['a_rank_score'] = floor($total*0.9);
$live_json['b_rank_score'] = floor($total*0.8);
$live_json['c_rank_score'] = floor($total*0.7);

$live = fopen("../upload/lives.json", "w");
fwrite($live, json_encode($live_json));
fclose($live);

$version = fopen("../upload/version.json", "w");
fwrite($version, '{"version": 1}');
fclose($version);

exec("honokamiku -e -j4 ../upload/notes.json");
exec("honokamiku -e -j4 ../upload/audio.mp3");
exec("honokamiku -e -j4 ../upload/audio.ogg");
exec("honokamiku -e -j4 ../upload/lives.json");
exec("honokamiku -e -j4 ../upload/version.json");
exec("honokamiku -e -j4 ../upload/tx.texb");
exec("honokamiku -e -j4 ../upload/title.png.imag");
exec("honokamiku -e -j4 ../upload/cover.png.imag");

mkdir("../upload/lives");
mkdir("../upload/lives/".$_POST['proj_name']);
rename("../upload/notes.json", "../upload/lives/".$_POST['proj_name']."/notes.json");
rename("../upload/lives.json", "../upload/lives/".$_POST['proj_name']."/lives.json");
rename("../upload/version.json", "../upload/lives/".$_POST['proj_name']."/version.json");
rename("../upload/tx.texb", "../upload/lives/".$_POST['proj_name']."/tx.texb");
rename("../upload/title.png.imag", "../upload/lives/".$_POST['proj_name']."/title.png.imag");
rename("../upload/cover.png.imag", "../upload/lives/".$_POST['proj_name']."/cover.png.imag");

exec("7z a ../upload/".$_POST['proj_name']."_all.zip ./../upload/lives");
unlink("../upload/lives/".$_POST['proj_name']."/notes.json");
unlink("../upload/lives/".$_POST['proj_name']."/lives.json");
unlink("../upload/lives/".$_POST['proj_name']."/version.json");
unlink("../upload/lives/".$_POST['proj_name']."/tx.texb");
unlink("../upload/lives/".$_POST['proj_name']."/title.png.imag");
unlink("../upload/lives/".$_POST['proj_name']."/cover.png.imag");

rename("../upload/audio.ogg", "../upload/lives/".$_POST['proj_name']."/audio.ogg");
exec("7z a ../upload/".$_POST['proj_name']."_an.zip ./../upload/lives");
unlink("../upload/lives/".$_POST['proj_name']."/audio.ogg");

rename("../upload/audio.mp3", "../upload/lives/".$_POST['proj_name']."/audio.mp3");
exec("7z a ../upload/".$_POST['proj_name']."_ios.zip ./../upload/lives");
unlink("../upload/lives/".$_POST['proj_name']."/audio.mp3");

$dl = json_decode('{
    "need_dl":true,
    "show_cover_version": 1,
    "cover_before_dl": "pl_assets/custom_live_default_cover.png",
    "bg_before_dl": "assets/image/background/b_liveback_012.png",
    "version": ["lives/imp_LLP105557/version.json","version",1],
    "files": [],
    "add_local_live": "lives/imp_LLP105557\/live.json"
}', true);
$dl['bg_before_dl'] = "assets/image/background/b_liveback_0".$_POST['bg'].".png";
$dl['version'] = ["lives/".$_POST['proj_name']."/version.json","version",1];
$dl['files'][] = ["url"=>"http://plserver.xyz/../upload/".$_POST['proj_name']."_an.zip", "size"=>filesize("../upload/".$_POST['proj_name']."_an.zip"), "os"=>"android"];
$dl['files'][] = ["url"=>"http://plserver.xyz/../upload/".$_POST['proj_name']."_ios.zip", "size"=>filesize("../upload/".$_POST['proj_name']."_ios.zip"), "os"=>"ios"];
$dl['files'][] = ["url"=>"http://plserver.xyz/../upload/".$_POST['proj_name']."_all.zip", "size"=>filesize("../upload/".$_POST['proj_name']."_all.zip"), "os"=>"all"];
$dl['add_local_live'] = "lives/".$_POST['proj_name']."/live.json";

$mysql->query("INSERT INTO programmed_live (dl, live_json, notes_setting_asset) VALUES(?, ?, ?)",[json_encode($dl), json_encode($live_json), $proj_name]);
$mysql->query("INSERT INTO notes_setting VALUES(?, ?)",[$proj_name, $_POST['notes']]);
$mysql->query("commit");

//清理
rmdir("../upload/lives/".$_POST['proj_name']);
rmdir("../upload/lives");
unlink("../upload/title.png");

print("<h1>提交完成！</h1>");
?>