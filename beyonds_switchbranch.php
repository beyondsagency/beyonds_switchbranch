<?php
/*
* MIT License
*
* Copyright (c) 2022 Agence Beyonds
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\Module\Beyonds_switchbranch\Config\ConfigForm;
use PrestaShop\Module\Beyonds_switchbranch\Adapter\Git\GitClient;
use PrestaShop\Module\Beyonds_switchbranch\Adapter\Git\GitClientException;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\AjaxProcess;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\ResponseJsonPresenter;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\DefaultResponseFactory;

class Beyonds_switchbranch extends Module
{
    protected $configForm;
    protected $gitClient;

    public function __construct()
    {
        $this->name = 'beyonds_switchbranch';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'beyonds';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Switch branches');
        $this->description = $this->l('Switch and show branches');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];

        $this->configForm = new ConfigForm($this);
        $this->gitClient = new GitClient();

        if (Tools::isSubmit('submitBeyondsGitAjax')) {
            (new AjaxProcess(
                DefaultResponseFactory::create(),
                new ResponseJsonPresenter()
            ))->run();
        }
    }

    public function install()
    {
        $this->configForm->seDefaultConfig();
        return parent::install()
            && $this->registerHook('backOfficeHeader')
        ;
    }

    public function uninstall()
    {
        $this->configForm->deleteConfig();
        return parent::uninstall();
    }

    public function getContent()
    {
        return $this->configForm->getContent();
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getContext()
    {
        return $this->context;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if ($this->configForm->employeeHasAccess()) {
            try {
                $output = $this
                    ->context
                    ->smarty
                    ->assign([
                        'curren_branch_name' => $this->gitClient->getCurrentBranchName(),
                        'branch_list' => $this->gitClient->getBranchList(),
                        'module_name' => $this->name,
                    ])
                    ->fetch($this->local_path . 'views/templates/admin/branches.tpl')
                ;
            } catch (GitClientException $gitException) {
                $this->context->controller->warnings[] = $this->l('Erreur module ').$this->name.' '.$gitException->getMessage();
                return;
            }

            Media::addJsDef([
                'branch_selector' => $output,
                'header_selector' => Configuration::get(ConfigForm::HEADER_CSS_SELECTOR),
                'reload_page_after' => Configuration::get(ConfigForm::RELOAD_PAGE),
                'checkout_selector' => '.'.$this->name.'_switch',
                'pull_selector' => '.'.$this->name.'_pull',
                'ajax_url' => $this->context->link->getAdminLink(
                    'AdminModules',
                    true,
                    [],
                    [
                        'configure' => $this->name
                    ]
                ),
                'confirm_message' => $this->l('Cette branche semble une branche technique. Voulez-vous réaliser cette action ?'),
            ]);

            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }
}
