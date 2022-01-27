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
namespace PrestaShop\Module\Beyonds_switchbranch\Config;

use PrestaShop\Module\Beyonds_switchbranch\Adapter\Git\GitClient;
use HelperForm;
use Tools;
use Configuration;
use Validate;

class ConfigForm
{
    protected $module;
    protected $context;

    const HEADER_CSS_SELECTOR = 'BEYONDS_SWITCHBRANCH_CSS_SELECTOR';
    const EMPLOYEE_DOMAIN = 'BEYONDS_SWITCHBRANCH_EMPLOYEE_DOMAIN';
    const CLEAR_CACHE = 'BEYONDS_SWITCHBRANCH_CLEAR_CACHE';
    const RELOAD_PAGE = 'BEYONDS_SWITCHBRANCH_RELOAD_PAGE';
    const TECHNICAL_BRANCHES_PREFIX = 'BEYONDS_SWITCHBRANCH_TECHNICAL_BRANCHES_PREFIX';
    const TECH_BRANCHES_SEPARATOR = ',';

    public function __construct(\Beyonds_switchbranch $module)
    {
        $this->module = $module;
        $this->context = $this->module->getContext();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->module->getTable();
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->module->getIdentifier();
        $helper->submit_action = 'submitBeyonds_switchbranchModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->module->name.'&tab_module='.$this->module->getTable().'&module_name='.$this->module->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([
            $this->getConfigForm(),
        ]);
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitBeyonds_switchbranchModule')) == true) {
            $this->postProcess();
        }
        return $this->renderForm();
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $formDescription =
            $this->module->l('Dans le champs css selector indiquez où la liste des branches doit s\'afficher')
        ;

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'description' => $formDescription,
                'input' => [
                    [
                        'type' => 'text',
                        'name' => static::HEADER_CSS_SELECTOR,
                        'label' => $this->module->l('Header CSS selector'),
                        'desc' => $this->module->l('Dans le champs css selector indiquez où la liste des branches doit s\'afficher'),
                    ],
                    [
                        'type' => 'text',
                        'name' => static::EMPLOYEE_DOMAIN,
                        'label' => $this->module->l('Employée domain'),
                    ],
                    [
                        'type' => 'text',
                        'name' => static::TECHNICAL_BRANCHES_PREFIX,
                        'label' => $this->module->l('Prefixes de branches technique serparés par ').static::TECH_BRANCHES_SEPARATOR,
                        'desc' => $this->module->l('Certaines branches peuvent demander l\'intervetion d\'un développeur'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Vider le cache'),
                        'name' => static::CLEAR_CACHE,
                        'is_bool' => true,
                        'desc' => $this->module->l('Vide le cache prestashop après le changement de branche'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Recharger la page'),
                        'name' => static::RELOAD_PAGE,
                        'is_bool' => true,
                        'desc' => $this->module->l('Forcer le rechargement de la page après avoir réaliser une action'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled')
                            ]
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        return [
            static::HEADER_CSS_SELECTOR => $this->getConfigValueByKey(static::HEADER_CSS_SELECTOR),
            static::EMPLOYEE_DOMAIN => $this->getConfigValueByKey(static::EMPLOYEE_DOMAIN),
            static::CLEAR_CACHE => $this->getConfigValueByKey(static::CLEAR_CACHE),
            static::RELOAD_PAGE => $this->getConfigValueByKey(static::RELOAD_PAGE),
            static::TECHNICAL_BRANCHES_PREFIX => $this->getConfigValueByKey(static::TECHNICAL_BRANCHES_PREFIX),
        ];
    }

    public function seDefaultConfig()
    {
        Configuration::updateValue(ConfigForm::HEADER_CSS_SELECTOR, '#header_quick');
        Configuration::updateValue(ConfigForm::EMPLOYEE_DOMAIN, null);
        Configuration::updateValue(ConfigForm::CLEAR_CACHE, true);
        Configuration::updateValue(ConfigForm::RELOAD_PAGE, true);
        Configuration::updateValue(ConfigForm::TECHNICAL_BRANCHES_PREFIX, 'dev/,tech/');
    }

    public function deleteConfig()
    {
        Configuration::deleteByName(ConfigForm::HEADER_CSS_SELECTOR);
        Configuration::deleteByName(ConfigForm::EMPLOYEE_DOMAIN);
        Configuration::deleteByName(ConfigForm::CLEAR_CACHE);
        Configuration::deleteByName(ConfigForm::RELOAD_PAGE);
        Configuration::deleteByName(ConfigForm::TECHNICAL_BRANCHES_PREFIX);
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $configKey) {
            $configValue = Tools::getValue($configKey);

            if (is_array($configValue) && Validate::isArrayWithIds($configValue)) {
                $configValue = serialize($configValue);
            }

            Configuration::updateValue($configKey, $configValue);
        }
    }

    public function getConfigValueByKey($configKey)
    {
        $configValue = Configuration::get($configKey);

        if (empty($configValue)) {
            return [];
        }

        if (Validate::isSerializedArray($configValue)) {
            return unserialize($configValue);
        }

        return $configValue;
    }

    public static function getTechnicalBranchesPrefixes()
    {
        $branchesPrefixes = Configuration::get(static::TECHNICAL_BRANCHES_PREFIX);

        if (empty($branchesPrefixes)) {
            return [];
        }

        return array_map('trim', explode(static::TECH_BRANCHES_SEPARATOR, $branchesPrefixes));
    }

    public function employeeHasAccess()
    {
        $availableDomain = $this->getConfigValueByKey(static::EMPLOYEE_DOMAIN);

        if (empty($availableDomain)) {
            return true;
        }

        $employeeEmail = $this->context->employee->email;
        $employeeDomains = explode('@', $employeeEmail);
        $employeeDomain = array_pop($employeeDomains);

        return trim($employeeDomain) == trim($availableDomain);
    }
}
