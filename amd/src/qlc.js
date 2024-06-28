// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Module javascript to place new conditions.
 *
 * @module    qlc
 * @category  Classes - autoloading
 * @copyright 2023, ISYC
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import Url from 'core/url';
import ajax from 'core/ajax';

export const init = () => {
    selectConfigUrlEvent();
    selectWorkspaceEvent();
    selectApplicationEvent();
}

/**
 * Select Qlowcode application
 */
const selectConfigUrlEvent = () => {
    const selectElement = document.querySelector('[id*="id_configurl"]');
    
    // Listen for toggled manual completion states of activities.
    selectElement.addEventListener('change', (e) => {
        selectConfigUrlAjax(e.target.value);
    });
};

const selectConfigUrlAjax = (id) => {
    const targetId = 'id_applicationid';
    const targetIdPageUrl = 'id_pageurl';
    ajax.call([{
        methodname: 'qtype_qlowcode_get_applications',
        args: {
            id
        },
    }])[0].done(function(data) {
        clearSelect(targetId);
        const selectTarget = document.getElementById(targetId);
        for (var i = 0; i < data.length; i++) {
            let option = document.createElement("option");
            option.text = data[i].name;
            option.value = data[i].id;
            selectTarget.add(option);
            if(i == 0){
                selectWorkspaceAjax(data[i].id)
            }
        }
        return;
    }).fail(function(err) {
        clearSelect(targetId);
        clearSelect(targetIdPageUrl);
        console.log(err);
        //notification.exception(new Error('Failed to load data'));
        return;
    });
}

/**
 * Select Qlowcode workspace
 */
const selectWorkspaceEvent = () => {
    const selectElement = document.querySelector('[id*="id_workspaceid"]');
    
    // Listen for toggled manual completion states of activities.
    selectElement.addEventListener('change', (e) => {
        selectWorkspaceAjax(e.target.value)
    });
};

const selectWorkspaceAjax = (workspaceId) => {
    const configurl = document.getElementById('id_configurl');
    const targetId = 'id_applicationid';
    const targetIdPageUrl = 'id_pageurl';
    ajax.call([{
        methodname: 'qtype_qlowcode_get_applications',
        args: {
            id: configurl.value,
            workspaceId
        },
    }])[0].done(function(data) {
        clearSelect(targetId);
        const selectTarget = document.getElementById(targetId);
        for (var i = 0; i < data.length; i++) {
            let option = document.createElement("option");
            option.text = data[i].name;
            option.value = data[i].id;
            selectTarget.add(option);
        }
        selectApplicationAjax(null)
        return;
    }).fail(function(err) {
        clearSelect(targetId);
        clearSelect(targetIdPageUrl);
        console.log(err);
        //notification.exception(new Error('Failed to load data'));
        return;
    });
}

/**
 * Select Qlowcode url
 */
const selectApplicationEvent = () => {
    const selectElement = document.querySelector('[id*="id_applicationid"]');
    
    // Listen for toggled manual completion states of activities.
    selectElement.addEventListener('change', (e) => {
        console.log('addEventListener', e.target.value);
        selectApplicationAjax(e.target.value)
    });
};

const selectApplicationAjax = (applicationId) => {
    const configurl = document.getElementById('id_configurl');
    const targetId = 'id_pageurl';
    ajax.call([{
        methodname: 'qtype_qlowcode_get_pages',
        args: {
            id: configurl.value,
            applicationId
        },
    }])[0].done(function(data) {
        clearSelect(targetId);
        const selectTarget = document.getElementById(targetId);
        for (var i = 0; i < data.length; i++) {
            let option = document.createElement("option");
            option.text = data[i].name;
            option.value = data[i].id;
            selectTarget.add(option);
        }
        return;
    }).fail(function(err) {
        clearSelect(targetId);
        console.log(err);
        //notification.exception(new Error('Failed to load data'));
        return;
    });
}

const clearSelect = (id) => {
    const selectQuestionnaire = document.getElementById(id);
    // clear out old values
    selectQuestionnaire.innerHTML = '';
}