# wp-proxy
旨在解决中国内地服务器无法连接WordPress.org官网，导致无法安装或更新WordPress的问题

## 使用条件
您必须拥有一台自己的海外服务器，这台服务器能顺利连接WordPress官网

## 使用方法
将wp-proxy.php脚本上传至您的海外服务器后，在WordPress主题functions中添加如下代理，即可实现代理下载
```php
/**
 *@author wndwp.com
 *
 *自建代理，解决国内服务器无法更新下载WordPress的问题
 *注意替换代理地址为脚本文件在海外服务器上实际的网址
 */
add_filter('pre_http_request', function ($pre, $parsed_args, $url) {
	$host = parse_url($url, PHP_URL_HOST);
	if (!in_array($host, ['api.wordpress.org', 'downloads.wordpress.org'])) {
		return $pre;
	}

	// 注意替换为脚本文件实际的网址
	$proxy_url = 'https://www.example.com/wp-proxy.php';
	if (!$proxy_url) {
		return $pre;
	}

	return wp_remote_request($proxy_url . '?url=' . urlencode($url), $parsed_args);
}, 10, 3);
```