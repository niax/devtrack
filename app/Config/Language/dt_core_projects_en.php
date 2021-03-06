<?php
/**
 *
 * ADVANCED USERS ONLY
 * Do NOT configure this file unless you know what you are doing.
 * Editing this file incorrectly could reduce the stability of the system.
 *
 * tl;dr Here be dragons.
 *
 * Core Configuration file for the DevTrack system
 * Provides the core config for the system
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     DevTrack Development Team 2012
 * @link          http://github.com/chrisbulmer/devtrack
 * @package       DevTrack.Config
 * @since         DevTrack v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/*
 * Configuration for the projects section
 * APP/Projects
 */

// APP/Projects/view
$config['dtcore']['pages']['projects']['view']['en']['header.text'] = "Project overview";

$config['dtcore']['pages']['projects']['view']['en']['summary.issues.title'] = "Tasks";
$config['dtcore']['pages']['projects']['view']['en']['summary.issues.open'] = "Open Tasks";
$config['dtcore']['pages']['projects']['view']['en']['summary.issues.closed'] = "Closed Tasks";
$config['dtcore']['pages']['projects']['view']['en']['summary.issues.total'] = "Total Tasks";
$config['dtcore']['pages']['projects']['view']['en']['summary.issues.percent'] = "complete";