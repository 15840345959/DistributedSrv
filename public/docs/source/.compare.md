---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://localhost/docs/collection.json)
<!-- END_INFO -->

#general
<!-- START_5b4a0ea71d9ece2f40ffdfcc4d99b37c -->
## api/v1/test

> Example request:

```bash
curl -X GET "http://localhost/api/v1/test" \
-H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/test",
    "method": "GET",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```

> Example response:

```json
{
    "message": "Class 'App\\Http\\Controllers\\Test\\RequestValidator' not found",
    "exception": "Symfony\\Component\\Debug\\Exception\\FatalThrowableError",
    "file": "C:\\xampp\\htdocs\\JTYSSrv\\app\\Http\\Controllers\\Test\\TestController.php",
    "line": 20,
    "trace": [
        {
            "function": "test",
            "class": "App\\Http\\Controllers\\Test\\TestController",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Controller.php",
            "line": 54,
            "function": "call_user_func_array"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\ControllerDispatcher.php",
            "line": 45,
            "function": "callAction",
            "class": "Illuminate\\Routing\\Controller",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php",
            "line": 212,
            "function": "dispatch",
            "class": "Illuminate\\Routing\\ControllerDispatcher",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php",
            "line": 169,
            "function": "runController",
            "class": "Illuminate\\Routing\\Route",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 645,
            "function": "run",
            "class": "Illuminate\\Routing\\Route",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Pipeline.php",
            "line": 30,
            "function": "Illuminate\\Routing\\{closure}",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 102,
            "function": "Illuminate\\Routing\\{closure}",
            "class": "Illuminate\\Routing\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 647,
            "function": "then",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 622,
            "function": "runRouteWithinStack",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 588,
            "function": "runRoute",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 577,
            "function": "dispatchToRoute",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
            "line": 176,
            "function": "dispatch",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Pipeline.php",
            "line": 30,
            "function": "Illuminate\\Foundation\\Http\\{closure}",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 102,
            "function": "Illuminate\\Routing\\{closure}",
            "class": "Illuminate\\Routing\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
            "line": 151,
            "function": "then",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
            "line": 116,
            "function": "sendRequestThroughRouter",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\mpociot\\laravel-apidoc-generator\\src\\Mpociot\\ApiDoc\\Generators\\LaravelGenerator.php",
            "line": 116,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\mpociot\\laravel-apidoc-generator\\src\\Mpociot\\ApiDoc\\Generators\\AbstractGenerator.php",
            "line": 98,
            "function": "callRoute",
            "class": "Mpociot\\ApiDoc\\Generators\\LaravelGenerator",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\mpociot\\laravel-apidoc-generator\\src\\Mpociot\\ApiDoc\\Generators\\LaravelGenerator.php",
            "line": 58,
            "function": "getRouteResponse",
            "class": "Mpociot\\ApiDoc\\Generators\\AbstractGenerator",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\mpociot\\laravel-apidoc-generator\\src\\Mpociot\\ApiDoc\\Commands\\GenerateDocumentation.php",
            "line": 261,
            "function": "processRoute",
            "class": "Mpociot\\ApiDoc\\Generators\\LaravelGenerator",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\mpociot\\laravel-apidoc-generator\\src\\Mpociot\\ApiDoc\\Commands\\GenerateDocumentation.php",
            "line": 83,
            "function": "processLaravelRoutes",
            "class": "Mpociot\\ApiDoc\\Commands\\GenerateDocumentation",
            "type": "->"
        },
        {
            "function": "handle",
            "class": "Mpociot\\ApiDoc\\Commands\\GenerateDocumentation",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php",
            "line": 29,
            "function": "call_user_func_array"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php",
            "line": 87,
            "function": "Illuminate\\Container\\{closure}",
            "class": "Illuminate\\Container\\BoundMethod",
            "type": "::"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php",
            "line": 31,
            "function": "callBoundMethod",
            "class": "Illuminate\\Container\\BoundMethod",
            "type": "::"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php",
            "line": 549,
            "function": "call",
            "class": "Illuminate\\Container\\BoundMethod",
            "type": "::"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php",
            "line": 180,
            "function": "call",
            "class": "Illuminate\\Container\\Container",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\symfony\\console\\Command\\Command.php",
            "line": 264,
            "function": "execute",
            "class": "Illuminate\\Console\\Command",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php",
            "line": 167,
            "function": "run",
            "class": "Symfony\\Component\\Console\\Command\\Command",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\symfony\\console\\Application.php",
            "line": 888,
            "function": "run",
            "class": "Illuminate\\Console\\Command",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\symfony\\console\\Application.php",
            "line": 224,
            "function": "doRunCommand",
            "class": "Symfony\\Component\\Console\\Application",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\symfony\\console\\Application.php",
            "line": 125,
            "function": "doRun",
            "class": "Symfony\\Component\\Console\\Application",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Application.php",
            "line": 88,
            "function": "run",
            "class": "Symfony\\Component\\Console\\Application",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php",
            "line": 121,
            "function": "run",
            "class": "Illuminate\\Console\\Application",
            "type": "->"
        },
        {
            "file": "C:\\xampp\\htdocs\\JTYSSrv\\artisan",
            "line": 37,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Console\\Kernel",
            "type": "->"
        }
    ]
}
```

### HTTP Request
`GET api/v1/test`

`HEAD api/v1/test`


<!-- END_5b4a0ea71d9ece2f40ffdfcc4d99b37c -->

