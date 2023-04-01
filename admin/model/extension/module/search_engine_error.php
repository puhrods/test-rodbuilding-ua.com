<?php

/**
 * @author  sv2109
 * @link    sv2109@gmail.com
*/

function ioncube_event_handler($err_code, $params) {

  $module_name = "Search Engine";

  echo "Ошибка модуля " . $module_name . ". Обратитесь к разработчику sv2109@gmail.com <br />";
  echo "Fatal error in " . $module_name . " module. Please, contact a developer sv2109@gmail.com <br /><br />";

  switch ($err_code) {
    case ION_CORRUPT_FILE:
      echo "Файл поврежден<br />ION_CORRUPT_FILE<br />";
      break;
    case ION_EXPIRED_FILE:
      echo "Файл прострочен<br />ION_EXPIRED_FILE<br />";
      break;
    case ION_NO_PERMISSIONS:
      echo "Ошибка доступа<br />ION_NO_PERMISSIONS<br />";
      break;
    case ION_CLOCK_SKEW:
      echo "<br />ION_CLOCK_SKEW<br />";
      break;
    case ION_LICENSE_NOT_FOUND:
      echo "Лицензия не найдена<br />ION_LICENSE_NOT_FOUND<br />";
      break;
    case ION_LICENSE_CORRUPT:
      echo "Лицензия повреждена<br />ION_LICENSE_CORRUPT<br />";
      break;
    case ION_LICENSE_EXPIRED:
      echo "Лицензия прострочена<br />ION_LICENSE_EXPIRED<br />";
      break;
    case ION_LICENSE_PROPERTY_INVALID:
      echo "Свойство лицензии не найдено<br />ION_LICENSE_PROPERTY_INVALID<br />";
      break;
    case ION_LICENSE_HEADER_INVALID:
      echo "Не правильный заголово лицензии<br />ION_LICENSE_HEADER_INVALID<br />";
      break;
    case ION_LICENSE_SERVER_INVALID:
      echo "Ошибка сервера лицензии<br />ION_LICENSE_SERVER_INVALID<br />";
      break;
    case ION_UNAUTH_INCLUDING_FILE:
      echo "<br />ION_UNAUTH_INCLUDING_FILE<br />";
      break;
    case ION_UNAUTH_INCLUDED_FILE:
      echo "<br />ION_UNAUTH_INCLUDED_FILE<br />";
      break;
    case ION_UNAUTH_APPEND_PREPEND_FILE:
      echo "<br />ION_UNAUTH_APPEND_PREPEND_FILE<br />";
      break;
  }

  if (isset($_GET['debug_ioncube'])) {
    print_r($params);
  }

  exit();
}