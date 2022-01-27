{*
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
*}
<div id="{$module_name}" class="component">
    <div class="dropdown">
        <button id="{$module_name}_select" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
            <span id="{$module_name}_current_branch_name">
                {$curren_branch_name} <i class="material-icons">arrow_drop_down</i>
            </span>
        </button>
        <ul class="dropdown-menu">
            {if !empty($branch_list)}
                {foreach from=$branch_list item=branch}
                    <li class="{$module_name}_switch"
                        data-branch-name="{$branch.name}"
                        data-is-technical="{$branch.is_technical}"
                    >
                        <a href="javascript:void(0);">
                            {if $branch.is_technical}<i class="material-icons">error_outline</i>{/if}
                            {$branch.name}
                        </a>
                    </li>
                {/foreach}
            {/if}
            <li class="divider"></li>
            <li class="{$module_name}_pull">
                <a href="javascript:void(0);">
                    <i class="material-icons">refresh</i>
                    {l s='Mettre à jour'}
                </a>
            </li>
        </ul>
    </div>
</div>