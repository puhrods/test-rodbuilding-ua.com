<?php echo $header; ?><?php echo isset($column_left)?$column_left:''; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><a href="http://opencart.zone/"><img src="http://opencart.zone/ocz_logo.png" style="width:177px; height:31px; border:0;float: left;" alt="Разработка и сопровождение модулей и сайтов" title="Разработка и сопровождение модулей и сайтов" /></a><div style="position: relative;display: inline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Разработчик: <a href="http://opencart.zone/autor">Игорь Голубев</a> (ambalocha69@yandex.ru). Официальная страница модуля: <a href="http://opencart.zone/modules-2-0/xml2.html">http://opencart.zone/modules-2-0/xml2.html</a></div> </h3>
      </div>

<style>
.any_feed {display:none;}
.any_dialog {display:none;}
.tab-content {padding-bottom: 50px;}
p .form-control {height: initial;}
input .form-control {width: initial;}
</style>

      <div class="panel-body">
        <form class="form-horizontal">
          <div class="col-sm-2">
            <h3 class="panel-title">ПОСТАВЩИКИ</h3>
            <hr />
            <ul class="nav nav-pills nav-stacked" id="suppliers">
              <?php $row = 0; ?>
              <?php foreach ($suppliers as $session_key => $supplier) { if (isset($filter_supplier)&&$filter_supplier!=$session_key) continue; ?>
                <li>
                  <a href="#tab-supplier<?php echo $row; ?>" data-toggle="tab">
                    <i class="fa fa-minus-circle"  style="float: left;margin-right: 5px;" data-toggle="tooltip" title="Удалить поставщика" onclick="$('.del_dialog_for_<?php echo $row; ?>').show(); setTimeout(function(){$('.any_dialog').hide();},10000);"></i> <?php echo $supplier['settings']['name']; ?>
                    <i class="fa fa-save" style="float: right;" data-toggle="tooltip" title="Сохранить настройки" onclick="$(this).addClass('fa-spinner').addClass('fa-spin'); saveSupplier('<?php echo $session_key; ?>');"></i>
                    <i class="fa fa-exchange" style="float: right;margin: 0px 10px;" data-toggle="tooltip" title="Загрузить товары" onclick="$(this).addClass('fa-spinner').addClass('fa-spin'); $('#LOG<?php echo $row; ?> a:first').tab('show'); loadSupplier('<?php echo $session_key; ?>',<?php echo $supplier['settings']['log_last_id']; ?>);"></i>
                    </a>
                  <div class="row any_dialog del_dialog_for_<?php echo $row; ?>">
                    <div class="col-sm-12" style="text-align: center;"><br /><h4>УДАЛИТЬ ПОСТАВЩИКА?</h4></div>
                    <div class="col-sm-6" style="text-align: center;">
                      <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delFeed('<?php echo $session_key; ?>');" class="btn btn-primary"><i class="fa fa-check"></i>&nbsp;&nbsp;ДА</a>
                    </div>
                    <div class="col-sm-6" style="text-align: center;">
                      <a onclick="$('.any_dialog').hide();" class="btn btn-primary"><i class="fa fa-ban"></i>&nbsp;&nbsp;НЕТ</a>
                    </div>
                  </div>
                </li>
              <?php $row++; } ?>
              <li style="margin-top: 50px;"><a href="#tab-module" data-toggle="tab"><i class="fa fa-cog fa-fw" ></i> НАСТРОЙКИ МОДУЛЯ<i class="fa fa-save" style="float: right;" data-toggle="tooltip" title="Сохранить настройки" onclick="$(this).addClass('fa-spinner').addClass('fa-spin'); saveModule();"></i></a></li>
              <li style="margin-top: 50px;">
                <div class="col-sm-12"><h4>ЗАГРУЗИТЬ ТОЛЬКО:</h4></div>
                <?php foreach ($suppliers_list as $session_key => $supplier) { ?>
                  <div class="col-sm-12" style="margin-top: 20px;"><a href="index.php?route=extension/module/zoxml2&user_token=<?php echo $real_token; ?>&filter_supplier=<?php echo $session_key; ?>"><?php echo $supplier; ?></a></div>
                <?php } ?>
              </li>
            </ul>
          </div>
          <div class="col-sm-10">
            <div class="tab-content">
              <?php $row = 0; ?>
              <?php foreach ($suppliers as $session_key => $supplier) { if (isset($filter_supplier)&&$filter_supplier!=$session_key) continue; ?>
              <div class="tab-pane" id="tab-supplier<?php echo $row; ?>">
                <h3 class="panel-title" style="padding-left:25px;">НАСТРОЙКИ ПОСТАВЩИКА</h3>
                <hr />


          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general<?php echo $row; ?>" data-toggle="tab">УПРАВЛЕНИЕ</a></li>
            <li><a href="#tab-vendors<?php echo $row; ?>" data-toggle="tab">ПРОИЗВОДИТЕЛИ</a></li>
            <li><a href="#tab-category<?php echo $row; ?>" data-toggle="tab">КАТЕГОРИИ</a></li>
            <li><a href="#tab-attribute<?php echo $row; ?>" data-toggle="tab">АТРИБУТЫ\ОПЦИИ</a></li>
            <li id="LOG<?php echo $row; ?>"><a href="#tab-log<?php echo $row; ?>" data-toggle="tab">ЛОГ</a></li>
<?php if (isset($module['load_event_log'])) { ?>
            <li><a href="#tab-events<?php echo $row; ?>" data-toggle="tab">УВЕДОМЛЕНИЯ</a></li>
<?php } ?>
            <li><a href="#tab-replace<?php echo $row; ?>" data-toggle="tab">ПОДСТАНОВКИ</a></li>
            <li><a href="#tab-hooking<?php echo $row; ?>" data-toggle="tab">СОБЫТИЯ</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="tab-general<?php echo $row; ?>">

                <div class="form-group">
                  <label class="col-sm-4 control-label">ДЕЙСТВИЯ:</label>
                  <div class="col-sm-8">
                    <div class="col-sm-12">
                      <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); $('#LOG<?php echo $row; ?> a:first').tab('show'); scanSupplier('<?php echo $session_key; ?>',<?php echo $supplier['settings']['log_last_id']; ?>);" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Загрузить производителей, категории и атрибуты</a>
                    </div>
                    <?php if(isset($extensions[$supplier['settings']['module']])&&$extensions[$supplier['settings']['module']]['can_do_link']=='yes') { ?>
                      <div class="col-sm-12">
                        <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); $('#LOG<?php echo $row; ?> a:first').tab('show'); linkSupplier('<?php echo $session_key; ?>',<?php echo $supplier['settings']['log_last_id']; ?>);" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Привязка существующих товаров к поставщику</a>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ИМЯ:</label>
                  <div class="col-sm-6">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][name]" value="<?php echo $supplier['settings']['name']; ?>" class="form-control" />
                    <input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][module]" value="<?php echo $supplier['settings']['module']; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-1">
                    <i class="fa fa-download" style="float: right;" data-toggle="tooltip" title="Экспорт настроек в XML" onclick="$(this).addClass('fa-spinner').addClass('fa-spin'); saveSupplier('<?php echo $session_key; ?>',1);"></i>
                  </div>
                </div>

                <?php if(isset($extensions[$supplier['settings']['module']])&&$extensions[$supplier['settings']['module']]['need_path']=='yes') { ?>
                <div class="form-group">
                  <label class="col-sm-4 control-label">URL:</label>
                  <div class="col-sm-6">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][url]" value="<?php echo $supplier['settings']['url']; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-1">
                    <a href="<?php echo $supplier['settings']['url']; ?>"><i class="fa fa-download" style="float: right;" data-toggle="tooltip" title="Скачать файл" ></i></a>
                  </div>
                </div>
                <?php } else { ?>
                    <input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][url]" value="<?php echo $supplier['settings']['url']; ?>" class="form-control" />
                <?php } ?>

                <?php if(isset($extensions[$supplier['settings']['module']])&&$extensions[$supplier['settings']['module']]['need_key']=='yes') { ?>
                <div class="form-group">
                  <label class="col-sm-4 control-label">ЛИЦЕНЗИЯ (дополнительные данные):</label>
                  <div class="col-sm-8">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][license]" value="<?php echo $supplier['settings']['license']; ?>" class="form-control" />
                  </div>
                </div>
                <?php } else { ?>
                    <input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][license]" value="<?php echo $supplier['settings']['license']; ?>" class="form-control" />
                <?php } ?>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ДЕЙСТВИЕ ПЕРЕД ЗАГРУЗКОЙ:</label>
                  <div class="col-sm-4">
                    <select name="supplier[<?php echo $session_key; ?>][settings][before]" class="form-control">
                      <option value="nop"          <?php echo $supplier['settings']['before']=='nop'?'selected':''; ?>>Ничего не делать</option>
                      <option value="del"          <?php echo $supplier['settings']['before']=='del'?'selected':''; ?>>Удалить</option>
                      <option value="hide"         <?php echo $supplier['settings']['before']=='hide'?'selected':''; ?>>Отключить</option>
                      <option value="zero_no_hide" <?php echo $supplier['settings']['before']=='zero_no_hide'?'selected':''; ?>>Сбросить количество в 0 (включая опции), без отключения</option>
                      <option value="zero"         <?php echo $supplier['settings']['before']=='zero'?'selected':''; ?>>Сбросить количество в 0 (включая опции)</option>
                      <option value="zero_product" <?php echo $supplier['settings']['before']=='zero_product'?'selected':''; ?>>Сбросить количество в 0 (только у продуктов)</option>
                    </select>
                  </div>
                  <div class="col-sm-4">
                    <select name="supplier[<?php echo $session_key; ?>][settings][before_mode]" class="form-control">
                      <option value="all"          <?php echo $supplier['settings']['before_mode']=='all'?'selected':''; ?>>Для всех товаров</option>
                      <option value="supplier"     <?php echo $supplier['settings']['before_mode']=='supplier'?'selected':''; ?>>Только для товаров этого поставщика</option>
                      <?php foreach ($suppliers_list as $to_session_key => $to_supplier) { if ($session_key==$to_session_key) continue; ?>
                        <option value="supplier|<?php echo $to_session_key; ?>"     <?php echo $supplier['settings']['before_mode']==('supplier|' . $to_session_key)?'selected':''; ?>>Для <?php echo $to_supplier; ?> (<?php echo $to_session_key; ?>)</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ДЕЙСТВИЕ ПРИ ЗАГРУЗКЕ:</label>
                  <div class="col-sm-4">
                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][insert]" value="1" <?php echo isset($supplier['settings']['insert'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-10">добавлять новые товары</div>
                    </div>

              <?php if (isset($module['can_noindex_new'])) { ?>
                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][noindex_new]" value="1" <?php echo isset($supplier['settings']['noindex_new'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-10">запретить индексацию новых товаров</div>
                    </div>
              <?php } ?>

                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][hide_new]" value="1" <?php echo isset($supplier['settings']['hide_new'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-10">новые отключать в ожидании модерации</div>
                    </div>
                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][not_empty_only]" value="1" <?php echo isset($supplier['settings']['not_empty_only'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-10">не загружать товары с нулевым остатком</div>
                    </div>
                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][log_new]" value="1" <?php echo isset($supplier['settings']['log_new'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-10">сообщать о новых товарах в УВЕДОМЛЕНИЯ</div>
                      </div>
                    <div class="row">
                      <div class="col-sm-2">
                          <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][link2category_ids]" value="1" <?php echo isset($supplier['settings']['link2category_ids'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-10">разрешить привязку товара к нескольким категориям</div>
                      </div>
                  </div>

                  <div class="col-sm-4">
                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update]" value="1" <?php echo isset($supplier['settings']['update'])?'checked':''; ?> class="form-control" /> 
                      </div>
                      <div class="col-sm-10">обновлять товары</div>
                    </div>
                    <div class="row">
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][no_update]" value="1" <?php echo isset($supplier['settings']['no_update'])?'checked':''; ?> class="form-control" /> 
                      </div>
                      <div class="col-sm-10">не обновлять отключенные товары</div>
                    </div>
                  </div>

                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">РАЗРЕШАТЬ ОБНОВЛЕНИЕ ПОЛЕЙ:</label>
                  <div class="col-sm-8">

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_price]" value="1" <?php echo isset($supplier['settings']['update_price'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">цена</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_special]" value="1" <?php echo isset($supplier['settings']['update_special'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">акции</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_quantity]" value="1" <?php echo isset($supplier['settings']['update_quantity'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">количество</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_image]" value="1" <?php echo isset($supplier['settings']['update_image'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">изображения</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_name]" value="1" <?php echo isset($supplier['settings']['update_name'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">название</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_description]" value="1" <?php echo isset($supplier['settings']['update_description'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">описание</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_category]" value="1" <?php echo isset($supplier['settings']['update_category'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">категория</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_atributes]" value="1" <?php echo isset($supplier['settings']['update_atributes'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">атрибуты</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_vendor]" value="1" <?php echo isset($supplier['settings']['update_vendor'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">производитель</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_sku]" value="1" <?php echo isset($supplier['settings']['update_sku'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">артикул</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_model]" value="1" <?php echo isset($supplier['settings']['update_model'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">модель</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_weight]" value="1" <?php echo isset($supplier['settings']['update_weight'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">вес</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_l_w_h]" value="1" <?php echo isset($supplier['settings']['update_l_w_h'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">габариты</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_stock_status_id]" value="1" <?php echo isset($supplier['settings']['update_stock_status_id'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">статус отсутствия</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_ean]" value="1" <?php echo isset($supplier['settings']['update_ean'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">ean</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_minimum]" value="1" <?php echo isset($supplier['settings']['update_minimum'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">минимальное кол-во</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_upc]" value="1" <?php echo isset($supplier['settings']['update_upc'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">upc</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_jan]" value="1" <?php echo isset($supplier['settings']['update_jan'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">jan</div>
                    </div>

                    <div class="row">
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_isbn]" value="1" <?php echo isset($supplier['settings']['update_isbn'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">isbn</div>
  
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][update_mpn]" value="1" <?php echo isset($supplier['settings']['update_mpn'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-5">mpn</div>
                    </div>

                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ЗАГРУЗКА ИЗОБРАЖЕНИЙ:</label>
                  <div class="col-sm-8">
                    <div class="col-sm-12">
                      <select name="supplier[<?php echo $session_key; ?>][settings][images]" class="form-control">
                        <option value="nop"          <?php echo $supplier['settings']['images']=='nop'?'selected':''; ?>>Не загружать</option>
                        <option value="main"         <?php echo $supplier['settings']['images']=='main'?'selected':''; ?>>Загружать только основное изображение</option>
                        <option value="all"          <?php echo $supplier['settings']['images']=='all'?'selected':''; ?>>Загружать все изображения</option>
                      </select>
                    </div>
                    <div class="col-sm-12 form-group">
                      <div class="col-sm-6">
                        Способ загрузки изображений:
                      </div>
                      <div class="col-sm-6">
                        <select name="supplier[<?php echo $session_key; ?>][settings][zo_image_loader]" class="form-control">
                          <option value="file_get_contents" <?php echo $supplier['settings']['zo_image_loader']=='file_get_contents'?'selected':''; ?>>file_get_contents (по умолчанию)</option>
                          <option value="curl"              <?php echo $supplier['settings']['zo_image_loader']=='curl'?'selected':''; ?>>CURL</option>
                          <option value="ydisk"             <?php echo $supplier['settings']['zo_image_loader']=='ydisk'?'selected':''; ?>>загрузка с Яндекс.Диска</option>
                        </select>
                      </div>
                      <div class="col-sm-6">
                        Дополнительные опции CURL:
                      </div>
                      <div class="col-sm-6">
                        <textarea placeholder="CURLOPT_USERAGENT|string|booyah!;CURLOPT_FTP_USE_EPSV|int|0" rows="2" name="supplier[<?php echo $session_key; ?>][settings][curl_options]" class="form-control"><?php echo $supplier['settings']['curl_options']; ?></textarea>
                      </div>
                    </div>
                    <div class="col-sm-12 form-group">
                      <div class="col-sm-6">
                        URL папки с изображениями (используется если в фиде указаны относительные пути):
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][img_path]" value="<?php echo $supplier['settings']['img_path']; ?>" class="form-control" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">СОХРАНЕНИЕ ИЗОБРАЖЕНИЙ:</label>
                  <div class="col-sm-8">
                    <div class="col-sm-12 form-group">
                      <div class="col-sm-6">
                        Папка для сохранения изображений:
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][image_save_to]" value="<?php echo $supplier['settings']['image_save_to']; ?>" class="form-control" />
                      </div>
                    </div>
                    <div class="col-sm-12 form-group">
                      <div class="col-sm-6">
                        Структура папки:
                      </div>
                      <div class="col-sm-6">
                        <select name="supplier[<?php echo $session_key; ?>][settings][image_save_as]" class="form-control">
                          <option value="url"          selected                                                           >использовать URL категории для подпапок</option>
                          <option value="md5"        <?php echo $supplier['settings']['image_save_as']=='md5'?'selected':''; ?>>сбалансированное распределение на основе хэш</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-12 form-group">
                      <div class="col-sm-6">
                        Структура папки (изображения опций):
                      </div>
                      <div class="col-sm-6">
                        <select name="supplier[<?php echo $session_key; ?>][settings][option_image_save_as]" class="form-control">
                          <option value="old"          selected                                                           >отдельная папка для каждой опции</option>
                          <option value="md5"        <?php echo $supplier['settings']['option_image_save_as']=='md5'?'selected':''; ?>>сбалансированное распределение на основе хэш</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-12 form-group">
                      <div class="col-sm-6">
                        Максимально допустимый размер изображения (ширина:высота или в байтах):<br />Изображения больше указанного размера будут уменьшены. <b>Важно: эта функция работает только в PHP 5 >= 5.5.0, PHP 7! Если на вашем сервере версия PHP ниже указанной обязательно оставляйте это поле пустым!</b>
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][max_image_size]" value="<?php echo $supplier['settings']['max_image_size']; ?>" class="form-control" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">СВЯЗУЮЩИЕ ПОЛЯ:</label>

                    <div class="col-sm-4">
                      <div class="col-sm-12">
                        <b>Обязательные:</b>
                      </div>
                      <div class="col-sm-12">
                        <select name="supplier[<?php echo $session_key; ?>][settings][link]" class="form-control">
                          <option value="sku"          selected                                                           >Артикул (SKU)</option>
                          <option value="model"        <?php echo $supplier['settings']['link']=='model'?'selected':''; ?>>Модель (model)</option>
                          <option value="name"         <?php echo $supplier['settings']['link']=='name'?'selected':''; ?>>Название (name)</option>
                          <option value="mpn"          <?php echo $supplier['settings']['link']=='mpn'?'selected':''; ?> >Номер произодителя (mpn)</option>
                          <option value="upc"          <?php echo $supplier['settings']['link']=='upc'?'selected':''; ?> >Универсальный код товара (upc)</option>
                          <option value="ean"          <?php echo $supplier['settings']['link']=='ean'?'selected':''; ?> >Европейский код товара (ean)</option>
                          <option value="jan"          <?php echo $supplier['settings']['link']=='jan'?'selected':''; ?> >Японский код товара (jan)</option>
                          <option value="isbn"         <?php echo $supplier['settings']['link']=='isbn'?'selected':''; ?>>Международный стандарт номера книги (isbn)</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-4">
                      <div class="col-sm-12">
                        <b>Опциональные:</b>
                      </div>
                      <div class="row">
                        <div class="col-sm-2">
                          <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][link_vendor]" value="1" <?php echo isset($supplier['settings']['link_vendor'])?'checked':''; ?> class="form-control" />
                        </div>
                        <div class="col-sm-10">Производитель (manufacturer_id) </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-2">
                          <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][link_supplier]" value="1" <?php echo isset($supplier['settings']['link_supplier'])?'checked':''; ?> class="form-control" /> 
                        </div>
                        <div class="col-sm-10">Поставщик</div>
                      </div>
                    </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ФОРМУЛА МОДИФИКАЦИИ ЦЕНЫ:</label>
                  <div class="col-sm-8">
                    <div class="col-sm-12">
                      <div class="col-sm-4">
                        <div class="col-sm-12">Шаг 1 - прибавить к цене:</div>
                        <div class="col-sm-12">
                          <input type="text" name="supplier[<?php echo $session_key; ?>][settings][add_before]" value="<?php echo $supplier['settings']['add_before']; ?>" class="form-control" />
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="col-sm-12">Шаг 2 - умножить результат на:</div>
                        <div class="col-sm-12">
                          <input type="text" name="supplier[<?php echo $session_key; ?>][settings][mul_after]" value="<?php echo $supplier['settings']['mul_after']; ?>" class="form-control" />
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="col-sm-12">Шаг 3 - прибавить к результату:</div>
                        <div class="col-sm-12">
                          <input type="text" name="supplier[<?php echo $session_key; ?>][settings][add_after]" value="<?php echo $supplier['settings']['add_after']; ?>" class="form-control" />
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <b>ТАБЛИЦА НАЦЕНОК:</b> 
                      <br />
                      - формула, которая настроена выше, применяется до начала обработки таблицы наценок
                      <br />
                      - данные в формате: лимит1:наценка1;лимит2:наценка2;лимит3:наценка3 и т.д.
                      <br />
                      - если наценка равна 0, то это означает запрет на загрузку товаров с ценой ниже или равной указанного лимита
                      <br />
                      - ПРИМЕР: 500:0;2000:1.5;5000:1.25 - означает, что товары с ценой до 500 не загружать, до 2000 умножать цену на 1.5, до 5000 умножать на 1.25, свыше 5000 цена не изменяется 
                      <br />
                      <br />
                      Поддерживается вложенная формула: 2000:<b>-100|1.25|0</b>; - означает, что для товаров с ценой до 2000 вычесть 100, затем умножить на 1.25 и прибавить 0
                      <textarea rows="2" name="supplier[<?php echo $session_key; ?>][settings][price_table]" class="form-control"><?php echo $supplier['settings']['price_table']; ?></textarea>
                      <br />
                      <div class="col-sm-1">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][price_table4mc]" value="1" <?php echo isset($supplier['settings']['price_table4mc'])?'checked':''; ?> class="form-control" /> 
                      </div>
                      <div class="col-sm-11">применять наценки к валютным ценам</div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ОКРУГЛЕНИЕ:</label>
                  <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][round_mode]" class="form-control">
                          <option <?php if ($supplier['settings']['round_mode']==-3) { ?> selected="selected"<?php } ?> value="-3">До тысяч</option>
                          <option <?php if ($supplier['settings']['round_mode']==-2) { ?> selected="selected"<?php } ?> value="-2">До сотен</option>
                          <option <?php if ($supplier['settings']['round_mode']==-1) { ?> selected="selected"<?php } ?> value="-1">До десятков</option>
                          <option <?php if ($supplier['settings']['round_mode']==0)  { ?> selected="selected"<?php } ?> value="0">До ближайшего целого</option>
                          <option <?php if ($supplier['settings']['round_mode']==1)  { ?> selected="selected"<?php } ?> value="1">До одного знака после запятой</option>
                          <option <?php if ($supplier['settings']['round_mode']==2)  { ?> selected="selected"<?php } ?> value="2">До двух знаков после запятой</option>
                          <option <?php if ($supplier['settings']['round_mode']==3)  { ?> selected="selected"<?php } ?> value="3">До трех знаков после запятой</option>
                          <option <?php if ($supplier['settings']['round_mode']==4)  { ?> selected="selected"<?php } ?> value="4">До четырех знаков после запятой</option>
                        </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ШАБЛОНЫ:
                      <br /><br /><b>ВАЖНО!</b><br />
                      1. Не создавайте шаблоны для полей, которых нет в вашей сборке!<br />
                      Пример: поле meta_h1 отсутствует в Опенкарт и создание шаблона для этого поля приведет к ошибке при записи данных в БД<br />
                      2. Шаблоны обрабатываются в указанном порядке. И если вы на первом шаге изменили SKU, то далее везде будет подставляться измененное значение.   </label>
                  <div class="col-sm-8">
                    <div class="col-sm-6">

                      <div class="col-sm-4">model:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][model_tpl]" class="form-control"><?php echo $supplier['settings']['model_tpl']; ?></textarea>
                      </div>

                      <div class="col-sm-4">sku:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][sku]" class="form-control"><?php echo $supplier['settings']['sku']; ?></textarea>
                      </div>

                      <div class="col-sm-4">name:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][name_tpl]" class="form-control"><?php echo $supplier['settings']['name_tpl']; ?></textarea>
                      </div>

                      <div class="col-sm-4">meta_keyword:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][meta_keyword]" class="form-control"><?php echo $supplier['settings']['meta_keyword']; ?></textarea>
                      </div>

                      <div class="col-sm-4">meta_description:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][meta_description]" class="form-control"><?php echo $supplier['settings']['meta_description']; ?></textarea>
                      </div>

                      <div class="col-sm-4">meta_title:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" <?php echo $module['is_meta_title']?'':'disabled'; ?> name="supplier[<?php echo $session_key; ?>][settings][meta_title]" class="form-control"><?php echo $supplier['settings']['meta_title']; ?></textarea>
                      </div>

                      <div class="col-sm-4">meta_h1:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" <?php echo $module['is_meta_h1']?'':'disabled'; ?> name="supplier[<?php echo $session_key; ?>][settings][meta_h1]" class="form-control"><?php echo $supplier['settings']['meta_h1']; ?></textarea>
                      </div>

                      <div class="col-sm-4">seo_title:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" <?php echo $module['is_seo_title']?'':'disabled'; ?> name="supplier[<?php echo $session_key; ?>][settings][seo_title]" class="form-control"><?php echo $supplier['settings']['seo_title']; ?></textarea>
                      </div>

                      <div class="col-sm-4">seo_h1:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" <?php echo $module['is_seo_h1']?'':'disabled'; ?> name="supplier[<?php echo $session_key; ?>][settings][seo_h1]" class="form-control"><?php echo $supplier['settings']['seo_h1']; ?></textarea>
                      </div>

                      <div class="col-sm-4">tag:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][tag]" class="form-control"><?php echo $supplier['settings']['tag']; ?></textarea>
                      </div>

                      <div class="col-sm-4">SEO URL:</div>
                      <div class="col-sm-8">
                        <textarea rows="1" name="supplier[<?php echo $session_key; ?>][settings][url_tpl]" class="form-control"><?php echo $supplier['settings']['url_tpl']; ?></textarea>
                      </div>

                    </div>
                    <div class="col-sm-6">
                      <b>РАБОТА С ШАБЛОНАМИ</b><br />
                      {name} - заменяется на название товара<br />
                      {brand} - заменяется на название производителя товара<br />
                      {shop} - заменяется на название магазина<br />
                      {price} - заменяется на цену товара<br />
                      {sku} - заменяется на артикул товара<br />
                      {model} - заменяется на модель товара<br />
                       
                    </div>
                  </div>
                </div>

                 <div class="form-group">
                  <label class="col-sm-4 control-label">ПАРАМЕТРЫ ПО УМОЛЧАНИЮ:</label>
                  <div class="col-sm-8">

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        КОЛИЧЕСТВО ТОВАРОВ:
                      </div>
                      <div class="col-sm-8">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][quantity]" value="<?php echo isset($supplier['settings']['quantity'])?$supplier['settings']['quantity']:'1'; ?>" class="form-control" />
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        МИНИМАЛЬНОЕ КОЛИЧЕСТВО ТОВАРОВ:
                      </div>
                      <div class="col-sm-8">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][minimum]" value="<?php echo isset($supplier['settings']['minimum'])?$supplier['settings']['minimum']:'1'; ?>" class="form-control" />
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        ВЫЧИТАТЬ СО СКЛАДА:
                      </div>
                      <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][subtract]" class="form-control">
                          <option value="0"  <?php echo $supplier['settings']['subtract']==0?'selected':''; ?>>Нет</option>
                          <option value="1"  <?php echo $supplier['settings']['subtract']==1?'selected':''; ?>>Да</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        ОТСУТСТВИЕ НА СКЛАДЕ:
                      </div>
                      <div class="col-sm-8">
                        <div class="col-sm-12">
                          <div class="col-sm-12">
                            <select name="supplier[<?php echo $session_key; ?>][settings][stock_status_id]" class="form-control">
                              <?php foreach ($all_stock_status_id as $stock_status_id => $stock_status_name) { ?>                 
                              <option value="<?php echo $stock_status_id; ?>"  <?php echo $supplier['settings']['stock_status_id']==$stock_status_id?'selected':''; ?>><?php echo $stock_status_name; ?></option>
                              <?php } ?>                 
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-3">
                            <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][hide]" value="1" <?php echo isset($supplier['settings']['hide'])?'checked':''; ?> class="form-control" />
                          </div>
                        <div class="col-sm-9">отключать товары с нулевым остатком</div>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        НАЛОГИ:
                      </div>
                      <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][tax_class_id]" class="form-control">
                          <option value="0" <?php echo empty($supplier['settings']['tax_class_id'])?'selected':''; ?>>Без налога</option>
                          <?php foreach ($tax_classes as $tax_class) { ?>
                            <option value="<?php echo $tax_class['tax_class_id']; ?>"  <?php echo $supplier['settings']['tax_class_id']==$tax_class['tax_class_id']?'selected':''; ?>><?php echo $tax_class['title']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        МЕРЫ ДЛИНЫ:
                      </div>
                      <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][length_class_id]" class="form-control">
                          <?php foreach ($length_classes as $length_class) { ?>
                            <option value="<?php echo $length_class['length_class_id']; ?>"  <?php echo $supplier['settings']['length_class_id']==$length_class['length_class_id']?'selected':''; ?>><?php echo $length_class['title']; ?></option>
                          <?php } ?>
                        </select>
                        <p><b>Дублировать длину в атрибуты:</b></p>

