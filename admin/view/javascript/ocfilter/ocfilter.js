+function(global, $) {
  'use strict';
  
  var SUMMERNOTE = {
    placeholder: '',
    emptyPara: '',
    lang: 'en-US',
    tabsize: 2,
    height: 200,
    disableDragAndDrop: false,
    icons: {
      caret: 'fa fa-caret-down',
      magic: 'fa fa-magic',
      bold: 'fa fa-bold',
      underline: 'fa fa-underline',
      eraser: 'fa fa-eraser',
      font: 'fa fa-font',
      unorderedlist: 'fa fa-list-ul',
      orderedlist: 'fa fa-list-ol',
      alignLeft: 'fa fa-align-left',
      alignRight: 'fa fa-align-right',
      alignCenter: 'fa fa-align-center',
      alignJustify: 'fa fa-align-justify',
      indent: 'fa fa-indent',
      outdent: 'fa fa-outdent',
      table: 'fa fa-table',
      link: 'fa fa-link',
      video: 'fa fa-video-camera',
      arrowsAlt: 'fa fa-arrows-alt',
      code: 'fa fa-code',
      question: 'fa fa-info-circle',
    },
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'clear']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['table', ['table']],
      ['insert', ['link', 'image', 'video']],
      ['view', ['fullscreen', 'codeview', 'help']]
    ],
    buttons: {
      image: function(context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fa fa-picture-o"></i>',
          tooltip: $.summernote.lang[$.summernote.options.lang].image.image,
          click: function () {
            $('#modal-image').remove();
          
            $.ajax({
              url: ocfilter.link('common/filemanager'),
              dataType: 'html',
              beforeSend: function() {
                $('#button-image').prop('disabled', true).find('i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
              },
              complete: function() {
                $('#button-image').prop('disabled', false).find('i').replaceWith('<i class="fa fa-upload"></i>');
              },
              success: function(html) {
                $('body').append('<div id="modal-image" class="modal">' + html + '</div>');
                
                $('#modal-image').modal('show');
                
                $('#modal-image').delegate('a.thumbnail', 'click', function(e) {
                  e.preventDefault();
                  
                  $(context.$note).summernote('insertImage', $(this).attr('href'));
                                
                  $('#modal-image').modal('hide');
                });
              }
            });            
          }
        });
      
        return button.render();
      }
    }  
  };
   
  var _filters = $.expr[":"];
  
  if (!_filters.focus) { 
    _filters.focus = function(elem) {
      return elem === document.activeElement && (elem.type || elem.href);
    };
  }   
   
  var OCFilter = function() {
    var that = this;
    
    $.fn.autocomplete = this.autocomplete;
    
    document.addEventListener('DOMContentLoaded', function() {
      $(that.startup.call(that)); 
    }); 
  };
  
  OCFilter.prototype.startup = function() {      
    // Fix BS nested tabs
    $('a[data-toggle="tab"]').on('hide.bs.tab', function() {
      $($(this).attr('href')).removeClass('active');
    }).on('show.bs.tab', function() {
      $($(this).attr('href')).addClass('active');

     // $(this).closest('.nav').parent().find('.tab-content > .tab-pane').removeClass('active');
    });
    
    $('[data-toggle="popover"]').each(function() {
      var options = $.extend({}, $(this).data());
      
      if (!options.trigger) {
        options.trigger = 'hover';
      }
      
      options.html = true;
      
      options.template = '<div class="popover ocf-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>';
      
      $(this).popover(options);
    });
  
    $(document).on('click.dismiss-popover', function(e) {
      $('[data-toggle="popover"], [aria-describedby^="popover"]').each(function() {        
        //the 'is' for buttons that trigger popups
        //the 'has' for icons within a button that triggers a popup
        if (($(this).popover().data().trigger == 'click' || ($(this).data('bs.popover').filters || { trigger: '' }).trigger == 'click') && !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
          (($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false  // fix for BS 3.3.6
        }
      });
    });
    
    $(document).on('click', '[data-dismiss="popover"]', function(e) {
      $('[aria-describedby="' + $(this).closest('.popover').attr('id') + '"]').popover('hide');
    });
    
    $('[data-trigger="onclick"]').trigger('click');

    $('select[data-selected]').each(function() {
      $(this).val($(this).attr('data-selected')).trigger('change');  
    });
    
    $('[data-checked]').each(function() {
      $(this).find('input').prop('checked', false).parent('.btn').removeClass('active'); 
      
      $(this).find('input[value="' + $(this).attr('data-checked') + '"]').prop('checked', true).trigger('change').parent('.btn').addClass('active'); 
    });    

    // Indicate "on" buttons
    var markCheckedBtn = function() {
      $(this).closest('[data-toggle="buttons"]').find('.btn').removeClass('btn-info');

      if ((this.value - 0) > 0) {
        $(this).closest('.btn').addClass('btn-info');
      }
    };

    $('[data-toggle="buttons"] input[type="radio"]').on('change', markCheckedBtn).filter(':checked').each(markCheckedBtn);
   
    // Extended sort
    $('.ocf-sort-order').on('click', function(e) {
      e.stopPropagation();

      var $target = $(e.target);
           
      if ($target.is('.ocf-input-placeholder')) {
        $target = $target.prev();
        
        $('input[name="' + $target.attr('name') + '"][type="radio"]').prop('checked', false);
        
        $target.prop('disabled', false).focus();      
      } else if ($target.is('[type="radio"]')) {
        $('input[name="' + $target.attr('name') + '"][type="number"]').prop('disabled', true);
      }
    });
     
    // Get #column-left transition-duration 
    var $columnLeft = $('#column-left');
    
    if (!$columnLeft.length) {
      $columnLeft = $('#column-left-fix');
    }
    
    if ($columnLeft.length) {
      var
        columnWidth = $columnLeft.width(),
        cssDuration = $columnLeft.css('transition-duration'),
        transitionDuration = parseFloat(cssDuration.replace(/[^0-9\.]+/g, ''));

      if (/[0-9\.]+?s$/.test(cssDuration)) {
        transitionDuration *= 1000;
      }

      if (transitionDuration) {
        $columnLeft.data('transition-duration', transitionDuration);
      } else {
        function checkColumnLeft() {
          if (columnWidth != $columnLeft.width()) {
            columnWidth = $columnLeft.width();

            $columnLeft.trigger('resize');
          }

          setTimeout(checkColumnLeft, 1000);
        }

        checkColumnLeft();
      }    
    }   
    
    // BTN & Popover
    $('.form-group-popover input').on('change', function(e, isTrigger) {
      var $btn = $(this).parent(), $nav = $(this).closest('.form-group-popover');

      $nav.find('.popover').popover('hide');

      $btn.popover({
        animation: false,
        content: function() {
          return $btn.data().content
        },
        container: $nav,
        viewport: { selector: $nav, padding: 15 },
        html: true,
        placement: 'right',
        trigger: 'manual'
      }).popover('show');
    });

    var checkNavPopover = function() {
      $('.form-group-popover:visible input:checked').trigger('change', [ true ]);
    };

    $('.collapse').on('shown.bs.collapse', function() {
      if ($(this).find('.form-group-popover').length > 0) {
        checkNavPopover();
      }
    });

    $('.nav-tabs a').on('shown.bs.tab', checkNavPopover);

    if ($('#column-left').data('transition-duration')) {
      $('#column-left').on('bsTransitionEnd', checkNavPopover).emulateTransitionEnd($('#column-left').data('transition-duration'));
    } else {
      $('#column-left').on('resize', checkNavPopover);
    }

    checkNavPopover();       
     
    // New features 
    var openedTime = localStorage.getItem('ocfOpenedTime') - 0;

    if (!openedTime) {
      openedTime = new Date().getTime();

      localStorage.setItem('ocfOpenedTime', openedTime);
    }

    if ((openedTime + (24 * 60 * 60 * 1000 * 5)) > new Date().getTime()) {
      $('[new-feature]').addClass('new-feature');
    }      
    
    // Init Product Form
    var route = this.getURLParam('route');
      
    if (route.substring(0, 16) == 'catalog/product/') {
      this.initProduct();
    }        
  };
  
  OCFilter.prototype.getSummernoteOptions = function() {
    return SUMMERNOTE;
  };
  
  OCFilter.prototype.getURLParam = function(key) {
    if (this.url) {
      return 'undefined' != typeof this.url[key] && this.url[key];
    } else {
      this.url = {};
    }

    var urlQueryParts = [], urlPart;    
    
    if (global.location.search) {
      urlQueryParts = global.location.search.substring(1).split('&');
      
      for (var i = 0, len = urlQueryParts.length; i < len; i++) {
        urlPart = urlQueryParts[i].split('=');
        
        if (urlPart[0] && urlPart[1]) {
          this.url[urlPart[0]] = urlPart[1];
        }
      }
    }
    
    return this.getURLParam(key);
  };
  
  OCFilter.prototype.link = function(route, params) {
    var url = '';
    
    url += 'index.php?route=' + route;
    
    if (this.getURLParam('user_token')) {
      url += '&user_token=' + this.getURLParam('user_token');
    } else if (this.getURLParam('token')) {
      url += '&token=' + this.getURLParam('token');
    }    
    
    if (params) {
      if ($.isPlainObject(params)) {
        for (var i in params) {
          if (params.hasOwnProperty(i)) {
            url += '&' + i + '=' + encodeURIComponent(params[i]); 
          }
        }
      } else {
        url += '&' + params.replace(/(^[&?])|([&?]$)/g, ''); 
      }      
    }
    
    return url;
  };
  
  // https://www.sanwebe.com/2014/04/select-all-text-in-element-on-click#:~:text=To%20select%20all%20text%20inside,the%20range%20of%20the%20element.  
  OCFilter.prototype.selectText = function(el) {
    var sel, range;

    if (window.getSelection && document.createRange) { //Browser compatibility
      sel = window.getSelection();
      
      if (sel.toString() == '') { //no text selection
        window.setTimeout(function() {
          range = document.createRange(); //range object
          range.selectNodeContents(el); //sets Range
          sel.removeAllRanges(); //remove all ranges from selection
          sel.addRange(range);//add Range to a Selection.
        }, 1);
      }
    } else if (document.selection) { //older ie
      sel = document.selection.createRange();
      
      if (sel.text == '') { //no text selection
        range = document.body.createTextRange();//Creates TextRange object
        range.moveToElementText(el);//sets Range
        range.select(); //make selection.
      }
    }
  };
  
  OCFilter.prototype.initProduct = function() {
    var that = this;

    $('a[href="#tab-links"]').parent().after('<li><a href="#tab-ocfilter" data-toggle="tab"><i class="fa fa-sliders" aria-hidden="true"></i> OCFilter</a></li>');
    
    $('#tab-links').after('<div class="tab-pane" id="tab-ocfilter"></div>');    
    
    $('a[href="#tab-general"]').tab('show');    
    
    $('[form="form-product"][type="submit"]').on('click', function(e) {
      if ('localStorage' in window) {
        var ocfilter_product_filter = {}, filter_key;
        
        $('[name^="ocfilter_filter"]').serializeArray().forEach(function(item) {
          filter_key = item.name.match(/^ocfilter_filter\[(.+?)\]/)[1];
          
          if ('undefined' == typeof ocfilter_product_filter[filter_key] && /\[(min|max)\]$/.test(item.name)) {
            ocfilter_product_filter[filter_key] = {};            
          }
          
          if (/\[min\]$/.test(item.name)) {
            ocfilter_product_filter[filter_key].min = item.value;
          } else if (/\[max\]$/.test(item.name)) {
            ocfilter_product_filter[filter_key].max = item.value;                   
          } else {
            if ('undefined' == typeof ocfilter_product_filter[filter_key]) {
              ocfilter_product_filter[filter_key] = [ item.value ];
            } else {
              ocfilter_product_filter[filter_key].push(item.value);
            }
          }
        });                              
               
        localStorage.setItem('ocfilter_product_filter', JSON.stringify(ocfilter_product_filter));
      }     
    });
    
    if ($('.alert-danger').length < 1 && 'localStorage' in window) {
      localStorage.removeItem('ocfilter_product_filter');      
    }
    
    function getCategoryId() {
      var category_id = [];
      
      $('input[type="checkbox"][name="product_category[]"]:checked, input[type="hidden"][name="product_category[]"], select[name="main_category_id"]').each(function(i) {
        $(this).val() > 0 && category_id.push($(this).val());
      });
      
      return $.unique(category_id);
    }    
    
    var options = {
      product_id: this.getURLParam('product_id'),
      category_id: getCategoryId(),
      container: '#tab-ocfilter'
    };
   
    if ($('.alert-danger').length > 0 && 'localStorage' in window && localStorage.getItem('ocfilter_product_filter')) {
      options.selected = JSON.parse(localStorage.getItem('ocfilter_product_filter')) || ''; 
  
      localStorage.removeItem('ocfilter_product_filter');
    }   
   
    this.category_length = $('#product-category div').length;

    var category_length;

    $('a[href="#tab-ocfilter"]').on('click', function() {
      category_length = $('#product-category div').length;

      if (that.category_length != category_length) {
        that.category_length = category_length;
      
        that.getRelationForm($.extend({}, options, { category_id: getCategoryId() }));
      }
    });

    $('input[type="checkbox"][name="product_category[]"], select[name="main_category_id"]').on('change', function() {         
      that.getRelationForm($.extend({}, options, { category_id: getCategoryId() }));
    });

    this.getRelationForm(options);   
  };
  
  OCFilter.prototype.getRelationForm = function(options) {
    var that = this;

    (function init(parent, options) {       
      if (parent.ready) {
        return;
      }
      
      parent.ready = true;
      
      // Values Dropdown select
      $(options.eventContainer || options.container).on('click', function(e) {
        var $target = $(e.target), $menu = $target.closest('.ocf-filter-dm');

        if ($menu.length > 0) {
          e.stopPropagation();
           
          var text = [];
          var $label = $menu.closest('.dropdown').find('.dropdown-label');
          var $menuItem = $target.closest('li');
          var $input = $menuItem.find('input');
                    
          if (e.target != $input.get(0)) {
            $input.prop('checked', !$input.prop('checked')).trigger('change');
          }

          $menuItem.toggleClass('active', $input.prop('checked'));

          $menu.find('input:checked').each(function(i) {
            text.push($.trim($(this).next().text()));
          });

          if (text.length) {
            $label.addClass('label-selected').html('<span class="label label-ocf-value">' + text.join('</span><span class="label label-ocf-value">') + '</span>');
          } else {
            $label.removeClass('label-selected').html($label.attr('data-default'));
          }
          
          if (!$target.is('input')) {
            return false;  
          }
        } else if ($target.is('.ocf-input-placeholder')) {
          // Remove disabled attribute on slider inputs         
          e.stopPropagation();
          e.preventDefault();
          
          var $group = $target.closest('.form-group');
          
          if ($group.find('input[name^="ocfilter_filter"][value="0"]:checked').length < 1) {
            $group.find('input[type="number"][disabled]').prop('disabled', false);
            
            $target.prev().focus();
          }          
        } else if ($target.parent('.remove-autocomplete-value').length > 0) {
          // Remove autocomplete values label with input
          e.stopPropagation();    
          
          $target.parent().remove();
          
          options.onSelect && options.onSelect();
        }
      })

      // Add disabled attribute on empty value slider inputs
      .on('focusout', '[name$="[min]"], [name$="[max]"]', function(e) {
        setTimeout(function(that) {         
          var $group = $(that).closest('.form-group');

          if ($group.find('input[type="number"]:focus').length < 1 && ($group.find('input[name$="[min]"]').val().length + $group.find('input[name$="[max]"]').val().length) < 1) {
            $group.find('input[type="number"]').prop('disabled', true);  
          }          
        }, 100, this);       
      })
      
      // Select "all values"
      .on('change', 'input[type="checkbox"][name^="ocfilter_filter"][value="0"]', function(e) {
        if ($(this).prop('disabled')) {
          return false;
        }
        
        var $group = $(this).closest('.form-group');
        
        if ($group.hasClass('ocf-form-group-slider')) {
          if (this.checked) {
            $group.find('input[type="number"]').prop('disabled', true).addClass('disabled');    
          } else {
            $group.find('input[type="number"]').removeClass('disabled');
            
            if ($group.find('input[name$="[min]"]').val().length > 0 || $group.find('input[name$="[max]"]').val().length > 0) {
              $group.find('input[type="number"]').prop('disabled', false);                 
            }
          }          
        } else if ($group.hasClass('ocf-form-group-autocomplete')) {
          $group.find('input[name="filter_value_name"]').prop('disabled', this.checked);    
          $group.find('.label-ocf-list').toggleClass('disabled', this.checked);
        } else {
          $group.find('.dropdown-toggle').toggleClass('disabled', this.checked);
        }  
      })   

      // Set values autocomplete
      .on('focus', 'input[name="filter_value_name"]', function() {
        if ($(this).data('autocomplete')) {
          return;
        }

        $(this).data('autocomplete', true).autocomplete({
          'before': function() {
            $(this).parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');  
          },
          'source': function(request, response) {
            var $this = $(this), data = {
              filter_key: $(this).attr('data-filter-key'),
              filter_name: request
            };
                                                            
            $.ajax({
              url: ocfilter.link('extension/module/ocfilter/filter/autocompleteValues'),
              dataType: 'json',
              data: data,
              success: function(json) {
                response($.map(json, function(item) {
                  return {
                    label: item['name'],
                    value: item['value_id'],
                    filter_key: item['filter_key'],
                  }
                }));
                
                $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-question-circle');
              }
            });
          },
          'select': function(item) {
            var $labelList = $($(this).attr('data-target'));
          
            $(this).val('');
            
            $labelList.find('[value="' + item.value + '"]').parent().remove();
            $labelList.append('<span class="label label-ocf-value remove-autocomplete-value" title="' + item.label + '"><input type="hidden" name="ocfilter_filter[' + item.filter_key + '][]" value="' + item.value + '" /> <span>' + item.label + '</span> <i class="fa fa-times-circle"></i></span>');
          
            options.onSelect && options.onSelect();     
          }
        });
      });      
    })(this, options);

    var html = [], data = { category_id: options.category_id };
    
    if ('undefined' != typeof options.product_id) {
      data.product_id = options.product_id;
    }
    
    if ('undefined' != typeof options.page_id) {
      data.page_id = options.page_id;
    }    
    
    if (options.selected) {
      data.selected = options.selected;
    }
    
    if (options.ignore_slide) {
      data.ignore_slide = 1;
    }

    if (options.allow_group) {
      data.allow_group = 1;
    }    

    $(options.container).html('<div style="text-align: center; padding-top: 5rem; padding-bottom: 5rem;"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>');

    $.get(this.link('extension/module/ocfilter/filter/relation'), data, function(response) {
      $(options.container).html(response);
           
      options.onLoad && options.onLoad();            
    });
  };
  
  OCFilter.prototype.buldMaskVarsList = function(options) {
    var 
      $maskVarsList = $(options.container).html('<li>' + options.textDefault + '</li>'),
      $formGroups = $(options.relationContainer).find('.form-group'),
      added = [],
      html = '';
  
    function addMaskVar($formGroup) {
      var filter_key = $formGroup.attr('data-ocfilter-filter-key'), text = $formGroup.find('.control-label').text();
      
      if (added.indexOf(filter_key) > -1) {
        return;
      }
      
      added.push(filter_key);
      
      html += '\
      <li> \
        <div class="media"> \
          <div class="media-left"> \
            <kbd onclick="ocfilter.selectText(this);">{F' + filter_key + '}</kbd> \
          </div> \
          <div class="media-body media-middle">' + text + '</div> \
        </div> \
        <div class="media mt-0"> \
          <div class="media-left"> \
            <kbd onclick="ocfilter.selectText(this);">{F' + filter_key + '|L}</kbd> \
          </div> \
          <div class="media-body media-middle">' + text.toLowerCase() + '</div> \
        </div> \
      </li>';
    };
  
    $formGroups.has('input[name^="ocfilter_filter"][type="hidden"]').each(function() {
      var $formGroup = $(this);
      
      if ($formGroup.find('input[name^="ocfilter_filter"][type="checkbox"][value="group"]:checked').length < 1 && $formGroup.find('input[name^="ocfilter_filter"][type="hidden"]').length > 1) {
        addMaskVar($formGroup);
      }
    });
    
    $formGroups.has('input[name^="ocfilter_filter"][type="checkbox"]:checked').each(function() {
      var $formGroup = $(this);
      
      if ($formGroup.find('input[name^="ocfilter_filter"][type="checkbox"][value="group"]:checked').length < 1 && ($formGroup.find('input[name^="ocfilter_filter"][type="checkbox"]:checked').length > 1 || $formGroup.find('input[name^="ocfilter_filter"][type="checkbox"]:checked').val() < 1)) {
        addMaskVar($formGroup);
      }
    });

    $formGroups.has('input[name$="[min]"]:not([disabled])').each(function() {
      var $formGroup = $(this), min = $formGroup.find('input[name$="[min]"]').val(), max = $formGroup.find('input[name$="[max]"]').val();
      
      if (min.length && max.length && min != max) {
        addMaskVar($formGroup);
      }
    });    
    
    if (html) {
      $maskVarsList.html(html);
    }  
  };  
    
  // Optimize, without focus and blur delay
  OCFilter.prototype.autocomplete = function(option) {
    return this.each(function() {     
      var $this = $(this);
      var $dropdown = $('<ul class="dropdown-menu" />');
      
      this.timer = null;
      this.items = [];
      this.mouseDownOnItem = false;

      $.extend(this, option);
      
      this.placement = option.placement || 'left';
      
      if (this.placement == 'right') {
        $dropdown.addClass('dropdown-menu-right').css({ 'min-width': '100%' });
      }

      $this.attr('autocomplete', 'off').on('focus', function() {
        this.mouseDownOnItem = false;
        
        if ($this.data().value != $this.val()) {
          this.request('focus');
        } else {
          this.show();
        }   
      }).on('blur', function() {
        if (!this.mouseDownOnItem) {
          this.hide();
        }
      }).on('keyup', function(e) {
        if (e.which == 27) {
          this.hide();
        } else {
          this.request('keyup');
        }
      });

      // Click
      this.click = function(e) {
        if (e.type == 'mousedown') {
          this.mouseDownOnItem = true;
                    
          return;
        }
        
        this.mouseDownOnItem = false;
        
        this.hide();
                
        e.preventDefault();

        var value = $(e.target).parent().attr('data-value');

        if (value && this.items[value]) {
          this.select(this.items[value]);
        }
      };

      // Show
      this.show = function() {
        var pos = $this.position();

        $dropdown.css({
          top: pos.top + $this.outerHeight(),
          left: (this.placement == 'right' ? 'auto' : pos.left)
        }).show();
      };

      // Hide
      this.hide = function() {
        $dropdown.hide();
      };

      // Request
      this.request = function(eventType) {
        clearTimeout(this.timer);
        
        this.before && this.before();
        
        if (eventType == 'focus') {
          this.source($(this).val(), $.proxy(this.response, this));
        } else {
          this.timer = setTimeout(function(object) {
            object.source($(object).val(), $.proxy(object.response, object));
          }, 450, this);
        }
      };

      // Response
      this.response = function(json) {
        $this.data().value = $this.val();
        
        var html = '';
        var category = {};
        var name;
        var i = 0, j = 0;

        if (json.length) {
          for (i = 0; i < json.length; i++) {
            // update element items
            this.items[json[i]['value']] = json[i];

            if (!json[i]['category']) {
              // ungrouped items
              html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
            } else {
              // grouped items
              name = json[i]['category'];
              if (!category[name]) {
                category[name] = [];
              }

              category[name].push(json[i]);
            }
          }

          for (name in category) {
            html += '<li class="dropdown-header">' + name + '</li>';

            for (j = 0; j < category[name].length; j++) {
              html += '<li data-value="' + category[name][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[name][j]['label'] + '</a></li>';
            }
          }
        }

        if (html) {
          this.show();
        } else {
          this.hide();
        }

        $dropdown.html(html);
      };

      $dropdown.on('mousedown click', '> li > a', $.proxy(this.click, this));
      
      $this.after($dropdown);
    });
  };
  
  global.ocfilter = new OCFilter;
  global.ocfilter.Constructor = OCFilter;       
}(window, jQuery);