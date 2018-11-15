# Matomo Custom API

## How to Modify original setup

1. Download  and extract matomo from https://matomo.org/download/
2. Copy this custom-api.php into the matomo root folder. 
3. Install Guzzle via composer in the matomo folder using the following command 
    ```
    composer require guzzlehttp/guzzle:~6.0
    ```
    More about Guzzle installtion at http://docs.guzzlephp.org/en/stable/overview.html#installation
4. Guzzle has the dependency facebook/xhprof which will be added as git sub module, So in need to be changed before committing it to VBXAnalytix repo
     ```
     git rm --cached vendor/facebook/xhprof
    ```
    The above command keeps the files and removes the sub module structure 
## Replacing Logo
Copy paste plugin folder to matomo root folder and replace the images.