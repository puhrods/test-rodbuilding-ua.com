<?php

class SoforpWidgets
{

	public $nameSpace = ""; // i.e. 'soforp_backup_'
	public $params = ""; // i.e. array('soforp_backup_status' => 1 );
	public $text_select_all = "";
	public $text_unselect_all = "";

	public function __construct($nameSpace, $params)
	{
		$this->nameSpace = $nameSpace;
		$this->params = $params;
		$this->config_language_id = isset($params['config_language_id']) ? $params['config_language_id'] : 1;
	}

	public function dropdown($property, $options, $params = array())
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="<?php echo (isset($params['disabled']) && $params['disabled'])? 'display:none' : 'display: inline-block; width: 100%;'; ?>">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <select name="<?php echo $this->nameSpace . $property; ?>"
                id="<?php echo $this->nameSpace . $property; ?>"
                class="form-control">
                    <?php foreach ($options as $value => $name) { ?>
                        <?php if ($this->params[$this->nameSpace . $property] == $value) { ?>
                        <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            </div>
        </div>
		<?php
	}

	public function dropdownLite($property, $options, $keys = array())
	{
		?>
		<?php if ($keys) { ?>
        <select class="form-control" name="<?php echo $this->nameSpace . $property . '[' . $keys[0] . ']' . '[' . $keys[1] . ']' . '[' . $keys[2] . ']'; ?>">
			<?php foreach ($options as $value => $name) { ?>
				<?php if ($this->params[$this->nameSpace . $property][ $keys[0]][ $keys[1]][ $keys[2]] == $value || $this->params[$this->nameSpace . $property][ $keys[0]][ $keys[1]][ $keys[2]] === $name) { ?>
                    <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
				<?php } else { ?>
                    <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
				<?php } ?>
			<?php } ?>
        </select>
	<?php } else { ?>
        <select class="form-control" name="<?php echo $this->nameSpace . $property; ?>">
			<?php foreach ($options as $value => $name) { ?>
				<?php if ($this->params[$this->nameSpace . $property] == $value) { ?>
                    <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
				<?php } else { ?>
                    <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
				<?php } ?>
			<?php } ?>
        </select>
	<?php } ?>
		<?php
	}

	public function dropdownLiteTemplate($property, $options, $keys = array())
	{
		?>
		<?php if ($keys) { ?>
        <select class="form-control" name="<?php echo $this->nameSpace . $property . '[' . $keys[0] . ']' . '[' . $keys[1] . ']' . '[' . $keys[2] . ']'; ?>">
			<?php foreach ($options as $value => $name) { ?>
				<?php if ($this->params[$this->nameSpace . $property][ $keys[0]][ $keys[1]][ $keys[2]] === $value || $this->params[$this->nameSpace . $property][ $keys[0]][ $keys[1]][ $keys[2]] === $name) { ?>
                    <option value="<?php echo $name; ?>" selected="selected"><?php echo $name; ?></option>
				<?php } else { ?>
                    <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
				<?php } ?>
			<?php } ?>
        </select>
	<?php } else { ?>
        <select class="form-control" name="<?php echo $this->nameSpace . $property; ?>">
			<?php foreach ($options as $value => $name) { ?>
				<?php if ($this->params[$this->nameSpace . $property] == $value) { ?>
                    <option value="<?php echo $name; ?>" selected="selected"><?php echo $name; ?></option>
				<?php } else { ?>
                    <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
				<?php } ?>
			<?php } ?>
        </select>
	<?php } ?>
		<?php
	}

	/**
	 * <select>
	 *
	 * @param array|string $stores магазины
	 * @param string $property имя параметра
	 * @param array $options значение => текст
	 */
	public function dropdownMultiStore($stores, $property, $options)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label" for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
                <?php echo isset($this->params['entry_' . $property . '_desc']) ? $this->params['entry_' . $property . '_desc'] : '' ?>
            </div>

            <div class="col-sm-7">
                <?php foreach ($stores as $store_id => $store) { ?>
                    <select name="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                            id="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                            class="form-control stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
                        <?php foreach ($options as $value => $name) { ?>
                            <?php if ($this->params[$this->nameSpace . $property][$store_id] == $value) { ?>
                                <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>
        </div>
		<?php
	}

	public function dropdownA($property, $array, $index, $options)
	{
		$field_id = $array . '-' . $property . '-' . $index;
		?>
        <div class="form-group" id="field_<?php echo $field_id; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $field_id; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <select name="<?php echo $array . '[' . $index . '][' . $property . ']'; ?>"
                id="<?php echo $this->nameSpace . $field_id; ?>"
                class="form-control">
                    <?php foreach ($options as $value => $name) { ?>
                        <?php if ($this->params[$array][$index][$property] == $value) { ?>
                        <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            </div>
        </div>
		<?php
	}

	public function dropdownB($property, $array, $options)
	{
		?>
        <label class="control-label"
                for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?>
        </label>
        <div class="panel panel-default" id="<?php echo $this->nameSpace . $property; ?>">
            <div class="panel-body">
                <?php
                foreach ($array as $index => $item) {
                    $field_id = $property . '-' . $index;
                    ?>
                    <div class="form-group" id="field_<?php echo $field_id; ?>" style="display: inline-block; width: 100%;">
                        <div class="col-sm-5">
                            <label class="control-label"
                                    for="<?php echo $this->nameSpace . $field_id; ?>"><?php echo $item; ?></label>
                            <br>
                            <?php
                            if (isset($this->params['entry_' . $property . '_desc']))
                                echo $this->params['entry_' . $property . '_desc'];
                            ?>
                        </div>
                        <div class="col-sm-7">
                            <select name="<?php echo $this->nameSpace . $property . '[' . $index . ']'; ?>"
                                    id="<?php echo $this->nameSpace . $field_id; ?>"
                                    class="form-control">
                                <?php foreach ($options as $value => $name) { ?>
                                    <?php if (isset($this->params[$this->nameSpace . $property][$index]) && $this->params[$this->nameSpace . $property][$index] == $value) { ?>
                                        <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
		<?php
	}

	public function debug_and_logs($property, $options, $clear, $button_clear_log)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-4">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-4">
            <select name="<?php echo $this->nameSpace . $property; ?>"
                id="<?php echo $this->nameSpace . $property; ?>"
                class="form-control">
                    <?php foreach ($options as $value => $name) { ?>
                        <?php if ($this->params[$this->nameSpace . $property] == $value) { ?>
                        <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            </div>

            <div class="col-sm-4 text-right">
            <button onclick="$('#form').attr('action', '<?php echo $clear; ?>');
                            $('#form').attr('target', '_self');
                            $('#form').submit();" class="btn btn-primary"><span><?php echo $button_clear_log; ?></span></button>
            </div>
        </div>
		<?php
	}

	public function debug_download_logs($property, $options, $clear, $download, $button_clear_log, $button_download_log)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-4">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-4">
            <select name="<?php echo $this->nameSpace . $property; ?>"
                id="<?php echo $this->nameSpace . $property; ?>"
                class="form-control">
                    <?php foreach ($options as $value => $name) { ?>
                        <?php if ($this->params[$this->nameSpace . $property] == $value) { ?>
                        <option value="<?php echo $value; ?>" selected="selected"><?php echo $name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            </div>
            <div class="pull-right">
            <a href="<?php echo $download; ?>" data-toggle="tooltip" title="" class="btn btn-primary"
                data-original-title="<?php echo $button_download_log; ?>"><i class="fa fa-download"></i></a>
            <a onclick=" $('#form').attr('action', '<?php echo $clear; ?>');
                            $('#form').attr('target', '_self');
                            $('#form').submit()" data-toggle="tooltip" title="" class="btn btn-danger"
                data-original-title="<?php echo $button_clear_log; ?>"><i class="fa fa-eraser"></i></a>
            </div>
        </div>
		<?php
	}

	public function input($property)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <input name="<?php echo $this->nameSpace . $property; ?>"
                    id="<?php echo $this->nameSpace . $property; ?>" class="form-control"
                    value="<?php echo $this->params[$this->nameSpace . $property]; ?>"/>
            </div>
        </div>
		<?php
	}

	public function inputLite($property, $keys = array())
	{
		?>
		<?php if ($keys) { ?>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cogs" aria-hidden="true" style="width:16px"></i></span>
            <input name="<?php echo $this->nameSpace . $property . '[' . $keys[0] . ']' . '[' . $keys[1] . ']' . '[' . $keys[2] . ']'; ?>"
                   id="<?php echo $this->nameSpace . $property . '_' . $keys[0] . '_' . $keys[1]; ?>"
                   class="form-control"
                   value="<?php echo $this->params[$this->nameSpace . $property][$keys[0]][$keys[1]][ $keys[2]]; ?>"/>
        </div>
	<?php } else { ?>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-cogs" aria-hidden="true" style="width:16px"></i></span>
            <input name="<?php echo $this->nameSpace . $property; ?>"
                   id="<?php echo $this->nameSpace . $property; ?>"
                   class="form-control"
                   value="<?php echo $this->params[$this->nameSpace . $property]; ?>"/>
        </div>
	<?php } ?>
		<?php
	}

	/**
	 * <input>
	 *
	 * @param array|string $stores магазины
	 * @param string $property имя параметра
	 */
	public function inputRequired($property, $required = false, $error = '')
	{
		?>
        <div class="form-group<?php echo $required ? ' required' : ''; ?>" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label"
                       for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
				<?php
				if (isset($this->params['entry_' . $property . '_desc']))
					echo $this->params['entry_' . $property . '_desc'];
				?>
            </div>
            <div class="col-sm-7">
                <input name="<?php echo $this->nameSpace . $property; ?>"
                       id="<?php echo $this->nameSpace . $property; ?>" class="form-control"
                       value="<?php echo $this->params[$this->nameSpace . $property]; ?>"/>
            </div>
			<?php if (!empty($error)) { ?>
                <div class="text-danger col-sm-7 col-sm-offset-5"><?php echo $error; ?></div>
			<?php } ?>
        </div>
		<?php
	}

	public function dateInput($property, $required = false, $error = '')
	{
		?>
        <div class="form-group <?php echo $required ? 'required' : ''; ?> " id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label" for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            </div>
            <div class="col-sm-7">
                <div class="input-group date">
                    <input type="text" data-date-format="YYYY-MM-DD" class="form-control"
                           name="<?php echo $this->nameSpace . $property; ?>"
                           id="<?php echo $this->nameSpace . $property; ?>"
                           value="<?php echo isset($this->params[$this->nameSpace . $property]) ? $this->params[$this->nameSpace . $property] : ''; ?>"
                           placeholder="<?php echo $this->params['entry_' . $property]; ?>"
                    />
                    <span class="input-group-btn">
						<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
				    </span>
                </div>
            </div>
			<?php if (!empty($error)) { ?>
                <div class="text-danger col-sm-7 col-sm-offset-5"><?php echo $error; ?></div>
			<?php } ?>
        </div>
		<?php
	}
	public function inputMultiStore($stores, $property)
	{
		?>
        <div class="form-group" id="field_<?php echo $property . '[0]'; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label" for="<?php echo $this->nameSpace . $property . '[0]'; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
                <?php echo isset($this->params['entry_' . $property . '_desc']) ? $this->params['entry_' . $property . '_desc'] : '' ?>
            </div>
            <div class="col-sm-7">
                <?php foreach ($stores as $store_id => $store) { ?>
                    <div class="input-group stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
                        <input name="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                            id="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                            class="form-control"
                            value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . $property])) ? $this->params[$this->nameSpace . $property][$store_id] : ''; ?>"/>
                    </div>
                <?php } ?>
            </div>
        </div>
		<?php
	}

	/**
	 * @param array|string $stores магазины
	 * @param array|string $property имя параметров (ширина, вісота)
	 * @param string $input_title заголовок инпута
	 *  Создается заголовок с двумя инпутами
	 */
	public function inputMultiStoreImageSize($stores, $input_title, $property)
	{
		?>
		<div class="form-group " id="field_<?php echo $input_title . '[0]'; ?>" style="display: inline-block; width: 100%;">
			<div class="col-sm-5">
				<label class="control-label" ><?php echo $this->params['entry_' . $input_title]; ?></label>
				<br>
				<?php echo isset($this->params['entry_' . $input_title . '_desc']) ? $this->params['entry_' . $input_title . '_desc'] : '' ?>
			</div>
			<div class="col-sm-7 form-inline">
				<?php foreach ($stores as $store_id => $store) { ?>
                    <div class="stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
						<input style="width:100px" type="text"
						       name="<?php echo $this->nameSpace . $property[0] . "[{$store_id}]"; ?>"
						       id="<?php echo $this->nameSpace . $property[0] . "[{$store_id}]"; ?>"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . $property[0]])) ? $this->params[$this->nameSpace . $property[0]][$store_id] : ''; ?>"/>
						        X  <input style="width:100px" type="text"
						                  name="<?php echo $this->nameSpace . $property[1] . "[{$store_id}]"; ?>"
						                  id="<?php echo $this->nameSpace . $property[1] . "[{$store_id}]"; ?>"
						                  value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . $property[1]])) ? $this->params[$this->nameSpace . $property[1]][$store_id] : ''; ?>"/>
                    </div>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	public function inputColor($property)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label"
                        for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
                <?php
                if (isset($this->params['entry_' . $property . '_desc']))
                    echo $this->params['entry_' . $property . '_desc'];
                ?>
            </div>
            <div class="col-sm-7">
                <div class="input-group colorpicker-component">
                    <input name="<?php echo $this->nameSpace . $property; ?>"
                            id="<?php echo $this->nameSpace . $property; ?>"
                            value="<?php echo $this->params[$this->nameSpace . $property]; ?>"
                            class="form-control"/>
                    <span class="input-group-addon"><i></i></span>
                </div>
            </div>
        </div>
		<?php
	}


	public function inputImage($property, $placeholder, $img)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label" for="<?php echo $this->nameSpace . $property; ?>">
                <?php echo $this->params['entry_' . $property]; ?>
            </label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-xs-7">
            <a href="" id="thumb-image-<?php echo $property ?>" data-toggle="image" class="img-thumbnail">
                <img src="<?php echo $img; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>"/></a>
            <input type="hidden" name="<?php echo $this->nameSpace . $property; ?>"
                    value="<?php echo $this->params[$this->nameSpace . $property]; ?>"
                    id="input-image-<?php echo $property ?>"/>
            </div>
        </div>
		<?php
	}

