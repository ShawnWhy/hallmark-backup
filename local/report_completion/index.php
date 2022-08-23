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

/**
 * @package   local_report_completion
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/completionlib.php');
//require_once($CFG->libdir.'/excellib.class.php');
require_once($CFG->dirroot.'/local/iomad_track/lib.php');
require_once($CFG->dirroot.'/local/iomad_track/db/install.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
//require_once(dirname(__FILE__).'/lib.php');
//require_once(dirname(__FILE__).'/locallib.php');

// chart stuff
define('PCHART_SIZEX', 500);
define('PCHART_SIZEY', 500);

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$participant = optional_param('participant', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_CLEAN);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$showhistoric = optional_param('showhistoric', 1, PARAM_BOOL);
$email  = optional_param('email', 0, PARAM_CLEAN);
$timecreated  = optional_param('timecreated', 0, PARAM_CLEAN);
$sort         = optional_param('sort', '', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_users, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$coursesearch = optional_param('coursesearch', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$completiontype = optional_param('completiontype', 0, PARAM_INT);
$charttype = optional_param('charttype', '', PARAM_CLEAN);
$showchart = optional_param('showchart', false, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_INT);
$fromraw = optional_param_array('compfromraw', null, PARAM_INT);
$toraw = optional_param_array('comptoraw', null, PARAM_INT);
$yearfrom = optional_param_array('fromarray', null, PARAM_INT);
$yearto = optional_param_array('toarray', null, PARAM_INT);

//custom //coursereation
$coursecreationyearfrom = optional_param_array('coursecreationfromarray', null, PARAM_INT);
$coursecreationyearto = optional_param_array('coursecreationtoarray', null, PARAM_INT);
//end //coursecompletion

$showpercentage = optional_param('showpercentage', 0, PARAM_INT);
$submitbutton = optional_param('submitbutton', '', PARAM_CLEAN);
$validonly = optional_param('validonly', 0, PARAM_BOOL);
$adminediting = optional_param('adminedit', -1, PARAM_BOOL);
$action = optional_param('action', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$rowid = optional_param('rowid', 0, PARAM_INT);
$redocertificate = optional_param('redocertificate', 0, PARAM_INT);

//custom code //coursecreationdate
$coursecreationfromraw = optional_param_array('compcoursecreationfromraw', null, PARAM_INT);
$coursecreationtoraw = optional_param_array('compcoursecreationtoraw', null, PARAM_INT);

//end //coursecreationdate

require_login($SITE);
$context = context_system::instance();
iomad::require_capability('local/report_completion:view', $context);

$params['courseid'] = $courseid;
if ($firstname) {
    $params['firstname'] = $firstname;
}
if ($lastname) {
    $params['lastname'] = $lastname;
}
if ($email) {
    $params['email'] = $email;
}
if ($sort) {
    $params['sort'] = $sort;
}
if ($dir) {
    $params['dir'] = $dir;
}
if ($page) {
    $params['page'] = $page;
}
if ($perpage) {
    $params['perpage'] = $perpage;
}
if ($coursesearch) {
    $params['coursesearch'] = $coursesearch;
}
if ($courseid) {
    $params['courseid'] = $courseid;
}
if ($departmentid) {
    $params['deptid'] = $departmentid;
}
if ($departmentid) {
    $params['departmentid'] = $departmentid;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
}
if ($completiontype) {
    $params['completiontype'] = $completiontype;
}
if ($fromraw) {
    
    if (is_array($fromraw)) {
        $from = mktime(0, 0, 0, $fromraw['month'], $fromraw['day'], $fromraw['year']);
    } else {
        $from = $fromraw;
    }
    $params['from'] = $from;
    $params['fromraw[day]'] = $fromraw['day'];
    $params['fromraw[month]'] = $fromraw['month'];
    $params['fromraw[year]'] = $fromraw['year'];
    $params['fromraw[enabled]'] = $fromraw['enabled'];
} else {
    $from = null;
}


//custom //coursecreationdate
if ($coursecreationfromraw) {
    
    if (is_array($coursecreationfromraw)) {
        $coursecreationfrom = mktime(0, 0, 0, $coursecreationfromraw['month'], $coursecreationfromraw['day'], $coursecreationfromraw['year']);
    } else {
        $coursecreationfrom = $coursecreationfromraw;
    }
    $params['coursecreationfrom'] = $coursecreationfrom;
    $params['$coursecreationfromraw[day]'] = $coursecreationfromraw['day'];
    $params['$coursecreationfromraw[month]'] = $coursecreationfromraw['month'];
    $params['$coursecreationfromraw[year]'] = $coursecreationfromraw['year'];
    $params['$coursecreationfromraw[enabled]'] = $coursecreationfromraw['enabled'];
} else {
    $coursecreationfrom = null;
}

// custom //end coursecreation 

if ($toraw) {
    if (is_array($toraw)) {
        $to = mktime(0, 0, 0, $toraw['month'], $toraw['day'], $toraw['year']);
    } else {
        $to = $toraw;
    }
    $params['to'] = $to;
    $params['toraw[day]'] = $toraw['day'];
    $params['toraw[month]'] = $toraw['month'];
    $params['toraw[year]'] = $toraw['year'];
    $params['toraw[enabled]'] = $toraw['enabled'];
} else {
    if (!empty($from)) {
        $to = time();
        $params['to'] = $to;
    } else {
        $to = null;
    }
}

if ($coursecreationtoraw) {
    // echo "coursecreationtoraw";
    // var_dump($coursecreationtoraw);
    
    if (is_array($coursecreationtoraw)) {
        $coursecreationto = mktime(0, 0, 0, $coursecreationtoraw['month'], $coursecreationtoraw['day'], $coursecreationtoraw['year']);
    } else {
        $coursecreationto = $coursecreationtoraw;
    }
    $params['coursecreationto'] = $coursecreationto;
    $params['coursecreationtoraw[day]'] = $coursecreationtoraw['day'];
    $params['coursecreationtoraw[month]'] = $coursecreationtoraw['month'];
    $params['coursecreationtoraw[year]'] = $coursecreationtoraw['year'];
    $params['coursecreationtoraw[enabled]'] = $coursecreationtoraw['enabled'];
} else {
    if (!empty($coursecreationfrom)) {
        $coursecreationto = time();
        $params['coursecreationto'] = $coursecreationto;
    } else {
        $coursecreationto = null;
    }
}

//end custom //coursecreation

$params['showpercentage'] = $showpercentage;
$params['validonly'] = $validonly;

// Url stuff.
$url = new moodle_url('/local/report_completion/index.php', array('validonly' => $validonly));
$dashboardurl = new moodle_url('/my');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_completion');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_completion/styles.css");
$PAGE->requires->jquery();
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($strcompletion, $url);
if (!empty($courseid)) {
    if ($courseid == 1) {
        $PAGE->navbar->add(get_string("allusers", 'local_report_completion'));
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
            $PAGE->navbar->add(format_string($course->fullname, true, 1));
    }
}

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Deal with the adhoc form.
$data = data_submitted();
if (!empty($data)) {
    if (!empty($data->redo_selected_certificates) && !empty($data->redo_certificates)) {
        if (!empty($confirm) && confirm_sesskey()) {
            iomad::require_capability('local/report_users:redocertificates', $context);
            echo $OUTPUT->header();
            foreach($data->redo_certificates as $redocertificate) {
                if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $redocertificate))) {
                    echo html_writer::start_tag('p');
                    local_iomad_track_delete_entry($redocertificate);
                    xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id);
                    echo html_writer::end_tag('p');
                }
            }
            echo $OUTPUT->single_button(new moodle_url('/local/report_completion/index.php',
                                     $params), get_string('continue'));
            echo $OUTPUT->footer();
            die;
        } else {
            iomad::require_capability('local/report_users:redocertificates', $context);
            $param_array = array('courseid' => $courseid,
                                 'confirm' => true,
                                 'redo_selected_certificates' => $data->redo_selected_certificates,
                                 'sesskey' => sesskey()
                                 );
            foreach ($data->redo_certificates as $key => $redocertificate) {
                $param_array["redo_certificates[$key]"] = $redocertificate;
            }
            $confirmurl = new moodle_url('/local/report_completion/index.php', $param_array + $params);

            $cancel = new moodle_url('/local/report_completion/index.php', $params);
            echo $OUTPUT->header();
            echo $OUTPUT->confirm(get_string('redoselectedcertificatesconfirm', 'block_iomad_company_admin'), $confirmurl, $cancel);
            echo $OUTPUT->footer();
            die;

        }
    } else if (!empty($data->origlicenseallocated) ||
               !empty($data->origtimeenrolled) ||
               !empty($data->origtimecompleted) ||
               !empty($data->origfinalscore)) {
        iomad::require_capability('local/report_users:updateentries', $context);
        if (!empty($data->licenseallocated)) {
            $data->licenseallocated = clean_param_array($data->licenseallocated, PARAM_INT, true);
        }
        if (!empty($data->timeenrolled)) {
            $data->timeenrolled = clean_param_array($data->timeenrolled, PARAM_INT, true);
        }
        if (!empty($data->timecompleted)) {
            $data->timecompleted = clean_param_array($data->timecompleted, PARAM_INT, true);
        }
        if (!empty($data->origlicenseallocated)) {
            $data->origlicenseallocated = clean_param_array($data->origlicenseallocated, PARAM_INT);
        }
        if (!empty($data->origtimeenrolled)) {
            $data->origtimeenrolled = clean_param_array($data->origtimeenrolled, PARAM_INT);
        }
        if (!empty($data->origtimecompleted)) {
            $data->origtimecompleted = clean_param_array($data->origtimecompleted, PARAM_INT);
        }
        if (!empty($data->finalscore)) {
            $data->finalscore = clean_param_array($data->finalscore, PARAM_INT);
        }
        if (!empty($data->origfinalscore)) {
            $data->origfinalscore = clean_param_array($data->origfinalscore, PARAM_INT);
        }

        // Update any data sent from the form.
        if (!empty($data->finalscore)) {
            foreach ($data->finalscore as $key => $value) {
                if ($data->origfinalscore[$key] != $value && confirm_sesskey()) {

                    $DB->set_field('local_iomad_track', 'finalscore', $value, array('id' => $key));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));

                    // Re-generate the certificate.
                    if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $key))) {
                        local_iomad_track_delete_entry($key);
                        xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false);
                    }
                }
            }
        }
        if (!empty($data->licenseallocated)) {
            // echo "iomad completion record 270++++++++++++++++++++++++++++++++++++++";
            foreach ($data->licenseallocated as $key => $value) {
                $testtime = strtotime("0:00", $data->origlicenseallocated[$key]);
                $senttime = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);

                if ($testtime != $senttime && confirm_sesskey()) {
                    // echo "local/reportcompletion 275++++++++++++++++++++++++++++++";

                    $DB->set_field('local_iomad_track', 'licenseallocated', $senttime, array('id' => $key));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));
                }
            }
        }
        if (!empty($data->timeenrolled)) {
            foreach ($data->timeenrolled as $key => $value) {
                $testtime = strtotime("0:00", $data->origtimeenrolled[$key]);
                $senttime = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);

                if ($testtime != $senttime && confirm_sesskey()) {
                    // echo "local/reportcompletion 288++++++++++++++++++++++++++++++";

                    $DB->set_field('local_iomad_track', 'timeenrolled', $senttime, array('id' => $key));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));
                }
            }
        }
        if (!empty($data->timecompleted)) {
            // echo "iomad completion record 297++++++++++++++++++++++++++++++++++++++";
            foreach ($data->timecompleted as $key => $value) {
                // echo "iomad completion record 299++++++++++++++++++++++++++++++++++++++";
                if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $key))) {
                    $testtime = strtotime("0:00", $data->origtimecompleted[$key]);
                    $senttime = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);

                    if ($testtime != $senttime && confirm_sesskey()) {
                        // echo "local/reportcompletion 302++++++++++++++++++++++++++++++";

                        $DB->set_field('local_iomad_track', 'timecompleted', $senttime, array('id' => $key));
                        $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));
                        if ($iomadcourseinfo = $DB->get_record('iomad_courses', array('courseid' => $trackrec->courseid))) {
                            // echo "local/reportcompletion 306+++++++++++++++++++++++++";
                            if (!empty($iomadcourseinfo->validlength)) {
                                $DB->set_field('local_iomad_track', 'timeexpires', $senttime + ($iomadcourseinfo->validlength * 24 * 60 * 60), array('id' => $key));
                            }
                        }

                        // Re-generate the certificate.
                        local_iomad_track_delete_entry($key);
                        xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false);
                    }
                }
            }
        }
    }
}

// Deal with edit buttons.
if ($adminediting != -1) {
    $SESSION->iomadeditingreports = $adminediting;
}
if (iomad::has_capability('local/report_users:updateentries', context_system::instance()) && !empty($courseid)) {
    // echo "iomad completion record 331++++++++++++++++++++++++++++++++++++++";
    $editurl = new moodle_url($CFG->wwwroot . '/local/report_completion/index.php', $params);
    if (!empty($SESSION->iomadeditingreports)) {
        $caption = get_string('turneditingoff');
        $editurl->param('adminedit', 'off');
    } else {
        $caption = get_string('turneditingon');
        $editurl->param('adminedit', 'on');
    }
    $buttons = $OUTPUT->single_button($editurl, $caption, 'get');
    $PAGE->set_button($buttons);
}

// Check for user/course delete?
if (!empty($action)) {
    if (!empty($confirm) && confirm_sesskey()) {
        if ($action == 'redocert' && !empty($redocertificate)) {
            if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $redocertificate))) {
                local_iomad_track_delete_entry($redocertificate);
                if (xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false)) {
                    redirect(new moodle_url('/local/report_completion/index.php', $params),
                             get_string($action . "_successful", 'local_report_users'),
                             null,
                             \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url('/local/report_completion/index.php', $params),
                             get_string($action . "_failed", 'local_report_users'),
                             null,
                             \core\output\notification::NOTIFY_ERROR);
                }
            }
        }
    } else {
        echo $OUTPUT->header();
        $confirmurl = new moodle_url('/local/report_completion/index.php',
                                     array('rowid' => $rowid,
                                     'confirm' => $redocertificate,
                                     'redocertificate' => $redocertificate,
                                     'courseid' => $courseid,
                                     'action' => $action,
                                     'sesskey' => sesskey()
                                     ) + $params);
        $cancel = new moodle_url('/local/report_completion/index.php',
                                 $params);
        echo $OUTPUT->confirm(get_string('redocertificateconfirm', 'local_report_users'), $confirmurl, $cancel);

        echo $OUTPUT->footer();
        die;
    }
}

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Work out department level.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// Work out where the user sits in the company department tree.
if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', \context_system::instance())) {
    $userlevels = array($parentlevel->id => $parentlevel->id);
} else {
    $userlevels = $company->get_userlevel($USER);
}

$userhierarchylevel = key($userlevels);
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Get the company additional optional user parameter names.
$foundobj = iomad::add_user_filter_params($params, $companyid);
$idlist = $foundobj->idlist;
$foundfields = $foundobj->foundfields;

$url = new moodle_url('/local/report_completion/index.php', $params);
$selectparams = $params;
$selecturl = new moodle_url('/local/report_completion/index.php', $selectparams);

// Set up the user search parameters.
if ($courseid == 1) {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);
} else {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, false, false);
}

// Create data for filter form.
$customdata = null;

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Are we showing the overview table?
if (empty($courseid)) {
    // echo"course completion report 431 ++++++++++++++++++++++++++++++++";
    // Set up the course display table.
    $coursetable = new \local_report_completion\tables\course_table('local_report_completion_course_table');
    $coursetable->is_downloading($download, 'local_report_course_completion_course', 'local_report_coursecompletion_course123');

    if (!$coursetable->is_downloading()) {

        // echo"course completion report 438 ++++++++++++++++++++++++++++++++";
        // Display the header.
        echo $output->header();

        // What heading are we displaying?
        if (empty($courseid)) {
            // echo"course completion report 444 ++++++++++++++++++++++++++++++++";
            if (empty($to) && empty($from)) {
                echo "<h3>".get_string('coursesummary', 'local_report_completion')."</h3>";
            } else {
                $fromstring = get_string('beginningoftime', 'local_report_completion');
                $tostring = get_string('now');
                if (!empty($from)) {
                    $fromstring = date($CFG->iomad_date_format,$from);
                }
                if (!empty($to)) {
                    $tostring= date($CFG->iomad_date_format,$to);
                }
                echo "<h3>".get_string('coursesummarywithdate', 'local_report_completion', array('from' => $fromstring, 'to' => $tostring))."</h3>";
            }
        } else if ($courseid == 1) {
            echo "<h3>".get_string('reportallusers', 'local_report_completion')."</h3>";
        } else {
            echo "<h3>".get_string('courseusers', 'local_report_completion').format_string($courseinfo[$courseid]->coursename, true, 1)."</h3>";
        }

        // Display the department selector.
        echo $output->display_tree_selector($company, $parentlevel, $selecturl, $selectparams, $departmentid);
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrols'));

        $mform = new iomad_course_search_form($url, $params);
        $mform->set_data($params);

        // Set up the date filter form.
        $datemform = new iomad_date_filter_form($url, $params);
        $datemform->set_data(array('departmentid' => $departmentid));
        $options = $params;
        $options['compfromraw'] = $from;
        $options['comptoraw'] = $to;
        $datemform->set_data($options);
        $datemform->get_data();

        //custom //coursecreationdate
        $coursecreationdateform = new iomad_date_filter_form_course_completion($url, $params);
        $coursecreationdateform->set_data(array('departmentid' => $departmentid));
        $options = $params;
        $options['compcoursecreationfromraw'] = $coursecreationfrom;
        $options['compcoursecreationtoraw'] = $coursecreationto;
        $coursecreationdateform->set_data($options);
        $coursecreationdateform->get_data();

        //end //coursecreationdate

        // Display the control buttons.
        $alluserslink = new moodle_url($url, array(
            'courseid' => 1,
            'departmentid' => $departmentid,
        ));
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo $output->single_button($alluserslink, get_string("allusers", 'local_report_completion'));
        echo html_writer::end_tag('div');

        // Also for suspended user controls.
        $showsuspendedparams = $params;
        if (!$showsuspended) {
            $showsuspendedparams['showsuspended'] = 1;
            $suspendeduserslink = new moodle_url($url, $showsuspendedparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($suspendeduserslink, get_string("showsuspendedusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        } else {
            $showsuspendedparams['showsuspended'] = 0;
            $suspendeduserslink = new moodle_url($url, $showsuspendedparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($suspendeduserslink, get_string("hidesuspendedusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        }

        // Also for percentage of user controls.
        $showpercentageparams = $params;
        if (!$showpercentage) {
            // echo"course completion report 511 ++++++++++++++++++++++++++++++++";
            $showpercentageparams['showpercentage'] = 1;
            $percentageuserslink = new moodle_url($url, $showpercentageparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($percentageuserslink, get_string("showpercentageusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        } else {
            $showpercentageparams['showpercentage'] = 0;
            $percentageuserslink = new moodle_url($url, $showpercentageparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($percentageuserslink, get_string("hidepercentageusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        }

        // Also for validonly courses user controls.
        $validonlyparams = $params;
        $validonlyparams['validonly'] = !$validonly;
        if (!$validonly) {
            // echo"course completion report 529 ++++++++++++++++++++++++++++++++";
            $validonlystring = get_string('hidevalidcourses', 'block_iomad_company_admin');
        } else {
            // echo"course completion report 532 ++++++++++++++++++++++++++++++++";
            $validonlystring = get_string('showvalidcourses', 'block_iomad_company_admin');
        }
        $validonlylink = new moodle_url($url, $validonlyparams);
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo $output->single_button($validonlylink, $validonlystring);
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        $mform->display();
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        $datemform->display();
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');

        //custom //coursecreationdate
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo "<h3 style='margin-left:1em'> Search by Course Creation Date</h3>";
        $coursecreationdateform->display();
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
     
        // var_dump($params);
      

    //custom //coursecreationdate
    }


    // Deal with any course searches.
    $searchparams = array();
    if (!empty($coursesearch)) {
        echo"course completion report 552 ++++++++++++++++++++++++++++++++";
        $coursesearchsql = " AND courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") AND " . $DB->sql_like('coursename', ':coursename', false, false);
        $searchparams['coursename'] = "%" . $coursesearch . "%";
    } else {
        echo"course completion report 556 ++++++++++++++++++++++++++++++++";
        $coursesearchsql = " AND courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
    }
    
    ////////////////////////////////////////////
    if(!empty($coursecreationfrom)){

        if ($DB->get_records_sql("SELECT id AS courseid FROM {course} WHERE timecreated >= ". $coursecreationfrom . " AND timecreated <=" . $coursecreationto)) {
        $ids = array();
        // $headers[] = 'ids';
        $idsgot = $DB->get_records_sql("SELECT id AS courseid FROM {course} WHERE timecreated >= ". $coursecreationfrom . " AND timecreated <=" . $coursecreationto);
        
        foreach ($idsgot as $id ){
        array_push($ids, $id->{'courseid'});
        }
        // var_dump($ids);      
        // foreach($ids as $id){
        //     echo $id;
        // }
        
        $coursesearchsql = $coursesearchsql . " AND courseid IN (" . implode(",",$ids) . ") ";
        // echo "dsdsdsdsdsdsdsdsd";
        // echo $coursesearchsql;

    }
    else{

        echo "<h2>Invalid Search Date or No data was found within that time period</h2>";

    }
    }
    var_dump( $coursesearch);
    echo $coursesearchsql;

    // Set up the SQL for the table.
    $selectsql = "courseid as id, coursename, $departmentid AS departmentid, $showsuspended AS showsuspended, companyid";
    $fromsql = "{local_iomad_track}";
    $sqlparams = array('companyid' => $companyid) + $searchparams;

    $wheresql = "companyid = :companyid $coursesearchsql group by courseid, coursename, companyid";

    // Set up the headers for the table.
    $courseheaders = array(get_string('coursename', 'local_report_completion'),
                     get_string('licenseallocated', 'local_report_user_license_allocations'),
                     get_string('usersummary', 'local_report_completion'));
    $coursecolumns = array('coursename',
                     'licenseallocated',
                     'usersummary');

    $coursetable->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $coursetable->define_baseurl($url);
    $coursetable->define_columns($coursecolumns);
    $coursetable->define_headers($courseheaders);
    $coursetable->no_sorting('licenseallocated');
    $coursetable->no_sorting('usersummary');
    $coursetable->sort_default_column = 'coursename';
    $coursetable->out($CFG->iomad_max_list_users, true);
    // echo"++++++++++++coursetable";
    // var_dump($coursetable);

    if (!$coursetable->is_downloading()) {
        echo $output->footer();
    }
} else {
    // Do we have any additional reporting fields?
    $extrafields = array();
    if (!empty($CFG->iomad_report_fields)) {
        $companyrec = $DB->get_record('company', array('id' => $companyid));
        foreach (explode(',', $CFG->iomad_report_fields) as $extrafield) {
            $extrafields[$extrafield] = new stdclass();
            $extrafields[$extrafield]->name = $extrafield;
            if (strpos($extrafield, 'profile_field') !== false) {
                // Its an optional profile field.
                $profilefield = $DB->get_record('user_info_field', array('shortname' => str_replace('profile_field_', '', $extrafield)));
                if ($profilefield->categoryid == $companyrec->profileid ||
                    !$DB->get_record('company', array('profileid' => $profilefield->categoryid))) {
                    $extrafields[$extrafield]->title = $profilefield->name;
                    $extrafields[$extrafield]->fieldid = $profilefield->id;
                } else {
                    unset($extrafields[$extrafield]);
                }
            } else {
                $extrafields[$extrafield]->title = get_string($extrafield);
            }
        }
    }

    // Set up the display table.
    $table = new \local_report_completion\tables\user_table('local_report_course_completion_user_table');
    $table->is_downloading($download, 'local_report_course_completion_user', 'local_report_coursecompletion_user123');

    // Deal with sort by course for all courses if sort is empty.
    if (empty($sort) && $courseid == 1) {
        echo"course completion report 619 ++++++++++++++++++++++++++++++++";
        $table->sort_default_column = 'coursename';
    }

    if (!$table->is_downloading()) {
        echo"course completion report 624 ++++++++++++++++++++++++++++++++";
        echo $output->header();

        // Display the search form and department picker.
        if (!empty($companyid)) {
            echo"course completion report 629 ++++++++++++++++++++++++++++++++";
            if (empty($table->is_downloading())) {
                echo $output->display_tree_selector($company, $parentlevel, $selecturl, $selectparams, $departmentid);

                // Set up the filter form.
                $options = $params;
                $options['companyid'] = $companyid;
                $options['addfrom'] = 'compfromraw';
                $options['addto'] = 'comptoraw';
                $options['adddodownload'] = false;
                $options['compfromraw'] = $from;
                $options['comptoraw'] = $to;
                $options['addvalidonly'] = true;

                //custom code 
                
                $mform = new iomad_user_filter_form(null, $options);
                $mform->set_data(array('departmentid' => $departmentid, 'validonly' => $validonly));
                $mform->set_data($options);
                $mform->get_data();

                // Display the user filter form.
                $mform->display();
            }
        }
    }

    $sqlparams = array('companyid' => $companyid, 'courseid' => $courseid);

    // Deal with where we are on the department tree.
    $currentdepartment = company::get_departmentbyid($departmentid);
    $showdepartments = company::get_subdepartments_list($currentdepartment);
    $showdepartments[$departmentid] = $departmentid;
    $departmentsql = " AND d.id IN (" . implode(',', array_keys($showdepartments)) . ")";

    // all companies?
    if ($parentslist = $company->get_parent_companies_recursive()) {
        // echo"course completion report 663 ++++++++++++++++++++++++++++++++";
        $companysql = " AND u.id NOT IN (
                        SELECT userid FROM {company_users}
                        WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
    } else {
        // echo"course completion report 668 ++++++++++++++++++++++++++++++++";
        $companysql = "";
    }

    // All courses or just the one?
    if ($courseid != 1) {
        // echo"course completion report 674 ++++++++++++++++++++++++++++++++";
        $coursesql = " AND lit.courseid = :courseid AND lit.courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
    } else {
        echo"course completion report 677 ++++++++++++++++++++++++++++++++";
        $coursesql = " AND lit.courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
    }

    // Deal with any search dates.
    $datesql = "";
    if (!empty($params['from'])) {
        // echo"course completion report 684 ++++++++++++++++++++++++++++++++";
        $datesql = " AND (lit.timeenrolled > :enrolledfrom OR lit.timecompleted > :completedfrom ) ";
        $sqlparams['enrolledfrom'] = $params['from'];
        $sqlparams['completedfrom'] = $params['from'];
    }
    if (!empty($params['to'])) {
        // echo"course completion report 690 ++++++++++++++++++++++++++++++++";
        $datesql .= " AND (lit.timeenrolled < :enrolledto OR lit.timecompleted < :completedto) ";
        $sqlparams['enrolledto'] = $params['to'];
        $sqlparams['completedto'] = $params['to'];
    }

    // Just valid courses?
    if ($validonly) {
        // echo"course completion report 697 ++++++++++++++++++++++++++++++++";
        $validsql = " AND (lit.timeexpires > :runtime or (lit.timecompleted IS NULL) or (lit.timecompleted > 0 AND lit.timeexpires IS NULL))";
        $sqlparams['runtime'] = time();
    } else {
        echo"course completion report 702 ++++++++++++++++++++++++++++++++";
        $validsql = "";
    }

    // Set up the initial SQL for the form.
    $selectsql = "lit.id,u.id as userid,u.firstname,u.lastname,d.name AS department,u.email,lit.id as certsource, lit.courseid,lit.coursename,lit.timecompleted,lit.timeenrolled,lit.timestarted,lit.timeexpires,lit.finalscore,lit.licenseid,lit.licensename, lit.licenseallocated";
    $fromsql = "{user} u JOIN {local_iomad_track} lit ON (u.id = lit.userid) JOIN {company_users} cu ON (u.id = cu.userid AND lit.userid = cu.userid AND lit.companyid = cu.companyid) JOIN {department} d ON (cu.departmentid = d.id)";
    $wheresql = $searchinfo->sqlsearch . " AND cu.companyid = :companyid $departmentsql $companysql $datesql $coursesql $validsql";
    $sqlparams = $sqlparams + $searchinfo->searchparams;

    // Custom Code Start
    // Set up the headers for the form.
    $headers = array(get_string('firstname'),
                     get_string('lastname'),
                     get_string('department', 'block_iomad_company_admin'),
                     get_string('email'));
    // Custom Code End

    $columns = array('firstname',
                     'lastname',
                     'department',
                     'email');

    if (empty($SESSION->iomadeditingreports)) {
        echo"course completion report 726 ++++++++++++++++++++++++++++++++";
        // Deal with optional report fields.
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headers[] = $extrafield->title;
                $columns[] = $extrafield->name;
                if (!empty($extrafield->fieldid)) {
                    // Its a profile field.
                    // Skip it this time as these may not have data.
                } else {
                    $selectsql .= ", u." . $extrafield->name;
                }
            }
            foreach ($extrafields as $extrafield) {
                if (!empty($extrafield->fieldid)) {
                    // Its a profile field.
                    $selectsql .= ", P" . $extrafield->fieldid . ".data AS " . $extrafield->name;
                    $fromsql .= " LEFT JOIN {user_info_data} P" . $extrafield->fieldid . " ON (u.id = P" . $extrafield->fieldid . ".userid AND P".$extrafield->fieldid . ".fieldid = :p" . $extrafield->fieldid . "fieldid )";
                    $sqlparams["p".$extrafield->fieldid."fieldid"] = $extrafield->fieldid;
                }
            }
        }
    }

    // Are we showing all courses?
    // if ($courseid == 1) {
    // Modified due to wanting to always show the course
    // Custom Code Start
    if (true) {
        echo"course completion report 755 ++++++++++++++++++++++++++++++++";
        $headers[] = get_string('course');
        $columns[] = 'coursename';
    }
    // Custom Code End

    // Status column.
    $headers[] =  get_string('status');
    $columns[] = 'status';

    // Is this licensed?
    if ($courseid == 1 ||
        $DB->get_record('iomad_courses', array('courseid' => $courseid, 'licensed' => 1)) ||
        $DB->count_records_sql("SELECT count(id) FROM {local_iomad_track}
                                WHERE courseid = :courseid
                                AND licensename IS NOT NULL",
                                array('courseid' => $courseid)) > 0) {

        echo "completion reprt index line 742 +++++++++++++++++++++++++";
        // Need to add the license columns
        if (empty($SESSION->iomadeditingreports)) {
            $headers[] = get_string('licensename', 'block_iomad_company_admin');
            $columns[] = 'licensename';
        }
        $headers[] = get_string('licensedateallocated', 'block_iomad_company_admin');
        $columns[] = 'licenseallocated';
    }

    // And enrolment columns.
    $headers[] = get_string('timestarted', 'local_report_completion');
    $headers[] = get_string('timecompleted', 'local_report_completion');
    $columns[] = 'timeenrolled';
    $columns[] = 'timecompleted';

    // Does this course have an expiry time?
    if (($courseid == 1 && $DB->get_records_sql("SELECT id FROM {iomad_courses} WHERE courseid IN (SELECT courseid FROM {local_iomad_track} WHERE companyid = :companyid) AND expireafter != 0", array('companyid' => $company->id))) ||
        $DB->get_record_sql("SELECT id FROM {iomad_courses} WHERE courseid = :courseid AND validlength > 0", array('courseid' => $courseid))) {
        $columns[] = 'timeexpires';
        $headers[] = get_string('timeexpires', 'local_report_completion');
        echo "iomad completion record 763++++++++++++++++++++++++++++++++++++++";
    }

    // Does this course have an visible grade?
    if (($courseid == 1 && $DB->get_records_sql("SELECT id FROM {iomad_courses} WHERE courseid IN (SELECT courseid FROM {local_iomad_track} WHERE companyid = :companyid) AND hasgrade = 1", array('companyid' => $company->id))) ||
        $DB->get_record_sql("SELECT id FROM {iomad_courses} WHERE courseid = :courseid AND hasgrade = 1", array('courseid' => $courseid))) {
        $columns[] = 'finalscore';
        $headers[] = get_string('grade');
        echo "iomad completion record 771++++++++++++++++++++++++++++++++++++++";
    }

    // And finally the last of the columns.
    if (!$table->is_downloading()) {
        $headers[] = get_string('certificate', 'local_report_completion');
        $columns[] = 'certificate';
        echo "iomad completion record 778++++++++++++++++++++++++++++++++++++++";
    }

    // Set up the form.
    if (!empty($SESSION->iomadeditingreports) && !$table->is_downloading()) {
        echo "iomad completion record 783++++++++++++++++++++++++++++++++++++++";
        echo html_writer::start_tag('form', array('action' => $url,
                                                  'enctype' => 'application/x-www-form-urlencoded',
                                                  'method' => 'post',
                                                  'name' => 'iomad_report_user_userdisplay_values',
                                                  'id' => 'iomad_report_user_userdisplay_values'));
        echo "<input type='hidden' name='sesskey' value=" . sesskey() .">";
        echo "<input type='hidden' name='download' value=''>";
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo html_writer::start_tag('div', array('class' => 'singlebutton'));
        echo "<input type = 'submit' id='redo_all_certs' name='redo_selected_certificates' value = '" . get_string('redoselectedcertificates', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));

    }

    // Set up the table and display it.
    $table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $table->define_baseurl($url);
    $table->define_columns($columns);
    $table->define_headers($headers);
    $table->no_sorting('status');
    $table->no_sorting('certificate');
    $table->sort_default_column = 'lastname';
    if (!empty($SESSION->iomadeditingreports)) {
        $table->downloadable = false;
    }

    if (!$table->is_downloading()) {
        echo html_writer::start_tag('div', array('class' => 'tablecontainer'));
    }

    $table->out($CFG->iomad_max_list_courses, true);

    if (!$table->is_downloading()) {
        if (!empty($SESSION->iomadeditingreports)) {
            // Set up the form.
            echo html_writer::end_tag('div');
            echo html_writer::start_tag('div', array('class' => 'iomadclear'));
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo html_writer::start_tag('div', array('class' => 'singlebutton'));
            echo "<input type = 'submit' id='redo_all_certs_bottom' name='redo_selected_certificates' value = '" . get_string('redoselectedcertificates', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('form');
            echo html_writer::end_tag('div');
            form_init_date_js();
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');

        echo "<script> console.log($('[role='main']'))</script>";
?>

<script>

$(".checkbox").change(function() {
    var inputElems = document.getElementsByTagName("input")
    var matched = this.value;
    if(this.checked) {
        if(this.classList.contains("enableallcertificates")) {
            $(".enablecertificates").prop("checked", this.checked);
        }
        if(this.classList.contains("enableallentries")) {
            $(".enableentries").prop("checked", this.checked);
        }
    } else {
        if(this.classList.contains("enableallcertificates")) {
            $(".enablecertificates").prop("checked", '');
        }
        if(this.classList.contains("enableallentries")) {
            $(".enableentries").prop("checked", '');
        }
    }
});
</script>
<?php
        echo $output->footer();
    }
  
}

//custom //coursecreationdate
?>
<script>
$("[role='main']").css("display", "inline-block")
</script>
<?php ?>