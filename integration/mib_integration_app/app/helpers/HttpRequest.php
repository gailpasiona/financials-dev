<?php
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

class HttpRequest{
	public static function post($url,$data){
		$client = new GuzzleClient($url['server']);
		$cookieJar = new ArrayCookieJar();
		// Create a new cookie plugin
		$cookiePlugin = new CookiePlugin($cookieJar);
		// Add the cookie plugin to the client
		$client->addSubscriber($cookiePlugin);
		
		$reply = $client->post($url['uri'], array(), $data)->send();
		
		return $reply->getBody();
	}
}