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
 * Utility class for learning path block
 *
 * @package    block_iomad_learningpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_learningpath;

defined('MOODLE_INTERNAL') || die();

class path {

    protected $companyid;

    protected $context;

    public function __construct($companyid, $context) {
        $this->companyid = $companyid;
        $this->context = $context;
    }

    /**
     * Get list of courses in path
     * @param int $pathid
     * @return [array, int/null]
     */
    public function get_courselist($pathid, $groupid, $sequenced = false) {
        global $DB;
        global $USER;

        // Calculate overall progress for group
        $cumulativeprogress = 0;
        $completioncoursecount = 0;

        $sql = 'SELECT c.id courseid, c.shortname shortname, c.fullname fullname, c.summary summary, lpc.*
            FROM {iomad_learningpathcourse} lpc JOIN {course} c ON lpc.course = c.id
            WHERE lpc.path = :pathid
            AND lpc.groupid = :groupid
            ORDER BY lpc.sequence';
        $courses = $DB->get_records_sql($sql, ['pathid' => $pathid, 'groupid' => $groupid]);

        // Handle sequencing if required.
        $first = true;

        // Spot of processing
        foreach ($courses as $course) {
            $progress = null;
            $courserecord = $DB->get_record_sql("SELECT * 
                                FROM {local_iomad_track} 
                                WHERE courseid = :courseid
                                AND userid = :userid
                                ORDER BY timecompleted desc
                                LIMIT 1",
                                array('courseid' => $course->courseid, 'userid' => $USER->id)
            ); 

            if (is_object($courserecord)){                                 
                if (($courserecord->timecompleted > 0)){
                   $progress = 100;
                }
            }
            
            if ($progress == null) {
                $fullcourse = $DB->get_record('course', ['id' => $course->courseid], '*', MUST_EXIST);
                $progress = \core_completion\progress::get_course_progress_percentage($fullcourse);
            }


            $course->link = new \moodle_url('/course/view.php', ['id' => $course->courseid]);
            $course->imageurl = $this->get_course_image_url($course->courseid);
            $course->hasprogress = $progress !== null;
            $course->progresspercent = $course->hasprogress ? $progress : 0;

            // Deal with sequencing if we have to.
            if ($first || !$sequenced) {
                $course->available = true;
            }
            if ($sequenced && !$first) {
                if (!empty($previouscourse->hasprogress) && $previouscourse->progresspercent == 100) {
                    $course->available = true;
                } else {
                    $course->available = false;
                    $course->prerequisite = $previouscourse->fullname;
                }
            }

            // Count progress for any courses that actually have some.
            // Ones that don't will be ignored.
            if ($course->hasprogress) {
                $cumulativeprogress += $course->progresspercent;
                $completioncoursecount++;
            }

            // Stash the previous course in case we need it.
            if ($sequenced) {
                $previouscourse = clone($course);
                $first = false;
            }
        }

        // Calculate overall progress for group
        if ($completioncoursecount) {
            $groupprogress = round($cumulativeprogress / $completioncoursecount);
        } else {
            $groupprogress = null;
        }

        return [$courses, $groupprogress];
    }

    /**
     * Get groups for path adding courselist
     * @param int $pathid
     * @return array
     */
    protected function get_groups($pathid) {
        global $DB;

        // Calculate overall progress for path
        $cumulativeprogress = 0;
        $completiongroupcount = 0;

        $groups = $DB->get_records('iomad_learningpathgroup', ['learningpath' => $pathid]);
        foreach ($groups as $group) {
            list($courses, $progress) = $this->get_courselist($pathid, $group->id, $group->sequence);
            $group->progress = $progress !== null ? $progress : 0;
            $group->courses = array_values($courses);
            if ($progress !== null) {
                $cumulativeprogress += $progress;
                $completiongroupcount++;
            }
        }

        // Calcultate overall progress for path
        if ($completiongroupcount) {
            $pathprogress = round($cumulativeprogress / $completiongroupcount);
        } else {
            $pathprogress = null;
        }

        return [$groups, $pathprogress];
    }


    /**
     * Get available learning paths for user
     * and details of courses attached to them
     * @param int $userid
     * @return array
     */
    public function get_user_paths($userid) {
        global $DB;
	global $USER;

//        $sql = 'SELECT lp.* FROM {iomad_learningpath} lp
//            JOIN {iomad_learningpathuser} lpu ON lpu.pathid = lp.id
//            WHERE lp.company = 1
//            AND lpu.userid = :userid
//            AND lp.active = 1
//            ORDER BY lp.name ASC';
//        $paths = $DB->get_records_sql($sql, ['userid' => $userid, 'companyid' => $this->companyid]);

/** Removed above because specification of company was not working and seemed unnecessary anyway **/

	$sql = 'SELECT lp.* FROM {iomad_learningpath} lp
            JOIN {iomad_learningpathuser} lpu ON lpu.pathid = lp.id
            WHERE lpu.userid = :userid
            AND lp.active = 1
            ORDER BY lp.name ASC';
        $paths = $DB->get_records_sql($sql, ['userid' => $userid]);

        // Add url for image and courses array
        foreach ($paths as $path) {
            $path->imageurl = $this->get_path_image_url($path->id);
            list($groups, $pathprogress) = $this->get_groups($path->id);
            $path->groups = array_values($groups);
            $path->progress = $pathprogress !== null ? $pathprogress : 0;
        }

        return $paths;
    }

    /**
     * Get path image url
     * @param int $pathid
     * @return mixed url or false if no image
     */
    public function get_path_image_url($pathid) {
        global $OUTPUT;

        $fs = get_file_storage();
        $pic = $fs->get_file(
            $this->context->id,
            'local_iomad_learningpath',
            'mainpicture',
            $pathid,
            '/',
            'picture.png'
        );

        if (!$pic) {
            return $OUTPUT->image_url('learningpath', 'local_iomad_learningpath');
        }

        return \moodle_url::make_pluginfile_url($pic->get_contextid(), $pic->get_component(), $pic->get_filearea(),
                    $pic->get_itemid(), $pic->get_filepath(), $pic->get_filename());
    }

    /**
     * Get course image url
     * @param int $courseid
     * @return mixed url or false if no image
     */
    public function get_course_image_url($courseid) {
        global $OUTPUT;

        $fs = get_file_storage();

        $context = \context_course::instance($courseid);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                return \moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    null, $file->get_filepath(), $file->get_filename());
            }
        }

        // No image defined, so...
        return $OUTPUT->image_url('courseimage', 'block_iomad_learningpath')->out();
    }

}