	public function inputA($property, $array, $index)
	{ // Arrays
		$field_id = $array . '-' . $property . '-' . $index;
		?>
        <div class="form-group" id="field_<?php echo $field_id; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $field_id; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <input name="<?php echo $array . '[' . $index . '][' . $property . ']'; ?>"
                    id="<?php echo $field_id; ?>" class="form-control"
                    value="<?php echo $this->params[$array][$index][$property]; ?>"/>
            </div>
        </div>
		<?php
	}

	public function localeInput($property, $languages)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <?php foreach ($languages as $language) { ?>
                <div class="input-group">
                    <span class="input-group-addon">
                        <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>"/>
                    </span>
                    <input
                    name="<?php echo $this->nameSpace . $property; ?>[<?php echo $language['language_id']; ?>]"
                    id="<?php echo $this->nameSpace . $property . $language['language_id']; ?>"
                    class="form-control"
                    value="<?php if (isset($this->params[$this->nameSpace . $property][$language['language_id']]))  echo $this->params[$this->nameSpace . $property][$language['language_id']]; else echo ''; ?>"/>
                </div>
            <?php } ?>
            </div>
        </div>
		<?php
	}

	public function localeInputLite($property, $languages, $keys = array())
	{
		?>
		<?php if ($keys) { ?>
		<?php foreach ($languages as $language) { ?>
            <div class="input-group">
                <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>"/></span>
                <input name="<?php echo $this->nameSpace . $property . '[' . $keys[0] . ']' . '[' .$language['language_id'] . ']' . '[' . $keys[1] . ']'; ?>"
                       id="<?php echo $this->nameSpace . $property . '_' . $keys[0] . '_' . $language['language_id']; ?>"
                       class="form-control"
                       value="<?php echo $this->params[$this->nameSpace . $property][ $keys[0]][ $language['language_id']][ $keys[1]] ?>"/>
            </div>
		<?php } ?>
	<?php } else { ?>
		<?php foreach ($languages as $language) { ?>
            <div class="input-group">
                <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>"/></span>
                <input name="<?php echo $this->nameSpace . $property; ?>[<?php echo $language['language_id']; ?>]"
                       id="<?php echo $this->nameSpace . $property . $language['language_id']; ?>"
                       class="form-control"
                       value="<?php if (isset($this->params[$this->nameSpace . $property][$language['language_id']]))  echo $this->params[$this->nameSpace . $property][$language['language_id']]; else echo ''; ?>"/>
            </div>
		<?php } ?>
	<?php } ?>
		<?php
	}

	public function localeInputRequired($property, $languages, $required = false, $error = '')
	{ //нет этой функции
		?>
        <div class="form-group<?php echo $required ? ' required' : ''; ?>" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label"
                       for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
				<?php
				if (isset($this->params['entry_' . $property . '_desc']))
					echo $this->params['entry_' . $property . '_desc'];
				?>
            </div>
            <div class="col-sm-7">
				<?php foreach ($languages as $language) { ?>
                    <div class="input-group">
				    <span class="input-group-addon">
						<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>"/>
					</span>
                        <input
                                name="<?php echo $this->nameSpace . $property; ?>[<?php echo $language['language_id']; ?>]"
                                id="<?php echo $this->nameSpace . $property . $language['language_id']; ?>"
                                class="form-control"
                                value="<?php if (isset($this->params[$this->nameSpace . $property][$language['language_id']]))  echo $this->params[$this->nameSpace . $property][$language['language_id']]; else echo ''; ?>"/>
                    </div>
				<?php } ?>
            </div>
			<?php if (!empty($error)) { ?>
                <div class="text-danger col-sm-7 col-sm-offset-5"><?php echo $error; ?></div>
			<?php } ?>
        </div>
		<?php
	}
	/**
	 * <input>
	 *
	 * @param array|string $stores магазины
	 * @param string $property имя параметра
	 * @param array $languages языки
	 */
	public function localeInputMultiStore($stores, $property, $languages)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label" for="<?php echo $this->nameSpace . $property . '[0]'; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
                <?php echo isset($this->params['entry_' . $property . '_desc']) ? $this->params['entry_' . $property . '_desc'] : '' ?>
            </div>
            <div class="col-sm-7">
                <?php foreach ($stores as $store_id => $store) { ?>
                    <?php foreach ($languages as $language) { ?>
                        <div class="input-group stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
                            <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>"/></span>
                            <input
                                    name="<?php echo $this->nameSpace . $property . "[{$store_id}]" . '[' . $language['language_id'] . ']'; ?>"
                                    id="<?php echo $this->nameSpace . $property . "[{$store_id}]" . '[' . $language['language_id'] . ']'; ?>"
                                    class="form-control"
                                    value="<?php echo $this->params[$this->nameSpace . $property][$store_id][$language['language_id']]; ?>"/>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
		<?php
	}

	public function localeTextarea($property, $languages)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <ul class="nav nav-tabs">
                <?php foreach ($languages as $language) { ?>
                    <li class="<?php echo $language['language_id'] == $this->config_language_id ? 'active' : ''; ?>"><a
                        href="#column-<?php echo $this->nameSpace; ?><?php echo $property; ?>_<?php echo $language['language_id']; ?>"
                        data-toggle="tab">
                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>">
                         <?php echo $language['name']; ?>
                    </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                    <div class="tab-pane <?php echo $language['language_id'] == $this->config_language_id ? 'active' : ''; ?>"
                     id="column-<?php echo $this->nameSpace; ?><?php echo $property; ?>_<?php echo $language['language_id']; ?>">
                    <textarea rows="6"
                          name="<?php echo $this->nameSpace . $property; ?>[<?php echo $language['language_id']; ?>]"
                          id="<?php echo $this->nameSpace . $property . $language['language_id']; ?>"
                          class="form-control"><?php echo $this->params[$this->nameSpace . $property][$language['language_id']]; ?></textarea>
                    </div>
                <?php } ?>
            </div>
            </div>
        </div>
		<?php
	}

	function password($property)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <input type="password" name="<?php echo $this->nameSpace . $property; ?>"
                    id="<?php echo $this->nameSpace . $property; ?>" class="form-control"
                    value="<?php echo $this->params[$this->nameSpace . $property]; ?>"/>
            </div>
        </div>
		<?php
	}

	public function textarea($property, $rows = 6)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <textarea name="<?php echo $this->nameSpace . $property; ?>"
                  id="<?php echo $this->nameSpace . $property; ?>"
                  rows="<?php echo $rows; ?>"
                  class="form-control"><?php echo $this->params[$this->nameSpace . $property]; ?></textarea>
            </div>
        </div>
		<?php
	}

	public function textareaMultiStore($stores, $property, $rows = 6, $style = "")
	{
		$style = !empty($style) ? "style='" . $style . "'" : "";
		$rows = $style ? '' : 'rows="' . $rows . '"';

		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
                <label class="control-label"
                        for="<?php echo $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
                <?php
                if (isset($this->params['entry_' . $property . '_desc']))
                    echo $this->params['entry_' . $property . '_desc'];
                ?>
            </div>
            <div class="col-sm-7">
                <?php foreach ($stores as $store_id => $store) { ?>
                    <div class="stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
                        <textarea name="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                                  id="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                            <?php echo $rows; ?>
                                  class="form-control" <?php echo $style; ?> ><?php echo $this->params[$this->nameSpace . $property][$store_id]; ?></textarea>
                    </div>
                <?php } ?>
            </div>
        </div>
		<?php
	}

	public function textareaWideMultiStore($stores, $property, $rows = 6, $style = "")
	{
		$style = !empty($style) ? "style='" . $style . "'" : "";
		$rows = $style ? '' : ('rows="' . $rows . '"');

		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">

            <div class="col-sm-12">
                <?php foreach ($stores as $store_id => $store) { ?>
                    <div class="stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
                        <textarea name="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                                  id="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>"
                            <?php echo $rows; ?>
                                  class="form-control" <?php echo $style; ?> ><?php echo $this->params[$this->nameSpace . $property][$store_id]; ?></textarea>
                    </div>
                <?php } ?>
            </div>
        </div>
		<?php
	}


	public function textareaA($property, $array, $index, $rows = 6)
	{
		$field_id = $array . '-' . $property . '-' . $index;
		?>
        <div class="form-group <?php echo $property; ?>" id="field_<?php echo $field_id; ?>"
             style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $field_id; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <textarea name="<?php echo $array . '[' . $index . '][' . $property . ']'; ?>"
                  id="<?php echo $field_id; ?>"
                  rows="<?php echo $rows; ?>"
                  class="form-control"><?php echo $this->params[$array][$index][$property]; ?></textarea>
            </div>
        </div>
		<?php
	}

	public function localeTextareaA($property, $array, $index, $languages, $rows = 6)
	{
		$field_id = $array . '-' . $property . '-' . $index;
		?>
        <div class="form-group <?php echo $property; ?>" id="field_<?php echo $field_id; ?>"
             style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $field_id; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <ul class="nav nav-tabs">
                <?php foreach ($languages as $language) { ?>
                    <li class="<?php echo $language['language_id'] == $this->config_language_id ? 'active' : ''; ?>">
                        <a href="#column-<?php echo $field_id ?>-<?php echo $language['language_id']; ?>" data-toggle="tab" >
                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>">
                            <?php echo $language['name']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                    <div class="tab-pane <?php echo $language['language_id'] == $this->config_language_id ? 'active' : ''; ?>"
                     id="column-<?php echo $field_id . '-' . $language['language_id']; ?>">
                    <textarea name="<?php echo $array . '[' . $index . '][' . $property . '][' . $language['language_id'] . ']'; ?>"
                          id="<?php echo $field_id . '_' . $language['language_id']; ?>"
                          rows="<?php echo $rows; ?>"
                          class="form-control"><?php echo $this->params[$array][$index][$property][$language['language_id']]; ?></textarea>
                    </div>
                <?php } ?>
            </div>
            </div>
        </div>
		<?php
	}

	public function text($property)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <?php echo $this->params[$this->nameSpace . $property]; ?>
            </div>
        </div>
		<?php
	}

	public function textA($property, $array, $index)
	{
		$field_id = $array . '-' . $property . '-' . $index;
		?>
        <div class="form-group <?php echo $property; ?>" id="field_<?php echo $field_id; ?>"
             style="display: inline-block; width: 100%;">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $field_id; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <?php echo $this->params[$array][$index][$property]; ?>
            </div>
        </div>
		<?php
	}

	public function checklist($property, $options)
	{
		?>
        <div class="form-group store" id="field_<?php echo $property; ?>" style="display: inline-block; width:100%">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <div class="well well-sm" style="height: 150px; overflow: auto;">
                <?php $class = 'odd'; ?>
                <?php foreach ($options as $value => $name) { ?>
                    <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                    <div class="<?php echo $class; ?>">
                    <input type="checkbox" name="<?php echo $this->nameSpace . $property; ?>[]"
                            value="<?php echo $value; ?>"<?php
                            if (in_array($value, $this->params[$this->nameSpace . $property]))
                                echo ' checked="checked"';
                            ?> />
                            <?php echo $name; ?>
                    </div>
                <?php } ?>
            </div>
            <button type="button" onclick="$(this).parent().find(':checkbox').prop('checked', true);"
                class="btn btn-primary"><i class="fa fa-pencil"></i> <?php echo $this->text_select_all; ?></button>
            <button type="button" onclick="$(this).parent().find(':checkbox').prop('checked', false);"
                class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $this->text_unselect_all; ?>
            </button>
            </div>
        </div>
		<?php
	}

	public function checklistA($property, $array, $index, $options)
	{
		$field_id = $array . '-' . $property . '-' . $index;
		?>
        <div class="form-group store" id="field_<?php echo $field_id; ?>" style="display: inline-block; width:100%">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $field_id; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <div class="well well-sm" style="height: 150px; overflow: auto;">
                <?php $class = 'odd'; ?>
                <?php foreach ($options as $value => $name) { ?>
                    <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                    <div class="<?php echo $class; ?>">
                    <input type="checkbox"
                            name="<?php echo $array . '[' . $index . '][' . $property . '][]'; ?>"
                            value="<?php echo $value; ?>"
                            <?php
                            if (in_array($value, $this->params[$array][$index][$property]))
                                echo ' checked="checked"';
                            ?> />
                            <?php echo $name; ?>
                    </div>
                <?php } ?>
            </div>
            <button type="button" onclick="$(this).parent().find(':checkbox').prop('checked', true);"
                class="btn btn-primary"><i class="fa fa-pencil"></i> <?php echo $this->text_select_all; ?></button>
            <button type="button" onclick="$(this).parent().find(':checkbox').prop('checked', false);"
                class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $this->text_unselect_all; ?>
            </button>
            </div>
        </div>
		<?php
	}
	public function listAutocomplete($property, $options)
	{
		?>
        <div class="form-group" id="field_<?php echo $property; ?>" style="display: inline-block; width:100%">
            <div class="col-sm-5">
                <label class="control-label"
                       for="input-<?php echo $property; ?>">
                    <span data-toggle="tooltip" title="<?php echo $this->params['help_' . $property]; ?>">
                    <?php echo $this->params['entry_' . $property]; ?></span>
                </label>
            </div>
            <div class="col-sm-7">
                <input type="text" name="input-<?php echo $property; ?>" value="" placeholder="<?php echo $this->params['entry_' . $property]; ?>" id="input-<?php echo $property; ?>" class="form-control" />
                <div id="list-<?php echo $property; ?>" class="well well-sm" style="height: 150px; overflow: auto;">
					<?php foreach ($options as $option ) { ?>
                        <div id="<?php echo $property; ?><?php echo $option['id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $option['name']; ?>
                            <input type="hidden" name="auto-<?php echo $property; ?>[]" value="<?php echo $option['id']; ?>" />
                        </div>
					<?php } ?>
                </div>
            </div>
        </div>
		<?php
	}
	/**
	 * <input>
	 *
	 * @param array|string $stores магазины
	 * @param string $property имя параметра
	 * @param array $options  массив
	 */
	public function checklistMultiStore($stores, $property, $options)
	{
		?>
        <div class="form-group store" id="field_<?php echo $property; ?>" style="display: inline-block; width:100%">
            <div class="col-sm-5">
                <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
                <br>
                <?php
                    if (isset($this->params['entry_' . $property . '_desc']))
                        echo $this->params['entry_' . $property . '_desc'];
                ?>
            </div>
            <div class="col-sm-7">
                <?php foreach ($stores as $store_id => $store) { ?>
                    <div class="stores-group store-<?php echo $store_id ?> <?php echo $store_id != 0 ? 'hidden' : '' ?>">
                        <div class="well well-sm" style="height: 150px; overflow: auto;">
                            <?php $class = 'odd'; ?>
                            <?php foreach ($options as $value => $name) { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div class="<?php echo $class; ?>">
                                    <input type="checkbox" name="<?php echo $this->nameSpace . $property . "[{$store_id}]"; ?>[]"
                                        value="<?php echo $value; ?>"<?php
                                        if (array_key_exists($store_id, $this->params[$this->nameSpace . $property]) && in_array($value, $this->params[$this->nameSpace . $property][$store_id]))
                                            echo ' checked="checked"';
                                    ?> />
                                    <?php echo $name; ?>
                                </div>
                            <?php } ?>
                        </div>
                        <button type="button" onclick="$(this).parent().find(':checkbox').prop('checked', true);"
                            class="btn btn-primary"><i class="fa fa-pencil"></i> <?php echo $this->text_select_all; ?></button>
                        <button type="button" onclick="$(this).parent().find(':checkbox').prop('checked', false);"
                            class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $this->text_unselect_all; ?></button>
                    </div>
                <?php } ?>
            </div>
        </div>
		<?php
	}

	public function checkbox($property)
	{
		?>
        <div class="form-group store" id="field_<?php echo $property; ?>" style="display: inline-block; width:100%">
            <div class="col-sm-5">
            <label class="control-label"
                    for="<?php echo $this->nameSpace . $property; ?>"><?php echo $this->params['entry_' . $property]; ?></label>
            <br>
            <?php
            if (isset($this->params['entry_' . $property . '_desc']))
                echo $this->params['entry_' . $property . '_desc'];
            ?>
            </div>
            <div class="col-sm-7">
            <input type="checkbox" name="<?php echo $this->nameSpace . $property; ?>"
                    value="<?php echo $this->params[$this->nameSpace . $property]; ?>"<?php
                    if ($this->params[$this->nameSpace . $property])
                        echo ' checked="checked"';
                    ?> />
            </div>
        </div>
		<?php
	}

	public function watermarkMultiStore($stores)
	{
		?>
		<?php foreach ($stores as $store_id => $store) { ?>
			<div class="form-group store store-<?php echo $store_id; ?>" id="field_<?php echo "neoseo_watermark" . $store_id; ?>" style="display: inline-block; width:100%">

				<div class="col-sm-5">
					<label class="control-label" for="draggable-zone-<?php echo $store_id; ?>"><?php echo $this->params['entry_image']; ?></label>
					<br>
				</div>
				<div class="col-sm-7">
						<input type="hidden" value="" id="tmp-<?php echo $store_id; ?>"/>
						<input type="hidden" name="neoseo_watermark_image[<?php echo $store_id; ?>]"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'image'])) ? $this->params[$this->nameSpace . 'image'][$store_id] : ''; ?>"
						       id="neoseo_watermark_image-<?php echo $store_id; ?>"/>
						<input type="hidden" name="neoseo_watermark_top[<?php echo $store_id; ?>]"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'top'])) ? $this->params[$this->nameSpace . 'top'][$store_id] : ''; ?>"
						       id="neoseo_watermark_top-<?php echo $store_id; ?>"/>
						<input type="hidden" name="neoseo_watermark_left[<?php echo $store_id; ?>]"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'left'])) ? $this->params[$this->nameSpace . 'left'][$store_id] : ''; ?>"
						       id="neoseo_watermark_left-<?php echo $store_id; ?>"/>
						<input type="hidden" name="neoseo_watermark_width[<?php echo $store_id; ?>]"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'width'])) ? $this->params[$this->nameSpace . 'width'][$store_id] : ''; ?>"
						       id="neoseo_watermark_width-<?php echo $store_id; ?>"/>
						<input type="hidden" name="neoseo_watermark_height[<?php echo $store_id; ?>]"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'height'])) ? $this->params[$this->nameSpace . 'height'][$store_id] : ''; ?>"
						       id="neoseo_watermark_height-<?php echo $store_id; ?>"/>
						<input type="hidden" name="neoseo_watermark_angle[<?php echo $store_id; ?>]"
						       value="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'angle'])) ? $this->params[$this->nameSpace . 'angle'][$store_id] : ''; ?>"
						       id="neoseo_watermark_angle-<?php echo $store_id; ?>"/>

						<div class="draggable-zone"
						     style="background:url(<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'product_image_thumb'])) ? $this->params[$this->nameSpace . 'product_image_thumb'][$store_id] : ''; ?>) 0 0 no-repeat">

							<div class="draggable-wrapper"
							     style="width: <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'width'])) ? $this->params[$this->nameSpace . 'width'][$store_id] : '') *3; ?>px;
									     height: <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'height'])) ? $this->params[$this->nameSpace . 'height'][$store_id] : '') *3; ?>px;
									     left: <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'left'])) ? $this->params[$this->nameSpace . 'left'][$store_id] : '') *3; ?>px;
									     top: <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'top'])) ? $this->params[$this->nameSpace . 'top'][$store_id] : '') *3; ?>px;">
								<div class="resizable-wrapper">
									<img id="neoseo_watermark_image_thumb-<?php echo $store_id; ?>"
									     src="<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'image_root'])) ? $this->params[$this->nameSpace . 'image_root'][$store_id] : ''; ?>/image/<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'image'])) ? $this->params[$this->nameSpace . 'image'][$store_id] : ''; ?>"
									     width="<?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'width'])) ? $this->params[$this->nameSpace . 'width'][$store_id] : '') *3; ?>"
									     height="<?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'height'])) ? $this->params[$this->nameSpace . 'height'][$store_id] : '') *3; ?>"
									     alt="Водяной знак"/>
								</div>
							</div>
						</div>
						<div style="margin-top:0.5em;width:300px;text-align: right">
							<button type="button" target="neoseo_watermark_image-<?php echo $store_id; ?>"
							        class="button-image btn btn-primary"><i
										class="fa fa-pencil"></i><?php echo $this->params['text_browse']; ?></button>
							<button type="button" target="neoseo_watermark_image-<?php echo $store_id; ?>"
							        class="button-clear btn btn-danger"><i
										class="fa fa-eraser"></i><?php echo $this->params['text_clear']; ?></button>
						</div>
				</div>
			</div>
		<?php } ?>
		<script type="text/javascript">

			$(window).load(function () {

				var drWr, elem;
				<?php foreach ($stores as $store_id => $store_data) { ?>
				drWr = $('.store-<?php echo $store_id; ?> .draggable-wrapper');
				elem = $('#neoseo_watermark_image_thumb-<?php echo $store_id; ?>');
				elem.resizable({
					aspectRatio: true,
					handles: 'ne, nw, se, sw'
				});
				$(".store-<?php echo $store_id; ?> .ui-resizable-ne")
					.css("width", "20px").css("height", "20px").css("right", "-10px").css("top", "-10px");
				$(".store-<?php echo $store_id; ?> .ui-resizable-nw")
					.css("width", "20px").css("height", "20px").css("left", "-10px").css("top", "-10px");
				$(".store-<?php echo $store_id; ?> .ui-resizable-se")
					.removeClass("ui-icon-gripsmall-diagonal-se")
					.removeClass("ui-icon")
					.css("width", "20px").css("height", "20px").css("right", "-10px").css("bottom", "-10px");
				$(".store-<?php echo $store_id; ?> .ui-resizable-sw")
					.css("width", "20px").css("height", "20px").css("left", "-10px").css("bottom", "-10px");
				drWr.draggable();
				elem.parent().rotatable({
					angle: <?php echo str_replace(',','.', ((array_key_exists($store_id, $this->params[$this->nameSpace . 'angle']) ? $this->params[$this->nameSpace . 'angle'][$store_id] : '') *3.1415926 / 180)) ; ?>
			});
				<?php } ?>
			});

			<?php foreach ($stores as $store_id => $store_data) { ?>
			$('.store-<?php echo $store_id; ?> .button-image').on('click', function () {
				$('#modal-image').remove();
				$('#tmp-<?php echo $store_id; ?>').val("").attr('target', $(this).attr('target'));
				$.ajax({
					url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token') + '&target=tmp-<?php echo $store_id; ?>',
					dataType: 'html',
					beforeSend: function () {
						$('.store-<?php echo $store_id; ?> .button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('.store-<?php echo $store_id; ?> .button-image').prop('disabled', true);
					},
					complete: function () {
						$('.store-<?php echo $store_id; ?> .button-image i').replaceWith('<i class="fa fa-upload"></i>');
						$('.store-<?php echo $store_id; ?> .button-image').prop('disabled', false);
						$('#modal-image').on('hide.bs.modal', function () {
							if ($('#tmp-<?php echo $store_id; ?>').val() != "") {
								var target = $('#tmp-<?php echo $store_id; ?>').attr('target');
								$("#" + target).val($('#tmp-<?php echo $store_id; ?>').val());
								{{ target }}
								if (target == 'neoseo_watermark_image-<?php echo $store_id; ?>') {

									$.ajax({
										url: 'index.php?route=extension/module/neoseo_watermark/getImgSize&user_token=<?php echo $this->params['user_token']?>',
										data: "src=" + $("#tmp-<?php echo $store_id; ?>").val(),
										type: 'post',
										dataType: 'json',
										success: function (json) {
											var rate, width, height;
											if (json["size"][0] > 130 || json["size"][1] > 130) {
												rate = (json["size"][0] > json["size"][1]) ? (json["size"][0] / 130) : (json["size"][1] / 130);
												width = Math.round(json["size"][0] / rate);
												height = Math.round(json["size"][1] / rate);
											} else {
												width = json["size"][0];
												height = json["size"][1];
											}

											$('#neoseo_watermark_image_thumb-<?php echo $store_id; ?>').css("width", width).css("height", height);
											$('.store-<?php echo $store_id; ?> .draggable-wrapper').css("width", width).css("height", height);
											$('.store-<?php echo $store_id; ?> .ui-wrapper').css("width", width).css("height", height);
										}
									});
									$('#neoseo_watermark_image_thumb-<?php echo $store_id; ?>').attr('src', '<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'image_root'])) ? $this->params[$this->nameSpace . 'image_root'][$store_id] : ''; ?>image/' + $('#tmp-<?php echo $store_id; ?>').val());
								} else if (target == 'neoseo_watermark_product_image-<?php echo $store_id; ?>') {
									$.ajax({
										url: 'index.php?route=extension/module/neoseo_watermark/image&user_token=<?php echo $this->params['user_token']?>&image=' + encodeURIComponent($('#tmp-<?php echo $store_id; ?>').val()),
										dataType: 'text',
										success: function (data) {
											$('.store-<?php echo $store_id; ?> .draggable-zone').css('background-image', 'url(' + data + ')');
										}
									});
								}

							}
						});
					},
					success: function (html) {
						$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
						$('#modal-image').modal('show');
					}
				});
			});
			<?php } ?>
			<?php foreach ($stores as $store_id => $store_data) { ?>
			$('.store-<?php echo $store_id; ?> .button-clear').on('click', function () {
				$("#neoseo_watermark_image_thumb-<?php echo $store_id; ?>")
					.attr('src', '/image/<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image'])) ? $this->params[$this->nameSpace . 'default_image'][$store_id] : ''; ?>')
			.css("width", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_width'])) ? $this->params[$this->nameSpace . 'default_image_width'][$store_id] : '')*3; ?>)
			.css("height", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_height'])) ? $this->params[$this->nameSpace . 'default_image_height'][$store_id] : '')*3; ?>);
				$(".store-<?php echo $store_id; ?> .draggable-wrapper")
					.css("left", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_left'])) ? $this->params[$this->nameSpace . 'default_image_left'][$store_id] : '')*3; ?>)
			.css("top", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_top'])) ? $this->params[$this->nameSpace . 'default_image_top'][$store_id] : '')*3; ?>)
			.css("width", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_width'])) ? $this->params[$this->nameSpace . 'default_image_width'][$store_id] : '')*3; ?>)
			.css("height", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_height'])) ? $this->params[$this->nameSpace . 'default_image_height'][$store_id] : '')*3; ?>);
				$(".store-<?php echo $store_id; ?> .ui-wrapper")
					.css("left", "auto")
					.css("top", "auto")
					.css("width", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_width'])) ? $this->params[$this->nameSpace . 'default_image_width'][$store_id] : '') * 3 ; ?>)
			.css("height", <?php echo ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_height'])) ? $this->params[$this->nameSpace . 'default_image_height'][$store_id] : '') * 3 ; ?>)
			.rotatable("angle", <?php echo str_replace(',','.', ((array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_angle'])) ? $this->params[$this->nameSpace . 'default_image_angle'][$store_id] : '')* 3.1415926/ 180); ?>);
				$("#neoseo_watermark_image-<?php echo $store_id; ?>").val('<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image'])) ? $this->params[$this->nameSpace . 'default_image'][$store_id] : ''; ?>');
				$("#neoseo_watermark_left-<?php echo $store_id; ?>").val(<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_left'])) ? $this->params[$this->nameSpace . 'default_image_left'][$store_id] : ''; ?>);
				$("#neoseo_watermark_top-<?php echo $store_id; ?>").val(<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_top'])) ? $this->params[$this->nameSpace . 'default_image_top'][$store_id] : ''; ?>);
				$("#neoseo_watermark_width-<?php echo $store_id; ?>").val(<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_width'])) ? $this->params[$this->nameSpace . 'default_image_width'][$store_id] : ''; ?>);
				$("#neoseo_watermark_height-<?php echo $store_id; ?>").val(<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_height'])) ? $this->params[$this->nameSpace . 'default_image_height'][$store_id] : ''; ?>);
				$("#neoseo_watermark_angle-<?php echo $store_id; ?>").val(<?php echo (array_key_exists($store_id, $this->params[$this->nameSpace . 'default_image_angle'])) ? $this->params[$this->nameSpace . 'default_image_angle'][$store_id] : ''; ?>);
			});
			<?php } ?>

			function beforeSave(action) {
				var angle = 0;
				$('#input-action').val(action);
				<?php foreach ($stores as $store_id => $store_data) { ?>
				angle = ($(".store-<?php echo $store_id; ?> .ui-wrapper").rotatable("ui").angle.current * 180) / Math.PI;
				if (!angle)
					angle = 0;
				while (angle < 0)
					angle += 360;
				while (angle >= 360)

					angle -= 360;
				$("#neoseo_watermark_angle-<?php echo $store_id; ?>").val(
					Math.ceil(angle));
				$("#neoseo_watermark_width-<?php echo $store_id; ?>").val(
					Math.ceil($(".store-<?php echo $store_id; ?> .resizable-wrapper .ui-wrapper").css('width').replace(/[^0-9]/gi, "") / 3));
				$("#neoseo_watermark_height-<?php echo $store_id; ?>").val(
					Math.ceil($(".store-<?php echo $store_id; ?> .resizable-wrapper .ui-wrapper").css('height').replace(/[^0-9]/gi, "") / 3));
				$("#neoseo_watermark_left-<?php echo $store_id; ?>").val(
					Math.ceil((Number($(".store-<?php echo $store_id; ?> .draggable-wrapper").css('left').replace(/[^\-\.0-9]/gi, "")) +
						Number($(".store-<?php echo $store_id; ?> .ui-wrapper").css('left').replace(/[^\-\.0-9]/gi, ""))) / 3));
				$("#neoseo_watermark_top-<?php echo $store_id; ?>").val(
					Math.ceil((Number($(".store-<?php echo $store_id; ?> .draggable-wrapper").css('top').replace(/[^\-\.0-9]/gi, "")) +
						Number($(".store-<?php echo $store_id; ?> .ui-wrapper").css('top').replace(/[^\-\.0-9]/gi, ""))) / 3));
				<?php } ?>
				return true;
			}
		</script>
		<?php
	}

	public function button($action,$name,$class='btn btn-default')
	{
		?>
        <div class="form-group" style="display: inline-block; width: 100%;">
            <div class="col-sm-12">
                <a class="<?php echo $class; ?>" href="<?php echo $action; ?>"><?php echo $name; ?></a>
            </div>
        </div>
		<?php
	}

	/**
	 * выводит кнопку выбора магазина в настройках модуля
	 *
	 * @param array	$stores магазины
	 */
	public function storesDropdown($stores)
	{
		?>
        <select id="select-store-dropdown" class="form-control pull-left">
            <?php foreach($stores as $store_id => $store_data) { ?>
                <option value="<?php echo $store_id; ?>"><?php echo $store_data['name']; ?></option>
            <?php } ?>
        </select>

		<script type="text/javascript">
			$(document).ready(function() {
				$('body').on('change', '#select-store-dropdown', function() {
					var store_id = $(this).val();

					$('.stores-group').each(function() {
						if ($(this).hasClass('store-' + store_id))
							$(this).removeClass('hidden');
						else
							$(this).addClass('hidden');
					});

				});
			});
		</script>
		<?php
	}

	public function usefullLinks()
	{
		?>
        <div class="form-group" style="display: inline-block; width: 100%;">
            <div class="col-sm-12">
                <ul>
                    <?php if (isset($this->params['instruction_link'])) { ?>
                        <li><b><?php echo $this->params['entry_instruction']; ?></b> <?php echo $this->params['instruction_link']; ?></li>
                    <?php } ?>
                    <?php if (isset($this->params['history_link'])) { ?>
                        <li><b><?php echo $this->params['entry_history']; ?></b> <?php echo $this->params['history_link']; ?></li>
                    <?php } ?>
                    <?php if (isset($this->params['faq_link'])) { ?>
                        <li><b><?php echo $this->params['entry_faq']; ?></b> <?php echo $this->params['faq_link']; ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
		<?php
	}

	public function newWidgets($widget, $property, $options = array())
	{
		$main_class = 'col-sm-12';
		$label_class = 'col-sm-3';
		$input_class = 'col-sm-9';
		$required = '';
		$placeholder = '';
		$languages = array();
		$listItems = array();

		foreach ($options as $k=>$v){
			if(isset(${$k})){
				${$k} = $v;
			}
		}
		$desc = "";
		if(!is_array($property)){
			if (isset($this->params['entry_' . $property . '_help'])) {
				$title =  "<span data-toggle='tooltip' title=\"" . $this->params['entry_' . $property . '_help'] . "\" >" . $this->params['entry_' . $property] . "</span>";
			} else {
				$title = $this->params['entry_' . $property];
			}
			if (isset($this->params['entry_' . $property . '_desc'])) {
				$desc =  "<br><small style=\"font-weight: 400\">". $this->params['entry_' . $property . '_desc']."</small>";
			}

		} else {
			// $property[0] - Общее название, для лэйбла
			// $property[1] - 1е свойство
			// $property[2] - 2е свойство
			if (isset($this->params['entry_' . $property[0] . '_help'])) {
				$title =  "<span data-toggle='tooltip' title=\"" . $this->params['entry_' . $property[0] . '_help'] . "\" >" . $this->params['entry_' . $property[0]] . "</span>";
			} else {
				$title = $this->params['entry_' . $property[0]];
			}
			if (isset($this->params['entry_' . $property[0] . '_desc'])) {
				$desc =  "<br><small style=\"font-weight: 400\">". $this->params['entry_' . $property[0] . '_desc']."</small>";
			}
		}

		// Пробовал на switch... ничего не понятно потом. Не красивый код
		if($widget == 'switcher'){
			return "<div class='form-group $main_class' id='field_{$property}' >
            <label class='$label_class control-label' for='cb_{$this->nameSpace}{$property}'>".$title.$desc."</label>
            <div class='$input_class'>
                <input type='hidden' name='{$this->nameSpace}{$property}' value='".(($this->params[$this->nameSpace . $property] == 1)?"1":"0")."' id='i_{$this->nameSpace}{$property}'>
                <input type='checkbox' value='1' id='cb_{$this->nameSpace}{$property}' ".(($this->params[$this->nameSpace . $property] == 1)?"checked='checked'":"")." data-iname='i_{$this->nameSpace}{$property}' onclick='let iname = \$(this).attr(\"data-iname\");if(\$(this).is(\":checked\")){\$(\"#\"+iname).val(1)}else{\$(\"#\"+iname).val(0)}'>
                <span class='switcher'></span>
            </div>
            </div>";
		} elseif ($widget == 'double_input' && is_array($property)){
			return "<div class='form-group $required'>
                  <label class='$label_class control-label' for='{$property[1]}'>".$title.$desc."</label>
                  <div class='$main_class'>
                    <div class='row'>
                      <div class='$input_class'>
                        <input type='text' name='{$this->nameSpace}{$property[1]}' value='{$this->params[$this->nameSpace . $property[1]]}' placeholder='".(is_array($placeholder)?$placeholder[1]:"")."' id='{$property[1]}' class='form-control' />
                      </div>
                      <div class='$input_class'>
                        <input type='text' name='{$this->nameSpace}{$property[2]}' value='{$this->params[$this->nameSpace . $property[2]]}' placeholder='".(is_array($placeholder)?$placeholder[2]:"")."' class='form-control' />
                      </div>
                    </div>
                    ".((isset($this->params['error_' . $property[0]]) && $this->params['error_' . $property[0]])?"<div class='text-danger'>{$this->params['error_' . $property[0]]}</div>":"")."
                  </div>
                </div>";
		} elseif($widget == 'gradient_input') {
			if($this->params[$this->nameSpace . $property] == "" || strlen($this->params[$this->nameSpace . $property]) > 25){
				$this->params[$this->nameSpace . $property] = "#000000";
			}
			return "<div class='form-group $main_class'>
            <label class='$label_class control-label text-left' >".$title.$desc."</label>
            <div class='$input_class'>
                <input type='text' name='{$this->nameSpace}{$property}' id='{$this->nameSpace}{$property}' class='gradientInput' value='{$this->params[$this->nameSpace . $property]}'>
            </div>
</div><script>
  $('#{$this->nameSpace}{$property}').coloringPick({
    on_select: function (hex) {
      $(this).children('input').val(hex);
    },
    change: function (hex) {
      $(this).children('input').val(hex);
    },
    'picker':'solid',
    'picker_changeable' : false,
  });
  </script>";
		} elseif($widget == 'input') {
			return "<div class='form-group $main_class $required'>
                      <label class='$label_class control-label text-left' for='{$this->nameSpace}{$property}'>".$title.$desc."</label>
                      <div class='$input_class'>
                        <input type='text' class='form-control' name='{$this->nameSpace}{$property}' value='{$this->params[$this->nameSpace . $property]}' placeholder='$placeholder'>
                        ".((isset($this->params['error_' . $property]) && $this->params['error_' . $property])?"<div class='text-danger'>{$this->params['error_' . $property]}</div>":"")."
                      </div>
                    </div>";
		} elseif($widget == 'color') {
			return "<div class='form-group $main_class $required'>
                      <label class='$label_class control-label text-left' for='{$this->nameSpace}{$property}'>".$title.$desc."</label>
                      <div class='$input_class'>
                        <input type='color' class='form-control' name='{$this->nameSpace}{$property}' value='{$this->params[$this->nameSpace . $property]}' placeholder='$placeholder'>
                        ".((isset($this->params['error_' . $property]) && $this->params['error_' . $property])?"<div class='text-danger'>{$this->params['error_' . $property]}</div>":"")."
                      </div>
                    </div>";
		} elseif($widget == 'input_multilang') {
			$data = "<div class='form-group $main_class $required'>
                      <label class='$label_class control-label text-left' for='{$this->nameSpace}{$property}'>".$title.$desc."</label>
                      <div class='$input_class'>";

			foreach ($languages as $language){
				$data .="<div class='input-group'>
                    <span class='input-group-addon'>
                        <img src='language/{$language['code']}/{$language['code']}.png' title='{$language['name']}'/>
                    </span>
                <input type='text' class='form-control' name='{$this->nameSpace}{$property}[{$language['language_id']}]' value='{$this->params[$this->nameSpace . $property][$language['language_id']]}' placeholder='$placeholder'></div>";

			}
			$data .= ((isset($this->params['error_' . $property]) && $this->params['error_' . $property])?"<div class='text-danger'>{$this->params['error_' . $property]}</div>":"")."</div></div>";
			return $data;
		} elseif($widget == 'dropdown') {
			$data = "<div class='form-group $main_class $required'>
                      <label class='$label_class control-label' for='{$this->nameSpace}{$property}'>".$title.$desc."</label>
                      <div class='$input_class'>
                        <select name='{$this->nameSpace}{$property}' id='{$this->nameSpace}{$property}' class='form-control'>";
			foreach ($listItems as $key=>$value){
				$data .= "<option value='$key' ".(($this->params[$this->nameSpace . $property] == $key)?" selected ":"").">$value</option>";
			}
			$data .="</select>";
			$data .= ((isset($this->params['error_' . $property]) && $this->params['error_' . $property])?"<div class='text-danger'>{$this->params['error_' . $property]}</div>":"")."</div></div>";
			return $data;
		} elseif($widget == 'textarea') {
			return "<div class='form-group $main_class $required'>
                      <label class='$label_class control-label text-left' for='{$this->nameSpace}{$property}'>".$title.$desc."</label>
                      <div class='$input_class'>
                        <textarea type='text' class='form-control' name='{$this->nameSpace}{$property}' placeholder='$placeholder'>{$this->params[$this->nameSpace . $property]}</textarea>
                        ".((isset($this->params['error_' . $property]) && $this->params['error_' . $property])?"<div class='text-danger'>{$this->params['error_' . $property]}</div>":"")."
                      </div>
                    </div>";
		} elseif($widget == 'textarea_multilang') {

			$data = " <div class='form-group $main_class $required'>
                        <label class='$label_class control-label text-left' >".$title.$desc."</label>
                        <div class='$input_class'>
                          <ul class='nav nav-tabs'>";
			foreach ($languages as $language) {
				$data .= "<li class='".($language['language_id'] == $this->config_language_id ? 'active' : '')."'><a href='#column-{$this->nameSpace}{$property}_{$language['language_id']}' data-toggle='tab'><img src='language/{$language['code']}/{$language['code']}.png' title='{$language['name']}'>{$language['name']}</a></li>";
			}

			$data .="</ul><div class='tab-content'>";
			foreach ($languages as $language) {
				$data .= "<div id='column-{$this->nameSpace}{$property}_{$language['language_id']}' class='tab-pane fade ".($language['language_id'] == $this->config_language_id ? 'active in' : '')." '>
        <textarea type='text' data-toggle='summernote' data-lang='ru-RU' name='{$this->nameSpace}{$property}[{$language['language_id']}]'>{$this->params[$this->nameSpace . $property][$language['language_id']]}</textarea>
        </div>";
			}
			$data .= "</div>";
			$data .= ((isset($this->params['error_' . $property]) && $this->params['error_' . $property])?"<div class='text-danger'>{$this->params['error_' . $property]}</div>":"")."</div></div>";

			return $data;
		} else {
			// не поняли что за виджет
			return "<div class='alert alert-danger'>Check widget name</div>";
		}
		return "<div class='alert alert-danger'>Widget return nothing :)</div>";
	}
}
