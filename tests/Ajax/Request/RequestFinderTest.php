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

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Exception\RequestNotFoundException;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Request\RequestFinder;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\AbstractResponse;
use Prestashop\Module\Beyonds_switchbranch\Ajax\Request\Git\Pull;
use Prestashop\Module\Beyonds_switchbranch\Ajax\Request\Git\Checkout;

final class RequestFinderTest extends TestCase{

    private $requestFinder;
    private $responseType;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestFinder = new RequestFinder();
        $this->responseType = $this->getMockForAbstractClass(
            AbstractResponse::class, [
            'message',
            false,
            ['key'=>'value']
        ]);
    }

    public function testMissingActionName(){

        $this->expectException(RequestNotFoundException::class);
        $this->expectExceptionMessage('Missing action name');

        $this->requestFinder->find(null, $this->responseType);
    }

    public function testActionNotFound(){
        $actionName = 'GitFoo';

        $this->expectException(RequestNotFoundException::class);
        $this->expectExceptionMessage('Action '.$actionName.' not found ');

        $this->requestFinder->find($actionName, $this->responseType);
    }

    public function testInvalidResponse(){
        $this->expectException(RequestNotFoundException::class);
        $this->expectExceptionMessage('Invalid response type');

        $this->requestFinder->find('Pull', null);
    }
}