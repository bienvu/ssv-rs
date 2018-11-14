#FFW Styleguide Template

##Getting started

###Prerequisites:
The FFW Styleguide has a few dependencies that you will need to install to use it. Don't worry, we're got all the link you need right here:

* [Composer](https://getcomposer.org/) – Dependency Manager for PHP,
* [Node.js](https://nodejs.org/en/) – The backbone of the framework. Node Package Manager takes care of installing all the dev dependencies,
* [Bower](http://bower.io/) – Bower manages all the front end packages and fetches them for, when we start a new project.
* [Gulp](http://gulpjs.com/) – Task manager and automation. Gulp is responsible for running all the small tasks like compiling SCSS and Twig templates, starting a web server and automatically reloading browsers.

###Starting a new project
So you got the prerequisites installed, and you're ready to get started? Great! Just follow the simple steps below:


1. Download a copy of the framework.

2. Open up the project folder in Terminal, navigate to the `.npm` folder.

    ```
    cd .npm/
    ```

3. Run below command to install all the required packages with Node Package Manager.

    ```
    npm install
    ```

4. Start up watches and local server, run below command in `.npm` folder.

    ```
    gulp
    ```

5. For just compile style, run below command in `.npm` folder.

    ```
    gulp sass-watch
    ```
