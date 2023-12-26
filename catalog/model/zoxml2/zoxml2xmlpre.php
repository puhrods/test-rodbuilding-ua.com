<?php
class ModelZoXml2ZoXml2XMLPre extends Model {

public function doUserXMLPre($data,$src) {                
  if (!isset($data['replace'])) return $src;
  foreach ($data['replace'] as $result) {
    if ($result['type']=='xmlpre') $src = $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , $src );
    }
  return  $src;      
  }

protected function doReplaceAction ($data, $mode, $a, $b, $c) {
  $musor = '{QWERTY}';
  switch ($mode) {
//    case 'replace':       return str_replace ( html_entity_decode($a), html_entity_decode($b), $c ); 
    case 'replace':       return str_replace ( $a, $b, $c ); 
    case 'preg':          return preg_replace ( html_entity_decode($a), html_entity_decode($b), $c );
    case 'after':         return str_replace ( $musor, $b, str_replace ( $a, $a . $musor, $c ) );
    case 'before':        return str_replace ( $musor, $b, str_replace ( $a, $musor . $a, $c ) );
    case 'xmlpre':        return $this->__doUserXMLPre($a,$data,$c,$b);
    }
  return $c;
}

protected function __doUserXMLPre($script_name,$data,$src,$param) {                
  $session_key = $data['session_key'];
  $user        = $data['user'];           

if ($script_name=='RealKeramikaFeed') {
  // Дерево категорий
  $src =  preg_replace_callback('/(<Folder>)(.+?)(<\/Folder>)/misu', function($matches){ return str_replace("UidParent","parent_id",$matches[0]); }, $src);
  $src =  preg_replace_callback('/(<Folder>)(.+?)(<\/Folder>)/misu', function($matches){ return str_replace("Uid","id",$matches[0]); }, $src);
  $src =  preg_replace_callback('/(<Folder>)(.+?)(<\/Folder>)/misu', function($matches){ return str_replace("Name","name",$matches[0]); }, $src);
  // Товары - категория
  $src =  preg_replace_callback('/(<Nomenclature>)(.+?)(<\/Nomenclature>)/misu', function($matches){ return str_replace("UidParent","categoryId",$matches[0]); }, $src);
  // Товары - атрибуты
  $src =  preg_replace_callback('/(<Nomenclature>)(.+?)(<\/Nomenclature>)/misu', function($matches){ return str_replace("Properties","specs",$matches[0]); }, $src);
  $src =  preg_replace_callback('/(<P type=")(.+?">)(.+?)(<\/P>)/misu', function($matches){ return "<spec>" . $matches[3] . "</spec>"; }, $src);
  $src =  preg_replace_callback('/(<N>)(.+?)(<\/N>)/misu', function($matches){ return "<name>" . $matches[2] . "</name>"; }, $src);
  $src =  preg_replace_callback('/(<V>)(.+?)(<\/V>)/misu', function($matches){ return "<value>" . $matches[2] . "</value>"; }, $src);
  return $src;
  }
if ($script_name=='sm2mm') {
  return preg_replace_callback('/(unit="см">)(.+?)(<\/param>)/misu', function($matches){ return 'unit="мм">' . 10*(float)str_replace(",",".",$matches[2]) . ' мм.</param>'; }, $src);
  }
if ($script_name=='kill_br_from_attr') {
  return preg_replace_callback('/(name="Материал">)(.+?)(<\/param>)/misu', function($matches){ return $matches[1] . str_replace('<br/>',", ",$matches[2]) . $matches[3]; }, $src);
  }
if ($script_name=='kill_value') {
  return preg_replace_callback('/(<value>)(.+?)(<\/value>)|isxU/m', function($matches){ return $matches[2]; }, $src);
  }
if ($script_name=='kill_mp4') { // Убираем MP4     
  return preg_replace_callback('/(<picture>)(.+?)(mp4<\/picture>)|isxU/m', function($matches){ return ""; }, $src);
  }
if ($script_name=='vistasport') { 
  // Дублируем модель как имя
  $src =  preg_replace_callback('/(<model>)(.+?)(<\/model>)/m', function($matches){ 
    $parts = explode ('(',trim($matches[2]));
    return $matches[0] . "\n<name>" . trim($parts[0]) . "</name>" . "\n<md5_sku>" . md5(trim($parts[0])) . "</md5_sku>"; 
    }, $src);
  return $src;
  }
if ($script_name=='SMARKA2cat_name') { // Дублируем бренд как назание категории
  return preg_replace_callback('/(<SMARKA>)(.+?)(<\/SMARKA>)|isxU/m', function($matches){ return $matches[0] . "<cat_name>" . $matches[2] . "</cat_name>" . "<Марка>" . $matches[2] . "</Марка>"; }, $src);
  }
if ($script_name=='url2sku') { // Используем хэш от URL в качестве артикула
  return preg_replace_callback('/(<url>)(.+?)(<\/url>)|isxU/m', function($matches){ return $matches[0] . "<sku_from_url>" . md5($matches[2]) . "</sku_from_url>"; }, $src);
  }
if ($script_name=='description2sku') { // Используем хэш от description в качестве артикула
  return preg_replace_callback('/(<description>)(.+?)(<\/description>)|isxU/m', function($matches){ return $matches[0] . "<sku_from_description>" . md5($matches[2]) . "</sku_from_description>"; }, $src);
  }
if ($script_name=='name2sku') { // Используем хэш от name в качестве артикула
  return preg_replace_callback('/(<name>)(.+?)(<\/name>)|isxU/m', function($matches){ return $matches[0] . "<sku_from_name>" . md5($matches[2]) . "</sku_from_name>"; }, $src);
  }
if ($script_name=='totalfit.com.ua') {
  return preg_replace_callback('/(<param name="Артикул">)(.+?)(<\/param>)/misu', function($matches){
    $parts = explode ('-',trim($matches[2]));

    return $matches[0] . "<model>". $parts[0] . "</model>" . "<sku_color>". $parts[1] . "</sku_color>" . "<model_color>". $parts[0] . "-" . $parts[1] . "</model_color>";
    }, $src);
  }
if ($script_name=='skiboard') {
  //         <param name="Размер" unit="mx">Арт: 67142982; Размер: L; Сезон: S16; </param>
  return preg_replace_callback('/(name="Размер")(.+?)(<\/param>)/msuxi', function($matches){
    $size  = '';
//    $matches[2] = str_replace ( '&lt;&gt;', '', $matches[2] );
    $parts = explode (';',html_entity_decode(trim($matches[2]),ENT_QUOTES));
    foreach ($parts as $part) {
      $blocks = explode (':',trim($part));
      if (trim($blocks[0]) == "Размер") $size  = "\n" . "<size><![CDATA[". trim($blocks[1]) . "]]></size>";
      }
//    $size  = "\n" . "<size>". trim($matches[2]) . "</size>";
    return $matches[0] . $size;
    }, $src);
  }
if ($script_name=='sku_from_param') {
  return preg_replace_callback('/(<param.+?="Артикул">)(.*?)(<\/param>)/mis', function($matches){
    return "<sku>" . trim($matches[2]) . "</sku>";
    }, $src);
  }
if ($script_name=='cat2yml') {
/*
<categories>
<category>
<id>167</id>
<name>Акции</name>
</category>
<category>
<id>57</id>
<name>Пена, ПСУЛ</name>
<parent_id>56</parent_id>
</category>
*/
  $src   = preg_replace_callback('|(<categories>)(.+)(</categories>)|iU', function($matches){
    $cats =  str_replace("\n", "", $matches[2]);  

    $cats = preg_replace_callback('|(<category)(.+)(</category>)|iU', function($matches){
      //$matches[2] = ><id>309</id><name>Оптим Практик (Россия)</name><parent_id>24</parent_id>
      $xml = simplexml_load_string ($matches[0]); 
      $res = "<category";
      if (isset($xml->id)) $res .= ' id="' . $xml->id . '"';
      if (isset($xml->parent_id)) $res .= ' parentId="' . $xml->parent_id . '"';
      $res .= ">";
      if (isset($xml->name)) $res .= $xml->name;
      $res .= "</category>";
      return $res;
      }, $cats);

    return "<categories>" . $cats . "</categories>";
    }, $src);
  return str_replace("><",   ">\n<", $src);
  }
if ($script_name=='picture2poip') {
  $src = preg_replace_callback('|(<picture>)(.+)(</picture>)|iU', function($matches){
    return "<picture>". $matches[2] . "</picture>" . "\n<poip>". $matches[2] . "</poip>";
    }, $src);
  return $src;
  }
if ($script_name=='kill_nbsp') {
  return str_replace (chr(194).chr(160), " ", $src);  //  удаляем "&nbsp;
  }
if ($script_name=='br2vertical') {
  $src = str_replace ( html_entity_decode("<br />"), '|', $src );
  $src = str_replace ( "<br />", '|', $src );
  $src = str_replace ( "&lt;br /&gt;", '|', $src );
  $src = preg_replace_callback('|(<description>)(.+)(</description>)|iU', function($matches){
    $description = str_replace("\n", "", $matches[2]);
    $description = str_replace("| ", "|", $description);
    $description = str_replace(" |", "|", $description);
    $description = str_replace(": ", ":", $description);
    $description = str_replace(" :", ":", $description);
    $description = str_replace ("&quot;", "", $description);     
    $description = str_replace ("»", "", $description);     
    $description = str_replace ("«", "", $description);     
    $description = str_replace (chr(194).chr(160), " ", $description);  //  удаляем "&nbsp;
    $params = "";   
    $parts = explode ('|', $description);
    foreach ($parts as $part) {
      $attribute = explode (':', trim($part));
      if (!empty($attribute[0]) && !empty($attribute[1])) $params .= "\n" . '<param name="' . trim($attribute[0]) . '">' . trim($attribute[1]) . "</param>";
      }
    return "<description><![CDATA[". $description . "]]></description>" . $params;
    }, $src);
  return $src;
  }
if ($script_name=='n2br') {
  return preg_replace_callback('|(<description>)(.+)(</description>)|iU', function($matches){
    return "<description>". str_replace("\n", "<br />", $matches[2]) . "</description>";
    }, $src);
  }
if ($script_name=='weight_list') {
  $src = str_replace("<weight_list>", "", $src);
  return str_replace("</weight_list>", "", $src);
  }
if ($script_name=='ungzip') {
  // СОХРАНЯЕМ ЗАГРУЖЕННЫЙ ФАЙЛ В КЭШЕ
  $zip_file = DIR_CACHE . $data['session_key'] . ".GZIP";
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('СОХРАНЯЕМ ЗАГРУЖЕННЫЙ ФАЙЛ В КЭШЕ: ' . $zip_file) ."', user = '" . $this->db->escape($user) . "'");
  if (file_put_contents ($zip_file, $src )===FALSE) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Ошибка сохранение файла!') ."', user = '" . $this->db->escape($user) . "'");
    return NULL;
    }
  $src = null; // ОСВОБОЖДАЕМ ПАМЯТЬ!
  // РАСПАКОВЫВАЕМ
  $buffer_size = 4096;
  $file = gzopen($zip_file, 'rb');
  // Keep repeating until the end of the input file
  while (!gzeof($file)) $src .= gzread($file, $buffer_size);
  gzclose($file);
  return $src; 
  }
