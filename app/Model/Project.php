<?php
/**
*
* Project model for the DevTrack system
* Represents a project in the system
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @copyright     DevTrack Development Team 2012
* @link          http://github.com/chrisbulmer/devtrack
* @package       DevTrack.Model
* @since         DevTrack v 0.1
* @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
*/

App::uses('AppModel', 'Model');
App::uses('Folder', 'Utility');
/**
 * Project Model
 *
 * @property RepoType $RepoType
 * @property Collaborator $Collaborator
 */
class Project extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please enter a name for the project',
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'This project name has already been taken',
            ),
            'minLength' => array(
                'rule' => array('minLength', 4),
                'message' => 'Project names must be at least 4 characters long',
            ),
            'alphaNumericDashUnderscore' => array(
                'rule' => '/^[0-9a-zA-Z_-]+$/',
                'message' => 'May contain only letters, numbers, dashes and underscores',
            ),
            'startWithALetter' => array(
                'rule' => '/^[a-zA-Z].+$/',
                'message' => 'Project names must start with a letter',
            ),
        ),
        'public' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Please select the visibility of the project',
            ),
        ),
        'repo_type' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Please select a repository type',
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'RepoType' => array(
            'className' => 'RepoType',
            'foreignKey' => 'repo_type',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Collaborator' => array(
            'className' => 'Collaborator',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Task' => array(
            'className' => 'Task',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Milestone' => array(
            'className' => 'Milestone',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Source' => array(
            'className' => 'Source',
            'foreignKey' => 'project_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Time' => array(
            'className' => 'Time',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectHistory' => array(
            'className' => 'ProjectHistory',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function beforeSave($options = array()) {
        if (!empty($this->data['Project']['name'])) {
            $this->data['Project']['name'] = strtolower($this->data['Project']['name']);
        }
        return true;
    }

    public function beforeDelete($cascade = true) {
        $location = $this->Source->_repoLocation();
        if ($location == NULL || !is_dir($location)) {
            return true;
        }
        $folder = new Folder($location);
        if ($folder->delete()) {
            // Successfully deleted project and its nested folders
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches a project from either its name or its id
     *
     * @param $key string id or name of project to fetch
     *
     * @return Project The project found by the given key, null if no project is found
     */
    public function getProject($key) {
        if ($key == null){ //Sanity check
            return null;
        }

        $project = null;
        if (is_numeric($key)) {
            $project = $this->find('first', array('conditions' => array('Project.id' => $key)));
        } else {
            $project = $this->find('first', array('conditions' => array('Project.name' => $key)));
        }
        if (empty($project)){
            $project = null;
        }

        // Lock out those who are not allowed to read
        if ( !$this->hasRead($this->_auth_user_id, $project['Project']['id']) ) {
            throw new ForbiddenException(__('You do not have permissions to access this project'));
        }

        return $project;
    }

    /**
     * Checks to see if a user has read access of this project
     *
     * @param $user int id of the user to check
     * @return boolean true if read permissions
     */
    public function hasRead($user = null, $project = null) {
        if ( $user == null ) $user = $this->_auth_user_id;
        if ( $user == null ) return false;

        if ($this->id) $project = $this->id;

        $member = $this->Collaborator->find('first', array('conditions' => array('user_id' => $user, 'project_id' => $project), 'fields' => array('access_level')));

        if ( !empty($member) && $member['Collaborator']['access_level'] > -1 ) {
            return true;
        }

        return false;
    }

    /**
     * Checks to see if a user has write access of this project
     *
     * @param $user int id of the user to check
     * @return boolean true if write permissions
     */
    public function hasWrite($user = null, $project = null) {
        if ( $user == null ) $user = $this->_auth_user_id;
        if ( $user == null ) return false;

        if ($this->id) $project = $this->id;

        $member = $this->Collaborator->find('first', array('conditions' => array('user_id' => $user, 'project_id' => $project), 'fields' => array('access_level')));

        if ( !empty($member) && $member['Collaborator']['access_level'] > 0 ) {
            return true;
        }

        return false;
    }

    /**
     * Checks to see if a user is an admin of this project
     *
     * @param $user int id of the user to check
     * @return boolean true if admin
     */
    public function isAdmin($user = null, $project = null) {
        if ( $user == null ) $user = $this->_auth_user_id;
        if ( $user == null ) return false;

        if ($this->id) $project = $this->id;

        $member = $this->Collaborator->find('first', array('conditions' => array('user_id' => $user, 'project_id' => $project), 'fields' => array('access_level')));

        if ( !empty($member) && $member['Collaborator']['access_level'] > 1 ) {
            return true;
        }

        return false;
    }

    public function fetchEventsForProject() {
        $this->recursive = 2;
        $project = $this->getProject($this->id);

        $events = array();

        $this->Source->init();
        foreach ( array('Collaborator', 'Time', 'Source', 'Task') as $x ) {
            $events = array_merge($events, $this->{$x}->fetchHistory($project['Project']['name'], 40));
        }

        // Sort function for events
        // assumes $array{ $array{ 'modified' => 'date' }, ... }
        $cmp = function($a, $b) {
            if (strtotime($a['modified']) == strtotime($b['modified'])) return 0;
            if (strtotime($a['modified']) < strtotime($b['modified'])) return 1;
            return -1;
        };

        usort($events, $cmp);

        return array_slice($events, 0, 8);
    }

}
