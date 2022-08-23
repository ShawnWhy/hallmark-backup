<?php

namespace local_iomad\task;
use context_course;


class cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'local_iomad');
    }

    /**
     * Run local_iomad cron.
     */
    public function execute() {
        global $DB, $CFG;

        // We need company stuff.
        require_once($CFG->dirroot . '/local/iomad/lib/company.php');
        require_once($CFG->dirroot . '/config.php');




        //require_once('locallib.php');


        $runtime = time();

//bulk completing users
mtrace("\Bulk completing users based on custom profile fields\n");


    //course 1
       $users = $DB->get_records_sql("SELECT ci.userid, ci.data as courseid, unix_timestamp(cd.data) as date, cs.data as score, cu.companyid, c.shortname as coursename
                                    FROM {user_info_data} ci
                                    JOIN {user_info_data} cd on cd.userid = ci.userid
                                    JOIN {user_info_data} cs on cs.userid = ci.userid
                                    JOIN {company_users} cu on cu.userid = ci.userid
                                    JOIN {course} c on c.id = ci.data
                                    WHERE ci.fieldid = 17
                                    AND ci.data <> ''
                                    AND cd.fieldid = 18
                                    AND cd.data <> ''
                                    AND cs.fieldid = 19
                                    AND cs.data <> '' ");

        
        foreach ($users as $user) {
            //delete old record if exists
            $DB->delete_records('local_iomad_track', array('userid'=>$user->userid, 'courseid'=>$user->courseid));

            $ins = (object)array('userid' => $user->userid, 'courseid' => $user->courseid, 'timecompleted' => $user->date + 12*60*60, 'timeenrolled' => $user->date + 12*60*60, 'timestarted' => $user->date + 12*60*60, 'modifiedtime' => $user->date + 12*60*60, 'finalscore' => preg_replace('/\D/', '', $user->score), 'companyid'=>$user->companyid, 'coursename'=>$user->coursename);

            mtrace("adding completion record in course id ".  $user->courseid . " for user id " . $user->userid );         

            //insert learning path user record
            $DB->insert_record('local_iomad_track', $ins);
            $DB->set_field_select('user_info_data','data','',"fieldid IN(17,18,19)",array('userid'=>$user->userid));
       }

       //course 2
       $users = $DB->get_records_sql("SELECT ci.userid, ci.data as courseid, unix_timestamp(cd.data) as date, cs.data as score, cu.companyid, c.shortname as coursename
                                    FROM {user_info_data} ci
                                    JOIN {user_info_data} cd on cd.userid = ci.userid
                                    JOIN {user_info_data} cs on cs.userid = ci.userid
                                    JOIN {company_users} cu on cu.userid = ci.userid
                                    JOIN {course} c on c.id = ci.data
                                    WHERE ci.fieldid = 21
                                    AND ci.data <> ''
                                    AND cd.fieldid = 22
                                    AND cd.data <> ''
                                    AND cs.fieldid = 23
                                    AND cs.data <> '' ");

        
        foreach ($users as $user) {
            //delete old record if exists
            $DB->delete_records('local_iomad_track', array('userid'=>$user->userid, 'courseid'=>$user->courseid));


            $ins = (object)array('userid' => $user->userid, 'courseid' => $user->courseid, 'timecompleted' => $user->date + 12*60*60, 'timeenrolled' => $user->date + 12*60*60, 'timestarted' => $user->date + 12*60*60, 'modifiedtime' => $user->date + 12*60*60, 'finalscore' => preg_replace('/\D/', '', $user->score), 'companyid'=>$user->companyid, 'coursename'=>$user->coursename);


            mtrace("line 92 - adding completion record in course id ".  $user->courseid . " for user id " . $user->userid );
            //insert learning path user record
            $DB->insert_record('local_iomad_track', $ins);
            $DB->set_field_select('user_info_data','data','',"fieldid IN(21,22,23)",array('userid'=>$user->userid));

       }

       //course 3
       $users = $DB->get_records_sql("SELECT ci.userid, ci.data as courseid, unix_timestamp(cd.data) as date, cs.data as score, cu.companyid, c.shortname as coursename
                                    FROM {user_info_data} ci
                                    JOIN {user_info_data} cd on cd.userid = ci.userid
                                    JOIN {user_info_data} cs on cs.userid = ci.userid
                                    JOIN {company_users} cu on cu.userid = ci.userid
                                    JOIN {course} c on c.id = ci.data
                                    WHERE ci.fieldid = 24
                                    AND ci.data <> ''
                                    AND cd.fieldid = 25
                                    AND cd.data <> ''
                                    AND cs.fieldid = 26
                                    AND cs.data <> '' ");

        
        foreach ($users as $user) {
            //delete old record if exists
            $DB->delete_records('local_iomad_track', array('userid'=>$user->userid, 'courseid'=>$user->courseid));



            $ins = (object)array('userid' => $user->userid, 'courseid' => $user->courseid, 'timecompleted' => $user->date + 12*60*60, 'timeenrolled' => $user->date + 12*60*60, 'timestarted' => $user->date + 12*60*60, 'modifiedtime' => $user->date + 12*60*60, 'finalscore' => preg_replace('/\D/', '', $user->score), 'companyid'=>$user->companyid, 'coursename'=>$user->coursename);


            mtrace("adding completion record in course id ".  $user->courseid . " for user id " . $user->userid );
            //insert learning path user record
            $DB->insert_record('local_iomad_track', $ins);
            $DB->set_field_select('user_info_data','data','',"fieldid IN(24,25,26)",array('userid'=>$user->userid));

       }

       //course 4
       $users = $DB->get_records_sql("SELECT ci.userid, ci.data as courseid, unix_timestamp(cd.data) as date, cs.data as score, cu.companyid, c.shortname as coursename
                                    FROM {user_info_data} ci
                                    JOIN {user_info_data} cd on cd.userid = ci.userid
                                    JOIN {user_info_data} cs on cs.userid = ci.userid
                                    JOIN {company_users} cu on cu.userid = ci.userid
                                    JOIN {course} c on c.id = ci.data
                                    WHERE ci.fieldid = 27
                                    AND ci.data <> ''
                                    AND cd.fieldid = 28
                                    AND cd.data <> ''
                                    AND cs.fieldid = 29
                                    AND cs.data <> '' ");

        
        foreach ($users as $user) {
            //delete old record if exists
            $DB->delete_records('local_iomad_track', array('userid'=>$user->userid, 'courseid'=>$user->courseid));



            $ins = (object)array('userid' => $user->userid, 'courseid' => $user->courseid, 'timecompleted' => $user->date + 12*60*60, 'timeenrolled' => $user->date + 12*60*60, 'timestarted' => $user->date + 12*60*60, 'modifiedtime' => $user->date + 12*60*60, 'finalscore' => $user->score, 'companyid'=>$user->companyid, 'coursename'=>$user->coursename);


            mtrace("line 154 - adding completion record in course id ".  $user->courseid . " for user id " . $user->userid );
            //insert learning path user record
            $DB->insert_record('local_iomad_track', $ins);
            $DB->set_field_select('user_info_data','data','',"fieldid IN(27,28,29)",array('userid'=>$user->userid));

       }

        //course 5
            $users = $DB->get_records_sql("SELECT ci.userid, ci.data as courseid, unix_timestamp(cd.data) as date, cs.data as score, cu.companyid, c.shortname as coursename
            FROM {user_info_data} ci
            JOIN {user_info_data} cd on cd.userid = ci.userid
            JOIN {user_info_data} cs on cs.userid = ci.userid
            JOIN {company_users} cu on cu.userid = ci.userid
            JOIN {course} c on c.id = ci.data
            WHERE ci.fieldid = 30
            AND ci.data <> ''
            AND cd.fieldid = 31
            AND cd.data <> ''
            AND cs.fieldid = 32
            AND cs.data <> '' ");


    foreach ($users as $user) {
        
        //delete old record if exists
        $DB->delete_records('local_iomad_track', array('userid'=>$user->userid, 'courseid'=>$user->courseid));



        $ins = (object)array('userid' => $user->userid, 'courseid' => $user->courseid, 'timecompleted' => $user->date + 12*60*60, 'timeenrolled' => $user->date + 12*60*60, 'timestarted' => $user->date + 12*60*60, 'modifiedtime' => $user->date + 12*60*60, 'finalscore' => preg_replace('/\D/', '', $user->score), 'companyid'=>$user->companyid, 'coursename'=>$user->coursename);


        mtrace("line 186 - adding completion record in course id ".  $user->courseid . " for user id " . $user->userid );
        //insert learning path user record
        $DB->insert_record('local_iomad_track', $ins);
        $DB->set_field_select('user_info_data','data','',"fieldid IN(30,31,32)",array('userid'=>$user->userid));

    }



/*** add department manager tag to regional manager if not already */
mtrace("\nAdding department manager role for regional managers\n");
$users = $DB->get_records_sql("SELECT cu.id, cu.departmentid, cu.userid
FROM {company_users} cu
JOIN {user_info_data} ur ON ur.userid = cu.userid
JOIN {user_info_data} ud ON ud.userid = cu.userid
WHERE ur.fieldid = 34
AND ud.fieldid = 15
AND ur.data = 1
AND ud.data <> 1
");

foreach ($users as $user){
    mtrace("adding department manager check to  " . $user->userid . " based on regional manager status");
    $DB->set_field('user_info_data','data',1,array('userid'=>$user->userid, 'fieldid'=> 15));
}




/**assign department managers */

mtrace("\nAssigning Department Managers\n");
$users = $DB->get_records_sql("SELECT cu.id, cu.departmentid, cu.userid,  uc.data as DepartmentManager
FROM {company_users} cu
JOIN {user_info_data} uc ON uc.userid = cu.userid
WHERE uc.fieldid = 15
AND uc.data = 1
AND cu.managertype <> 2  

");
//                                 

foreach ($users as $user){
    //all of these users need to be made the 'department manager' role, 2
    mtrace("setting user id " . $user->userid . " to department manager");
    $DB->set_field('company_users','managertype',2,array('userid'=>$user->userid));
    role_assign(11,$user->userid,1);
    //company::upsert_company_user($user->id, 1, $user->departmentid, 2, $educator=false, $ws=false);
}



//remove department managers people w/ 0 in field
mtrace("\nUnassigning Department Managers\n");
$users = $DB->get_records_sql("SELECT cu.id, cu.userid,  uc.data as DepartmentManager
FROM {company_users} cu
JOIN {user_info_data} uc ON uc.userid = cu.userid
WHERE uc.fieldid = 15
AND (uc.data = 0 OR uc.data = '')
AND cu.managertype <> 0
");

foreach ($users as $user){
//all of these users need to be made the 'student' role, 0
mtrace("setting user id " . $user->userid . " to student");
$DB->set_field('company_users','managertype',0,array('userid'=>$user->userid));
//clear the checkbox
$DB->set_field('user_info_data','data','',array('userid'=>$user->userid, 'fieldid'=>15));
role_unassign(11,$user->userid,1);
}

//end remove managers



//assign departments
mtrace("\nAssigning department based on custom profile fields\n");


       //find all users with a cost center or primary airline field value that doesn't match their current department
        $users = $DB->get_records_sql("SELECT cu.userid, cu.departmentid, cu.companyid, d.name as departmentname, uc.data as costCenter, up.data as primaryAirline, ua.data as activeStatus
            FROM {company_users} cu
            JOIN {company} c ON cu.companyid = c.id
            JOIN {department} d ON cu.departmentid = d.id
            JOIN {user_info_data} uc ON uc.userid = cu.userid
            JOIN {user_info_data} up ON up.userid = cu.userid
            JOIN {user_info_data} ua ON ua.userid = cu.userid
            WHERE uc.fieldid = 4
            AND up.fieldid = 5
            AND ua.fieldid = 12
            AND cu.companyid = 1
            AND cu.managertype = 0
            AND ua.data <> 1
            AND (LOCATE(uc.data, d.name) + LOCATE(up.data, d.name)) = 0
        ");


        //pull department names for convenience
        $departments = $DB->get_records('department', array(), '', 'id,name');
        
        foreach ($users as $user) {
            //load the values for each custom field
            $costCenter = $DB->get_field('user_info_data', 'data', array('userid' => $user->userid, 'fieldid' => 4));
            $primaryAirline = $DB->get_field('user_info_data', 'data', array('userid' => $user->userid, 'fieldid' => 5));
            //find the first department for user's company that that contains either of the above strings
            $newDepartment = $DB->get_field_sql("SELECT d.id
                                                FROM {department} d
                                                WHERE (LOCATE(:costcenter, d.name) + LOCATE(:primaryairline, d.name)) > 0
                                                AND company = :company 
                                                ORDER BY d.id DESC
                                                LIMIT 1",
                                                array('costcenter' => $costCenter, 'primaryairline' => $primaryAirline, 'company' => $user->companyid)
            );
            if ($newDepartment != 0) {
            mtrace("setting user id " . $user->userid . " department to " .  $departments[$newDepartment]->name);
            //update department field in company_users to the new one
            $DB->set_field('company_users','departmentid',$newDepartment,array('userid'=>$user->userid));
            }
        }

//Assign mangers to parent departments based on custom profile fields
       mtrace("\nAssigning manager department based on custom profile fields\n");

       //find all users with a cost center or primary airline field value that doesn't match their current department
        $users = $DB->get_records_sql("SELECT cu.userid, cu.departmentid, cu.companyid, d.name as departmentname, uc.data as costCenter, up.data as primaryAirline, ua.data as activeStatus, ur.data as regionalManager
            FROM {company_users} cu
            JOIN {company} c ON cu.companyid = c.id
            JOIN {department} d ON cu.departmentid = d.id
            JOIN {user_info_data} uc ON uc.userid = cu.userid
            JOIN {user_info_data} up ON up.userid = cu.userid
            JOIN {user_info_data} ua ON ua.userid = cu.userid
            JOIN {user_info_data} ur ON ur.userid = cu.userid
            WHERE uc.fieldid = 4
            AND up.fieldid = 5
            AND ua.fieldid = 12
            AND cu.companyid = 1
            AND cu.managertype = 2
            AND ua.data <> 1
            AND ur.fieldid = 34
        ");


        //pull department names for convenience
        $departments = $DB->get_records('department', array(), '', 'id,name');
        
        foreach ($users as $user) {
            //load the values for each custom field
            $costCenter = $DB->get_field('user_info_data', 'data', array('userid' => $user->userid, 'fieldid' => 4));
            $primaryAirline = $DB->get_field('user_info_data', 'data', array('userid' => $user->userid, 'fieldid' => 5));
            //find the first department for user's company that that contains either of the above strings
            $newDepartment = $DB->get_field_sql("SELECT d.id
                                                FROM {department} d
                                                WHERE (LOCATE(:costcenter, d.name) + LOCATE(:primaryairline, d.name)) > 0
                                                AND company = :company
                                                ORDER BY d.id DESC
                                                LIMIT 1",
                                                array('costcenter' => $costCenter, 'primaryairline' => $primaryAirline, 'company' => $user->companyid)
            );

            $parent = $DB->get_field_sql("SELECT d.parent 
                                        FROM {department} d
                                        WHERE d.id = :new",
                                        array('new' => $newDepartment)
                                        );
            if ($parent != 0 || $parent != 1) {
                if ($user->regionalmanager == 1) {
                    $nextParent = $parent;
                    while ($nextParent != 1) {
                        $parent = $nextParent;
                        $nextParent = $DB->get_field_sql("SELECT d.parent 
                                            FROM {department} d
                                            WHERE d.id = :new",
                                            array('new' => $parent)
                                            );
                        if ($nextParent == 1 && $parent != $user->departmentid) {
                            mtrace("setting regional manager user id " . $user->userid . " department to " .  $departments[$parent]->name);
                            //update department field in company_users to the new one
                            $DB->set_field('company_users','departmentid',$parent,array('userid'=>$user->userid));
                        }
                    }
                }
                else if ($parent != $user->departmentid) {                    
                    mtrace("setting department manager user id " . $user->userid . " department to " .  $departments[$parent]->name);
                    //update department field in company_users to the new one
                    $DB->set_field('company_users','departmentid',$parent,array('userid'=>$user->userid));
                }
            
            }
        }
        

/**Making Users Inactive ***/
        mtrace("\nMoving inactive users to 'Inactive' Department\n");
        $users = $DB->get_records_sql("SELECT cu.id, cu.userid, cu.departmentid, cu.companyid,  uc.data as ActiveStatus
        FROM {company_users} cu
        JOIN {company} c ON cu.companyid = c.id
        JOIN {user_info_data} uc ON uc.userid = cu.userid
        WHERE uc.fieldid = 12
        AND uc.data = 1
        AND cu.companyid = 1
        AND cu.departmentid <> 2018 
        
    ");
        foreach ($users as $user){
            //all of these users need to be put in the inactive dpt, 2018
            mtrace("setting user id " . $user->userid . "department to Inactive");
            $DB->set_field('company_users','departmentid',2018,array('userid'=>$user->userid));
            $DB->set_field('user','suspended',1,array('id'=>$user->userid));

        }




/**update learning paths***/
        mtrace("\nAssigning learning paths\n");

        //pull out users with anything in learning path profile field and no corresponding learning path table entry
        $users = $DB->get_records_sql("SELECT uc.userid, uc.data AS learningpath
        FROM {user_info_data} uc
        WHERE uc.fieldid = 14 
        AND uc.data <> ''
    ");

        //pull learning path names for convenience
        $learningpaths = $DB->get_records('iomad_learningpath', array(), '', 'id,name');
        
        foreach ($users as $user) {
            //load value for learning path custom field
            $learningpath = $user->learningpath;
            $option = $DB->get_field('user_info_data', 'data', array('userid' => $user->userid, 'fieldid' => 33));

            //add the learning path
            if ($option != 'delete') {
                $ins = (object)array('pathid' => $learningpath, 'userid' => $user->userid);

                mtrace("enrolling user id " . $user->userid . " in learning path " .  $learningpaths[$learningpath]->name);

                //insert learning path user record
                $DB->delete_records('iomad_learningpathuser', array('userid'=>$user->userid, 'pathid'=>$learningpath));
                $DB->insert_record('iomad_learningpathuser', $ins);

                //auto-enroll in courses if selected
                if ($option == 'course-enroll'){
                    mtrace("enrolling user id " . $user->userid . " in courses for learning path " .  $learningpaths[$learningpath]->name);

                    
                    //pull user record directly
                    $user_instance = $DB->get_record('user', array('id' => $user->userid, 'deleted' => 0), '*', MUST_EXIST);

                    //pull related courses
                    $courses = $DB->get_records_sql("SELECT lc.id, lc.course as courseid, c.shortname as coursename
                    FROM {iomad_learningpathcourse} lc
                    JOIN {course} c on c.id = lc.course
                    WHERE lc.path = :pathid
                    ", array('pathid' => $learningpath)
                    );

                    foreach ($courses as $course_temp){

                        $course_records = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                                        WHERE userid = :userid
                                                        AND courseid = :courseid",
                                                        array('userid' => $user->userid, 'courseid' => $course_temp->courseid )
                                                        );

                        if (count($course_records) > 0) {
                            mtrace("skipping course enrolment for user " . $user->userid . ", course " .  $course_temp->courseid);
                        }

                        else {
                            //get actual course record
                            $course = $DB->get_record('course', array('id' => $course_temp->courseid), '*', MUST_EXIST);

                            $context = context_course::instance($course->id);
                            if (!is_enrolled($context, $user_instance)) {
                                $enrol = enrol_get_plugin('manual');
                                if ($enrol === null) {
                                    return false;
                                }
                                $instances = enrol_get_instances($course->id, true);
                                $manualinstance = null;
                                foreach ($instances as $instance) {
                                    if ($instance->enrol == 'manual') {
                                        $manualinstance = $instance;
                                        break;
                                    }
                                }
                                //mtrace(var_dump($manualinstance));
                                
                                $enrol->enrol_user($manualinstance, $user->userid, 5);

                            }
                        }
                    }
                    
                }   
            }


            //remove the learning path
            else {
               mtrace("removing user id " . $user->userid . " from learning path: " .  $learningpaths[$learningpath]->name);
                
               //wipe the learning path enrollment record
                $DB->delete_records('iomad_learningpathuser', array('userid'=>$user->userid, 'pathid'=>$learningpath));

                //wipe the completion record if user hasn't completed any courses in it yet
                $DB->delete_records('local_iomad_track_learningpath', array('userid'=>$user->userid, 'pathid'=>$learningpath, 'courses_completed'=>0));


                mtrace("unenrolling user id " . $user->userid . " from courses for learning path " .  $learningpaths[$learningpath]->name);

                //unenrol from courses
                
                //pull user record directly
                $user_instance = $DB->get_record('user', array('id' => $user->userid, 'deleted' => 0), '*', MUST_EXIST);
                
                //pull related courses
                $courses = $DB->get_records_sql("SELECT lc.id, lc.course as courseid, c.shortname as coursename
                FROM {iomad_learningpathcourse} lc
                JOIN {course} c on c.id = lc.course
                WHERE lc.path = :pathid
                ", array('pathid' => $learningpath)
                );

                foreach ($courses as $course){
                    //delete completion record if not done
                    $DB->delete_records('local_iomad_track', array('userid'=>$user->userid, 'courseid'=>$course->courseid, 'timecompleted'=>NULL));

                    $context = context_course::instance($course->courseid);
                    if (is_enrolled($context, $user_instance)) {
                        $enrol = enrol_get_plugin('manual');

                        $instances = enrol_get_instances($course->courseid, true);
                        $manualinstance = null;
                        foreach ($instances as $instance) {
                            if ($instance->enrol == 'manual') {
                                $manualinstance = $instance;
                                break;
                            }
                        }
                        //mtrace(var_dump($manualinstance));

                        $enrol->unenrol_user($manualinstance, $user->userid);
                    }

                }
                
            }

            //clear the fields
            $DB->set_field('user_info_data','data','',array('userid'=>$user->userid, 'fieldid'=> 14));
            $DB->set_field('user_info_data','data','',array('userid'=>$user->userid, 'fieldid'=> 33));
            
        }



        // Copy employee # to address  field
        mtrace("\n Copying employee number to address field \n");
        // Get the users where it's wrong.
        $users = $DB->get_records_sql("SELECT u.*, uc.data
                                        FROM {user} u
                                        JOIN {user_info_data} uc ON uc.userid = u.id
                                        WHERE u.address != uc.data
                                        AND uc.fieldid = 3
                                        ");
        foreach ($users as $user) {
        profile_load_data($user);
        mtrace("logging user id " . $user->id . " employee # as " . $user->profile_field_HallmarkSettingsemployeenum);
            $user->address = $user->profile_field_HallmarkSettingsemployeenum;
            $DB->update_record('user', $user);
        }
        $users = array();
    



    // Are we copying Company to institution?
    if (!empty($CFG->iomad_sync_institution)) {
        mtrace("\nCopying company shortnames to user institution fields\n");
        // Get the users where it's wrong.
        $users = $DB->get_records_sql("SELECT u.*, c.id as companyid
                                        FROM {user} u
                                        JOIN {company_users} cu ON cu.userid = u.id
                                        JOIN {company} c ON cu.companyid = c.id
                                        WHERE u.institution != c.shortname
                                        AND c.parentid = 0");
        // Get all of the companies.
        $companies = $DB->get_records('company', array(), '', 'id,shortname');
        foreach ($users as $user) {
mtrace("setting user id " . $user->id . " institution to " .  $companies[$user->companyid]->shortname);
            $user->institution = $companies[$user->companyid]->shortname;
            $DB->update_record('user', $user);
        }
        $companies = array();
        $users = array();
    }


    // Are we copying department to department?
    if (!empty($CFG->iomad_sync_department)) {
        mtrace("Copying company department name to user department fields\n");
        // Get the users where it's wrong.
        $users = $DB->get_records_sql("SELECT u.*, d.id as departmentid
                                        FROM {user} u
                                        JOIN {company_users} cu ON cu.userid = u.id
                                        JOIN {company} c ON cu.companyid = c.id
                                        JOIN {department} d ON cu.departmentid = d.id
                                        WHERE u.department != d.name
                                        AND c.parentid = 0");
        // Get all of the companies.
        $departments = $DB->get_records('department', array(), '', 'id,name');
        foreach ($users as $user) {
        mtrace("setting user id " . $user->id . " department to " .  $departments[$user->departmentid]->name);
            $user->department = $departments[$user->departmentid]->name;
            $DB->update_record('user', $user);
        }
        $companies = array();
        $users = array();
    }

    // Suspend any companies which need it.
    if ($suspendcompanies = $DB->get_records_sql("SELECT * FROM {company}
                                                    WHERE suspended = 0
                                                    AND validto IS NOT NULL
                                                    AND validto + suspendafter < :runtime",
                                                    array('runtime' => $runtime))) {
        foreach ($suspendcompanies as $suspendcompany) {
            $target = new \company($suspendcompany->id);
            $target->suspend(true);
        }
    }

    // Terminate any companies which need it.
    if ($terminatecompanies = $DB->get_records_sql("SELECT * FROM {company}
                                                    WHERE companyterminated = 0
                                                    AND validto IS NOT NULL
                                                    AND validto < :runtime",
                                                    array('runtime' => $runtime))) {
        foreach ($suspendcompanies as $suspendcompany) {
            $target = new \company($suspendcompany->id);
            $target->terminate();
        }
    }

           


    }
}

