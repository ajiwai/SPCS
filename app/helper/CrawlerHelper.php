<?php

class CrawlerHelper {

    static function getSiteinfo($siteName, $method, $url, $word, $keyword, $spaceChar){

        //半角スペースを指定文字に置き換え
        if( $spaceChar != "" ) {
            $word = str_replace(" ", $spaceChar ,$word);
        }
        //サイト情報（HTML）取得
        switch ($method) {
        case "POST":
            switch ($siteName) {
            case "mynavi":
                $data = array(
                     'sr_free_search_cd' => 4
                    ,'sr_free_search_keyword' => urlencode($word)
                    ,'jobsearch_flg' => 16
                    ,'search_flg' => 20
                    ,'press_flg' => 1
                    ,'free_search_log' => 1
                    ,'search_condition_login_check_flg' => true
                    ,'v_scrl' => 0
                );
                break;
            default:
                $data = array(
                    $keyword => urlencode($word)
                );
                break;
            }
            $fileData = self::postFileGetContents($url, $data);
            //$fileData = post_file_cget_contents($url, $data);
            break;
        default:
    //        $search_url = $url.urlencode($word);
            $searchUrl = str_replace("[word]", urlencode($word), $url);
            //$fileData = file_get_contents($search_url);
            $fileData = self::fileCgetContents($searchUrl);
            break;
        }
        //fwrite($fp, mb_convert_encoding($fileData, "UTF-8"));

        return $fileData;
    }

    static function fileCgetContents($url){
        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $result = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ
     
        return $result;
    }

    static function postFileCgetContents($url, $data) {

        // $post_data = array(
        //     'foo' => 'bar'
        // );
        // header
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
    //        "Content-Length: ".strlen($data)
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); // post
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // jsonデータを送信
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); // リクエストにヘッダーを含める
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);    

        $response = curl_exec($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE); 
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $result = json_decode($body, true); 

        curl_close($curl);

        return $result;

    }

    function postFileGetContents($url, $data) {
        // POSTデータ
        //$data = array(
        //    "param1" => "1",
        //    "param2" => 2
        //);
        $data = http_build_query($data, "", "&");

        // header
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: ".strlen($data)
        );

        $context = array(
            "http" => array(
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            )
        );

        $post_data = array(
            "http" => array(
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            )
        );

        $file_data = file_get_contents($url, false, stream_context_create($context));
        //$file_data = file_cget_contents($url, $data);

        return $file_data;
    }

    static function getCountByMatch($fileData, $match){

        mb_regex_encoding("UTF-8");
        //件数取得
        preg_match_all("/".$match."/", $fileData, $result);
    //        $result = preg_match("/".$word."/is", $fileData, $result);
        //echo var_dump($result);
        if (isset($result[0][0])) {
            $cnt = mb_ereg_replace('[^0-9]', '', $result[0][0]);
        } else {
            if (trim($fileData) == "") {
                $cnt = -1;
            }else{
                $cnt = 0;
            }
        }
        return $cnt;
    }
}
