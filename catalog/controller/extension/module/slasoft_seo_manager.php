<?php
class ControllerExtensionModuleSlaSoftSeoManager extends Controller {
	public function index() {
		$this->load->config('slasoft_seo_manager');
		if (isset($this->request->get['secret']) && $this->request->get['secret'] && $this->request->get['secret'] == $this->config->get('slasoft_seo_manager_secret') || php_sapi_name() == 'cli') {
			$seourl = new Seomanager\Seourl($this->registry);
			$seourl->mainGenerate();
		} else {
			$log = NEW LOG('slasoft_seomanager.log');
			$log->write('error acccess');
		}
	}
}