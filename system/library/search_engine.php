<?php

function se_clean ($text) {
  $text = html_entity_decode($text);

  $text = str_replace( '<', ' <', $text);
  $text = str_replace( '>', '> ', $text);

  $text = preg_replace("/&([a-zA-Z0-9]+);/", " ", $text);

  $text = strip_tags($text);

  $text = preg_replace("/[-\'\"\r\n\s]+/", " ", $text);

  $text = preg_replace("/[^ a-zA-Zа-яА-Я0-9_]+/u", "", $text);

  $text = preg_replace("/[\s]+/", " ", $text);

  return $text;
}

function se_cyr2lat($text){
    $cyr = array('а','б','в','г','ґ','д','е','ё','є','ж', 'з','и','і','ї','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч', 'ш', 'щ', 'ъ','ы','ь','э','ю', 'я');
    $lat = array('a','b','v','g','g','d','e','e','e','zh','z','i','i','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sh','', 'i','', 'e','ju','ja');
    return  str_replace($cyr, $lat, $text);
}

function se_transliterate($text) {

    $ru_to_en = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        'і' => 'i',   'ї' => 'i',   'є' => 'e',
    );

    $en_to_ru = array(
        'zh' => 'ж', 'ch' => 'ч',  'sh' => 'щ',
        'sch'=> 'щ', 'yu' => 'ю',  'ya' => 'я',   

        'a' => 'а',   'b' => 'б',   'c' => 'к',
        'd' => 'д',   'e' => 'е',   'f' => 'ф',
        'g' => 'г',   'h' => 'х',   'i' => 'и',
        'j' => 'ж',   'k' => 'к',   'l' => 'л',
        'm' => 'м',   'n' => 'н',   'o' => 'о',
        'p' => 'п',   'q' => 'к',   'r' => 'р',
        's' => 'с',   't' => 'т',   'u' => 'у',
        'v' => 'в',   'w' => 'в',   'x' => 'х',
        'y' => 'й',   'z' => 'з'
    );

    $text = mb_strtolower($text);

    if (preg_match('/[а-яё]/u', $text)) {
      return strtr($text, $ru_to_en);
    } else {
      return strtr($text, $en_to_ru);
    }
}

function se_change_keyboard_layout($text) {

  $ru = array('й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', '\\', 'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю', '.');
  $en = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '[', ']', '\\', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', '\'', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.', '/');

  if (se_strpos_array($text, $ru) !== false) {
    return str_replace($ru, $en, $text);
  } elseif (se_strpos_array($text, $en) !== false) {
    return str_replace($en, $ru, $text);
  } else {
    return false;
  }
}

function se_strpos_array($haystack, $needles) {
  if (is_array($needles)) {
    foreach ($needles as $str) {
      $pos = strpos($haystack, $str);
      if ($pos !== false) {
        return $pos;
      }
    }
  }
  return false;
}
