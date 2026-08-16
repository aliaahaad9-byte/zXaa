<?php 

function UserPosts(){
	if(isset($_COOKIE['userPosts'])){
		$user_posts = $_COOKIE['userPosts'];
		$user_posts =str_replace("\\",'', $user_posts);
		$user_posts = (array)json_decode($user_posts);
		asort($user_posts); 
		$user_posts = array_slice($user_posts,-5,5,true);
		$user_posts = array_keys($user_posts);
		return $user_posts ;
	}
}