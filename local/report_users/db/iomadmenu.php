<?php
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Define the Iomad menu items that are defined by this plugin

function local_report_users_menu() {

        return array(
            'users' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => get_string('pluginname', 'local_report_users'),
                'url' => '/local/report_users/index.php',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-users',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            // Custom Code Start
            'learning-path-all' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => 'Learning path completion report - all',
                'url' => '/blocks/configurable_reports/viewreport.php?id=6',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-check-square-o',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            'learning-path-incomplete' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => 'Learning path completion report - incomplete only',
                'url' => '/blocks/configurable_reports/viewreport.php?id=7',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-check-square-o',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            'learning-path-complete' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => 'Learning path completion report - complete only',
                'url' => '/blocks/configurable_reports/viewreport.php?id=9',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-check-square-o',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            'courses-incomplete' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => 'Course completion by user - incomplete only',
                'url' => '/blocks/configurable_reports/viewreport.php?id=10',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-check-square-o',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            'active-users' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => 'Active Users',
                'url' => '/blocks/configurable_reports/viewreport.php?id=16',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-check-square-o',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            'all-users' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => 'All Users',
                'url' => '/blocks/configurable_reports/viewreport.php?id=15',
                'cap' => 'local/report_users:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-check-square-o',
                'iconsmall' => 'fa-bar-chart-o',
            ),
            // Custom Code End
        );
}
