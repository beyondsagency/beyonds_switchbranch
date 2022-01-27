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

$(document).ready(function(){

    $(header_selector).after(branch_selector);

    $(checkout_selector).click(function(){
        let confirmationMessage = confirm_message
        let data = {
            action : 'Checkout',
            params : {
                branch_name: $(this).data('branch-name')
            },
            submitBeyondsGitAjax: true,
        }
        let needConfirmation = $(this).data('is-technical');

        if(!needConfirmation){
            confirmationMessage = false;
        }

        ajaxQuery(data, ajax_url, reload_page_after, confirmationMessage);
    });

    $(pull_selector).click(function(){
        let data = {
            action : 'Pull',
            params: {},
            submitBeyondsGitAjax: true,
        }

        ajaxQuery(data, ajax_url, reload_page_after);
    });

    function ajaxQuery(
        data,
        ajax_url,
        reload,
        confirmationMessage = null
    ){
        $.ajax({
            type: 'POST',
            url: ajax_url,
            async: false,
            cache: false,
            dataType: "json",
            data: data,
            success: function(response){
                if(confirmationMessage){
                    if(confirm(confirmationMessage) != true){
                        return false;
                    }
                }

                if(typeof response === 'object' && response !== null){
                    alert(response.message)
                    if(response.status && reload){
                        document.location.reload();
                    }
                }
            },
            error: function() {
                alert('Error 505')
            }
        });
    }
});