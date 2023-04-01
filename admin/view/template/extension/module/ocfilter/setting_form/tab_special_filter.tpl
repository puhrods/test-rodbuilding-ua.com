<div role="tabs">
  <div class="row">
    <div class="col-md-3">
      <div class="jumbotron" style="padding: 15px;">
        <ul class="nav nav-pills nav-stacked" role="tablist">
          <li class="active"><a href="#tab-special-price" data-toggle="tab"><i class="fa fa-fw fa-usd"></i> <?php echo $tab_price; ?></a></li>
          <li><a href="#tab-special-manufacturer" data-toggle="tab"><i class="fa fa-fw fa-industry"></i> <?php echo $tab_manufacturer; ?></a></li>
          <li new-feature><a href="#tab-special-discount" data-toggle="tab"><i class="fa fa-fw fa-certificate"></i> <?php echo $tab_discount; ?></a></li>
          <li new-feature><a href="#tab-special-newest" data-toggle="tab"><i class="fa fa-fw fa-flash"></i> <?php echo $tab_newest; ?></a></li>
          <li new-feature><a href="#tab-special-dimension" data-toggle="tab"><i class="fa fa-fw fa-arrows-alt"></i> <?php echo $tab_dimension; ?></a></li>
          <li><a href="#tab-special-stock" data-toggle="tab"><i class="fa fa-fw fa-cubes"></i> <?php echo $tab_stock; ?></a></li>        
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab-special-price">
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_price; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price', $special_price); ?>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-3 control-label" new-feature for="input-price-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-6">
              <?php $tpl_sort_order('special_price_sort_order', $special_price_sort_order); ?>
            </div>
          </div>          
          
          <div class="form-group">
            <label class="col-sm-3 control-label" new-feature><?php echo $entry_special_price_logarithmic; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price_logarithmic', $special_price_logarithmic); ?>                           
            </div>
          </div>          
          
          <div class="form-group">
            <label class="col-sm-3 control-label" new-feature><?php echo $entry_consider_tax; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price_consider_tax', $special_price_consider_tax, 'y/n'); ?>

              <p class="help-block"><?php echo $help_consider_tax; ?></p>
            </div>
          </div>

          <h3 class="jumbotron"><?php echo $nav_price_source; ?></h3>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_price_consider_regular_price; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price_consider_regular_price', $special_price_consider_regular_price, 'y/n'); ?>

              <p class="help-block"><?php echo $help_special_price_consider_regular_price; ?></p>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_price_consider_discount; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price_consider_discount', $special_price_consider_discount, 'y/n'); ?>

              <p class="help-block"><?php echo $help_special_price_consider_discount; ?></p>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_price_consider_special; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price_consider_special', $special_price_consider_special, 'y/n'); ?>

              <p class="help-block"><?php echo $help_special_price_consider_special; ?></p>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_price_consider_option; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_price_consider_option', $special_price_consider_option, 'y/n'); ?>

              <p class="help-block"><?php echo $help_special_price_consider_option; ?></p>
            </div>
          </div>          
        </div><!-- /.price -->
        
        <div role="tabpanel" class="tab-pane" id="tab-special-manufacturer">
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_manufacturer; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_manufacturer', $special_manufacturer, 'e/d'); ?>
              <p class="help-block"><?php echo $help_special_manufacturer; ?></p>      
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-3 control-label" new-feature for="input-manufacturer-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-6">
              <?php $tpl_sort_order('special_manufacturer_sort_order', $special_manufacturer_sort_order); ?>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-manufacturer-type"><?php echo $entry_type; ?></label>
            <div class="col-sm-3">
              <select name="special_manufacturer_type" id="input-manufacturer-type" class="form-control">
                <?php foreach ($types as $type) { ?>
                <?php if ($type == $special_manufacturer_type) { ?>
                <option value="<?php echo $type; ?>" selected="selected"><?php echo ucfirst($type); ?></option>
                <?php } else { ?>
                <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label" new-feature><?php echo $entry_special_manufacturer_dropdown; ?></label>
            <div class="col-sm-5">
              <?php $tpl_bool_button('special_manufacturer_dropdown', $special_manufacturer_dropdown); ?>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-3 control-label" new-feature><?php echo $entry_special_manufacturer_image; ?></label>
            <div class="col-sm-5">
              <?php $tpl_bool_button('special_manufacturer_image', $special_manufacturer_image, 'y/n'); ?>
            </div>
          </div>
        </div><!-- /.manufacturer -->
        
        <div role="tabpanel" class="tab-pane" id="tab-special-discount">
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_discount; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_discount', $special_discount, 'e/d'); ?>
              <p class="help-block"><?php echo $help_special_discount; ?></p>      
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-discount-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-6">
              <?php $tpl_sort_order('special_discount_sort_order', $special_discount_sort_order); ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_discount_consider_special; ?></label>
            <div class="col-sm-5">
              <?php $tpl_bool_button('special_discount_consider_special', $special_discount_consider_special, 'y/n'); ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_discount_consider_discount; ?></label>
            <div class="col-sm-5">
              <?php $tpl_bool_button('special_discount_consider_discount', $special_discount_consider_discount, 'y/n'); ?>
            </div>
          </div>
        </div><!-- /.discount -->   

        <div role="tabpanel" class="tab-pane" id="tab-special-newest">
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_newest; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_newest', $special_newest, 'e/d'); ?>
              
              <p class="help-block"><?php echo $help_special_newest; ?></p>      
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-newest-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-6">
              <?php $tpl_sort_order('special_newest_sort_order', $special_newest_sort_order); ?>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_newest_interval; ?></label>
            <div class="col-sm-3">
              <div class="input-group">
                <input type="number" min="1" name="special_newest_interval" value="<?php echo $special_newest_interval; ?>" class="form-control" />
                <div class="input-group-btn">
                  <select name="special_newest_period" class="btn btn-default" data-selected="<?php echo $special_newest_period; ?>">
                    <option value="hour"><?php echo $text_special_newest_interval_hour; ?></option>
                    <option value="day"><?php echo $text_special_newest_interval_day; ?></option>
                    <option value="week"><?php echo $text_special_newest_interval_week; ?></option>
                    <option value="month"><?php echo $text_special_newest_interval_month; ?></option>
                  </select>
                </div>
              </div>                          
            </div>
            <div class="col-sm-offset-3 col-sm-9">
              <p class="help-block"><?php echo $help_special_newest_interval; ?></p>
            </div>
          </div>
        </div><!-- /.newest -->           
        
        <div role="tabpanel" class="tab-pane" id="tab-special-dimension">
          <div class="row v-border">
            <div class="col-sm-6">
              <div class="form-group-vertical">
                <label><?php echo $entry_special_weight; ?></label>
                <div>
                  <?php $tpl_bool_button('special_weight', $special_weight, 'e/d'); ?>
                </div>
              </div>
              <div class="form-group-vertical">
                <label><?php echo $entry_sort_order; ?></label>
                <div>
                  <?php $tpl_sort_order('special_weight_sort_order', $special_weight_sort_order); ?>
                </div>
              </div>              
            </div>
            <div class="col-sm-6">
              <div class="form-group-vertical">
                <label><?php echo $entry_special_length; ?></label>
                <div>
                  <?php $tpl_bool_button('special_length', $special_length, 'e/d'); ?>
                </div>
              </div>
              <div class="form-group-vertical">
                <label><?php echo $entry_sort_order; ?></label>
                <div>
                  <?php $tpl_sort_order('special_length_sort_order', $special_length_sort_order); ?>
                </div>
              </div> 
            </div>
          </div>
          <hr />
          <div class="row v-border">
            <div class="col-sm-6">
              <div class="form-group-vertical">
                <label><?php echo $entry_special_width; ?></label>
                <div>
                  <?php $tpl_bool_button('special_width', $special_width, 'e/d'); ?>
                </div>
              </div>
              <div class="form-group-vertical">
                <label><?php echo $entry_sort_order; ?></label>
                <div>
                  <?php $tpl_sort_order('special_width_sort_order', $special_width_sort_order); ?>
                </div>
              </div> 
            </div>
            <div class="col-sm-6">
              <div class="form-group-vertical">
                <label><?php echo $entry_special_height; ?></label>
                <div>
                  <?php $tpl_bool_button('special_height', $special_height, 'e/d'); ?>
                </div>
              </div>
              <div class="form-group-vertical">
                <label><?php echo $entry_sort_order; ?></label>
                <div>
                  <?php $tpl_sort_order('special_height_sort_order', $special_height_sort_order); ?>                
                </div>
              </div> 
            </div>            
          </div>
        </div><!-- /.dimension -->           
        
        <div role="tabpanel" class="tab-pane" id="tab-special-stock">
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_stock; ?></label>
            <div class="col-sm-9">
              <?php $tpl_bool_button('special_stock', $special_stock, 'e/d'); ?>

              <p class="help-block"><?php echo $help_special_stock; ?></p>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo $entry_special_stock_method; ?></label>
            <div class="col-sm-9">
              <div class="btn-group" data-toggle="buttons">
                <?php if ($special_stock_method == 'quantity') { ?>
                <label class="btn btn-default active" data-trigger="onclick" onclick="$('.collapse-group-1').collapse('hide').filter('#stock-status-quantity').collapse('show')">
                  <input type="radio" name="special_stock_method" value="quantity" checked="checked" /> <?php echo $text_special_stock_method_by_quantity; ?>
                </label>
                <label class="btn btn-default" onclick="$('.collapse-group-1').collapse('hide').filter('#stock-status-id').collapse('show')">
                  <input type="radio" name="special_stock_method" value="stock_status_id" /> <?php echo $text_special_stock_method_by_status_id; ?>
                </label>
                <?php } else { ?>
                <label class="btn btn-default" onclick="$('.collapse-group-1').collapse('hide').filter('#stock-status-quantity').collapse('show')">
                  <input type="radio" name="special_stock_method" value="quantity" /> <?php echo $text_special_stock_method_by_quantity; ?>
                </label>
                <label class="btn btn-default active" data-trigger="onclick" onclick="$('.collapse-group-1').collapse('hide').filter('#stock-status-id').collapse('show')">
                  <input type="radio" name="special_stock_method" value="stock_status_id" checked="checked" /> <?php echo $text_special_stock_method_by_status_id; ?>
                </label>
                <?php } ?>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-stock-status-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-6">
              <?php $tpl_sort_order('special_stock_sort_order', $special_stock_sort_order); ?>
            </div>
          </div>          

          <div class="collapse collapse-group-1" id="stock-status-id">
            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-stocks-tatus-type"><?php echo $entry_type; ?></label>
              <div class="col-sm-3">
                <select name="special_stock_type" id="input-stocks-tatus-type" class="form-control">
                  <?php foreach ($types as $type) { ?>
                  <?php if ($type == $special_stock_type) { ?>
                  <option value="<?php echo $type; ?>" selected="selected"><?php echo ucfirst($type); ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

          <div class="collapse collapse-group-1" id="stock-status-quantity">
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $entry_special_stock_out_value; ?></label>
              <div class="col-sm-9">
                <?php $tpl_bool_button('special_stock_out_value', $special_stock_out_value, 'y/n'); ?>
              </div>
            </div>
          </div>        
        </div><!-- /.stock -->   
      </div>
    </div>
  </div>
</div>