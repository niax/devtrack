<?php
/**
 *
 * View class for APP/Source/tree for the DevTrack system
 * Allows users to view tree objects
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     DevTrack Development Team 2012
 * @link          http://github.com/chrisbulmer/devtrack
 * @package       DevTrack.View.Source
 * @since         DevTrack v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$this->Html->css('pages/source', null, array ('inline' => false));
$this->Html->css('prettify/prettify', null, array ('inline' => false));
$this->Html->script('prettify/prettify', array('block' => 'scriptBottom'));
$this->Html->scriptBlock("prettyPrint()", array('inline' => false));

$smallText = " <small>" . $project['Project']['description'] . " </small>";
$pname = $project['Project']['name'];

// Base url for the view
$url = array('project' => $project['Project']['name'], 'action' => 'tree', $branch);
$this->Bootstrap->add_crumb($project['Project']['name'], $url);

// Create the base url to be used for all links and add breadcrumbs
foreach (explode('/',$path) as $crumb) {
    $url[] = $crumb;
    $this->Bootstrap->add_crumb($crumb, $url);
}

// Header for the page
echo $this->Bootstrap->page_header($pname . $smallText);

?>
<div class="row">
    <div class="span2">
        <?= $this->element('Sidebar/project') ?>
    </div>
    <div class="row">
        <?= $this->element('Source/topbar', array('branches' => $branches, 'branch' => $branch)) ?>
        <div class="span10">
            <?= $this->Bootstrap->breadcrumbs(array("divider" => "/")) ?>
        </div>
        <? if ($tree['type'] == 'tree') echo $this->element('Source/folder_view', array('url' => $url, 'branches' => $branches, 'branch' => $branch, 'files' => $tree['content'])); ?>
        <? if ($tree['type'] == 'blob') echo $this->element('Source/file_view', array('url' => $url, 'branches' => $branches, 'branch' => $branch, 'file' => $tree)) ?>
    </div>
</div>