<i class="fa fa-minus-circle" style="float:right;margin-top:5px;font-size:16px;" data-toggle="tooltip" title="Отключить" onclick="$(this).next().val(0);$(this).next().next().val('');$(this).next().next().attr('placeholder', 'Не дублировать');"></i>
<input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][length_to_attr]" value="<?php echo $supplier['settings']['length_to_attr']; ?>" />
<input style="float:left;width:95%;" type="text" placeholder="Не дублировать" value="<?php echo $supplier['settings']['length_to_attr']>0?(isset($attr_groups[$supplier['settings']['length_to_attr']])?$attr_groups[$supplier['settings']['length_to_attr']]:''):''; ?>" class="form-control attributeautocomplete" />

                        <p><b>Дублировать ширину в атрибуты:</b></p>

<i class="fa fa-minus-circle" style="float:right;margin-top:5px;font-size:16px;" data-toggle="tooltip" title="Отключить" onclick="$(this).next().val(0);$(this).next().next().val('');$(this).next().next().attr('placeholder', 'Не дублировать');"></i>
<input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][width_to_attr]" value="<?php echo $supplier['settings']['width_to_attr']; ?>" />
<input style="float:left;width:95%;" type="text" placeholder="Не дублировать" value="<?php echo $supplier['settings']['width_to_attr']>0?(isset($attr_groups[$supplier['settings']['width_to_attr']])?$attr_groups[$supplier['settings']['width_to_attr']]:''):''; ?>" class="form-control attributeautocomplete" />

                        <p><b>Дублировать высоту в атрибуты:</b></p>

