<?php
class ControllerExtensionModuleMayAdvancedOptions extends Controller {

	public function install() {
		$this->load->model('user/user_group');

		$data = $this->model_user_user_group->getUserGroup($this->user->getGroupId());

		$data['permission']['access'][] = 'extension/may';
		$data['permission']['modify'][] = 'extension/may';

        $data['permission']['access'][] = 'extension/may/advanced_options';
        $data['permission']['modify'][] = 'extension/may/advanced_options';

		$files = glob(DIR_APPLICATION . 'controller/extension/may/advanced_options/*.php');

        foreach ($files as $file) {
			$path = 'extension/may/advanced_options/' . str_replace('.php', '', basename($file));

			$data['permission']['access'][] = $path;
			$data['permission']['modify'][] = $path;
		}

		$data['permission']['access'] = array_unique($data['permission']['access']);
		$data['permission']['modify'] = array_unique($data['permission']['modify']);

        $this->model_user_user_group->editUserGroup($this->user->getGroupId(), $data);
        
		$this->load->model('extension/may/advanced_options');
		$this->model_extension_may_advanced_options->install();

		$events = $this->model_extension_may_advanced_options->getEvents();
		$this->load->model('setting/event');
		foreach ($events as $event_code => $event) {
			$this->model_setting_event->deleteEventByCode($event_code);
			$this->model_setting_event->addEvent($event_code, $event['trigger'], $event['action']);
		}

		$this->load->model('setting/setting');

		$this->model_setting_setting->editSetting('module_may_advanced_options', array(
			'module_may_advanced_options_status' => 1
		));

		$this->model_setting_setting->editSetting('may_advanced_options', array(
			'may_advanced_options_status' => 1,

			'may_advanced_options_attribute_model' => 1,
			'may_advanced_options_attribute_sku' => 1,
			'may_advanced_options_attribute_upc' => 1,
			'may_advanced_options_attribute_ean' => 1,
			'may_advanced_options_attribute_jan' => 1,
			'may_advanced_options_attribute_isbn' => 1,
			'may_advanced_options_attribute_mpn' => 1,
			'may_advanced_options_attribute_location' => 1,
			'may_advanced_options_attribute_dimension' => 1,
			'may_advanced_options_attribute_quantity' => 1,
			'may_advanced_options_attribute_stock_status' => 1,
			'may_advanced_options_attribute_price' => 1,
			'may_advanced_options_attribute_point' => 0,
			'may_advanced_options_attribute_weight' => 1,

			'may_advanced_options_show_option_price' => 1,
			'may_advanced_options_swatches' => 1,
			'may_advanced_options_swatch_image' => 0,
			'may_advanced_options_select_first_option' => 0,
			'may_advanced_options_show_first_option_in_list' => 1,
			'may_advanced_options_option_select_effect_in_list' => 0,
			'may_advanced_options_show_attribute_label' => 0,
			'may_advanced_options_show_out_of_stock_option' => 1,

			'may_advanced_options_swatch_style_shape' => 'custom',
			'may_advanced_options_swatch_style_size_width' => 35,
			'may_advanced_options_swatch_style_size_height' => 35,
			'may_advanced_options_swatch_style_size_radius' => 4,
			'may_advanced_options_swatch_style_selected' => 'default',
			'may_advanced_options_swatch_style_out_of_stock' => 'cross',
			'may_advanced_options_swatch_style_border_width' => 1,
			'may_advanced_options_swatch_style_border_color_default' => 'dddddd',
			'may_advanced_options_swatch_style_border_color_selected' => '555555',
			'may_advanced_options_swatch_style_space_padding' => 1,

			'may_advanced_options_swatch_css' => '
.may-swatches label {
	margin-right: 2px;
	position: relative;
	cursor: pointer;
}
.may-swatches label i,
.may-swatches input[type=radio] {
	display: none;
}
.may-swatches input[type=radio] + img,
.may-swatches input[type=radio] + span {
	display: inline-block;
	text-align: center;
	-webkit-transition: border .2s ease-in-out;
	-o-transition: border .2s ease-in-out;
	transition: border .2s ease-in-out;
	-webkit-transition: background .2s ease-in-out;
	-o-transition: background .2s ease-in-out;
	transition: background .2s ease-in-out;
	-webkit-transition: opacity .2s ease-in-out;
	-o-transition: opacity .2s ease-in-out;
	transition: opacity .2s ease-in-out;
	opacity: 0.8;
	vertical-align: top;
}
.may-swatches input[type=radio] + img {
	background: #fff !important;
}
.may-swatches input[type=radio]:enabled + img:hover,
.may-swatches input[type=radio]:enabled + span:hover {
	background-color: #eee;
	opacity: 1;
}
.may-swatches input[type=radio]:checked + img,
.may-swatches input[type=radio]:checked + span {
	opacity: 1 !important;
}
.may-swatches input[type=radio]:checked + img + i.fa-check,
.may-swatches input[type=radio]:checked + span + i.fa-check {
	display: inline-block;
	color: #fff;
	position: absolute;
	text-align: center;
	font-size: 140%;
	-webkit-text-stroke: 0.5px black;
	background: rgba(0,0,0,0.05);
}
.may-swatches input[type=radio].out-of-stock + img + i.slash,
.may-swatches input[type=radio].out-of-stock + span + i.slash {
	display: inline-block;
	position: absolute;
}
.may-swatches input[type=radio].out-of-stock + img + i.cross,
.may-swatches input[type=radio].out-of-stock + span + i.cross {
	display: inline-block;
	position: absolute;
}
.may-swatches input[type=radio].out-of-stock + span {
	color: #ccc;
	background: rgba(0,0,0,0.05);
}
.may-swatches input[type=radio]:disabled + span {
	background-color: #f0f0f0;
	color: #e0e0e0;
	cursor: default;
}
.may-swatches input[type=radio].out-of-stock + img {
	opacity: .3;
}
.may-swatches input[type=radio]:disabled + img {
	opacity: .3;
	cursor: default;
}
.may-swatches.in-grid-view {
	text-align: center;
}
.may-swatches.in-grid-view,
.may-swatches.in-list-view {
	padding-top: 5px;
}
.may-add-to-cart-disabled {
	position: relative;
}
.may-add-to-cart-disabled:after {
	display: block;
	width: 100%;
	height: 100%;
	position: absolute;
	left: 0;
	top: 0;
	z-index: 9999;
	background: rgba(0, 0, 0, 0.3);
}
.may-loading {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 80px;
}
.may-loading div {
	position: absolute;
	width: 6px;
	height: 6px;
	background: #ddd;
	border-radius: 50%;
	animation: may-loading 1.2s linear infinite;
}
.may-loading div:nth-child(1) {
	animation-delay: 0s;
	top: 37px;
	left: 66px;
}
.may-loading div:nth-child(2) {
	animation-delay: -0.1s;
	top: 22px;
	left: 62px;
}
.may-loading div:nth-child(3) {
	animation-delay: -0.2s;
	top: 11px;
	left: 52px;
}
.may-loading div:nth-child(4) {
	animation-delay: -0.3s;
	top: 7px;
	left: 37px;
}
.may-loading div:nth-child(5) {
	animation-delay: -0.4s;
	top: 11px;
	left: 22px;
}
.may-loading div:nth-child(6) {
	animation-delay: -0.5s;
	top: 22px;
	left: 11px;
}
.may-loading div:nth-child(7) {
	animation-delay: -0.6s;
	top: 37px;
	left: 7px;
}
.may-loading div:nth-child(8) {
	animation-delay: -0.7s;
	top: 52px;
	left: 11px;
}
.may-loading div:nth-child(9) {
	animation-delay: -0.8s;
	top: 62px;
	left: 22px;
}
.may-loading div:nth-child(10) {
	animation-delay: -0.9s;
	top: 66px;
	left: 37px;
}
.may-loading div:nth-child(11) {
	animation-delay: -1s;
	top: 62px;
	left: 52px;
}
.may-loading div:nth-child(12) {
	animation-delay: -1.1s;
	top: 52px;
	left: 62px;
}
@keyframes may-loading {
	0%, 20%, 80%, 100% {
		transform: scale(1);
	}
	50% {
		transform: scale(1.5);
	}
}
ul.thumbnails li {
	position: relative;
}
ul.thumbnails li .may-loading {
	position: absolute;
	left: 50%;
	top: 50%;
	margin-left: -40px;
	margin-top: -40px;
}
ul.thumbnails li.image-additional .may-loading {
	display: none;
}
.may-loading div {
	background: #ddd;
}
.hidden {
	display: none !important;
}
#error-quantity.has-error {
	margin-top: 5px;
	color: rgba(208, 30, 36, 1) !important;
}
			',
		));
	}

	public function uninstall() {
		$this->load->model('extension/may/advanced_options');

		$this->model_extension_may_advanced_options->uninstall();

		$this->load->model('setting/event');

		$events = $this->model_setting_event->getEvents();

		foreach ($events as $event) {
			if ($this->model_extension_may_advanced_options->isValidEvent($event['code'])) {
				$this->model_setting_event->deleteEventByCode($event['code']);
			}
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_may_advanced_options');
	}

	public function index() {
		$this->response->redirect($this->url->link('extension/may/advanced_options/setting', 'user_token=' . $this->session->data['user_token'], true));
	}
}
