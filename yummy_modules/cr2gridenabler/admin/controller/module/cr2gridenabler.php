<?php
class ControllerModuleCR2GridEnabler extends Controller {
	private $error = array();
	private $_name = "cr2gridenabler";
	private $_version = "0.5.1";

	public function index() {
		$this->load->language('module/cr2gridenabler');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cr2gridenabler', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['entry_enabled'] = $this->language->get('entry_enabled');
		$this->data['entry_mode'] = $this->language->get('entry_mode');
		$this->data['entry_function0'] = $this->language->get('entry_function0');
		$this->data['entry_function1'] = $this->language->get('entry_function1');

		$this->data['entry_moduleselector'] = $this->language->get('entry_moduleselector');
		$this->data['entry_selector0'] = $this->language->get('entry_selector0');
		$this->data['entry_selector1'] = $this->language->get('entry_selector1');

		$this->data['entry_area1'] = $this->language->get('entry_area1');

		$this->data['entry_moduleposition'] = $this->language->get('entry_moduleposition');
		$this->data['help_moduleposition'] = $this->language->get('help_moduleposition');
		$this->data['entry_contenttop'] = $this->language->get('entry_contenttop');
		$this->data['entry_contentbottom'] = $this->language->get('entry_contentbottom');
		$this->data['entry_columnleft'] = $this->language->get('entry_columnleft');
		$this->data['entry_columnright'] = $this->language->get('entry_columnright');

		$this->data['entry_layoutcount'] = $this->language->get('entry_layoutcount');
		$this->data['entry_layoutwarning'] = $this->language->get('entry_layoutwarning');
		$this->data['entry_layouts'] = $this->language->get('entry_layouts');
		$this->data['help_layouts'] = $this->language->get('help_layouts');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_menuitem'] = $this->language->get('button_add_menuitem');
		$this->data['button_remove'] = $this->language->get('button_remove');

		$this->data['_version'] = $this->_version;
		$this->data['_info'] = $this->language->get('info');

		$this->load->model('design/layout');
		$layouts = $this->model_design_layout->getLayouts();
		$this->data['layouts'] = $layouts;

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/cr2gridenabler', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('module/cr2gridenabler', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


          if (isset($this->request->post['cr2gridenabler_moduleposition'])) {
			$this->data['cr2gridenabler_moduleposition'] = $this->request->post['cr2gridenabler_moduleposition'];
		} elseif ($this->config->get('cr2gridenabler_moduleposition')) {
			$this->data['cr2gridenabler_moduleposition'] = $this->config->get('cr2gridenabler_moduleposition');
		} else {$this->data['cr2gridenabler_moduleposition'] = "content_top"; };

          if (isset($this->request->post['cr2gridenabler_layouts'])) {
			$this->data[$this->_name.'_layouts'] = $this->request->post['cr2gridenabler_layouts'];
		} elseif ($this->config->get('cr2gridenabler_layouts')) {
			$this->data[$this->_name.'_layouts'] = $this->config->get('cr2gridenabler_layouts');
		} else {$this->data['cr2gridenabler_layouts'] = array(0=>3,1=>5); };


		// fill in all modules with desired position
		$i = 0;
		$this->data['modules'] = array();
		foreach($layouts as $lay){
		    if (in_array($lay['layout_id'],$this->data['cr2gridenabler_layouts'])) {
                   $this->data['modules'][$i] = array('layout_id' => $lay['layout_id'], 'position' => $this->data['cr2gridenabler_moduleposition'], 'status' => 1, 'sort_order' => 0);
                   $i++;
                   };
		}
		$this->data['cr2gridenabler_layoutcount'] = count($layouts);
          // end fill in


          if (isset($this->request->post['cr2gridenabler_status'])) {
			$this->data[$this->_name.'_status'] = $this->request->post['cr2gridenabler_status'];
		} elseif ($this->config->get('cr2gridenabler_status')) {
			$this->data[$this->_name.'_status'] = $this->config->get('cr2gridenabler_status');
		}
          if (isset($this->request->post['cr2gridenabler_mode'])) {
			$this->data[$this->_name.'_mode'] = $this->request->post['cr2gridenabler_mode'];
		} elseif ($this->config->get('cr2gridenabler_mode')) {
			$this->data[$this->_name.'_mode'] = $this->config->get('cr2gridenabler_mode');
		}
          if (isset($this->request->post['cr2gridenabler_selector'])) {
			$this->data[$this->_name.'_selector'] = $this->request->post['cr2gridenabler_mode'];
		} elseif ($this->config->get('cr2gridenabler_selector')) {
			$this->data[$this->_name.'_selector'] = $this->config->get('cr2gridenabler_selector');
		}

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$this->data['languages'] = $languages;

		$this->template = 'module/cr2gridenabler.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/cr2gridenabler')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>