<i class="fa fa-minus-circle" style="float:right;margin-top:5px;font-size:16px;" data-toggle="tooltip" title="Отключить" onclick="$(this).next().val(0);$(this).next().next().val('');$(this).next().next().attr('placeholder', 'Не дублировать');"></i>
<input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][height_to_attr]" value="<?php echo $supplier['settings']['height_to_attr']; ?>" />
<input style="float:left;width:95%;" type="text" placeholder="Не дублировать" value="<?php echo $supplier['settings']['height_to_attr']>0?(isset($attr_groups[$supplier['settings']['height_to_attr']])?$attr_groups[$supplier['settings']['height_to_attr']]:''):''; ?>" class="form-control attributeautocomplete" />

                        <p><b>Дублировать габариты в атрибуты по шаблону:</b></p>

<i class="fa fa-minus-circle" style="float:right;margin-top:5px;font-size:16px;" data-toggle="tooltip" title="Отключить" onclick="$(this).next().val(0);$(this).next().next().val('');$(this).next().next().attr('placeholder', 'Не дублировать');"></i>
<input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][l_w_h_to_attr]" value="<?php echo $supplier['settings']['l_w_h_to_attr']; ?>" />
<input style="float:left;width:95%;" type="text" placeholder="Не дублировать" value="<?php echo $supplier['settings']['l_w_h_to_attr']>0?(isset($attr_groups[$supplier['settings']['l_w_h_to_attr']])?$attr_groups[$supplier['settings']['l_w_h_to_attr']]:''):''; ?>" class="form-control attributeautocomplete" />

                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][l_w_h_template]" value="<?php echo $supplier['settings']['l_w_h_template'] ?>" class="form-control" />
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        МЕРЫ ВЕСА:
                      </div>
                      <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][weight_class_id]" class="form-control">
                          <?php foreach ($weight_classes as $weight_class) { ?>
                            <option value="<?php echo $weight_class['weight_class_id']; ?>"  <?php echo $supplier['settings']['weight_class_id']==$weight_class['weight_class_id']?'selected':''; ?>><?php echo $weight_class['title']; ?></option>
                          <?php } ?>
                        </select>
                        <p><b>Дублировать вес в атрибуты:</b></p>
     
<i class="fa fa-minus-circle" style="float:right;margin-top:5px;font-size:16px;" data-toggle="tooltip" title="Отключить" onclick="$(this).next().val(0);$(this).next().next().val('');$(this).next().next().attr('placeholder', 'Не дублировать');"></i>
<input type="hidden" name="supplier[<?php echo $session_key; ?>][settings][weight_to_attr]" value="<?php echo $supplier['settings']['weight_to_attr']; ?>" />
<input style="float:left;width:95%;" type="text" placeholder="Не дублировать" value="<?php echo $supplier['settings']['weight_to_attr']>0?(isset($attr_groups[$supplier['settings']['weight_to_attr']])?$attr_groups[$supplier['settings']['weight_to_attr']]:''):''; ?>" class="form-control attributeautocomplete" />

                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        ЯЗЫК:
                      </div>
                      <div class="col-sm-8">
                        <div class="col-sm-12">
                          <div class="col-sm-12">
                            <select name="supplier[<?php echo $session_key; ?>][settings][language]" class="form-control">
                              <?php foreach ($languages as $language_id => $language_name) { ?>                 
                                <option value="<?php echo $language_id; ?>"  <?php echo $supplier['settings']['language']==$language_id?'selected':''; ?>>Использовать: <?php echo $language_name; ?></option>
                              <?php } ?>                 
                            </select>
                            </div>
                          </div>
                        <div class="col-sm-12">
                          <div class="col-sm-3">
                            <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][all_languages]" value="1" <?php echo isset($supplier['settings']['all_languages'])?'checked':''; ?> class="form-control" />
                          </div>
                        <div class="col-sm-9">При создании товара заполнять все языковые вкладки</div>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        ОПЦИИ (значения по умолчанию):
                      </div>
                      <div class="col-sm-8">

                        <div class="col-sm-12">
                          <div class="col-sm-4">
                            <input type="text" name="supplier[<?php echo $session_key; ?>][settings][dov_quantity]" value="<?php echo isset($supplier['settings']['dov_quantity'])?$supplier['settings']['dov_quantity']:'1'; ?>" class="form-control" />
                          </div>
                          <div class="col-sm-4">
                            кол-во
                          </div>
                          <div class="col-sm-4">
                            <a onclick="$('input[name=\'supplier[<?php echo $session_key; ?>][settings][dov_quantity]\']').val('{quantity}');" class="btn btn-primary"><i class="fa fa-bolt"></i>&nbsp;&nbsp;из товара</a>
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="col-sm-4">
                            <input type="text" name="supplier[<?php echo $session_key; ?>][settings][dov_price]" value="<?php echo isset($supplier['settings']['dov_price'])?$supplier['settings']['dov_price']:'0'; ?>" class="form-control" />
                          </div>
                          <div class="col-sm-4">
                            цена
                          </div>
                          <div class="col-sm-4">
                            <a onclick="$('input[name=\'supplier[<?php echo $session_key; ?>][settings][dov_price]\']').val('{price}');" class="btn btn-primary"><i class="fa fa-bolt"></i>&nbsp;&nbsp;из товара</a>
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="col-sm-4">
                            <select name="supplier[<?php echo $session_key; ?>][settings][dov_price_prefix]" class="form-control">
                              <option value="+"  <?php echo $supplier['settings']['dov_price_prefix']=="+"?'selected':''; ?>>+</option>
                              <option value="-"  <?php echo $supplier['settings']['dov_price_prefix']=="-"?'selected':''; ?>>-</option>
                              <option value="="  <?php echo $supplier['settings']['dov_price_prefix']=="="?'selected':''; ?>>=</option>
                            </select>
                          </div>
                        <div class="col-sm-8">префикс цены</div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-4">
                            <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][dov_required]" value="1" <?php echo $supplier['settings']['dov_required']==1?'checked':''; ?> class="form-control" />
                          </div>
                        <div class="col-sm-8">опция обязательная</div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-4">
                            <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][dov_subtract]" value="1" <?php echo $supplier['settings']['dov_subtract']==1?'checked':''; ?> class="form-control" />
                          </div>
                        <div class="col-sm-8">вычитать со склада</div>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12 form-group">
                      <div class="col-sm-4">
                        Магазины:
                      </div>
                      <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][stores]" class="form-control">
                          <option value="0"  selected>Только основной магазин</option>
                          <option value="all" <?php echo $supplier['settings']['stores']=="all"?'selected':''; ?>>Привязать ко всем магазинам</option>
                          <?php foreach ($all_stores as $store_id => $store_name) { ?>
                            <option value="<?php echo $store_id; ?>" <?php echo $supplier['settings']['stores']==$store_id?'selected':''; ?> ><?php echo $store_name; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                  </div>

                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">АТРИБУТЫ:</label>
                  <div class="col-sm-8">

                    <div class="col-sm-12">
                      <div class="col-sm-4">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][auto_atributes]" value="1" <?php echo isset($supplier['settings']['auto_atributes'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-8">автоматическое создание атрибутов</div>
                    </div>
                    <br />
                    <div class="col-sm-12">
                      <div class="col-sm-4">
                        <select name="supplier[<?php echo $session_key; ?>][settings][default_atribute_group]" class="form-control">
                          <?php foreach ($attribute_groups as $atribute_group_id => $name) { ?>
                            <option value="<?php echo $atribute_group_id; ?>"  <?php echo $supplier['settings']['default_atribute_group']==$atribute_group_id?'selected':''; ?> ><?php echo $name; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-sm-8">группа атрибутов по умолчанию</div>
                    </div>
                    <br />
                    <div class="col-sm-12">
                      <div class="col-sm-4">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][auto_atributes_db]" value="<?php echo $supplier['settings']['auto_atributes_db']; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-8">название служебной таблицы в БД сайта (3-8 букв на латинице)</div>
                    </div>

                  </div>
                  <div class="col-sm-12">
                    <div class="col-sm-4" style="text-align: right;">
                      <b>ВАЖНО!</b>
                    </div>
                    <div class="col-sm-8" style="padding-left: 40px;">
                      Не используйте этот механизм если поставщик поместил бренд или артикул в атрибуты типа param 
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">НАСТРОЙКА CRON:</label>
                  <div class="col-sm-8">
