# Warehouse 🏡
Just Another "Simple" Static Site Generator

## Installing
Download the latest version of Warehouse and unpack it into a directory for your website's data. You should have a file structure along the lines of:
* `/your_site/Warehouse.php`
* `/your_site/Classes/`
* `/your_site/Extensions/`

_(As extensions are optional you can remove this directory if you want)_

That's the "installation". _Zippy_, eh?

## Using Warehouse
Warehouse separates content from structure with an focus on writing. Thus, all content and writing for your website should be stored under the directory `/source/` and all structure for your website (the HTML your source is inserted into) should be stored under `/templates/`.
Your file structure should look along the lines of:
* `/your_site/source/`
* `/your_site/templates/`

### Source
Warehouse will process any file with the extension `.md` under `/source/` with conforming `/Extensions/`. All other files found under `/source/` will not be processed, but copied directly over to `/upload/` *after* all `.md` files have been successfully generated into `.html`.

You can also declare `@data` attributes that you can access in your template files such as who the author was of an article, the last update time, or which template file to load first. These can be declared inside your `.md` files (these take the greatest priority), or inside `config.json` files under `/source/`.

More information about the `/source/` directory can be found in the [Wiki] (https://github.com/OhItsShaun/Warehouse/wiki/The-Source-Directory).

### Templates
Template files are what your content is inserted into - the HTML structure of your site. Template files are saved as `.html`.

Warehouse, by default, will look for the template file `Main.html` unless otherwise specified in `/source/`. You can also call other template files using the syntax `@template({template_name})`. To fetch the contents of the source file use the data request `@data(Content)`.

More information about the `/templates/` directory can be found in the [Wiki] (https://github.com/OhItsShaun/Warehouse/wiki/The-Templates-Directory).

## Creating Your Site 💻
To tell Warehouse to generate your website `cd` down to `/your_site/` and run Warehouse using the command:
```php
php Warehouse.php
```
An `/upload/` folder will have been created with your swanky new site made ready for upload!

## I've Found a Bug 🐛
[Create an issue] (https://github.com/OhItsShaun/Warehouse/issues) with steps to reproduce and any sample files, and preferably open a pull request with a fix if you can. 😉
