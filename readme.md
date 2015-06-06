## About this fork

Aim of this fork is to provide centralized point for managing image formats, sizes and cuts (algorithm of resize)

#### Main differences

IProvider => IRepository
+ method saveImage
paths: images/<id>-<width>x<height>.jpg => images/<prefix>/<namespace>-<type>-<id>.jpg
extension configuration is available trough all extension classes

#### Requirements

- PHP 5.4+
- [nette/application](https://github.com/nette/application) >= 2.2
- [nette/di](https://github.com/nette/di) >= 2.2
- [nette/http](https://github.com/nette/http) >= 2.2
- [latte/latte](https://github.com/nette/latte) >= 2.2
- [nette/utils](https://github.com/nette/utils) >= 2.2

## Installation

1) Copy source codes from Github or using [Composer](http://getcomposer.org/):
update composer.json
```json
"repositories": [
        {
            "type": "git",
            "url": "https://github.com/tomasstrejcek/nette-webimages.git"
        }
    ],
```

```json
"require": {
	"dotblue/nette-webimages":"dev-master"
}
```

2) Register as Configurator's extension:
```
extensions:
	webimages: DotBlue\WebImages\Extension
```

## Concept

This addon gives you power to automatically generate different sized versions of images throughout your app. When browser will request new version of image, application will generate it and save it to its requested destination, so that in next HTTP request, your server will just serve existing file.

To enable this, modify your `.htaccess`:

```
# front controller
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule !\.(pdf|js|ico|gif|css|rar|zip|tar\.gz)$ index.php [L]
```

## Usage

First, you have to define your `DotBlue\WebImages\IRepository` implementation. Its responsibility is to save uploaded image or generate new version of image using `Nette\Image`. Check [examples](examples) for inspiration - the required method `getImage` should return `Nette\Image` instance of queried image and method saveImage which can process either Nette\Http\FileUpload or Nette\Utils\Image objects.

When you have it, register it in configuration:

```
webimages:
	repositories:
		- <name of your class>
```

Secondly you have to specify route where your images will be available. Central point of the route is `id` parameter, which should uniquely identify your image. Lets setup simple route:

```
webimages:
	routes:
		- images/<prefix>/<namespace>-<type>-<id>.jpg
```

> By default all these routes will be prepended before your other routes - assuming you use `Nette\Application\Routers\RouteList` as your root router. You can disable this by setting `prependRoutesToRouter: false`. Then it's your responsibility to plug webimages router (service `webimages.router`) to your routing implementation.

Addon gives you new macro `n:src` or `{src }`. Now you're ready to use it.

```html
<img n:src="foo, namespace, type">
```
```smarty
{src foo, namespace, type}
```
This will result in following HTML:

```html
<img src="/images/prefix/namespace-type-id.jpg">
```
note: prefix is substr(id, 0, 2)

Creation of this file will handle your implementation of `DotBlue\WebImages\IRepository`.
