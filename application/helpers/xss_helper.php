<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function sec_xss($x){
	echo htmlentities($x, ENT_QUOTES, 'UTF-8');
}