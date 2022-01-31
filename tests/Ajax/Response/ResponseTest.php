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
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\AbstractResponse;
use PrestaShop\Module\Beyonds_switchbranch\Ajax\Response\ResponseJsonPresenter;

final class ResponseTest extends TestCase {

    public function testResponse(){
        foreach ($this->getResponseData() as $responseInfo) {
            $message = $responseInfo[0];
            $status  = $responseInfo[1];
            $data  = $responseInfo[2];

            $response = $this->getMockForAbstractClass(AbstractResponse::class, $responseInfo);
            $this->assertSame($message, $response->getMessage());
            $this->assertSame($status, $response->getStatus());
            $this->assertSame($data, $response->getData());
        }
    }

    public function testJsonResponse(){
        foreach ($this->getResponseData() as $responseInfo) {
            $expectedResponsePresented = json_encode([
                'message' => $responseInfo[0],
                'status' => $responseInfo[1],
                'data' => $responseInfo[2],
            ]);

            $response = $this->getMockForAbstractClass(AbstractResponse::class, $responseInfo);
            $presenter = $this->getMockBuilder(ResponseJsonPresenter::class)
                ->setMethods(['present'])
                ->disableOriginalConstructor()
                ->getMock();

            $presenter
                ->expects($this->once())
                ->method('present')
                ->with($response)
                ->willReturn($expectedResponsePresented)
                ;

            $presenter->present($response);
        }
    }

    private function getResponseData(){
        return [
            [
                null, null, null,
            ],
            [
                'message *$palkzbjhjb', 1, [],
            ],
            [
                'Mxlkkjz', 1, [1, 'mpl',],
            ],
            [
            'KFJEFENFENE', false, ['od'=>44],
            ]
        ];
    }
}