if ($script_name=='Odeyalaoptom') {
    $src  = "<?xml version='1.0' encoding='UTF-8' ?>\n<yml_catalog>\n<shop>\n<offers>\n";
    $categories = array();
  // ODEYALAOPTOM_API_KEY и ODEYALAOPTOM_PARTNER_ID должны быть прописаны в config.php в корне сайта
    $url = 'http://odeyalaoptom.ru/sst/api/catalog/';
    $part     = 1;
    $parts    = 1;
    $updated  = 0;
    do {
      $request = array (
        'type'    => 'update',      // тип запроса
        'partner' => ODEYALAOPTOM_PARTNER_ID,    // ИД партнера
        'part'    => $part        // часть каталога
        );
  		$paramsJson = json_encode($request);
  		$paramsJson = base64_encode($paramsJson);
  		$signature = '';
  		foreach ($request as $val) $signature .=$val;
  		$signature .= ODEYALAOPTOM_API_KEY;
  		$signature = md5($signature);
  		$ch = curl_init($url);
  		curl_setopt($ch, CURLOPT_VERBOSE, 0);
  		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0); // используем локальный днс кеш
  		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0); // отключаем днс-кеширование
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		curl_setopt($ch, CURLOPT_POST, true);
  		curl_setopt($ch, CURLOPT_POSTFIELDS, 'RS='.$signature.'&data='.$paramsJson);
  		$res = curl_exec($ch);
  		$errNo = curl_errno($ch);
  		$err = curl_error($ch);
  		$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  		curl_close($ch);
  		$json = base64_decode($res);
  		$arResult = json_decode($json, 1);
  		if($arResult && $errNo == 0 && !$err && $http_response_status < 400) {
        if($arResult['STATUS'] == 1) {
          $parts        = $arResult['DATA']['CNT_PARTS'];
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("API - Загружена страница: " . $part . " из " . $parts) . "', user = '" . $this->db->escape($data['user']) . "'");
          foreach ($arResult['DATA']['ITEMS'] as $item) {
            $src .= "<offer>\n";
            foreach ($item as $key => $value) {
              if ($key=='PRODUCT') continue;
              if ($key=='SECTIONS') {
                $levels = array();
                foreach ($value as $cat) $levels[] = trim($cat['NAME']);
                $src .= "<sub_category>";
                $src .= trim(array_pop($levels));
                $src .= "</sub_category>";
                $src .= "<main_category>";
                $src .= trim(array_pop($levels));
                $src .= "</main_category>";
                continue;
                }
              $src .= "<". $key .">";
              $src .= trim($value);
              $src .= "</". $key .">\n";
              }
            $src .= "</offer>\n";
            }
          }
        else break;
        }
      else break;
      } while ($part++<$parts);
 
  $src .= "</offers>\n</shop>\n</yml_catalog>";
  return $src;
  }
