<?php

require_once '/var/www/html/SPCS/app/init.php';
require APP_ROOT_DIR . '/dao/DbConnection.php';
require APP_ROOT_DIR . '/dao/CommonDao.php';
require APP_ROOT_DIR . '/helper/CrawlerHelper.php';
require APP_ROOT_DIR . '/helper/ViewHelper.php';

$helper = new CrawlerHelper();

//検索条件の取得
$searchFreeWord = (isset($_POST["search_word"])) ? $_POST["search_word"] : "";
$searchSiteId = (isset($_POST["search_sel_site"])) ? $_POST["search_sel_site"] : '';

$conn = new DbConnection();
$dbh = $conn->getConnection();

$mSiteDao = new CommonDao($dbh, 'M_SITE');
$mSiteList = $mSiteDao->getAllData();

//プルダウンリスト用HTML生成
$selectSiteHtml = "<select name='search_sel_site' id ='search_sel_site'>\n<option id='0'></option>\n";
$selectSiteHtml .= ViewHelper::getSelectHtml('ID', 'SITE_NM', $mSiteList, $searchSiteId);

$word = $searchFreeWord;
$resultHtml = "<table border='1'><tr><th>サイト</th><th>件数</th></tr>";
for($i = 0; $i < count($mSiteList); $i++){
	$site = $mSiteList[$i];
	if($searchFreeWord != '' && ($searchSiteId == '' || $searchSiteId == $site['ID']) ) {
	//if($site['SITE_NM'] == 'type'){
	//echo var_dump($site) . "<br>";
	//echo $site['SITE_NM'] . "<br>";
		$fileData = $helper::getSiteinfo($site['SITE_NM'], $site['METHOD'], $site['URL'], $word, $site['POST_WORD'], '' );

		//echo $fileData;
        $fp = fopen( '/var/www/html/SPCS/type_' . date("Ymd") . '.html', "w");
	    //fwrite($fp, mb_convert_encoding($fileData, "UTF-8"));
	    fwrite($fp, $fileData );
	    fclose($fp);

		$cnt = $helper::getCountByMatch($fileData, $site['CNT_REPLACE_STRING']);
		$resultHtml .= "<tr><td>" . $site['SITE_NM'] . "</td><td>" . $cnt . "</td></tr>";
	//}
	}
}
$resultHtml .= "</table>";

?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>word count list</title>
</head>
<body>
<form action="" method="post">
<a href="../?p=CountList&f=1">日別件数リスト</a> <a href="../?p=CountList&f=2">月別件数リスト</a><br>
    サイト：<?php echo $selectSiteHtml ?>
    キーワード：<input type="text" name="search_word" id="search_word" value="<?php echo $searchFreeWord ?>">
    <input type="button" value="クリア" onclick="document.getElementById('search_word').value='';document.getElementById('search_sel_word').value='';">
    <input type="submit" value="絞り込み"><br>
</form>
<?php

echo $resultHtml;
?>
</body>
</html>