Для работы необходимо прописать на вашем хостинге в планировщике задач следующую строку:<br />
<b>wget -o /dev/null -O/dev/null <?php echo isset($module['SSL'])?HTTPS_CATALOG:HTTP_CATALOG; ?>zoxml2.php?<?php echo $session_key; ?></b><br />
Примечания:<br />
1. Приведен пример формата команды для ISPmanager. Уточните формат команды у вашего хостинг-провайдера<br />
2. В "ЛОГ" задачи выполненные через планировщик отображаются как инициированные пользователем <b>cron</b>
<br /><b>CLI:<br />{ПОЛНЫЙ ПУТЬ К ИНТЕРПРЕТАТОРУ}php -f {ПОЛНЫЙ ПУТЬ К СКИПТУ}zoxml2.php -- <?php echo $session_key; ?></b><br />
Примечания:<br />
1. Уточните пути у вашего хостинг-провайдера<br />
2. В "ЛОГ" задачи выполненные через планировщик отображаются как инициированные пользователем <b>cron (cli)</b>

                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">НАСТРОЙКА ФОРМАТА:</label>
                  <div class="col-sm-8">
                    <div class="col-sm-6" style="text-align: center;">тег <b>shop</b></div>
                    <div class="col-sm-6">
                      <input type="text" name="supplier[<?php echo $session_key; ?>][settings][tag_shop]" value="<?php echo $supplier['settings']['tag_shop']; ?>" class="form-control" />
                    </div>
                    <div class="col-sm-6" style="text-align: center;">тег <b>offers</b></div>
                    <div class="col-sm-6">
                      <input type="text" name="supplier[<?php echo $session_key; ?>][settings][tag_offers]" value="<?php echo $supplier['settings']['tag_offers']; ?>" class="form-control" />
                    </div>
                    <div class="col-sm-6" style="text-align: center;">тег <b>offer</b></div>
                    <div class="col-sm-6">
                      <input type="text" name="supplier[<?php echo $session_key; ?>][settings][tag_offer]" value="<?php echo $supplier['settings']['tag_offer']; ?>" class="form-control" />
                    </div>
                    <div class="col-sm-6" style="text-align: center;">тег <b>categories</b></div>
                    <div class="col-sm-6">
                      <input type="text" name="supplier[<?php echo $session_key; ?>][settings][tag_categories]" value="<?php echo $supplier['settings']['tag_categories']; ?>" class="form-control" />
                    </div>
                    <div class="col-sm-6" style="text-align: center;">тег <b>category</b></div>
                    <div class="col-sm-6">
                      <input type="text" name="supplier[<?php echo $session_key; ?>][settings][tag_category]" value="<?php echo $supplier['settings']['tag_category']; ?>" class="form-control" />
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ШАБЛОНЫ ФОРМАТОВ:</label>
                  <div class="col-sm-8" style="text-align: center;">
                    <a onclick="$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_shop]\']').val('shop');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offers]\']').val('offers');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offer]\']').val('offer');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_categories]\']').val('categories');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_category]\']').val('category');" class="btn btn-primary"><i class="fa fa-bolt"></i>&nbsp;&nbsp;YML</a>
                    <a onclick="$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_shop]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offers]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offer]\']').val('item');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_categories]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_category]\']').val('');" class="btn btn-primary"><i class="fa fa-bolt"></i>&nbsp;&nbsp;api.textiloptom.net v.4</a>
                    <a onclick="$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_shop]\']').val('store');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offers]\']').val('items');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offer]\']').val('item');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_categories]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_category]\']').val('');" class="btn btn-primary"><i class="fa fa-bolt"></i>&nbsp;&nbsp;ТоварДеньгиТовар</a>
                    <a onclick="$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_shop]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offers]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][user_pre]\']').val('Элемент');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][user_scan]\']').val('Элемент');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][user_filter]\']').val('Элемент');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_offer]\']').val('Элемент');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_categories]\']').val('');$('input[name=\'supplier[<?php echo $session_key; ?>][settings][tag_category]\']').val('');" class="btn btn-primary"><i class="fa fa-bolt"></i>&nbsp;&nbsp;Номенклатура (1C)</a>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">ПОЛЬЗОВАТЕЛЬСКИЙ СКРИПТ СКАНИРОВАНИЯ ФИДА:</label>
                  <div class="col-sm-2">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][user_scan]" value="<?php echo isset($supplier['settings']['user_scan'])?$supplier['settings']['user_scan']:''; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-6">Обратитесь к документации по использованию!</div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">СПОСОБ ЗАГРУЗКИ:</label>
                  <div class="col-sm-8">
                    <select name="supplier[<?php echo $session_key; ?>][settings][getXML_method]" class="form-control">
                      <option value="simplexml_load_file"   <?php echo $supplier['settings']['getXML_method']=='simplexml_load_file'?'selected':''; ?>    >simplexml_load_file (по умолчанию)</option>
                      <option value="file_get_contents"     <?php echo $supplier['settings']['getXML_method']=='file_get_contents'?'selected':''; ?>      >file_get_contents</option>
                      <option value="ssl_file_get_contents" <?php echo $supplier['settings']['getXML_method']=='ssl_file_get_contents'?'selected':''; ?>  >file_get_contents через SSL</option>
                      <option value="CURL"                  <?php echo $supplier['settings']['getXML_method']=='CURL'?'selected':''; ?>                   >CURL</option>
                      <option value="ssl_CURL"              <?php echo $supplier['settings']['getXML_method']=='ssl_CURL'?'selected':''; ?>               >CURL через SSL</option>
                      <option value="csv_as_yml_utf8"       <?php echo $supplier['settings']['getXML_method']=='csv_as_yml_utf8'?'selected':''; ?>        >Загрузить CSV (UTF-8) как YML</option>
                      <option value="csv_as_yml_1251"       <?php echo $supplier['settings']['getXML_method']=='csv_as_yml_1251'?'selected':''; ?>        >Загрузить CSV (windows-1251) как YML</option>
                      <option value="HappyGifts"            <?php echo $supplier['settings']['getXML_method']=='HappyGifts'?'selected':''; ?>             >Загрузить HappyGifts (остатки Москва)</option>
                    </select>
                    <div class="col-sm-2">
                      <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][xml2cache]" value="1" <?php echo isset($supplier['settings']['xml2cache'])?'checked':''; ?> class="form-control" />
                    </div>
                    <div class="col-sm-6">Сохранять загруженный файл в кэше. Неприменимо к simplexml_load_file</div>
                  </div>
                </div>

            </div>
            <div class="tab-pane" id="tab-vendors<?php echo $row; ?>">
                <div class="form-group">
                  <div class="col-sm-6">
                    <select onchange="loadVendorPage($(this).val(),'<?php echo $session_key; ?>');" name="vendor_pages[<?php echo $session_key; ?>]" class="form-control">
                      <?php $total = isset($supplier['vendors'])?count($supplier['vendors']):0;  ?>
                      <option value="nop">Выбрать диапазон</option>
                      <option value="all">Показать все бренды поставщика (<?php echo $total; ?>)</option>
                      <?php $pages = (int)ceil($total/100); $i=0; while ($i < $pages) { $next = $i*100+1; ?>
                        <option value="<?php echo $i; ?>">Показать начиная с <?php echo $next; ?>-й</option>
                      <?php $i++; } ?>
                    </select>
                </div>
                  <div class="col-sm-6">
                      <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); addAllVendors('<?php echo $session_key; ?>');" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Создать всех производителей</a>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">НАЗВАНИЕ</div>
                  <div class="col-sm-1">ТОВАРОВ</div>
                  <div class="col-sm-2">НАЦЕНКА</div>
                  <div class="col-sm-6">ПРАВИЛО</div>
                </div>
                <div id="vendor_page_<?php echo $session_key; ?>">
                </div>
            </div>

            <div class="tab-pane" id="tab-category<?php echo $row; ?>">
              <?php if (isset($supplier['categories'])) { ?>
                <div class="form-group">
                  <div class="col-sm-6">
                    <select onchange="loadCategoryPage($(this).val(),'<?php echo $session_key; ?>',<?php echo isset($module['do_category_autocomplete']); ?>);" name="categories_pages[<?php echo $session_key; ?>]" class="form-control">
                      <?php $total = count($supplier['categories']);  ?>
                      <option value="nop">Выбрать диапазон</option>
                      <option value="all">Показать все категории поставщика (<?php echo $total; ?>)</option>
                      <?php $pages = (int)ceil($total/100); $i=0; while ($i < $pages) { $next = $i*100+1; ?>
                        <option value="<?php echo $i; ?>">Показать начиная с <?php echo $next; ?>-й</option>
                      <?php $i++; } ?>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); addAllCategories('<?php echo $session_key; ?>');" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Создать все категории</a>
                  </div>
                  <div class="col-sm-2">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); lostCategories('<?php echo $session_key; ?>');" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Удалить битые ссылки</a>
                  </div>
                  <div class="col-sm-2">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); killCategories('<?php echo $session_key; ?>');" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Сбросить все связи</a>
                  </div>
                </div>
              <?php } ?>
              <div class="form-group">
                <div class="col-sm-5">НАЗВАНИЕ</div>
                <div class="col-sm-1">ТОВАРОВ</div>
                <div class="col-sm-1">НАЦЕНКА</div>
                <div class="col-sm-5">ПРАВИЛО</div>
              </div>
              <div id="categories_page_<?php echo $session_key; ?>">
              </div>
            </div>

            <div class="tab-pane" id="tab-attribute<?php echo $row; ?>">
                <div class="form-group">
                  <div class="col-sm-4">НАЗВАНИЕ</div>
                  <div class="col-sm-4">ПРАВИЛО</div>
                </div>
              <?php if (isset($supplier['options'])) foreach ($supplier['options'] as $option) { ?>
                <div class="form-group">
                  <div class="col-sm-4">
                    <p class="form-control" style="height: initial;"><?php echo $option['name']; ?></p>
                  </div>
                  <div class="col-sm-4">
                    <select name="supplier[<?php echo $session_key; ?>][options][<?php echo $option['id']; ?>][dest_type]" class="form-control">
                      <option value=""              <?php echo $option['dest_type']==''?'selected':''; ?>             >Не обрабатывать</option>
                      <option value="option"        <?php echo $option['dest_type']=='option'?'selected':''; ?>       >Опция (обработка скриптами)</option>
                      <option value="option_value"              <?php echo $option['dest_type']=='option_value'?'selected':''; ?>             >Значение опции (обработка по умолчанию)</option>
                      <option value="option_values_over_comma"  <?php echo $option['dest_type']=='option_values_over_comma'?'selected':''; ?> >Значения опции перечисленные через запятую - "40,42,44"(обработка по умолчанию)</option>
                      <option value="option_values_over_pipe"   <?php echo $option['dest_type']=='option_values_over_pipe'?'selected':''; ?>  >Значения опции перечисленные через вертикальную черту - "40|42|44" (обработка по умолчанию)</option>
                      <option value="option_values_over_slash"  <?php echo $option['dest_type']=='option_values_over_slash'?'selected':''; ?> >Значения опции перечисленные через слэш - "40/42/44" (обработка по умолчанию)</option>
                      <option value="option_values_over_semicolon"  <?php echo $option['dest_type']=='option_values_over_semicolon'?'selected':''; ?> >Значения опции перечисленные через точку с запятой - "40;42;44" (обработка по умолчанию)</option>
                      <option value="attr"          <?php echo $option['dest_type']=='attr'?'selected':''; ?>         >Атрибут</option>
                      <option value="vendor"        <?php echo $option['dest_type']=='vendor'?'selected':''; ?>       >Производитель</option>
                      <option value="model"         <?php echo $option['dest_type']=='model'?'selected':''; ?>        >Модель</option>
                      <option value="sku"           <?php echo $option['dest_type']=='sku'?'selected':''; ?>          >Артикул</option>
                      <option value="quantity"      <?php echo $option['dest_type']=='quantity'?'selected':''; ?>     >Кол-во товаров</option>
                      <option value="quantity_bool" <?php echo $option['dest_type']=='quantity_bool'?'selected':''; ?>>Булево кол-во товаров (true или false)</option>
                      <option value="weight"        <?php echo $option['dest_type']=='weight'?'selected':''; ?>       >Вес товара</option>
                      <option value="name"          <?php echo $option['dest_type']=='name'?'selected':''; ?>         >Название товара</option>
                      <option value="price"         <?php echo $option['dest_type']=='price'?'selected':''; ?>        >Цена</option>
                      <option value="image"         <?php echo $option['dest_type']=='image'?'selected':''; ?>        >Изображение товара</option>
                      <option value="poip"          <?php echo $option['dest_type']=='poip'?'selected':''; ?>  <?php echo isset($module['poip'])?'':'disabled'; ?> >Изображение опции PRO</option>
                      <option value="images"        <?php echo $option['dest_type']=='images'?'selected':''; ?>       >Изображения товара (массив)</option>
                      <option value="images_over_semicolon" <?php echo $option['dest_type']=='images_over_semicolon'?'selected':''; ?> >Изображения товара перечисленные через точку с запятой</option>
                      <option value="images_over_comma"   <?php echo $option['dest_type']=='images_over_comma'?'selected':''; ?> >Изображения товара перечисленные через запятую</option>
                      <option value="images_over_pipe"    <?php echo $option['dest_type']=='images_over_pipe'?'selected':''; ?>  >Изображения товара перечисленные через |</option>
                      <option value="o_description" <?php echo $option['dest_type']=='o_description'?'selected':''; ?>>Описание опции</option>
                      <option value="description"   <?php echo $option['dest_type']=='description'?'selected':''; ?>  >Описание товара</option>
                      <option value="add2description"   <?php echo $option['dest_type']=='add2description'?'selected':''; ?>  >Добавить к описанию товара</option>
                      <option value="description_array"   <?php echo $option['dest_type']=='description_array'?'selected':''; ?>  >Описание товара (массив)</option>
                      <option value="announcement"  <?php echo $option['dest_type']=='announcement'?'selected':''; ?>  <?php echo isset($module['zoannouncement2'])?'':'disabled'; ?>>Краткое описание товара</option>
                      <option value="location"      <?php echo $option['dest_type']=='location'?'selected':''; ?>     >location (расположение)</option>
                      <option value="cat_name"      <?php echo $option['dest_type']=='cat_name'?'selected':''; ?>     >Название категории</option>
                      <option value="par_cat_name"  <?php echo $option['dest_type']=='par_cat_name'?'selected':''; ?> >Родительская категория</option>
                      <option value="minimum"       <?php echo $option['dest_type']=='minimum'?'selected':''; ?>      >Минимальное количество</option>
                      <option value="oldprice"      <?php echo $option['dest_type']=='oldprice'?'selected':''; ?>     >Старая цена</option>
                      <option value="iprice"        <?php echo $option['dest_type']=='iprice'?'selected':''; ?>       >Закупочная цена</option>
                      <option value="iprice_cur"    <?php echo $option['dest_type']=='iprice_cur'?'selected':''; ?>    <?php echo isset($module['mcg2'])?'':'disabled'; ?>>Валюта закупочной цены</option>
                      <option value="mc_price"      <?php echo $option['dest_type']=='mc_price'?'selected':''; ?>      <?php echo (isset($module['mcg2']) || isset($module['valuta_plus']))?'':'disabled'; ?>>Валютная цена</option>
                      <option value="mc_price_cur"  <?php echo $option['dest_type']=='mc_price_cur'?'selected':''; ?>  <?php echo (isset($module['mcg2']) || isset($module['valuta_plus']))?'':'disabled'; ?>>Валюта валютной цены</option>
                      <option value="io_sku"        <?php echo $option['dest_type']=='io_sku'?'selected':''; ?>        <?php echo isset($module['io2'])?'':'disabled'; ?>>Артикул опции (для Расширенные опции)</option>
                      <option value="io_model"      <?php echo $option['dest_type']=='io_model'?'selected':''; ?>      <?php echo isset($module['io2'])?'':'disabled'; ?>>Модель опции (для Расширенные опции)</option>
                      <option value="io_upc"        <?php echo $option['dest_type']=='io_upc'?'selected':''; ?>        <?php echo isset($module['io2'])?'':'disabled'; ?>>UPC опции (для Расширенные опции)</option>
                      <option value="io_description" <?php echo $option['dest_type']=='io_description'?'selected':''; ?> <?php echo isset($module['io2'])?'':'disabled'; ?>>Описание опции (для Расширенные опции)</option>
                      <option value="ro_model"      <?php echo $option['dest_type']=='ro_model'?'selected':''; ?>      <?php echo isset($module['ro2'])?'':'disabled'; ?>>Модель (для Связанных опций)</option>
                      <option value="ro_sku"        <?php echo $option['dest_type']=='ro_sku'?'selected':''; ?>        <?php echo isset($module['ro2'])?'':'disabled'; ?>>Артикул (для Связанных опций)</option>
                      <option value="mpn"           <?php echo $option['dest_type']=='mpn'?'selected':''; ?>           >mpn</option>
                      <option value="upc"           <?php echo $option['dest_type']=='upc'?'selected':''; ?>           >upc</option>
                      <option value="ean"           <?php echo $option['dest_type']=='ean'?'selected':''; ?>           >ean</option>
                      <option value="jan"           <?php echo $option['dest_type']=='jan'?'selected':''; ?>           >jan</option>
                      <option value="isbn"          <?php echo $option['dest_type']=='isbn'?'selected':''; ?>          >isbn</option>
                      <option value="length"        <?php echo $option['dest_type']=='length'?'selected':''; ?>        >Габариты: длина</option>
                      <option value="width"         <?php echo $option['dest_type']=='width'?'selected':''; ?>         >Габариты: ширина</option>
                      <option value="height"        <?php echo $option['dest_type']=='height'?'selected':''; ?>        >Габариты: высота</option>
                      <option value="l_w_h_x"       <?php echo $option['dest_type']=='l_w_h_x'?'selected':''; ?>       >Габаритные размеры, перечисленные через  любой разделитель</option>
                      <option value="country"       <?php echo $option['dest_type']=='country'?'selected':''; ?>       <?php echo isset($module['mcg2'])?'':'disabled'; ?>>Страна происхождения</option>
                      <option value="sliv"          <?php echo $option['dest_type']=='sliv'?'selected':''; ?>          <?php echo isset($module['mcg2'])?'':'disabled'; ?>>Слив товара</option>
                      <option value="stock_status_id" <?php echo $option['dest_type']=='stock_status_id'?'selected':''; ?>>Статус отсутствия на складе</option>
                      </select>
                  </div>
                  <div class="col-sm-4">
                    <select name="supplier[<?php echo $session_key; ?>][options][<?php echo $option['id']; ?>][dest_id]" class="form-control">
                      <option value="0">Не определено</option>
                      <?php foreach ($all_options as $option_id => $option_name) { ?>
                        <option value="<?php echo $option_id; ?>" <?php echo ($option['dest_type']=='poip'||$option['dest_type']=='option'||$option['dest_type']=='option_value'||$option['dest_type']=='option_values_over_semicolon'||$option['dest_type']=='option_values_over_slash'||$option['dest_type']=='option_values_over_comma'||$option['dest_type']=='option_values_over_pipe')&&$option['dest_id']==$option_id?'selected':''; ?>>ОПЦИЯ: <?php echo $option_name; ?></option>
                      <?php } ?>
                      <?php if (!isset($supplier['settings']['auto_atributes'])) { ?>
                        <?php foreach ($attr_groups as $option_id => $option_name) { ?>
                          <option value="<?php echo $option_id; ?>" <?php echo $option['dest_type']=='attr'&&$option['dest_id']==$option_id?'selected':''; ?>>АТРИБУТ: <?php echo $option_name; ?></option>
                        <?php } ?>
                      <?php } ?>
                      <?php foreach ($all_stock_status_id as $stock_status_id => $stock_status_name) { ?>                 
                        <option value="<?php echo $stock_status_id; ?>"  <?php echo $option['dest_type']=='stock_status_id'&&$option['dest_id']==$stock_status_id?'selected':''; ?>>СТАТУС: <?php echo $stock_status_name; ?></option>
                      <?php } ?>                 
                    </select>
                  </div>
                </div>
              <?php } ?>
            </div>

            <div class="tab-pane" id="tab-log<?php echo $row; ?>">
                <div class="form-group" id="log_progress_<?php echo $session_key; ?>">
                  <div class="col-sm-2">ТИП СОБЫТИЯ</div>
                  <div class="col-sm-2">ПОЛЬЗОВАТЕЛЬ</div>
                  <div class="col-sm-2">ВРЕМЯ СОБЫТИЯ</div>
                  <div class="col-sm-4">ДАННЫЕ</div>
                  <div class="col-sm-2">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delRecords ('<?php echo $session_key; ?>');" class="btn btn-primary"><i class="fa fa fa-minus"></i>&nbsp;&nbsp;ОЧИСТИТЬ ЛОГ</a>
                  </div>
                </div>
              <?php if (isset($supplier['log'])) foreach ($supplier['log'] as $id => $record) { ?>
                <div class="form-group">
                  <div class="col-sm-2">
                    <p class="form-control"><?php echo $record['type']; ?></p>
                  </div>
                  <div class="col-sm-2">
                    <p class="form-control"><?php echo $record['user']; ?></p>
                  </div>
                  <div class="col-sm-2">
                    <p class="form-control"><?php echo $record['time']; ?></p>
                  </div>
                  <div class="col-sm-6">
                    <p class="form-control" style="height: initial;"><?php echo $record['data']; ?></p>
                  </div>
                </div>
              <?php } ?>
            </div>

