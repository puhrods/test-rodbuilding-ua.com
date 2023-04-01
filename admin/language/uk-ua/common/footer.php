<?php
// *	@copyright		Pavel Kravchenko; https://opencartforum.com/profile/711752-paulkravchenko/

if (file_exists(DIR_LANGUAGE.'ru-ru/common/footer.php'))
include(DIR_LANGUAGE.'ru-ru/common/footer.php');
elseif (file_exists(DIR_LANGUAGE.'en-gb/common/footer.php'))
include(DIR_LANGUAGE.'en-gb/common/footer.php');
else
// Text
$_['text_footer']  = '<a href="http://www.opencart.com">OpenCart</a> &copy; 2009-' . date('Y') . ' Всі права захищені.';
$_['text_version'] = 'Версія %s';
