<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

/**
 * Holds the paths that are used by the system to
 * locate the main directories, app, system, etc.
 * Modifying these allows you to restructure your application,
 * share a system folder between multiple applications, and more.
 *
 * All paths are relative to the project's root folder.
 */
class Paths
{
    /**
     * ---------------------------------------------------------------
     * SYSTEM FOLDER NAME
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "system" folder.
     * Include the path if the folder is not in the same directory
     * as this file.
     */
    public string $systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';

    /**
     * ---------------------------------------------------------------
     * APPLICATION FOLDER NAME
     * ---------------------------------------------------------------
     *
     * If you want this front controller to use a different "app"
     * folder than the default one you can set its name here. The folder
     * can also be renamed or relocated anywhere on your server. If
     * you do, use a full server path.
     * For more info please see the user guide:
     *
     * https://codeigniter.com/userguide4/installation/structure.html
     *
     * IMPORTANT: If you change the name, you should also set the related
     * property $appDirectory in ./app/Config/Constants.php
     */
    public string $appDirectory = __DIR__ . '/..';

    /**
     * ---------------------------------------------------------------
     * WRITABLE FOLDER NAME
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "writable" folder.
     * The writable folder allows you to group all directories that
     * need write permission to a single place that can be tucked
     * away for maximum security, keeping it out of the app and/or
     * system directories.
     */
    public string $writableDirectory = __DIR__ . '/../../writable';

    /**
     * ---------------------------------------------------------------
     * TESTS FOLDER NAME
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "tests" folder.
     */
    public string $testsDirectory = __DIR__ . '/../../tests';

    /**
     * ---------------------------------------------------------------
     * VIEW FOLDER NAME
     * ---------------------------------------------------------------
     *
     * This variable contains the location of the "views" folder
     * for the application. By default, this is in `app/Views`.
     * This value is used when no namespace is provided to the
     * `view()` helper function.
     */
    public string $viewDirectory = __DIR__ . '/../Views';
}
