<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * A4_embedded iomadcertificate type
 *
 * @package    mod
 * @subpackage iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

$pdf = new PDF($iomadcertificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($iomadcertificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
global $DB;

// Landscape
if ($iomadcertificate->orientation == 'L') {
    $x = 0;
    $y = 55;
    $sealx = 122;
    $sealy = 28;
    $sigx = 210;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 93;
    $wmarky = 65;
    $wmarkw = 100;
    $wmarkh = 100;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 203;
    $alignment = 'L';
} else { // Portrait
    $x = 10;
    $y = 76;
    $sealx = 90;
    $sealy = 50;
    $sigx = 130;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 50;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 249;
    $alignment = 'C';
}

$certificateseal = "";
$certificatesignature = "";
$certificateborder = "";
$certificatewatermark = "";
$showgrade = true;
$uselogo = true;
$usesignature = true;
$useborder = true;
$usewaterkark = true;

// Get the site defaults
$sitecontext = context_system::instance();
$fs = get_file_storage();
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_logo', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificateseal = $file->get_filename();
        $seal_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_logo_');
    }
}
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_signature', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificatesignature = $file->get_filename();
        $signature_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_signature_');
    }
}
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_border', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificateborder = $file->get_filename();
        $border_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_border_');
    }
}
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_watermark', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificatewatermark = $file->get_filename();
        $watermark_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_watermark_');
    }
}

$companyid = 0;
if ($companyid = iomad::is_company_user($certuser)) {
    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificateseal', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificateseal)) {
                @unlink($seal_filename);
            }
            $file = reset($files);
            unset($files);
            $certificateseal = $file->get_filename();
            $seal_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_logo_');
        }
    }

    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificatesignature', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificatesignature)) {
                @unlink($signature_filename);
            }
            $file = reset($files);
            unset($files);
            $certificatesignature = $file->get_filename();
            $signature_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_signature_');
        }
    }

    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificateborder', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificateborder)) {
                @unlink($border_filename);
            }
            $file = reset($files);
            unset($files);
            $certificateborder = $file->get_filename();
            $border_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_border_');
        }
    }

    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificatewatermark', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificatewatermark)) {
                @unlink($watermark_filename);
            }
            $file = reset($files);
            unset($files);
            $certificatewatermark = $file->get_filename();
            $watermark_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_watermark_');
        }
    }
}

// Get the company certificat design info, if appropriate.
if (!empty($companyid)) {
    if ($companycertificateinfo = $DB->get_record('companycertificate', array('companyid' => $companyid))) {
        $uselogo = $companycertificateinfo->uselogo;
        $usesignature = $companycertificateinfo->usesignature;
        $useborder = $companycertificateinfo->useborder;
        $usewatermark = $companycertificateinfo->usewatermark;
        $showgrade = $companycertificateinfo->showgrade;
    }
}
// Add images and lines
if ($useborder) {
    if (empty($certificateborder)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
    } else {
        $pdf->Image($border_filename, $brdrx, $brdry, $brdrw, $brdrh);
        @unlink($border_filename);
    }
}
iomadcertificate_draw_frame($pdf, $iomadcertificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(.9);
if ($usewatermark) {
    if (empty($certificatewatermark)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_WATERMARK, 'C', $wmarky, $wmarkw, $wmarkh);
    } else {
        $pdf->Image($watermark_filename, $wmarkx, $wmarky, $wmarkw, $wmarkh);
        @unlink($watermark_filename);
    }
}
$pdf->SetAlpha(1);
if ($uselogo) {
    if (empty($certificateseal)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_SEAL, 'C', $sealy, '', '');
    } else {
        $pdf->Image($seal_filename, $sealx, $sealy, 'C', '');
        @unlink($seal_filename);
    }
}
if ($usesignature) {
    if (empty($certificatesignature)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_SIGNATURE, 'C', $sigy, '', '');
    } else {
        $pdf->Image($signature_filename, $sigx, $sigy, '', '');
        @unlink($signature_filename);
    }
}

$gradeinfo = explode(':', iomadcertificate_get_grade($iomadcertificate, $course, $certuser->id));
if (!empty($gradeinfo[1])) {
    $gradestr = $gradeinfo[1];
} else {
    $gradestr = "0";
}

if ($showgrade) {
    $dategradestring = get_string('companyscore', 'iomadcertificate', $gradestr) . ' ' .
    get_string('companydate', 'iomadcertificate', iomadcertificate_get_date($iomadcertificate, $certrecord, $course, $certuser->id));
} else {
    $dategradestring = get_string('companydatecap', 'iomadcertificate', iomadcertificate_get_date($iomadcertificate, $certrecord, $course, $certuser->id));
}

// Add text
$pdf->SetTextColor(1, 57, 118);
$text = [];
iomadcertificate_print_text($pdf, $x, $y + 20, 'C', 'helvetica', '', 25, get_string('companycertify', 'iomadcertificate'));
iomadcertificate_print_text($pdf, $x, $y + 39, 'C', 'helvetica', 'I', 10, get_string('certify', 'iomadcertificate'));
if ($iomadcertificate->customtext) {
    $text=explode("\n", $iomadcertificate->customtext);
    iomadcertificate_print_text($pdf, $x, $y + 44, 'C', 'helvetica', '', 18, $text[0]);
}
else {
    iomadcertificate_print_text($pdf, $x, $y + 44, 'C', 'helvetica', '', 18, fullname($certuser));
}
iomadcertificate_print_text($pdf, $x, $y + 65, 'C', 'helvetica', 'I', 10, get_string('companydetails', 'iomadcertificate'));
if ($iomadcertificate->customtext) {
iomadcertificate_print_text($pdf, $x, $y + 70, 'C', 'helvetica', '', 20, $text[1]);
iomadcertificate_print_text($pdf, $x + 40, $y + 110, '', 'helvetica', '', 15, $text[2]);
} //used for retroactive date
else {
iomadcertificate_print_text($pdf, $x, $y + 70, 'C', 'helvetica', '', 20, $course->fullname);
iomadcertificate_print_text($pdf, $x + 40, $y + 110, '', 'helvetica', '', 15, $dategradestring);
}
// Custom Code Start
if ($course->id == 2919) {
iomadcertificate_print_text($pdf, 11, $codey, 'C', 'helvetica', '', 8, iomadcertificate_generate_code() . ' --- Certificate is valid from date of issue to the regulatory subject expiration date. --- Hallmark Aviation Services, 5757 W Century Blvd #860, Los Angeles, CA 90045');
} else {
iomadcertificate_print_text($pdf, 11, $codey, 'C', 'helvetica', '', 8, iomadcertificate_get_code($iomadcertificate, $certrecord) . ' --- Certificate is valid from date of issue to the regulatory subject expiration date. --- Hallmark Aviation Services, 5757 W Century Blvd #860, Los Angeles, CA 90045');
}
// Custom Code End
#iomadcertificate_print_text($pdf, 20, $codey, 'R', 'helvetica', '', 10, 'Certificate is valid from date of issue to the regulatory subject expiration date.');
iomadcertificate_print_text($pdf, $x + 62, $y + 117, '', 'helvetica', '', 9, 'Date');
iomadcertificate_print_text($pdf, $sigx - 5, $sigy + 17, '', 'helvetica', '', 9, 'Kenneth Pierce, Director of Training');
iomadcertificate_print_text($pdf, $sigx + 2, $sigy + 22, '', 'helvetica', '', 9, 'Hallmark Aviation Services');


