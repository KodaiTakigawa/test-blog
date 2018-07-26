<?php
// 設定項目
$api_key = "" ;	// APIキー
$api_secret = "" ;	// APIシークレット

// クレデンシャルを作成
$credential = base64_encode( $api_key . ":" . $api_secret ) ;

// リクエストURL
$request_url = "https://api.twitter.com/oauth2/token" ;

// リクエスト用のコンテキストを作成する
$context = array(
	"http" => array(
		"method" => "POST" , // リクエストメソッド
		"header" => array(			  // ヘッダー
			"Authorization: Basic " . $credential ,
			"Content-Type: application/x-www-form-urlencoded;charset=UTF-8" ,
		) ,
		"content" => http_build_query(	// ボディ
			array(
				"grant_type" => "client_credentials" ,
			)
		) ,
	) ,
) ;

// cURLを使ってリクエスト
$curl = curl_init() ;
curl_setopt( $curl, CURLOPT_URL , $request_url ) ;	// リクエストURL
curl_setopt( $curl, CURLOPT_HEADER, true ) ;	// ヘッダーを取得する 
curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context["http"]["method"] ) ;	// メソッド
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
curl_setopt( $curl, CURLOPT_HTTPHEADER , $context["http"]["header"] ) ;	// ヘッダー
curl_setopt( $curl, CURLOPT_POSTFIELDS , $context["http"]["content"] ) ;	// リクエストボディ
curl_setopt( $curl, CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
$res1 = curl_exec( $curl ) ;
$res2 = curl_getinfo( $curl ) ;
curl_close( $curl ) ;

// 取得したデータ
$response = substr( $res1, $res2["header_size"] ) ;	// 取得したデータ(JSONなど)
$header = substr( $res1, 0, $res2["header_size"] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

// [cURL]ではなく、[file_get_contents()]を使うには下記の通りです…
// $response = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

// JSONを配列に変換する
$arr = json_decode( $response, true ) ;

// 配列の内容を出力する (本番では不要)
echo '<p>下記の認証情報を取得しました。(<a href="' . explode( "?", $_SERVER["REQUEST_URI"] )[0] . '">もう1回やってみる</a>)</p>' ;

foreach ( $arr as $key => $value ) {
	echo "<b>" . $key . "</b>: " . $value . "<BR>" ;
}



// 設定
$bearer_token = $arr['access_token'] ;	// ベアラートークン ;	// リクエストURL
$request_url = 'https://api.twitter.com/1.1/search/tweets.json?q=from%3Akodai_t7';

// // パラメータ
// $params = array(
// 	'screen_name' => '@arayutw' ,
// 	'count' => 10 ,
// ) ;

// // パラメータがある場合
// if ( $params ) {
// 	$request_url .= '?' . http_build_query( $params ) ;
// }

// リクエスト用のコンテキスト
$context = array(
	'http' => array(
		'method' => 'GET' , // リクエストメソッド
		'header' => array(			  // ヘッダー
			'Authorization: Bearer ' . $bearer_token ,
		) ,
	) ,
) ;

// cURLを使ってリクエスト
$curl = curl_init() ;
curl_setopt( $curl, CURLOPT_URL, $request_url ) ;	// リクエストURL
curl_setopt( $curl, CURLOPT_HEADER, true ) ;	// ヘッダーを取得する
curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $context['http']['method'] ) ;	// メソッド
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;	// 証明書の検証を行わない
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;	// curl_execの結果を文字列で返す
curl_setopt( $curl, CURLOPT_HTTPHEADER, $context['http']['header'] ) ;	// ヘッダー
curl_setopt( $curl, CURLOPT_TIMEOUT, 5 ) ;	// タイムアウトの秒数
$res1 = curl_exec( $curl ) ;
$res2 = curl_getinfo( $curl ) ;
curl_close( $curl ) ;

// 取得したデータ
$json = substr( $res1, $res2['header_size'] ) ;	// 取得したデータ(JSONなど)
$header = substr( $res1, 0, $res2['header_size'] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

// [cURL]ではなく、[file_get_contents()]を使うには下記の通りです…
// $json = @file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

// JSONを変換
$obj = json_decode( $json ) ;	// オブジェクトに変換
// $arr = json_decode( $json, true ) ;	// 配列に変換

// HTML用
$html = '' ;

// 検証用にレスポンスヘッダーを出力 [本番環境では不要]
$html .= '<h2>取得したデータ</h2>' ;
$html .= '<p>下記のデータを取得できました。</p>' ;
$html .= 	'<h3>ボディ(JSON)</h3>' ;
$html .= 	'<p><textarea rows="8">' . $json . '</textarea></p>' ;
$html .= 	'<h3>レスポンスヘッダー</h3>' ;
$html .= 	'<p><textarea rows="8">' . $header . '</textarea></p>' ;
$html .= '<p>' . $obj->statuses[0]->text . '</p>';

// HTMLを出力
echo $html ;



?>