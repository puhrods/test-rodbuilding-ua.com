<?php
class ControllerToolSpeakerlaaplog extends Controller {
	private $error = array();

	public function index() {		
		$this->load->language('speaker/speakerlaap');
		
		$this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/speakerlaaplog');

        $seo_url_total = $this->model_tool_speakerlaaplog->getTotalLastAction();

        $url = '';

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $pagination = new Pagination();
        $pagination->total = $seo_url_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('tool/speakerlaaplog', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($seo_url_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($seo_url_total - $this->config->get('config_limit_admin'))) ? $seo_url_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $seo_url_total, ceil($seo_url_total / $this->config->get('config_limit_admin')));


        if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('tool/speakerlaaplog', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['clear'] = $this->url->link('tool/speakerlaaplog/clear', 'user_token=' . $this->session->data['user_token'], true);

        $this->load->model('tool/speakerlaaplog');

        $filter_data = array(
            'start'              => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'              => $this->config->get('config_limit_admin')
        );

        $get_all_action = $this->model_tool_speakerlaaplog->getListActions($filter_data);

        foreach ($get_all_action as $result) {
            $dateformat = date('d.m.Y H:i:s', $result['date']);
            $data['all_actions'][] = array(
                'date'   => $dateformat,
                'login'  => $result['login'],
                'ip'     => $result['ip'],
                'action' => $result['action'],
                'edit'   => $this->url->link('user/user/edit', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $result['user_id'], true)
            );
        }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tool/speakerlaaplog', $data));
	}

    public function clear() {
        $this->load->model('tool/speakerlaaplog');

        $this->model_tool_speakerlaaplog->clearTableActions();

        $this->response->redirect($this->url->link('tool/speakerlaaplog', 'user_token=' . $this->session->data['user_token'], true));
    }
}