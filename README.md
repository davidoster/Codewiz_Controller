Codewiz_Controller
==================

Codeigniter 2 Controller influenced by ZF 
 * Auto-loading views and layouts while retaining CI's functionality.
 * View data handling
 * Includes a request object holding all arguments sent from client, controller and method names, http verb, and if ssl.
 * Methods to easily add remote or raw CSS and JS to header or end of html.
 * Comes with a responsive CI calendar template.

### Init method

If you ever need to load something in the controller contructor, use the init() instead.
This is the standard ZF usage which preserves the __constructor from accidental assignment.

Any code within the controller init() method will fire after all inherited __constructor code is executed but before any of your controller methods are executed.

### View Auto-loading

Directory structure for view auto-loading;

```
application
| controller
| libraries
| views
| | layouts
| | | layoutName
| | | | header.php
| | | | footer.php
| | scripts
| | | controllerName
| | | | methodName.php
system
...
```
If you are familiar with ZF you will recognise this standard layout.
The things to know is each controller will have a subdirectory of "scripts" named after it, which will have ".php" files in it for each method in that controller with the filename matching the method name.
Files and directories are in lower-case.

The included example controller is named "Test" and the method is named "index" so the structure to autoload a view for this would be;
```
application
| controller
| | test.php
| libraries
| views
| | layouts
| | | standard
| | | | header.php
| | | | footer.php
| | scripts
| | | test
| | | | index.php
system
...
```
### Layouts

Layouts are a little different, each subfolder of "views/layouts" is named according to a layout name, the example in this package is "standard". Within the layout subdirectory you need two files; "header.php" and "footer.php".

If i were to add a new method to the "Test" controller called "create", a new controller named "Admin" with method "index" and "users", and a layout called "admin" - The new directory structure would look like this;
```
application
| controller
| | test.php
| | admin.php
| libraries
| views
| | layouts
| | | standard
| | | | header.php
| | | | footer.php
| | | admin
| | | | header.php
| | | | footer.php
| | scripts
| | | test
| | | | index.php
| | | | create.php
| | | admin
| | | | index.php
| | | | users.php
system
...
```
I hope that is clear enough.

Lastly, from your controller you will need to dispatch using this simple method;
```php
$this->dispatch();
```
The dispatch method can take an array of data, a $view arguament to load an alternative methods script, and you can also define which layout to use.
```php
$this->dispatch( array( 'key' => "some value" ), "script name of other view to load" , "other layout name to use" );
```
Note: If the requested view script does not exist int he directory structure the CI 404 page will be output.

### View Data

Data that is passed to a view is split into layout and view.

To commit an array of data to your view from your controller at any time call;
```php
$this->setDataView( array( 'key' => "some value" ) );
```
And access the value in your view script using the key;
```php
<?php echo $key;  ?> // will output "some value" 
```
The same can be done for layouts;
```php
$this->setDataLayout( array( 'key' => "some value" ) );
```
And access the value in your layout header or footer script using the key;
```php
<?php echo $key;  ?> // will output "some value" 
```
Alternatively you can add an array of data to the view when you dispatch it;
```php
$this->dispatch( array( 'key' => "some value" ) );
```

### Access arguments sent by client

Your client browser requests may send data over the url parameters, as GET arguments, or over the http input stream using POST, PUT, DELETE, ect.
All arguments are captured and accessible via the request object as an array of key/pair values.

In the standard CI controller/method/arguemnts way;
Consider the following url: http://example.com/test/index/prop/123

```php
$request = $this->prop();
print_r( $request->args );
//outputs
array(
  'prop' => 123
);
```
The above will get the entuire request object, and access the args object which is an array of arguments sent.

Alternatively you could access the arguamnet directly if you know the property key;
```php
$request = $this->prop( 'prop' );
print( $request );
//outputs: 123
```

With GET arguments: http://example.com/test/index/prop/123?testing=true
```php
$request = $this->prop();
print_r( $request->args );
//outputs
array(
  'prop' => 123,
  'testing' => true
);
```
The url and get arguments are merged!

This is also true if values are sent vie POST ect.

### Access request object values

Other properties available via the request object are as follows;
 * verb - string - the http request verb used; get, post, delete, put, ect.
 * ssl - bool - whether ssl was used.
 * controller - string - the controller name.
 * method - string - the method name within the controller.

```php
$request = $this->prop();
echo $request->verb; // output: get
echo $request->ssl; // output: false
echo $request->controller; // output: test
echo $request->method; // output: index
```

### Convert an array to JSONP

There may be times you need to accomodate JSONP, instead of thinking about how thats done just call;
```php
echo jsonp_format( array( 'key' => "some value" ) );
```

### JavaScript and CSS in views

Use the following methods to easily add JavaScript and CSS to your layout header and footer, either as raw or remote resource.

```php
$this->addRemoteJsHeader( "//jquery.com/latest.js" );
$this->addRemoteJsFooter( "//jquery.com/latest.js" );
$this->addRemoteCss( "//jqueryui.com/latest.css" );
$this->addCss( ".error { color: red; }" );
$this->addJsToHeader( "alert('js in html head')" );
$this->addJsToFooter( "alert('js at the end of the html')" );
```
All of the above examples can be used multiple times from your controller, each call adds another resource.

For the above to functiont he layout must include int he header and footer the following;
##### header.php
```php
<? foreach ( $remoteCss as $style ) : ?>
       <link rel="stylesheet" type="text/css" href="<?= $style; ?>">
<? endforeach; ?>
<? foreach ( $css as $style ) : ?>
        <style type="text/css">
            <?= $style; ?>
  </style>
<? endforeach; ?>
<? foreach ( $remoteJsHeader as $script ) : ?>
        <script type="text/javascript" charset="utf-8" src="<?= $script; ?>"></script>
<? endforeach; ?>
<? foreach ( $jsHeader as $script ) : ?>
        <script type="text/javascript" charset="utf-8">
            <?= $script; ?>
        </script>
<? endforeach; ?>
```
##### footer.php
```php
<? foreach ( $remoteJsFooter as $script ) : ?>
        <script type="text/javascript" charset="utf-8">
            <?= $script; ?>
        </script>
<? endforeach; ?>
<? foreach ( $jsFooter as $script ) : ?>
        <script type="text/javascript" charset="utf-8">
            <?= $script; ?>
        </script>
<? endforeach; ?>
```
Without the above added to the layout none of the resources have any way to be added tot he rendered view HTML.
