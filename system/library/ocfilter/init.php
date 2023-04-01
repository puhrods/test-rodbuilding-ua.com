<?php
  
function initOCFilter($registry) {
  if (version_compare(phpversion(), '5.4.0', '<') == true) {
    if (is_file(DIR_LOGS . 'ocfilter.log') && filesize(DIR_LOGS . 'ocfilter.log') > 1024 * 1024 * 1) { // 1Mb max
      rename(DIR_LOGS . 'ocfilter.log', DIR_LOGS . 'ocfilter-' . date('Y-m-d_H-i-s') . '.log');
    }
    
    $logs = glob(DIR_LOGS . 'ocfilter-*');
    
    if ($logs && count($logs) > 5) {
      foreach (array_slice($logs, 5) as $log) {
        unlink($log);
      }
    }
    
    $log = new Log('ocfilter.log');
    
    $log->write('PHP 5.4+ Required');
    
    return;
  }
  
  include_once(DIR_SYSTEM . 'library/ocfilter/core.php');
  
  $ocfilter = new OCFilter\Core();
  
  $ocfilter->init($registry);
  
  $registry->set('ocfilter', $ocfilter); 
  
  if ($ocfilter->opencart->isAdmin()) {
    $ocfilter->admin();
  } else {
    $ocfilter->catalog();
  }
}