@javascript
Feature: Set external services, settings and create question for qlowcourse
  Background:
    Given the following "course" exists:
      | fullname  | Course for qlow |
      | shortname | qlowcourse |
      | format    | topics|
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | teacher | Lastname | admin@gmail.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | qlowcourse | editingteacher |
      
    And I log in as "admin"
    And I wait "2" seconds

  Scenario: Admin set external services, settings and create question for qlowcourse

    Given I click on "Site administration" "link"
    And I wait "1" seconds
    And I click on "General" "link"
    And I wait "1" seconds
    
    And I click on "Users" "link"
    And I wait "1" seconds
    And I click on "Assign system roles" "link"
    And I wait "1" seconds
    And I click on "Qlowcode" "link"
    And I wait "1" seconds
    And I click on "teacher Lastname" "option"
    And I wait "1" seconds
    And I click on "Add" "button"
    And I wait "1" seconds
    Given I click on "Server" "link"
    And I wait "1" seconds
    And I click on "External services" "link"
    And I wait "1" seconds
    And I click on "Add" "link"
    And I wait "1" seconds
    Then I set the following fields to these values:
      | Name | qtype_qlowcode_endpoint |
    And I wait "1" seconds
    And I click on "enabled" "checkbox"
    And I wait "1" seconds
    And I click on "submitbutton" "button"
    And I wait "1" seconds
    And I click on "Add functions" "link"
    And I wait "1" seconds
    And I click the autocomplete selection
    And I press the tab key
    And I press the down key
    When I type "qtype_qlowcode_endpoint"
    And I press the enter key
    And I wait "1" seconds
    And I click on "id_submitbutton" "button"
    And I wait "1" seconds
    And I click on "Server" "link"
    And I wait "1" seconds
    And I click on "Manage tokens" "link"
    And I wait "1" seconds
    And I click on "Create token" "button"
    And I wait "1" seconds
    And I click the autocomplete selection
    And I press the tab key
    And I press the down key
    And I wait "1" seconds
    When I type "teacher Lastname"
    And I wait "1" seconds
    And I press the enter key
    And I click on "id_service" "select"
    And I wait "1" seconds
    And I should see "qtype_qlowcode_endpoint"
    And I select "qtype_qlowcode_endpoint" from the "id_service" singleselect
    And I wait "1" seconds
    And I click on "submitbutton" "button"
    And I wait "1" seconds
    # Plugin config
    Given I click on "Site administration" "link"
    And I wait "1" seconds
    And I click on "Plugins" "link"
    And I wait "1" seconds
    And I click on "ClozeScript" "link"
    Then I set the following fields to these values:
      | Description | Unimoodle |
      | URL | https://qlowcode.isyc.com/ |
      | API URL | https://qlc-api.isyc.com/ |
    And I click on the element with classname "fa-pencil"
    And I wait "1" seconds
    When I type "q45253c53v54yy6ub6u34vb"
    And I wait "1" seconds
    And I press "Save changes"
    And I log out



    And I log in as "teacher"
    And I click on "My courses" "link"
    And I wait "1" seconds
    And I click on "Course for qlow" "link"
    And I turn editing mode on
    And I wait "1" seconds
    Given I click on "Add an activity or resource" "button" in the "Topic 1" "section"
    And I click on "Add a new Quiz" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Adding a new Quiz"
    And I wait "1" seconds
    Then I set the following fields to these values:
      | Name | Quiz for qlow plugin |
    And I wait "1" seconds
    And I press "Save and display"
    And I wait "1" seconds
    Then I should see "Quiz for qlow plugin"
    And I turn editing mode off
    And I wait "1" seconds
    And I click on "Add question" "link"
    And I wait "1" seconds
    And I click on "action-menu-toggle-1" "link"
    And I wait "1" seconds
    And I click on the element with classname "fa-plus"
    And I wait "1" seconds
    And I click on "item_qtype_qlowcode" "radio"  
    And I wait "1" seconds
    And I click on the element with classname "submitbutton"
    And I wait "1" seconds

    # Quiz question edit
    Then I set the following fields to these values:
      | Question name | QuestionQlow |
    And I wait "1" seconds
    And I select "Workspace tests local" from the "id_workspaceid" singleselect
    And I select "Questions v7.3" from the "id_applicationid" singleselect
    And I select "Sandwiches" from the "id_pageurl" singleselect
    And I wait "1" seconds
    And I press "id_submitbutton"

    # Preview quiz
    And I wait "1" seconds
    Given I am on the "Quiz for qlow plugin" "mod_quiz > View" page
    And I wait "1" seconds
    And I press "Preview quiz"
    And I wait "1" seconds
    And I press "mod_quiz-next-nav"
    And I wait "10" seconds

    

    

    
    