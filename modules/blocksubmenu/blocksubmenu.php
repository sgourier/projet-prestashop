<?php
/**
 * Created by PhpStorm.
 * User: Sylvain Gourier
 * Date: 24/05/2016
 * Time: 17:47
 */
if(!defined('_PS_VERSION_'))
{
	exit;
}

class BlockSubMenu extends Module
{
	public function __construct()
	{
		$this->name = 'blocksubmenu';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'JeBeSyBeTi';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array(
			'min' => '1.6',
			'max' => _PS_VERSION_
		);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Sous menu sports');
		$this->description = $this->l("Permet d'afficher dans un sous menu les sous catégories dans un slider avec leurs images");

		$this->confirmUninstall = $this->l("Êtes vous sûr de vouloir désinstaller ce module ?");
	}

	function install()
	{
		if(!parent::install() || !$this->registerHook('displaySubHeader') || !Configuration::updateValue("SUBMENU_ITEM_SIZE",36) || !Configuration::updateValue("SUBMENU_ITEM_NUMBER",0))
		{
			return false;
		}
		return true;
	}

	function uninstall()
	{
		if(!parent::uninstall())
		{
			return false;
		}
		return true;
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/slick.css', 'all');
		$this->context->controller->addCSS($this->_path.'css/blockSubMenu.css', 'all');
		$this->context->controller->addJS($this->_path.'js/slick.min.js', 'all');
		$this->context->controller->addJS($this->_path.'js/subHeader.js', 'all');
	}
	
	public function hookDisplaySubHeader()
	{
		if (!$this->active)
			return;

		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$category = new CategoryCore(Tools::getValue('id_category'));
		$hasParent = false;
		$items = $category->getAllChildren($default_lang);

		if($category->id_parent != null && $category->id_parent != 0)
			$hasParent = true;
		
		$this->smarty->assign(array(
			'itemSize' => Configuration::get('SUBMENU_ITEM_SIZE'),
			'itemNumber' => Configuration::get('SUBMENU_ITEM_NUMBER'),
			'hasParent' => $hasParent,
			'items' => $items
		));
		return $this->display(__FILE__, 'payname.tpl');
	}

	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{
			$thumbnailsSize = Tools::getValue('SUBMENU_ITEM_SIZE');
			$nbDisplayedItems = Tools::getValue('SUBMENU_ITEM_NUMBER');
			if ( empty($thumbnailsSize) || empty($nbDisplayedItems))
				$output .= $this->displayError($this->l('Invalid Configuration value'));
			else
			{
				Configuration::updateValue('SUBMENU_ITEM_SIZE', $thumbnailsSize);
				Configuration::updateValue('SUBMENU_ITEM_NUMBER', $nbDisplayedItems);
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}

		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l(''),
				'icon' => 'icon-lock'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Size of the thumbnails (in px)'),
					'name' => 'SUBMENU_ITEM_SIZE',
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Number of item displayed (0 to display all possible)'),
					'name' => 'SUBMENU_ITEM_NUMBER',
					'required' => true
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);

		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// title and Toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
				array(
					'desc' => $this->l('Save'),
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
					          '&token='.Tools::getAdminTokenLite('AdminModules'),
				),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);

		// Load current value
		$helper->fields_value['SUBMENU_ITEM_SIZE'] = Configuration::get('SUBMENU_ITEM_SIZE');
		$helper->fields_value['SUBMENU_ITEM_NUMBER'] = Configuration::get('SUBMENU_ITEM_NUMBER');

		return $helper->generateForm($fields_form);
	}

	/* getCategories arguments
	*  arg 1 - id of the default language
	*  arg 2 - true | false - Only active categories
	*  arg 3 - true | false - Para indicar que no es un pedido
	*
	*  Without arguments the result array is confused [/font]
	[font=courier new,courier,monospace]*/

	//$cats = Category::getCategories( (int)($cookie->id_lang), true, false  ) ;
}