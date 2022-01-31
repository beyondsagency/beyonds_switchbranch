# Requirements

- Presatshop 1.7.x
- Connect to a git server using SSH
- Make sure you web user as access to run git command.

# Config

![image](https://user-images.githubusercontent.com/16455155/151821224-0fcb8ed6-421a-42d7-a655-0e35d99639fc.png)

| Var                                             | Description                                       | Default
| ------------------------------------------------| --------------------------------------------------|------------------------
| `BEYONDS_SWITCHBRANCH_CSS_SELECTOR`               | where the list of branches should appear. By default it will be displayed at the top of all pages after quick access | `#header_quick`
| `BEYONDS_SWITCHBRANCH_EMPLOYEE_DOMAIN`            | The domain name of the employees who can see the list of branches. By default everyone can see the list of branches                                                  |    `null`
| `BEYONDS_SWITCHBRANCH_TECHNICAL_BRANCHES_PREFIX`  | The prefix of technical branches. Some branches for example may require additional manual actions after or before the switch. A warning message will be displayed in this case.                                           |   `dev/`,`tech/`    
| `BEYONDS_SWITCHBRANCH_CLEAR_CACHE`                | Clear cache after branch change. Enabled by default                                           | `true`
| `BEYONDS_SWITCHBRANCH_RELOAD_PAGE`                | Reload page after branch change. Disabled by default                                           | `false`



# Add new git request

1 - Implement a new git request 

*`/src/Ajax/Request/Git/MyGitRequest.php`*

```php

<?php

namespace PrestaShop\Module\Beyonds_switchbranch\Ajax\Request\Git;

class DeleteBranch extends AbstractRequest
{
    public function exec($params = [])
    {
       $myBranchName = $params['my_branch_name'];
       $this->gitClient->deleteBrach($myBranchName);
       // Catch possible errors
       // Set response infos 
    }
}

```

2 - Js client

*`/views/js/back.js`*

```js

$(MySelector).click(function(){
     let data = {
         action : 'DeleteBranch',
         params : {
            my_branch_name: $(this).data('branch-name')
         },
     }
     
     let needConfirmation = $(this).data('is-technical');
     
      if(!needConfirmation){
          confirmationMessage = false;
      }

      ajaxQuery(data, ajax_url, reload_page_after, confirmationMessage);
 });

```

## License

This module is released under the [MIT Licence](https://opensource.org/licenses/MIT)
