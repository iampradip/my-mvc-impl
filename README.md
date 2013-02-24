Very small MVC framework I designed to use in my final year academic project in PHP.

index.php
=========
Define your "index" controller and "404 status" controller in this file.


config/database.php
===================
If database is used, provide all details to connect to database in this file.


Controllers
===========
1. Controller class must be defined in php file in "controllers" directory.
2. Controller class name must be suffixed with "_controller".
3. File name should be given as classname.php like index_controller.php.
4. Controller class must inherit "app_controller" class.
5. Public methods are accessible through URL, unless they are prefixed with underscore.
6. Controller object can be accessed from view by $controller variable.


Models
======
1. Model class must be defined in php file in "models" directory.
2. Model class name must be suffixed with "_model".
3. File name should be given as classname.php like users_model.php.
4. Model class must inherit "app_model" class.
5. Model object can be accessed from controller by $this->model($model_name); , $model_name is string and without "_model" suffix.
6. Model object is initialized only once and same object is returned when $this->model($model_name) is used several times in controller.


Libraries
=========
1. Library class must be defined in php file in "libraries" directory.
2. Library class name must be suffixed with "_library".
3. File name should be given as classname.php like pagination_library.php.
4. Library class must inherit "app_library" class.
5. Library object can be accessed from controller or model by $this->library($library_name); , $library_name is string and without "_library" suffix.
6. Library object is initialized only once and same object is returned when $this->library($library_name) is used several times in controller or model.
7. Library classes should not use controller or model classes. It should not access any view either. But it's still possible to access them.


Views
=====
1. View must be defined in php file in "views" directory.
2. View name must be suffixed with "_view".
3. File name should be given as viewname.php like login_view.php.
4. View can be echoed from controller by $this->view($view_name, [$view_variables_array]); $view_name is string and without "_view" suffix.
5. View can access variables given in $view_variables_array just like in CodeIgniter.
6. View can also access $base_url which points to URL where index.php is saved. It can be used to include scripts, css, images, etc. without worrying about path problems.


Core
====
Don't touch anything in this directory unless you know what you are doing.