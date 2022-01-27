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
namespace PrestaShop\Module\Beyonds_switchbranch\Ajax\Request\Git;

use PrestaShop\Module\Beyonds_switchbranch\Ajax\Request\AbstractRequest;
use PrestaShop\Module\Beyonds_switchbranch\Adapter\Git\GitClientException;

class Pull extends AbstractRequest
{
    public function exec($params = [])
    {
        try {
            $this->gitClient->pull();
            $this->responseType
                ->setData($this->gitClient->getBranchList())
                ->setStatus(true)
                ->setMessage(
                    $this->translator->trans('Mise à jour des branches')
                )
            ;
        } catch (GitClientException $gitException) {
            $this->responseType
                ->setData([])
                ->setStatus(false)
                ->setMessage(
                    $this->translator->trans('Erreur lors de la mise à jour des branches ').
                    $gitException->getMessage()
                )
            ;
        }
    }
}
