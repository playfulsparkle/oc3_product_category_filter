<?php
class ControllerExtensionModulePsProductCategoryFilter extends Controller
{
    /**
     * @var string The support email address.
     */
    const EXTENSION_EMAIL = 'support@playfulsparkle.com';

    /**
     * @var string The documentation URL for the extension.
     */
    const EXTENSION_DOC = 'https://github.com/playfulsparkle/oc3_product_category_filter.git';

    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/ps_product_category_filter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ps_product_category_filter', $this->request->post);

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
            'href' => $this->url->link('extension/module/ps_product_category_filter', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/ps_product_category_filter', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_ps_product_category_filter_status'])) {
            $data['module_ps_product_category_filter_status'] = (bool) $this->request->post['module_ps_product_category_filter_status'];
        } else {
            $data['module_ps_product_category_filter_status'] = (bool) $this->config->get('module_ps_product_category_filter_status');
        }

        $data['text_contact'] = sprintf($this->language->get('text_contact'), self::EXTENSION_EMAIL, self::EXTENSION_EMAIL, self::EXTENSION_DOC);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/ps_product_category_filter', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/ps_product_category_filter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }


    public function install()
    {
        $this->load->model('setting/setting');

        $data = array(
            'module_ps_product_category_filter_status' => 0,
        );

        $this->model_setting_setting->editSetting('module_ps_product_category_filter', $data);

        $this->load->model('extension/module/ps_product_category_filter');

        $this->model_extension_module_ps_product_category_filter->install();
    }

    public function uninstall()
    {
        $this->load->model('extension/module/ps_product_category_filter');

        $this->model_extension_module_ps_product_category_filter->uninstall();
    }

    public function removeunused()
    {
        $this->load->language('extension/module/ps_product_category_filter');

        $this->load->model('extension/module/ps_product_category_filter');

        if (isset($this->request->get['category_id'])) {
            $category_id = (int) $this->request->get['category_id'];
        } else {
            $category_id = 0;
        }

        $this->model_extension_module_ps_product_category_filter->removeUnusedFilters($category_id);

        $this->session->data['success'] = $this->language->get('text_filter_success');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->response->redirect($this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }
}
