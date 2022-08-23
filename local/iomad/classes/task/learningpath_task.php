<?php

namespace local_iomad\task;
use context_course;



class learningpath_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return 'learning path tracking';
    }

    /**
     * Run learningpath_task cron.
     */
    public function execute() {
        global $DB;

        $runtime = time();


        mtrace("Updating learning path completions");


        //pull list of learning paths
        $learningpaths = $DB->get_records_sql("SELECT id as pathid, company as companyid, name as pathname, active, flushed
                                            FROM {iomad_learningpath}
                                            ");
 
        //pull connected courses and users for each
        foreach ($learningpaths as $learningpath) {

            //handle the flush field
            $flush = false;
            if ($learningpath->active == 0 && $learningpath->flushed != 1) { //check for learning paths that aren't marked as flushed already
                mtrace('learning path ' . $learningpath->pathid . ' needs to be flushed');
                $flush = true;
            }

            //clear the flush column if path is active again
            if ($learningpath->active == 1 && $learningpath->flushed == 1) {
                mtrace('removing flushed tag from active path '. $learningpath->pathid);
                $DB->set_field('iomad_learningpath','flushed',0,array('id'=>$learningpath->pathid));
            }


            $courses = $DB->get_records_sql("SELECT lc.id, lc.course as courseid, c.shortname as coursename
                                            FROM {iomad_learningpathcourse} lc
                                            JOIN {course} c on c.id = lc.course
                                            WHERE lc.path = :pathid
                                            ", array('pathid' => $learningpath->pathid)
                                            );
 
            $users = $DB->get_records_sql("SELECT pu.id, pu.userid, u.firstname, u.lastname, cu.companyid
                                        FROM {iomad_learningpathuser} pu 
                                        JOIN {user} u on u.id = pu.userid
                                        JOIN {company_users} cu on cu.userid = pu.userid
                                        WHERE pu.pathid = :pathid
                                        ", array('pathid' => $learningpath->pathid)
                                        );
 
                //check for each user
                foreach ($users as $user){
                    $complete = false;
                    $exists = false;
                    $pathrecord = $DB->get_record('local_iomad_track_learningpath', 
                                            array('userid' => $user->userid,           
                                                  'pathid' => $learningpath->pathid));
                    
                    //does an entry exist, and is it already complete
                    if(is_object($pathrecord)) {
                        $exists = true;
                        if($pathrecord->timecompleted > 0){
                            $complete = true;
                        }
 
                    } 
 
                    //do we potentially need to update the record
                    if ($complete == false) {
                            $total = count($courses);
                            $progress = 0;
                            $unfinished = '';
 
                        //check course by course
                        foreach ($courses as $course) {
                             $courserecord = $DB->get_record_sql("SELECT * 
                                                                  FROM {local_iomad_track} 
                                                                  WHERE courseid = :courseid
                                                                  AND userid = :userid
                                                                  ORDER BY timecompleted desc
                                                                  LIMIT 1",
                                                                  array('courseid' => $course->courseid, 'userid' => $user->userid)
                                                                  ); 

                            //  $courserecord = $DB->get_record('local_iomad_track',
                            //                              array('courseid' => $course->courseid,
                            //                                    'userid' => $user->userid),
                            //                                    'id','courseid, timecompleted');

                             if (is_object($courserecord)){                                 
                                 if (($courserecord->timecompleted > 0)){
                                    $progress +=1;
                                 }

                                 else {
                                    $unfinished .= $course->coursename . ', ';
                                 }
                            }

                            else {
                                $unfinished .= $course->coursename . ', ';
                            }          

                        }
 

                    
 
 
                        //record exists, may or may not need to update
                        if ($exists == true) {
                            if ($flush == false || $progress > 0){ //only update the record if the user has progress or the course isn't being flushed
    
                                //check if we actually need to update
                                if (!($pathrecord->courses_completed == $progress && $pathrecord->courses_total == $total)){
    
                                    //yes, path is complete
                                    if ($total == $progress) {
                                        $pathrecord->courses_completed = $progress;
                                        $pathrecord->courses_total = $total;
                                        $pathrecord->courses_remaining = '';
                                        $pathrecord->timecompleted = $runtime;
                                    }
    
                                    //yes, path not complete
                                    else {
                                        $pathrecord->courses_completed = $progress;
                                        $pathrecord->courses_total = $total;
                                        $pathrecord->courses_remaining = $unfinished;
                                    }
    
                                    mtrace("updating learning path record for user" . $user->userid . ", path " . $learningpath->pathid);
                                    $DB->update_record('local_iomad_track_learningpath', $pathrecord);
    
                                }
                            }

                            
 
                        }
 
                        //record doesn't exist, need to insert
                        else {
                            if ($flush == false || $progress > 0) { //don't add a record if course is set to be flushed and the user has no progress
                                $newrecord = (object)(array());
    
                                //insert completed record
                                if ($total == $progress){
                                    $newrecord->userid = $user->userid;
                                    $newrecord->pathid = $learningpath->pathid;
                                    $newrecord->pathname = $learningpath->pathname;
                                    $newrecord->courses_completed = $progress;
                                    $newrecord->courses_total = $total;
                                    $newrecord->courses_remaining = '';
                                    $newrecord->timecompleted = $runtime;
                                    $newrecord->companyid = $user->companyid;
                                }
    
                                //insert unfinished record;
                                else {
                                    $newrecord->userid = $user->userid;
                                    $newrecord->pathid = $learningpath->pathid;
                                    $newrecord->pathname = $learningpath->pathname;
                                    $newrecord->courses_completed = $progress;
                                    $newrecord->courses_total = $total;
                                    $newrecord->courses_remaining = $unfinished;
                                    $newrecord->companyid = $user->companyid;
                                    $newrecord->timecompleted = NULL;
    
                                }
                                
                                mtrace("inserting learning path record for user" . $user->userid . ", path " . $learningpath->pathid);
                                $DB->insert_record('local_iomad_track_learningpath', $newrecord);
                            }
                        }

                        //unenroll from corresponding courses & learning path if not started and set to flush 
                        if ($flush == true && !($progress > 0)) {
                            mtrace("removing user id " . $user->userid . " from learning path: " .  $learningpath->pathid);
                
                            //wipe the learning path enrollment record
                             $DB->delete_records('iomad_learningpathuser', array('userid'=>$user->userid, 'pathid'=>$learningpath->pathid));
             
                             //wipe the completion record
                             $DB->delete_records('local_iomad_track_learningpath', array('userid'=>$user->userid, 'pathid'=>$learningpath->pathid));

                             mtrace("unenrolling user id " . $user->userid . " from courses for learning path " .  $learningpath->pathid);

                             $user_instance = $DB->get_record('user', array('id' => $user->userid, 'deleted' => 0), '*', MUST_EXIST);

                             //$courses already pulled above

                             foreach ($courses as $course){
                                //delete completion record if not completed
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
            
                                    $enrol->unenrol_user($manualinstance, $user->userid);
                                }
                            }
                        }
 
                    }
                }

            if ($flush == true) {
                //update the flush field so this doesn't fire next time
                mtrace('learning path '. $learningpath->pathid . ' has been flushed');
                $DB->set_field('iomad_learningpath','flushed',1,array('id'=>$learningpath->pathid));
            }
        }

    }
}