<?php
/**
 *本文件是一个简单的php代理脚本
 *文件设置了域名限制，仅代理api.wordpress.org、downloads.wordpress.org，防止被滥用
 *将本代理脚本上传至海外服务器后，在WordPress主题functions中添加如下代理，即可实现代理下载
 *
 * add_filter('pre_http_request', function ($pre, $parsed_args, $url) {
 * 	$host = parse_url($url, PHP_URL_HOST);
 * 	if (!in_array($host, ['api.wordpress.org', 'downloads.wordpress.org'])) {
 * 		return $pre;
 * 	}
 *
 * 	$proxy_url = '本文件在海外服务器的Url';
 * 	if (!$proxy_url) {
 * 		return $pre;
 * 	}
 *
 * 	return wp_remote_request($proxy_url . '?url=' . urlencode($url), $parsed_args);
 * }, 10, 3);
 *
 */
if (empty($_GET['url'])) {
	exit('未指定URL');
}

$url  = $_GET['url'];
$urls = parse_url($url);
if (!in_array($urls['host'], ['api.wordpress.org', 'downloads.wordpress.org', 'wordpress.org'])) {
	exit('仅代理WordPress');
}

if (!empty($_POST)) {
	$query = http_build_query($_POST);

	$options['http'] = array(
		'timeout' => 20,
		'method'  => 'POST',
		'header'  => 'Content-type:application/x-www-form-urlencoded',
		'content' => $query,
	);

	$context = stream_context_create($options);
} else {
	$context = null;
}

$result           = file_get_contents($url, false, $context);
$response_headers = $http_response_header;
foreach ($response_headers as $response_header) {
	header($response_header);
}

echo $result;