<?php if (isset($module['load_event_log'])) { ?>
            <div class="tab-pane" id="tab-events<?php echo $row; ?>">
                <div class="form-group">
                  <div class="col-sm-2">ТИП СОБЫТИЯ</div>
                  <div class="col-sm-2">ВРЕМЯ СОБЫТИЯ</div>
                  <div class="col-sm-6">ДАННЫЕ</div>
                  <div class="col-sm-2">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delEvents ('<?php echo $session_key; ?>');" class="btn btn-primary"><i class="fa fa fa-minus"></i>&nbsp;&nbsp;ОЧИСТИТЬ СПИСОК</a>
                  </div>
                </div>
              <?php if (isset($supplier['events'])) foreach ($supplier['events'] as $id => $event) { ?>
                <div class="form-group">
                  <div class="col-sm-2">
                    <p class="form-control"><?php echo $event['type']; ?></p>
                  </div>
                  <div class="col-sm-2">
                    <p class="form-control"><?php echo $event['time']; ?></p>
                  </div>
                  <div class="col-sm-8">
                    <p class="form-control" style="height: initial;"><?php echo $event['data']; ?></p>
                  </div>
                </div>
              <?php } ?>
            </div>
<?php } ?>

            <div class="tab-pane" id="tab-replace<?php echo $row; ?>">
                <div class="form-group">
                  <div class="col-sm-12">
                    <p class="form-control" style="width: 100%;">Укажите список слов или фраз в описании товара, которые нужно изменить на нужные вам</p>
                    <p class="form-control" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Перевод осуществляется с помощью <a href="http://translate.yandex.ru/">«Яндекс.Переводчик»</a>. Ознакомиться с условиями использования, тарифами и получить ключ можно на <a href="https://yandex.ru/legal/translate_api/">официальном сайте</a></p>
                    <p class="form-control" style="width: 100%;height: initial;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Вместо использовавшегося ранее <b>ПОЛЬЗОВАТЕЛЬСКИЙ СКРИПТ ПРЕДВАРИТЕЛЬНОЙ ОБРАБОТКИ ФИДА</b> добавьте правило:<br />
                    применить - к <b>загруженному XML-файлу</b>, искать - название скрипта (например <b>unzip</b>), правило - <b>вызов скрипта (только для XML-файла)</b>, данные - можно передать параметры в скрипт</p>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-1">ПОРЯДОК</div>
                  <div class="col-sm-2">ПРИМЕНИТЬ К</div>
                  <div class="col-sm-2">ИСКАТЬ</div>
                  <div class="col-sm-3">ПРАВИЛО</div>
                  <div class="col-sm-2">ДАННЫЕ</div>
                  <div class="col-sm-1">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delReplaces ('<?php echo $session_key; ?>');" class="btn btn-primary"><i class="fa fa fa-minus"></i>&nbsp;&nbsp;ОЧИСТИТЬ СПИСОК</a>
                  </div>
                </div>
              <?php if (isset($supplier['replace'])) foreach ($supplier['replace'] as $replace) { ?>
                <div class="form-group">
                  <div class="col-sm-1">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][replace][<?php echo $replace['id']; ?>][sort_order]" value="<?php echo $replace['sort_order']; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-2">
                    <select name="supplier[<?php echo $session_key; ?>][replace][<?php echo $replace['id']; ?>][type]" class="form-control">
                      <option value=""              <?php echo $replace['type']==''?'selected':''; ?>>Правило отключено</option>
                      <option value="description"   <?php echo $replace['type']=='description'?'selected':''; ?> >Описание товара</option>
                      <option value="name"          <?php echo $replace['type']=='name'?'selected':''; ?> >Название товара</option>
                      <option value="model"         <?php echo $replace['type']=='model'?'selected':''; ?> >Модель</option>
                      <option value="sku"           <?php echo $replace['type']=='sku'?'selected':''; ?> >Артикул</option>
                      <option value="attr"          <?php echo $replace['type']=='attr'?'selected':''; ?> >Значение атрибута</option>
                      <option value="option"        <?php echo $replace['type']=='option'?'selected':''; ?> >Значение опции</option>
                      <option value="price"         <?php echo $replace['type']=='price'?'selected':''; ?> >Цена товара</option>
                      <option value="quantity"      <?php echo $replace['type']=='quantity'?'selected':''; ?> >Кол-во товара</option>
                      <option value="weight"        <?php echo $replace['type']=='weight'?'selected':''; ?> >Вес товара</option>
                      <option value="xmlpre"        <?php echo $replace['type']=='xmlpre'?'selected':''; ?> >Загруженному XML-файлу</option>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <textarea rows="1" name="supplier[<?php echo $session_key; ?>][replace][<?php echo $replace['id']; ?>][txt_before]" class="form-control"><?php echo $replace['txt_before']; ?></textarea>
                  </div>
                  <div class="col-sm-3">
                    <select name="supplier[<?php echo $session_key; ?>][replace][<?php echo $replace['id']; ?>][mode]" class="form-control" >
                      <option value="replace"       <?php echo $replace['mode']=='replace'?'selected':''; ?>   >заменить найденное на</option>
                      <option value="before"        <?php echo $replace['mode']=='before'?'selected':''; ?>    >вставить перед найденным</option>
                      <option value="after"         <?php echo $replace['mode']=='after'?'selected':''; ?>     >вставить после найденного</option>
                      <option value="before_begin"  <?php echo $replace['mode']=='before_begin'?'selected':''; ?> >вставить в начале</option>
                      <option value="after_end"     <?php echo $replace['mode']=='after_end'?'selected':''; ?>    >вставить в конце</option>
                      <option value="preg"          <?php echo $replace['mode']=='preg'?'selected':''; ?>      >обработать регуляным выражением</option>
                      <option value="translate"     <?php echo $replace['mode']=='translate'?'selected':''; ?> >перевести</option>
                      <option value="htmlentities"  <?php echo $replace['mode']=='htmlentities'?'selected':''; ?> >преобразовать символы в соответствующие HTML сущности</option>
                      <option value="htmlspecialchars"  <?php echo $replace['mode']=='htmlspecialchars'?'selected':''; ?> >преобразовать кавычки в ординарные</option>
                      <option value="xmlpre"  <?php echo $replace['mode']=='xmlpre'?'selected':''; ?> >вызов скрипта (только для XML-файла)</option>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <textarea rows="1" name="supplier[<?php echo $session_key; ?>][replace][<?php echo $replace['id']; ?>][txt_after]" class="form-control"><?php echo $replace['txt_after']; ?></textarea>
                  </div>
                  <div class="col-sm-1">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delReplace (<?php echo $replace['id']; ?>);" class="btn btn-primary"><i class="fa fa fa-minus"></i>&nbsp;&nbsp;Удалить</a>
                  </div>
                </div>
              <?php } ?>
                <div class="form-group">
                  <div class="col-sm-1">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][replace][new][sort_order]" value="0" class="form-control" />
                  </div>
                  <div class="col-sm-2">
                    <select name="supplier[<?php echo $session_key; ?>][replace][new][type]" class="form-control" >
                      <option value=""              >Правило отключено</option>
                      <option value="description"   >Описание товара</option>
                      <option value="name"          >Название товара</option>
                      <option value="model"         >Модель</option>
                      <option value="sku"           >Артикул</option>
                      <option value="attr"          >Значение атрибута</option>
                      <option value="option"        >Значение опции</option>
                      <option value="price"         >Цена товара</option>
                      <option value="quantity"      >Кол-во товара</option>
                      <option value="weight"        >Вес товара</option>
                      <option value="xmlpre"        >Загруженному XML-файлу</option>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <textarea rows="1" name="supplier[<?php echo $session_key; ?>][replace][new][txt_before]" class="form-control" placeholder="Искать"></textarea>
                  </div>
                  <div class="col-sm-3">
                    <select name="supplier[<?php echo $session_key; ?>][replace][new][mode]" class="form-control" >
                      <option value="replace"       >заменить найденное на</option>
                      <option value="before"        >вставить перед найденным</option>
                      <option value="after"         >вставить после найденного</option>
                      <option value="before_begin"  >вставить в начале</option>
                      <option value="after_end"     >вставить в конце</option>
                      <option value="preg"          >обработать регуляным выражением</option>
                      <option value="translate"     >перевести</option>
                      <option value="htmlentities"  >преобразовать символы в соответствующие HTML сущности</option>
                      <option value="htmlspecialchars">преобразовать кавычки в ординарные</option>
                      <option value="xmlpre"        >вызов скрипта (только для XML-файла)</option>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <textarea rows="1" name="supplier[<?php echo $session_key; ?>][replace][new][txt_after]" class="form-control" placeholder="данные"></textarea>
                  </div>
                  <div class="col-sm-1">
                    <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); addReplace ('<?php echo $session_key; ?>');" class="btn btn-primary"><i class="fa fa fa-plus"></i>&nbsp;&nbsp;Добавить</a>
                  </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-hooking<?php echo $row; ?>">
                <div class="form-group">
                  <div class="col-sm-12">
                    <p class="form-control" style="width: 100%;height: initial;">
                    В этом разделе находятся механизмы обработки событий, возникающих при загрузке и обработке данных.<br />
                    Обращение ко всем обработчикам осуществяется путем указания в поле ввода алфавитно-числового ключа. Например: my_custom_filter
                    </p>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">ПОЛЬЗОВАТЕЛЬСКИЙ СТАРТ:</label>
                  <div class="col-sm-3">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][user_start]" value="<?php echo isset($supplier['settings']['user_start'])?$supplier['settings']['user_start']:''; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <p class="form-control" style="width: 100%;height: initial;">
                    Данный обработчик будет вызван ПЕРЕД началом обработкой товарных предложений, но ПОСЛЕ загрузки фида.<br />
                    Позволяет выполнить дополнительные предстартовые действия (например загрузить конфигурацию) и при необходимости остановить обработку всех товарных предложений.<br />
                    Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2start.php<br /> 
                    Обратитесь к документации по использованию!
                    </p>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">ПОЛЬЗОВАТЕЛЬСКИЙ ПРЕПРОЦЕССОР:</label>
                  <div class="col-sm-3">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][user_pre]" value="<?php echo isset($supplier['settings']['user_pre'])?$supplier['settings']['user_pre']:''; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <p class="form-control" style="width: 100%;height: initial;">
                    Данный обработчик будет вызван ПЕРЕД обработкой каждого товарного предложения.<br />
                    Позволяет обработать нестандартные данные и при необходимости предотвратить обработку этого товарного предложения.<br />
                    Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2pre.php<br /> 
                    Обратитесь к документации по использованию!
                    </p>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">ПОЛЬЗОВАТЕЛЬСКИЙ ФИЛЬТР:</label>
                  <div class="col-sm-3">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][user_filter]" value="<?php echo isset($supplier['settings']['user_filter'])?$supplier['settings']['user_filter']:''; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <p class="form-control" style="width: 100%;height: initial;">
                    Данный обработчик будет вызван ПОСЛЕ обработки каждого товарного предложения.<br />
                    Позволяет обработать нестандартные данные (такие как опции товара) и при необходимости предотвратить обработку этого товарного предложения.<br />
                    Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2filters.php<br />
                    ВАЖНО: данный обработчик вызывается после того как отработали ПОДСТАНОВКИ, шаблоны и модификаторы цены 
                    Обратитесь к документации по использованию!
                    </p>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">СВЯЗАННЫЕ ОПЦИИ:</label>
                  <div class="col-sm-3">
                    <input type="text" name="supplier[<?php echo $session_key; ?>][settings][user_ro]" value="<?php echo isset($supplier['settings']['user_ro'])?$supplier['settings']['user_ro']:''; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <p class="form-control" style="width: 100%;height: initial;">
                    Данный обработчик будет вызван ПОСЛЕ обработки каждого товарного предложения.<br />
                    Позволяет обработать нестандартные данные (такие как связанные опции товара) и при необходимости предотвратить обработку этого товарного предложения.<br />
                    Это расширение предоставляется за отдельную плату.<br />
                    ВАЖНО: данный обработчик вызывается после того как отработали ПОДСТАНОВКИ, шаблоны, модификаторы цены и ПОЛЬЗОВАТЕЛЬСКИЙ ФИЛЬТР<br /> 
                    Обратитесь к документации по использованию!
                    </p>
                  </div>
                </div>


                <div class="form-group">
                  <label class="col-sm-3 control-label">ПОЛЬЗОВАТЕЛЬСКИЕ ДЕЙСТВИЯ (afterUpdate):</label>
                  <div class="col-sm-3">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][update_use_script]" value="<?php echo isset($supplier['settings']['update_use_script'])?$supplier['settings']['update_use_script']:''; ?>" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <p class="form-control" style="width: 100%;height: initial;">
                    Данный обработчик будет вызван ПОСЛЕ обработки каждого товарного предложения.<br />
                    Позволяет обработать нестандартные данные (такие как опции товара) и сохранить их в БД.<br />
                    Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2usescript.php<br />
                    ВАЖНО: данный обработчик вызывается после того как завершено сохранения или обновление данных о товаре в БД<br />
                    Обратитесь к документации по использованию!
                    </p>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">ПОЛЬЗОВАТЕЛЬСКИЕ ВАЛИДАТОРЫ:</label>
                  <div class="col-sm-9">
                    <div class="col-sm-12">
                      <div class="col-sm-4">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][insert_analyzer]" value="<?php echo isset($supplier['settings']['insert_analyzer'])?$supplier['settings']['insert_analyzer']:''; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-8">
                        <p class="form-control" style="width: 100%;height: initial;">
                        Пользовательский валидатор добавления (preInsert)<br />
                        Данный обработчик будет вызван для запроса дополнительного подтверждения на ДОБАВЛЕНИЕ НОВОГО товара.<br />
                        Позволяет заблокировать добавление товаров не отвечающих определенным критериям.<br />
                        Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2insertanalyzer.php<br />
                        Обратитесь к документации по использованию!
                        </p>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="col-sm-4">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][update_analyzer]" value="<?php echo isset($supplier['settings']['update_analyzer'])?$supplier['settings']['update_analyzer']:''; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-8">
                        <p class="form-control" style="width: 100%;height: initial;">
                        Пользовательский валидатор обновления (preUpdate)<br />
                        Данный обработчик будет вызван для запроса дополнительного подтверждения на ОБНОВЛЕНИЕ товара.<br />
                        Позволяет заблокировать обновление некоторых товаров.<br />
                        Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2updateanalyzer.php<br />
                        Обратитесь к документации по использованию!
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">ДЕЙСТВИЕ ПОСЛЕ ЗАГРУЗКИ:</label>
                  <div class="col-sm-9">
                    <div class="row">
                      <div class="col-sm-4">
                        <input type="text" name="supplier[<?php echo $session_key; ?>][settings][user_after]" value="<?php echo isset($supplier['settings']['user_after'])?$supplier['settings']['user_after']:''; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-8">
                        <p class="form-control" style="width: 100%;height: initial;">
                        Пользовательские действия.<br />
                        Данный обработчик будет вызван по окончанию обработки ВСЕХ товарных предложений.<br />
                        Обработчики ключей находятся в файле catalog\model\zoxml2\zoxml2after.php<br />
                        Обратитесь к документации по использованию!
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-4">
                        Отключить товары содержащие атрибут:
                      </div>
                      <div class="col-sm-8">
                        <select name="supplier[<?php echo $session_key; ?>][settings][hide_by_attribute]" class="form-control">
                          <option value="0">Не определено</option>
                          <?php foreach ($attr_groups as $option_id => $option_name) { ?>
                            <option value="<?php echo $option_id; ?>" <?php echo $supplier['settings']['hide_by_attribute']==$option_id?'selected':''; ?>>АТРИБУТ: <?php echo $option_name; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-4">
                        Отключить необновленные товары:
                      </div>
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][hide_missing]" value="1" <?php echo isset($supplier['settings']['hide_missing'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        (Случаи: товар исчез из фида, товар отсечен по цене и т.п.)
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-4">
                        Обнулить необновленные товары:
                      </div>
                      <div class="col-sm-2">
                        <input type="checkbox" name="supplier[<?php echo $session_key; ?>][settings][zero_missing]" value="1" <?php echo isset($supplier['settings']['zero_missing'])?'checked':''; ?> class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        (Случаи: товар исчез из фида, товар отсечен по цене и т.п.)
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">ИСКЛЮЧЕНИЯ:</label>
                  <div class="col-sm-3">
                    <select name="supplier[<?php echo $session_key; ?>][settings][user_exception]" class="form-control">
                      <option value="0"             <?php echo $supplier['settings']['user_exception']==0?'selected':''; ?>            >Не использовать</option>
                      <option value="WhiteListOfSku"  <?php echo $supplier['settings']['user_exception']=='WhiteListOfSku'?'selected':''; ?> >Список разрешенных к загрузке артикулов</option>
                      <option value="BlackListOfSku"  <?php echo $supplier['settings']['user_exception']=='BlackListOfSku'?'selected':''; ?> >Список запрещенных к загрузке артикулов</option>
                    </select>
                  </div>
                  <div class="col-sm-6">
                    <textarea placeholder="Перечислите артикулы через запятую" rows="3" name="supplier[<?php echo $session_key; ?>][settings][user_exceptions]" class="form-control"><?php echo $supplier['settings']['user_exceptions']; ?></textarea>
                  </div>
                </div>

            </div>

          </div>

              </div>
            <?php $row++; } ?>
              <div class="tab-pane" id="tab-module">
                <h3 class="panel-title" style="padding-left:25px;">НАСТРОЙКИ МОДУЛЯ</h3>
                <hr />
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#tab-module-general" data-toggle="tab">СИСТЕМА</a></li>
                  <li><a href="#tab-module-add" data-toggle="tab">ПОДКЛЮЧИТЬ ПОСТАВШИКА</a></li>
                  <li><a href="#tab-module-log" data-toggle="tab">СИСТЕМНЫЙ ЛОГ</a></li>
                </ul>
      
                <div class="tab-content">

                  <div class="tab-pane active" id="tab-module-general">

                    <div class="form-group">
                      <label class="col-sm-2 control-label">ДЕЙСТВИЯ ПРИ СТАРТЕ МОДУЛЯ:</label>
                      <div class="col-sm-10">
                        <select name="module[default_supplier]" class="form-control">
                          <option value="no"     <?php echo $module['default_supplier']=='no'?'selected':''; ?>>Только страница настроек (рекомендуется)</option>
                          <option value="all"    <?php echo $module['default_supplier']=='all'?'selected':''; ?>  >Загрузить настройки всех поставщиков (может занять большое кол-во времени)</option>
                          <?php foreach ($suppliers_list as $session_key => $supplier) { ?>
                            <option value="<?php echo $session_key; ?>" <?php echo $module['default_supplier']==$session_key?'selected':''; ?>><?php echo $supplier; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="col-sm-2 control-label">ДОПОЛНИТЕЛЬНЫЕ МОДУЛИ:<br /><small>укажите, какие установлены дополнительные модули</small></label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input disabled type="checkbox" name="module[mcg2]" value="1" <?php echo isset($module['mcg2'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="http://opencart.zone/modules-2-0/mcg-2.html">Мультивалютные товары</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input disabled type="checkbox" name="module[zoannouncement2]" value="1" <?php echo isset($module['zoannouncement2'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="http://opencart.zone/modules-2-0/announcement-2.html">Анонсы продуктов</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input disabled type="checkbox" name="module[zotuning2]" value="1" <?php echo isset($module['zotuning2'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="http://opencart.zone/modules-2-0/tp-2-0.html">Тюнинг продуктов</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input disabled type="checkbox" name="module[costprice]" value="1" <?php echo isset($module['costprice'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://opencartforum.com/files/file/2685-costprice-%D0%B7%D0%B0%D0%BA%D1%83%D0%BF%D0%BE%D1%87%D0%BD%D0%B0%D1%8F-%D1%86%D0%B5%D0%BD%D0%B0-%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2-%D0%B2-opencart/">CostPrice - закупочная цена товаров в opencart</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[ro2]" value="1" <?php echo isset($module['ro2'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://liveopencart.ru/opencart-moduli-shablony/moduli/prochee/svyazannyie-optsii-2">liveopencart: Связанные опции</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[io2]" value="1" <?php echo isset($module['io2'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://liveopencart.ru/opencart-moduli-shablony/moduli/prochee/rasshirennyie-optsii-2">liveopencart: Расширенные опции</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[poip]" value="1" <?php echo isset($module['poip'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://liveopencart.ru/opencart-moduli-shablony/moduli/vneshniy-vid/izobrajeniya-optsiy-pro-2">liveopencart: Изображения опций PRO</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input disabled type="checkbox" name="module[valuta_plus]" value="1" <?php echo isset($module['valuta_plus'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://opencartforum.com/files/file/1645-valyuta-plyus/">louise170: Валюта плюс (бета)</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[mf_plus]" value="1" <?php echo isset($module['mf_plus'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://opencartforum.com/files/file/3879-mega-filter-plus-powered-by-mega-filter-pro2x/">OCMegaExtensions: Mega Filter Plus</a></div>
                        </div>
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[hpmodel_links]" value="1" <?php echo isset($module['hpmodel_links'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10"><a target="_blank" href="https://opencartforum.com/files/file/7505-30x-ajax-zamena-tovara-po-modelyam-hyper-product-models-for-oc30x/">HPM - Модели товара</a></div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">КАТЕГОРИИ:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[do_category_up]" value="1" <?php echo isset($module['do_category_up'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Привязать товар ко всем вышестоящим категориям</div>
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[do_category_sort]" value="1" <?php echo isset($module['do_category_sort'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Сортировать категории по алфавиту (только для версий 1.5.5.Х и выше)</div>
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[do_category_empty]" value="1" <?php echo isset($module['do_category_empty'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Отображать в списке категории без товаров</div>
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[do_category_autocomplete]" value="1" <?php echo isset($module['do_category_autocomplete'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Использовать autocomplete вместо списка выбора категории</div>
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[do_category_unique]" value="1" <?php echo isset($module['do_category_unique'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Запретить создание одноименных категорий</div>
                           <div class="col-sm-2">
                            <input type="checkbox" name="module[do_category_seourl]" value="1" <?php echo isset($module['do_category_seourl'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Генерировать ЧПУ при создании категории</div>
                          <div class="col-sm-2">
                          </div>
                          <div class="col-sm-10">
                            <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); doCategorySeourl();" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Генерировать ЧПУ для всех категории</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">ПРОИЗВОДИТЕЛИ:</label>
                      <div class="col-sm-10">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[do_vendor_seourl]" value="1" <?php echo isset($module['do_vendor_seourl'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Генерировать ЧПУ при создании производителя
                          </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">ВИЗУАЛИЗАЦИЯ:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[progress]" value="1" <?php echo isset($module['progress'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Отображать ход процесса (не все хостинги это поддерживают!)</div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">УВЕДОМЛЕНИЯ:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[load_event_log]" value="1" <?php echo isset($module['load_event_log'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-5">Загружать</div>
                          <div class="col-sm-5">
                            <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delEvents  ('all');" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Удалить все уведомления</a>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">ЛОГ:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[load_end_log]" value="1" <?php echo isset($module['load_end_log'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Загружать только завершающие уведомления</div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">СРОК ХРАНЕНИЯ ЛОГА:</label>
                      <div class="col-sm-5">
                        <select name="module[kill_log]" class="form-control">
                          <option value="nop"     <?php echo $module['kill_log']=='nop'?'selected':''; ?>     >не ограничено</option>
                          <option value="1 HOUR"  <?php echo $module['kill_log']=='1 HOUR'?'selected':''; ?>  >1 час</option>
                          <option value="2 HOUR"  <?php echo $module['kill_log']=='2 HOUR'?'selected':''; ?>  >2 часа</option>
                          <option value="6 HOUR"  <?php echo $module['kill_log']=='6 HOUR'?'selected':''; ?>  >6 часов</option>
                          <option value="12 HOUR" <?php echo $module['kill_log']=='12 HOUR'?'selected':''; ?> >12 часов</option>
                          <option value="1 DAY"   <?php echo $module['kill_log']=='1 DAY'?'selected':''; ?>   >1 день</option>
                          <option value="2 DAY"   <?php echo $module['kill_log']=='2 DAY'?'selected':''; ?>   >2 дня</option>
                          <option value="3 DAY"   <?php echo $module['kill_log']=='3 DAY'?'selected':''; ?>   >3 дня</option>
                          <option value="5 DAY"   <?php echo $module['kill_log']=='5 DAY'?'selected':''; ?>   >5 дней</option>
                          <option value="7 DAY"   <?php echo $module['kill_log']=='7 DAY'?'selected':''; ?>   >7 дней</option>
                        </select>
                      </div>
                      <div class="col-sm-5">
                        <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delRecords ('all');" class="btn btn-primary" style="width: 100%;"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Удалить все логи</a>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">ШАГ ВИЗУАЛИЗАЦИИ:</label>
                      <div class="col-sm-10">
                        <input type="text" name="module[step]" value="<?php echo isset($module['step'])?$module['step']:'10'; ?>"  class="form-control" />
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">ЗАДЕРЖКИ:</label>
                      <div class="col-sm-10">
                          <input type="text" name="module[sleep]" value="<?php echo isset($module['sleep'])?$module['sleep']:'100'; ?>"  class="form-control" />
                      </div>
                    </div>
                    <div class="form-group" style="display:none;">
                      <label class="col-sm-2 control-label">HTTP порт:</label>
                      <div class="col-sm-10">
                          <input type="text" name="module[http_port]" value="<?php echo isset($module['http_port'])?$module['http_port']:''; ?>"  class="form-control" />
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">КЛЮЧ АПИ ЯНДЕКС ПЕРЕВОДЧИКА:</label>
                      <div class="col-sm-8">
                          <input type="text" name="module[ya_translate]" value="<?php echo isset($module['ya_translate'])?$module['ya_translate']:''; ?>"  class="form-control" />
                      </div>
                      <div class="col-sm-2">
                        <a target="_blank" class="btn btn-primary" href="https://tech.yandex.ru/keys/get/?service=trnsl">Получить ключ!</a>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">Системное боковое меню:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[hide_system_menu]" value="1"  <?php echo isset($module['hide_system_menu'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">отключить для увеличения рабочей области</div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">SSL:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[SSL]" value="1" <?php echo isset($module['SSL'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Использовать SSL</div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">DEBUG:</label>
                      <div class="col-sm-10">
                        <div class="col-sm-12">
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[DEBUG]" value="1" <?php echo isset($module['DEBUG'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">Включить отладчик</div>
                          <div class="col-sm-2">
                            <input type="checkbox" name="module[HARD_DEBUG]" value="1" <?php echo isset($module['HARD_DEBUG'])?'checked':''; ?> class="form-control" />
                          </div>
                          <div class="col-sm-10">HARD_DEBUG!</div>
                        </div>
                      </div>
                    </div>
                    <?php if (isset($module['HARD_DEBUG'])) { ?>
                    <div class="form-group">        
                      <label class="col-sm-2 control-label">PHP:</label>
                      <div class="col-sm-10">
                        <?php echo phpinfo(); ?>
                      </div>
                    </div>
                    <?php } ?>
                  </div>

                  <div class="tab-pane" id="tab-module-add">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">ВЫБЕРИТЕ ТИП ПОСТАВЩИКА ИЗ СПИСКА:</label>
                      <div class="col-sm-8">
                        <select onchange="$('.any_feed').hide(); $('.add_data_for_' + $(this).val() ).show();" class="form-control">
                            <option value="">-- СПИСОК УСТАНОВЛЕННЫХ ПОСТАВЩИКОВ -- </option>
                          <?php foreach ($extensions as $key => $extension) { ?>
                            <option value="<?php echo $key; ?>"><?php echo $extension['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <?php foreach ($extensions as $key => $extension) { ?>
                      <div class="any_feed add_data_for_<?php echo $key; ?>">

                        <div class="form-group">
                          <label class="col-sm-4 control-label">ИМЯ:</label>
                          <div class="col-sm-8">
                            <input type="text" name="add[<?php echo $key; ?>][name]" value="<?php echo $extension['name']; ?>" class="form-control" />
                          </div>
                        </div>
                          
                      <?php if ($extension['need_path']=='yes') { ?>
                        <div class="form-group">
                          <label class="col-sm-4 control-label">URL:</label>
                          <div class="col-sm-8">
                            <input type="text" name="add[<?php echo $key; ?>][url]" value="" class="form-control" />
                          </div>
                        </div>
                      <?php } else { ?>
                            <input type="hidden" name="add[<?php echo $key; ?>][url]" value="" class="form-control" />
                      <?php } ?>
  
                      <?php if ($extension['need_key']=='yes') { ?>
                        <div class="form-group">
                          <label class="col-sm-4 control-label">ЛИЦЕНЗИЯ (дополнительные параметры):</label>
                          <div class="col-sm-8">
                            <input type="text" name="add[<?php echo $key; ?>][key]" value="" class="form-control" />
                          </div>
                        </div>
                      <?php } else { ?>
                            <input type="hidden" name="add[<?php echo $key; ?>][key]" value="" class="form-control" />
                      <?php } ?>
  
                        <div class="form-group">
                          <div class="col-sm-4"></div>
                          <div class="col-sm-8">
                            <?php if ($extension['disabled']=='no') { ?>
                              <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); addFeed ('<?php echo $key; ?>');" class="btn btn-primary"><i class="fa fa fa-plus"></i>&nbsp;&nbsp;ДОБАВИТЬ</a>
                            <?php } else { ?>
                              <a onclick="" class="btn btn-primary"><i class="fa fa fa-plus"></i>&nbsp;&nbsp;ДОБАВИТЬ НЕВОЗМОЖНО (РАЗРАБОТКА НЕ ЗАВЕРШЕНА)</a>
                            <?php } ?>
                          </div>
                        </div>
                          

                      </div>
                    <?php } ?>

                  </div>

                  <div class="tab-pane" id="tab-module-log">
                    <div class="form-group">
                      <div class="col-sm-2">ТИП СОБЫТИЯ</div>
                      <div class="col-sm-2">ПОЛЬЗОВАТЕЛЬ</div>
                      <div class="col-sm-2">ВРЕМЯ СОБЫТИЯ</div>
                      <div class="col-sm-4">ДАННЫЕ</div>
                      <div class="col-sm-2">
                        <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delRecords ('0');" class="btn btn-primary"><i class="fa fa fa-minus"></i>&nbsp;&nbsp;ОЧИСТИТЬ ЛОГ</a>
                      </div>
                    </div>

                    <?php if (isset($system_log)) foreach ($system_log as $id => $record) { ?>
                      <div class="form-group">
                        <div class="col-sm-2">
                          <p class="form-control"><?php echo $record['type']; ?></p>
                        </div>
                        <div class="col-sm-2">
                          <p class="form-control"><?php echo $record['user']; ?></p>
                        </div>
                        <div class="col-sm-2">
                          <p class="form-control"><?php echo $record['time']; ?></p>
                        </div>
                        <div class="col-sm-4">
                          <p class="form-control" style="height: initial;"><?php echo $record['data']; ?></p>
                        </div>
                        <div class="col-sm-2">
                          <a onclick="$(this).children('i').addClass('fa-spinner').addClass('fa-spin'); delRecord (<?php echo $id; ?>);" class="btn btn-primary"><i class="fa fa fa-minus"></i>&nbsp;&nbsp;Удалить</a>
                        </div>
                      </div>
                    <?php } ?>

                  </div>

                </div>


              </div>

            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
var row = <?php echo $row; ?>;
$('#suppliers a:first').tab('show');  

function delFeed (supplier) {
var pData = {supplier:supplier};
var url = 'index.php?route=extension/module/zoxml2/delfeed&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function addFeed (extension) {
var pData = $('input[type=\'text\'][name*=\'add[' + extension + ']\'], input[type=\'hidden\'][name*=\'add[' + extension + ']\']');
var url = 'index.php?route=extension/module/zoxml2/addfeed&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function saveModule () {
var pData = $('select[name*=\'module\'], input[type=\'radio\'][name*=\'module\'], input[type=\'text\'][name*=\'module\'], input[type=\'hidden\'][name*=\'module\'], input[type=\'checkbox\'][name*=\'module\']:checked');
var url = 'index.php?route=extension/module/zoxml2/module&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function addReplace (session_key) {
var pData = { supplier    :session_key,
              type        :$('select[name=\'supplier[' + session_key + '][replace][new][type]\']').val(),
              mode        :$('select[name=\'supplier[' + session_key + '][replace][new][mode]\']').val(),
              txt_before  :$('textarea[name=\'supplier[' + session_key + '][replace][new][txt_before]\']').val(),
              txt_after   :$('textarea[name=\'supplier[' + session_key + '][replace][new][txt_after]\']').val(),
              sort_order  :$('input[type=\'text\'][name=\'supplier[' + session_key + '][replace][new][sort_order]\']').val()
              };
var url = 'index.php?route=extension/module/zoxml2/addreplace&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function delReplaces (session_key) {
var pData = { supplier    :session_key};
var url = 'index.php?route=extension/module/zoxml2/delreplaces&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function delReplace (id) {
var pData = { id    :id};
var url = 'index.php?route=extension/module/zoxml2/delreplace&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function saveSupplier (session_key,toxml=0) {
var pData = $('textarea[name*=\'supplier[' + session_key + ']\'], select[name*=\'supplier[' + session_key + ']\'], input[type=\'radio\'][name*=\'supplier[' + session_key + ']\'], input[type=\'text\'][name*=\'supplier[' + session_key + ']\'], input[type=\'hidden\'][name*=\'supplier[' + session_key + ']\'], input[type=\'checkbox\'][name*=\'supplier[' + session_key + ']\']:checked');
if (toxml) {
  var url = 'index.php?route=extension/module/zoxml2/settings2xml&user_token=<?php echo $user_token; ?>';
  $.ajax({
  type:'post',
  url: url,
  data: pData,
  success: function(txt) { 
    if (txt) alert("Сохранено в:\r\n" + txt);
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    location = 'index.php?route=module/zoxml2&user_token=<?php echo $user_token; ?>';
    }
    });
  }
else {
  var url = 'index.php?route=extension/module/zoxml2/settings&user_token=<?php echo $user_token; ?>';
  $.ajax({
  type:'post',
  url: url,
  data: pData,
  success: function() { 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
    }
    });
  }
}

function delRecord (id) {
var pData = {id:id};
var url = 'index.php?route=extension/module/zoxml2/delrecord&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function delRecords (supplier) {
var pData = {supplier:supplier};
var url = 'index.php?route=extension/module/zoxml2/delrecords&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function delEvent (id) {
var pData = {id:id};
var url = 'index.php?route=extension/module/zoxml2/delevent&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function delEvents (supplier) {
var pData = {supplier:supplier};
var url = 'index.php?route=extension/module/zoxml2/delevents&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function scanSupplier (session_key,start_id) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/scansupplier&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
<?php if (!isset($module['progress'])) { ?>
  success: function() { 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
    },
<?php } ?>
data: pData
  });
<?php if (isset($module['progress'])) { ?>
  html =  '<div class="form-group">';

  html += '<div class="col-sm-12">';            
  html += '<p class="form-control" style="background-color: lightgreen;text-align: center;">Загрузка запущена в фоновом режиме. Вы можете покинуть эту страницу или дождаться окончания загрузки.</p>';            
  html += '</div>';            

  html += '</div>';            
  $('#log_progress_' + session_key).after(html);
  doLog (session_key,start_id);
<?php } ?>
}

function linkSupplier (session_key,start_id) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/linksupplier&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
<?php if (!isset($module['progress'])) { ?>
  success: function() { 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
    },
<?php } ?>
data: pData
  });
<?php if (isset($module['progress'])) { ?>
  html =  '<div class="form-group">';

  html += '<div class="col-sm-12">';            
  html += '<p class="form-control" style="background-color: lightgreen;text-align: center;">Загрузка запущена в фоновом режиме. Вы можете покинуть эту страницу или дождаться окончания загрузки.</p>';            
  html += '</div>';            

  html += '</div>';            
  $('#log_progress_' + session_key).after(html);
  doLog (session_key,start_id);
<?php } ?>
}

function addAllVendors (session_key) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/addallvendors&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function doCategorySeourl() {
var url = 'index.php?route=extension/module/zoxml2/docategoryseourl&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
  }
  
function addAllCategories (session_key) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/addallcategories&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function lostCategories (session_key) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/lostcategories&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function killCategories (session_key) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/killcategories&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function() { 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}
function doLog (supplier,start_id) {
var pData = {supplier:supplier,start_id:start_id};
var url = 'index.php?route=extension/module/zoxml2/progress&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
data: pData,
success: function(json) { 
  for (var i in json) {
    start_id = json[i]['id'];
    html =  '<div class="form-group">';

    html += '<div class="col-sm-2">';            
    html += '<p class="form-control">' + json[i]['type'] + '</p>';            
    html += '</div>';            

    html += '<div class="col-sm-2">';            
    html += '<p class="form-control">' + json[i]['user'] + '</p>';            
    html += '</div>';            

    html += '<div class="col-sm-2">';            
    html += '<p class="form-control">' + json[i]['time'] + '</p>';            
    html += '</div>';            

    html += '<div class="col-sm-6">';            
    html += '<p class="form-control" style="height: initial;">' + json[i]['data'] + '</p>';            
    html += '</div>';            

    html += '</div>';            
    $('#log_progress_' + supplier).after(html);
    if (json[i]['type']=='end') location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
    }
  setTimeout(
    function(){
      doLog (supplier,start_id);
      },
    2000);
  },
error: function(xhr, ajaxOptions, thrownError) { 
  alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
  location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
  }
  });
}

function loadSupplier (session_key,start_id) {
var pData = {supplier:session_key};
var url = 'index.php?route=extension/module/zoxml2/loadsupplier&user_token=<?php echo $user_token; ?>';
$.ajax({
type:'post',
url: url,
<?php if (!isset($module['progress'])) { ?>
  success: function() { 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>'; 
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    location = 'index.php?route=extension/module/zoxml2&user_token=<?php echo $user_token; ?>';
    },
<?php } ?>
data: pData
  });
<?php if (isset($module['progress'])) { ?>
  html =  '<div class="form-group">';

  html += '<div class="col-sm-12">';            
  html += '<p class="form-control" style="background-color: lightgreen;text-align: center;">Загрузка запущена в фоновом режиме. Вы можете покинуть эту страницу или дождаться окончания загрузки.</p>';            
  html += '</div>';            

  html += '</div>';            
  $('#log_progress_' + session_key).after(html);
  doLog (session_key,start_id);
<?php } ?>
}

// -----------------------------------------------------------------------------
function loadVendorPage(page,session_key) {
  var id = "#vendor_page_" + session_key; 
  html  = '<div id="vendor_page_' + session_key + '"><i class="fa"></i>&nbsp;&nbsp;загрузка...</div>';
  $(id).replaceWith(html);
  $(id).children('i').addClass('fa-spinner').addClass('fa-spin');
  
  if (page=='nop') {
    html  = '<div id="vendor_page_' + session_key + '"></div>';
    $(id).replaceWith(html);
    return;
    }   

  var pData = {supplier:session_key};
  if (page=='all') pData = {supplier:session_key,page:0};
  else             pData = {supplier:session_key,page:page,limit:100};

  var url = 'index.php?route=extension/module/zoxml2/loadvendorpage&user_token=<?php echo $user_token; ?>';
  $.ajax({
  type:'post',
  url: url,
  data: pData,
  success: function(json) { 
    var vendor = json['vendor'];
    var manufacturers = json['manufacturers'];
    html  = '<div id="vendor_page_' + session_key + '">';
    for (var i in vendor) {
      html += '<div class="form-group">';
      html += '<div class="col-sm-3">';
        html += '<p class="form-control" style="height: initial;">';
        html += vendor[i]['name'];
        html += '</p>';
      html += '</div>';
      html += '<div class="col-sm-1">';
        html += '<p class="form-control">' + vendor[i]['total'] + '</p>';
      html += '</div>';
      html += '<div class="col-sm-2">';
        html += '<textarea rows="1" name="supplier[' + session_key + '][vendors][' + vendor[i]['id'] + '][margin]" class="form-control">' + vendor[i]['margin'] + '</textarea>';
      html += '</div>';
                      
      html += '<div class="col-sm-6">';
        html += '<select name="supplier[' + session_key + '][vendors][' + vendor[i]['id'] + '][manufacturer_id]" class="form-control">';
        for (var key in manufacturers) {
          if (manufacturers[key]['id']!=vendor[i]['manufacturer_id']) html += '<option value="' + manufacturers[key]['id'] + '">' + manufacturers[key]['name']  + '</option>';
          else html += '<option selected value="' + manufacturers[key]['id'] + '">' + manufacturers[key]['name']  + '</option>';
          }
        html += '</select>';
      html += '</div>';

      html += '</div>';
      }
    html += '</div>';
    $(id).replaceWith(html);
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    }
    });

}
//------------------------------------------------------------------------------
var all_categories = <?php echo $all_categories; ?>;
function loadCategoryPage(page,session_key,do_category_autocomplete) {
  var id = "#categories_page_" + session_key; 
  html  = '<div id="categories_page_' + session_key + '"><i class="fa"></i>&nbsp;&nbsp;загрузка...';
  html += '</div>';
  $(id).replaceWith(html);
  $(id).children('i').addClass('fa-spinner').addClass('fa-spin');
  
  if (page=='nop') {
    html  = '<div id="categories_page_' + session_key + '">';
    html += '</div>';
    $(id).replaceWith(html);
    return;
    }   

  var pData = {supplier:session_key};
  if (page=='all') pData = {supplier:session_key,do_category_empty:<?php echo isset($module['do_category_empty'])?1:'0'; ?>,page:0};
  else             pData = {supplier:session_key,do_category_empty:<?php echo isset($module['do_category_empty'])?1:'0'; ?>,page:page,limit:100};

  var url = 'index.php?route=extension/module/zoxml2/loadcategorypage&user_token=<?php echo $user_token; ?>';
  $.ajax({
  type:'post',
  url: url,
  data: pData,
  success: function(category) { 
    html  = '<div id="categories_page_' + session_key + '">';
    for (var i in category) {
      html += '<div class="form-group">';
      html += '<div class="col-sm-5">';
      html += '<p class="form-control" style="height: initial;">';
      if (category[i]['parent'])  html += '(' + category[i]['parent'] + ' >)'; 
                                  html += category[i]['name'];
      if (category[i]['data'])    html += ' (id=' + category[i]['data'] + ')'; 
      html += '</p>';
      html += '</div>';
      html += '<div class="col-sm-1">';
      html += '<p class="form-control">' + category[i]['total'] + '</p>';
      html += '</div>';
      html += '<div class="col-sm-1">';
        html += '<textarea rows="1" name="supplier[' + session_key + '][categories][' + category[i]['id'] + '][margin]" class="form-control">' + category[i]['margin'] + '</textarea>';
      html += '</div>';
      html += '<div class="col-sm-5">';

      var tmp_name = 'supplier[' + session_key + '][categories][' + category[i]['id'] + '][category_id]';        
      if (!do_category_autocomplete) {
          html += '<select name="' + tmp_name + '" class="form-control">';
      
          for (var j in all_categories) {
            if (all_categories[j]['category_id']!=category[i]['category_id']) html += '<option value="' + all_categories[j]['category_id'] +'">' + all_categories[j]['name'] +'</option>';
            else                                                              html += '<option value="' + all_categories[j]['category_id'] +'" selected>' + all_categories[j]['name'] +'</option>';
            }
          html += '</select>';
          }
      else {
        html += '<i class="fa fa-minus-circle" style="float:right;margin-top:5px;font-size:16px;" data-toggle="tooltip" title="Отключить категорию" onclick="$(this).next().val(0);$(this).next().next().val(\'\');$(this).next().next().attr(\'placeholder\', \'Категория отключена\');"></i> '; 
        var tmp_show_name = 'category_name[' + session_key + '][' + category[i]['id'] + ']';        
        if (category[i]['category_id']<1 || all_categories[category[i]['category_id']]===undefined) {
          html += '<input type="hidden" name="' + tmp_name + '" value="0" />'; 
          html += '<input style="float:left;width:95%;" type="text" placeholder="Категория отключена" name="' + tmp_show_name + '" value="" class="form-control" />'; 
          }
        else {
          html += '<input type="hidden" name="' + tmp_name + '" value="' + category[i]['category_id'] + '" />'; 
          html += '<input style="float:left;width:95%;" type="text" placeholder="' + all_categories[category[i]['category_id']] + '" name="' + tmp_show_name + '" value="" class="form-control" />'; 
          }
        }

      html += '</div>';
      html += '</div>';
      }
    html += '</div>';
    $(id).replaceWith(html);
    if (do_category_autocomplete) {
      $('input[name*=\'category_name[' + session_key + ']\']').autocomplete({
      	'source': function(request, response) {
      		$.ajax({
      			url: 'index.php?route=catalog/category/autocomplete&user_token=<?php echo $user_token; ?>&filter_name=' +  encodeURIComponent(request),
      			dataType: 'json',
      			success: function(json) {
      				response($.map(json, function(item) {
      					return {
      						label: item['name'],
      						value: item['category_id']
      					}
      				}));
      			}
      		});
      	},
      	'select': function(item) {
      		$(this).prev().val(item['value']);
      		$(this).val('');
      		$(this).attr("placeholder", item['label']);  
      	}
      });
      }    
    },
  error: function(xhr, ajaxOptions, thrownError) { 
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); 
    }
    });

}

$('.attributeautocomplete').autocomplete({
  'source': function(request, response) {
  	$.ajax({
			url: 'index.php?route=catalog/attribute/autocomplete&user_token=<?php echo $user_token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						category: item.attribute_group,
						label: item.name,
						value: item.attribute_id
					}
				}));
			}
		});
	},
	'select': function(item) {
  		$(this).prev().val(item['value']);
  		$(this).val('');
  		$(this).attr("placeholder", item['label']);  
	}
});

//--></script>
</div>
<?php echo $footer; ?>