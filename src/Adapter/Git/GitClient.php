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
namespace PrestaShop\Module\Beyonds_switchbranch\Adapter\Git;

use PrestaShop\Module\Beyonds_switchbranch\Config\ConfigForm;
use CzProject\GitPhp\Git;

class GitClient
{
    protected $gitClient;

    public function __construct()
    {
        $this->gitClient = new GitClientWrapper(
            (new Git())->open(_PS_ROOT_DIR_)
        );
    }

    public function getCurrentBranchName()
    {
        return $this->gitClient->getCurrentBranchName();
    }

    public function pull()
    {
        $this->gitClient->pull();
    }

    public function getBranchList($pullBefore = false, $localOnly = true)
    {
        if ($pullBefore) {
            $this->gitClient->pull();
        }

        $repositoryBranches = $this->gitClient->getLocalBranches();

        if (!$localOnly) {
            $repositoryBranches = $this->gitClient->getBranches();
        }

        if (empty($repositoryBranches)) {
            return [];
        }

        $branchesList = [];

        foreach ($repositoryBranches as $branchName) {
            $isCurrentBranch = $branchName == $this->getCurrentBranchName();

            if ($isCurrentBranch) {
                continue;
            }

            $branchesList[] = [
                'name' => $branchName,
                'is_technical' => $this->isTechnicalBranch($branchName)
            ];
        }

        return $branchesList;
    }

    public function switchToBranch($branchName)
    {
        $this->gitClient->checkout($branchName);
    }

    public function isTechnicalBranch($branchName)
    {
        $branchesPrefixes = ConfigForm::getTechnicalBranchesPrefixes();

        if (empty($branchesPrefixes)) {
            return false;
        }

        foreach ($branchesPrefixes as $branchPrefix) {
            if (substr($branchName, 0, strlen($branchPrefix)) === $branchPrefix) {
                return true;
            }
        }

        return false;
    }
}