if ($script_name=='parent_id') return str_replace("parent_id", "parentId", $src);
if ($script_name=='windows-1251_to_UTF-8') return str_replace("windows-1251", "UTF-8", $src);
if ($script_name=='kolobokman') return str_replace("</shop>", "</offers></shop>", $src);
if ($script_name=='unzip') {
  // СОХРАНЯЕМ ЗАГРУЖЕННЫЙ ФАЙЛ В КЭШЕ
  $zip_file = DIR_CACHE . $data['session_key'] . ".ZIP";
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('СОХРАНЯЕМ ЗАГРУЖЕННЫЙ ФАЙЛ В КЭШЕ: ' . $zip_file) ."', user = '" . $this->db->escape($user) . "'");
  if (file_put_contents ($zip_file, $src )===FALSE) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Ошибка сохранение файла!') ."', user = '" . $this->db->escape($user) . "'");
    return NULL;
    }
  $src = null; // ОСВОБОЖДАЕМ ПАМЯТЬ!
  // РАСПАКОВЫВАЕМ
  $dest = DIR_CACHE . $data['session_key'];
  if (!empty($param)) {
    if ($param=="unique") $dest .= "_" . time();
    }
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('РАСПАКОВЫВАЕМ ЗАГРУЖЕННЫЙ ФАЙЛ В ПАПКУ: ' . $dest) ."', user = '" . $this->db->escape($user) . "'");
  $zip = new ZipArchive;
  if ($zip->open($zip_file) === TRUE) {
    $result = $zip->extractTo($dest);
    if ($result==FALSE) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Ошибка распаковки файла!') ."', user = '" . $this->db->escape($user) . "'");
      return NULL; 
      }  
    $zip->close();
    // ИЩЕМ ФАЙЛ В ПАПКЕ 
		$files = glob($dest . '/*.*');
		if ($files) {
		  foreach ($files as $file) {
        //ЗАГРУЖАЕМ
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('ЗАГРУЖАЕМ ФАЙЛ: ' . $file) ."', user = '" . $this->db->escape($user) . "'");
        return file_get_contents ($file);    
        }
     }    
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Ошибка загрузки файла!') ."', user = '" . $this->db->escape($user) . "'");
    return NULL; 
    }
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Ошибка распаковки файла!') ."', user = '" . $this->db->escape($user) . "'");
  return NULL; 
  }
