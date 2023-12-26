<?php
//Module: True File Manager by Sitecreator
//Version (Версия): 1.2.1
//Developer of this module (Разработчик): Malyutin R. A. (Малютин Р. А.), Developer Website: https://sitecreator.ru 2019(c)
//All rights reserved. (с)
//Все права защищены. (с)
//The copyright for this module belongs to Mr. Malyutin R. A.
//Авторские права на данный модуль принадлежат господину Малютину Р. А.
//Запрещается распространение данного программного продукта без разрешения автора.
//Distribution of this software product without the permission of the author is prohibited.
//
//Данное программное обеспечение предоставляется на основе простой неисключительной лицензии.
//Данное программное обеспечение распространяется по общепринятому принципу "как есть"  без предоставления каких-либо гарантий в явной или неявной форме.
//Разработчик не гарантирует пригодность данного программного обеспечения для особых целей.
//Ни при каких обстоятельствах разработчик не несет ответственности за любой ущерб, который может возникнуть в результате использования данного программного обеспечения.
//This software is provided based on a simple, non-exclusive license.
//This software is distributed on a generally accepted “as is” basis without any warranty, expressed or implied.
//The developer does not guarantee the suitability of this software for special purposes.
//Under no circumstances shall the developer be liable for any damage that may result from the use of this software.
//
//
//
//
//This software uses freely distributed software of 3 parties, namely the following software:
//Данное программное обеспечение использует в своем составе свободно распространяемое программное обеспечение 3-х сторон, а именно следующий софт:
//
//jquery ver. 3.4.1 (JavaScript library),  Developer Website: https://jquery.com/
//jquery ui ver. 1.12.1 (JavaScript library),  Developer Website: https://jqueryui.com/
//elFinder ver. 2.1.49, Developer Website: https://jqueryui.com/
//
//На указанное выше программное обеспечение третьих лиц распространяется авторское право, принадлежащее соответственно этим третьим лицам.
//Подробнее об этих авторских правах можно прочитать на соответствующих вебсайтах разработчиков. Адреса соответствующих вебсайтов указаны выше.
//
//The above third-party software is copyrighted by these third parties, respectively.
//You can read more about these copyrights on their respective developer websites. The addresses of the respective websites are indicated above.


class ControllerCommonFileManager extends ControllerCommonFileManager__ {
  public function index() {
    $oc23 = (version_compare(VERSION, "2.3", ">="))? true:false;
    $oc15 = (version_compare(VERSION, "2.0", "<"))? true:false;
    $oc30 = (version_compare(VERSION, "3.0", ">="))? true:false;

    $data = [];
    $data['module_ver'] = '1.2.1';

    if($oc30) $data['token']  = $this->session->data['user_token'];  // opencart 3
    else $data['token']  = $this->session->data['token'];

    // Return the target ID for the file manager to set the value
    if (isset($this->request->get['target'])) {
      $data['target'] = $this->request->get['target'];
    } else {
      $data['target'] = '';
    }

    // CKEditor
    if (isset($this->request->get['cke'])) {
      $data['cke'] = $this->request->get['cke'];
    } else {
      $data['cke'] = '';
    }

    // summernote_id
    if (isset($this->request->get['summernote_id'])) {
      $data['summernote_id'] = $this->request->get['summernote_id'];
    } else {
      $data['summernote_id'] = '';
    }

    // Return the thumbnail for the file manager to show a thumbnail
    if (isset($this->request->get['thumb'])) {
      $data['thumb'] = $this->request->get['thumb'];
    } else {
      $data['thumb'] = '';
    }

    $tpl = "truefilemanager.tpl";
    if($oc23) $tpl = "truefilemanager";
    if($oc30) $tpl = "truefilemanager_for_oc3";
    $tpl = 'extension/module/'. $tpl;

    $this->response->setOutput($this->load->view($tpl, $data));
  }

  public function connector() {

    require_once DIR_SYSTEM. "library/sitecreator/elFinder/php/elFinderConnector.class.php";
    require_once DIR_SYSTEM. "library/sitecreator/elFinder/php/elFinder.class.php";
    require_once DIR_SYSTEM. "library/sitecreator/elFinder/php/elFinderVolumeDriver.class.php";
    require_once DIR_SYSTEM. "library/sitecreator/elFinder/php/elFinderVolumeLocalFileSystem.class.php";

    if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')))
      $domain = (defined('HTTPS_CATALOG'))? HTTPS_CATALOG: HTTPS_SERVER;
    else {$domain = (defined('HTTP_CATALOG'))? HTTP_CATALOG: HTTP_SERVER;}

    $opts = array(
      'roots' => array(
        array(
          'driver' => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
          'path'   => DIR_IMAGE. 'catalog/',                // path to files (REQUIRED)
          'URL'    => $domain. 'image/catalog/',
          'tmbPath'=> DIR_IMAGE. 'elfinder_tmb',
          'tmbURL' => $domain. 'image/elfinder_tmb/',
          'tmbSize' => 100,
          'tmbCrop' => false,
          'tmbBgColor' => '#ffffff',
          'mimeDetect' => 'internal',
          'imgLib'     => 'auto',
          'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
          'uploadAllow' => array('image/jpeg', 'image/png', 'image/gif', 'image/svg+xml',
            // "особые" типы древних IE добавил на всякий случай sitecreator
            'image/pjpeg', 'image/x-png'),
          'uploadDeny' => array('all'),
          'uploadOrder' => array('allow, deny'),
        )

      )
    );


    $connector = new elFinderConnector(new elFinder($opts), true);
    $connector->run();

  }

}

?>