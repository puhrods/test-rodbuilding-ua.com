<?php
class ControllerExtensionModuleSpeakerlaap extends Controller {
	private $error = array();

	public function index() {
        $this->load->language('extension/module/speakerlaap');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_speakerlaap', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/speakerlaap', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/speakerlaap', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_speakerlaap_status'])) {
			$data['module_speakerlaap_status'] = $this->request->post['module_speakerlaap_status'];
		} else {
			$data['module_speakerlaap_status'] = $this->config->get('module_speakerlaap_status');
		}

		if (isset($this->request->post['module_speakerlaap_deletedays'])) {
			$data['module_speakerlaap_deletedays'] = $this->request->post['module_speakerlaap_deletedays'];
		} else {
			$data['module_speakerlaap_deletedays'] = $this->config->get('module_speakerlaap_deletedays');
		}

        if (isset($this->request->post['module_speakerlaap_savecat'])) {
            $data['module_speakerlaap_savecat'] = $this->request->post['module_speakerlaap_savecat'];
        } else {
            $data['module_speakerlaap_savecat'] = $this->config->get('module_speakerlaap_savecat');
        }

        if (isset($this->request->post['module_speakerlaap_saveprod'])) {
            $data['module_speakerlaap_saveprod'] = $this->request->post['module_speakerlaap_saveprod'];
        } else {
            $data['module_speakerlaap_saveprod'] = $this->config->get('module_speakerlaap_saveprod');
        }

        if (isset($this->request->post['module_speakerlaap_saverecur'])) {
            $data['module_speakerlaap_saverecur'] = $this->request->post['module_speakerlaap_saverecur'];
        } else {
            $data['module_speakerlaap_saverecur'] = $this->config->get('module_speakerlaap_saverecur');
        }

        if (isset($this->request->post['module_speakerlaap_savefilter'])) {
            $data['module_speakerlaap_savefilter'] = $this->request->post['module_speakerlaap_savefilter'];
        } else {
            $data['module_speakerlaap_savefilter'] = $this->config->get('module_speakerlaap_savefilter');
        }

        if (isset($this->request->post['module_speakerlaap_saveattr'])) {
            $data['module_speakerlaap_saveattr'] = $this->request->post['module_speakerlaap_saveattr'];
        } else {
            $data['module_speakerlaap_saveattr'] = $this->config->get('module_speakerlaap_saveattr');
        }

        if (isset($this->request->post['module_speakerlaap_saveattr_g'])) {
            $data['module_speakerlaap_saveattr_g'] = $this->request->post['module_speakerlaap_saveattr_g'];
        } else {
            $data['module_speakerlaap_saveattr_g'] = $this->config->get('module_speakerlaap_saveattr_g');
        }

        if (isset($this->request->post['module_speakerlaap_saveoption'])) {
            $data['module_speakerlaap_saveoption'] = $this->request->post['module_speakerlaap_saveoption'];
        } else {
            $data['module_speakerlaap_saveoption'] = $this->config->get('module_speakerlaap_saveoption');
        }

        if (isset($this->request->post['module_speakerlaap_savemanuf'])) {
            $data['module_speakerlaap_savemanuf'] = $this->request->post['module_speakerlaap_savemanuf'];
        } else {
            $data['module_speakerlaap_savemanuf'] = $this->config->get('module_speakerlaap_savemanuf');
        }

        if (isset($this->request->post['module_speakerlaap_savedownload'])) {
            $data['module_speakerlaap_savedownload'] = $this->request->post['module_speakerlaap_savedownload'];
        } else {
            $data['module_speakerlaap_savedownload'] = $this->config->get('module_speakerlaap_savedownload');
        }

        if (isset($this->request->post['module_speakerlaap_savereview'])) {
            $data['module_speakerlaap_savereview'] = $this->request->post['module_speakerlaap_savereview'];
        } else {
            $data['module_speakerlaap_savereview'] = $this->config->get('module_speakerlaap_savereview');
        }

        if (isset($this->request->post['module_speakerlaap_saveinfo'])) {
            $data['module_speakerlaap_saveinfo'] = $this->request->post['module_speakerlaap_saveinfo'];
        } else {
            $data['module_speakerlaap_saveinfo'] = $this->config->get('module_speakerlaap_saveinfo');
        }

        if (isset($this->request->post['module_speakerlaap_saveb_article'])) {
            $data['module_speakerlaap_saveb_article'] = $this->request->post['module_speakerlaap_saveb_article'];
        } else {
            $data['module_speakerlaap_saveb_article'] = $this->config->get('module_speakerlaap_saveb_article');
        }

        if (isset($this->request->post['module_speakerlaap_saveb_cat'])) {
            $data['module_speakerlaap_saveb_cat'] = $this->request->post['module_speakerlaap_saveb_cat'];
        } else {
            $data['module_speakerlaap_saveb_cat'] = $this->config->get('module_speakerlaap_saveb_cat');
        }

        if (isset($this->request->post['module_speakerlaap_saveb_review'])) {
            $data['module_speakerlaap_saveb_review'] = $this->request->post['module_speakerlaap_saveb_review'];
        } else {
            $data['module_speakerlaap_saveb_review'] = $this->config->get('module_speakerlaap_saveb_review');
        }

        if (isset($this->request->post['module_speakerlaap_savelayout'])) {
            $data['module_speakerlaap_savelayout'] = $this->request->post['module_speakerlaap_savelayout'];
        } else {
            $data['module_speakerlaap_savelayout'] = $this->config->get('module_speakerlaap_savelayout');
        }

        if (isset($this->request->post['module_speakerlaap_savebanner'])) {
            $data['module_speakerlaap_savebanner'] = $this->request->post['module_speakerlaap_savebanner'];
        } else {
            $data['module_speakerlaap_savebanner'] = $this->config->get('module_speakerlaap_savebanner');
        }

        if (isset($this->request->post['module_speakerlaap_saveseo_url'])) {
            $data['module_speakerlaap_saveseo_url'] = $this->request->post['module_speakerlaap_saveseo_url'];
        } else {
            $data['module_speakerlaap_saveseo_url'] = $this->config->get('module_speakerlaap_saveseo_url');
        }

        if (isset($this->request->post['module_speakerlaap_savereturn'])) {
            $data['module_speakerlaap_savereturn'] = $this->request->post['module_speakerlaap_savereturn'];
        } else {
            $data['module_speakerlaap_savereturn'] = $this->config->get('module_speakerlaap_savereturn');
        }

        if (isset($this->request->post['module_speakerlaap_savevoucher'])) {
            $data['module_speakerlaap_savevoucher'] = $this->request->post['module_speakerlaap_savevoucher'];
        } else {
            $data['module_speakerlaap_savevoucher'] = $this->config->get('module_speakerlaap_savevoucher');
        }

        if (isset($this->request->post['module_speakerlaap_savevoucher_theme'])) {
            $data['module_speakerlaap_savevoucher_theme'] = $this->request->post['module_speakerlaap_savevoucher_theme'];
        } else {
            $data['module_speakerlaap_savevoucher_theme'] = $this->config->get('module_speakerlaap_savevoucher_theme');
        }

        if (isset($this->request->post['module_speakerlaap_savecustomer'])) {
            $data['module_speakerlaap_savecustomer'] = $this->request->post['module_speakerlaap_savecustomer'];
        } else {
            $data['module_speakerlaap_savecustomer'] = $this->config->get('module_speakerlaap_savecustomer');
        }

        if (isset($this->request->post['module_speakerlaap_savecustomer_group'])) {
            $data['module_speakerlaap_savecustomer_group'] = $this->request->post['module_speakerlaap_savecustomer_group'];
        } else {
            $data['module_speakerlaap_savecustomer_group'] = $this->config->get('module_speakerlaap_savecustomer_group');
        }

        if (isset($this->request->post['module_speakerlaap_savecustom_field'])) {
            $data['module_speakerlaap_savecustom_field'] = $this->request->post['module_speakerlaap_savecustom_field'];
        } else {
            $data['module_speakerlaap_savecustom_field'] = $this->config->get('module_speakerlaap_savecustom_field');
        }

        if (isset($this->request->post['module_speakerlaap_savemarketing'])) {
            $data['module_speakerlaap_savemarketing'] = $this->request->post['module_speakerlaap_savemarketing'];
        } else {
            $data['module_speakerlaap_savemarketing'] = $this->config->get('module_speakerlaap_savemarketing');
        }

        if (isset($this->request->post['module_speakerlaap_savecoupon'])) {
            $data['module_speakerlaap_savecoupon'] = $this->request->post['module_speakerlaap_savecoupon'];
        } else {
            $data['module_speakerlaap_savecoupon'] = $this->config->get('module_speakerlaap_savecoupon');
        }

        if (isset($this->request->post['module_speakerlaap_saves_store'])) {
            $data['module_speakerlaap_saves_store'] = $this->request->post['module_speakerlaap_saves_store'];
        } else {
            $data['module_speakerlaap_saves_store'] = $this->config->get('module_speakerlaap_saves_store');
        }

        if (isset($this->request->post['module_speakerlaap_saves_user'])) {
            $data['module_speakerlaap_saves_user'] = $this->request->post['module_speakerlaap_saves_user'];
        } else {
            $data['module_speakerlaap_saves_user'] = $this->config->get('module_speakerlaap_saves_user');
        }

        if (isset($this->request->post['module_speakerlaap_saves_user_group'])) {
            $data['module_speakerlaap_saves_user_group'] = $this->request->post['module_speakerlaap_saves_user_group'];
        } else {
            $data['module_speakerlaap_saves_user_group'] = $this->config->get('module_speakerlaap_saves_user_group');
        }

        if (isset($this->request->post['module_speakerlaap_saves_api'])) {
            $data['module_speakerlaap_saves_api'] = $this->request->post['module_speakerlaap_saves_api'];
        } else {
            $data['module_speakerlaap_saves_api'] = $this->config->get('module_speakerlaap_saves_api');
        }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/speakerlaap', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/speakerlaap')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
        $this->load->model('extension/module/speakerlaap');
        $this->model_extension_module_speakerlaap->install();
	}

    public function uninstall() {
        $this->load->model('extension/module/speakerlaap');
        $this->model_extension_module_speakerlaap->uninstall();
    }
}