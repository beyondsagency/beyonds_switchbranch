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
namespace PrestaShop\Module\Beyonds_switchbranch\Ajax;

use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\ResponsePresenterInterface;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\ResponseInterface;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Exception\RequestNotFoundException;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Request\RequestFinder;
use Tools;

class AjaxProcess
{
    private $action;
    private $params;
    private $response;
    private $responsePresenter;

    public function __construct(
        ResponseInterface $responseType,
        ResponsePresenterInterface $responsePresenterType
    ) {
        $this->response = $responseType;
        $this->responsePresenter = $responsePresenterType;
        $this->action = $this->retrieveAction();
        $this->params = $this->retrieveParams();
    }

    public function run()
    {
        $responseObject = $this->response;
        try {
            $request = RequestFinder::find($this->action, $responseObject);
            $request->exec($this->params);
            $responseObject = $request->getResponse();
        } catch (RequestNotFoundException $requestNotFoundException) {
            $responseObject
                ->setMessage($requestNotFoundException->getMessage())
                ->setStatus(false)
                ->setData([])
            ;
        }

        echo $this->responsePresenter->present($responseObject);
        die();
    }

    private function retrieveAction()
    {
        return Tools::getValue('action');
    }

    private function retrieveParams()
    {
        return Tools::getValue('params');
    }
}