if ($script_name=='Anderson_only') {
  $vendor = "<vendor>Anderson</vendor>";
  $need = '|(<' . $data['settings']['tag_offer'] . '\s{1,})((?!(' . preg_quote($vendor) . ')).)*(</' . $data['settings']['tag_offer'] . '>)|iUs';
    $src =  preg_replace($need, "", $src);
  return $src; 
  }
if ($script_name=='Extra_content_at_the_end_of_the_document') {
  $src = stristr ( $src, "</yml_catalog>", TRUE ) . "</yml_catalog>";
  return $src; 
  }
if ($script_name=='add_carett') {  return str_replace("><",   ">\n<", $src); }
if ($script_name=='hide__categoryId') {
  $src = str_replace("categoryId",   "_categoryId",       $src);
  return $src; 
  }
if ($script_name=='snt__categoryId') {
  $src = str_replace("features>",   "specs>", $src);
  $src = str_replace("feature>",   "spec>", $src);
  return preg_replace_callback('|(<category>)(.+)(</category>)|iU', function($matches){
    $value = '';
    $parts = explode (',', trim($matches[2]));
    foreach ($parts as $part) $value .= "<categoryId>". (int)trim($part) . "</categoryId>";
  	return $value;
    }, $src);
  // теперь у нас записи вида: <categoryId>14</categoryId>
  }
if ($script_name=='bpsnico.ru') {
  // все что перед <yml_catalog нужно заменить на нормальный заголовок!
  $src  = "<?xml version='1.0' encoding='windows-1251' ?>" . substr ($src,strripos($src,"<yml_catalog"));
  $src = str_replace("strReferer1",   "",       $src);
  $src = str_replace("strReferer2",   "",       $src);
  $src = str_replace("<?=$; ?>",      "",       $src);
  return $src; 
  }
  
if ($script_name=='clear-fit') {
  $src = str_replace("<h2>",      "&lt;h2&gt;",       $src);
  $src = str_replace("</h2>",     "&lt;/h2&gt;",      $src);
  $src = str_replace("<table>",   "&lt;table&gt;",    $src);
  $src = str_replace("</table>",  "&lt;/table&gt;",   $src);
  $src = str_replace("<tr>",      "&lt;tr&gt;",       $src);
  $src = str_replace("</tr>",     "&lt;/tr&gt;",      $src);
  $src = str_replace("<td>",      "&lt;td&gt;",       $src);
  $src = str_replace("</td>",     "&lt;/td&gt;",      $src);
  return $src; 
  }

return $src; 
}


}
?>