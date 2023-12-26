<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

$_['module_name']           = 'Live Price <a href="http://feofan.club/" target="_blank" title="feofan.club" style="color:#ff7361"><i class="fa fa-download"></i></a> <a href="https://t.me/feofanchat/" target="_blank" title="HELP" style="color:#233746"><i class="fa fa-info-circle"></i></a>';
$_['module_page'] 			= '<a href="feofan.club" target="_blank">'.$_['module_name'].' feofan.club</a>';


$_lang_file = __DIR__.'/liveprice_common.php';
if ( file_exists($_lang_file) ) {
	require($_lang_file);
}