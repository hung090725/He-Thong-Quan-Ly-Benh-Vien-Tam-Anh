<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;

class CalendarSyncService
{
    /**
     * Generate Google Calendar Web URL for 1-click addition.
     */
    public function generateGoogleCalendarUrl(Appointment $appointment): string
    {
        return $this->getGoogleCalendarUrl($appointment);
    }

    /**
     * Generate Google Calendar Web URL for 1-click addition.
     */
    public function getGoogleCalendarUrl(Appointment $appointment): string
    {
        $appointment->loadMissing(['doctor.user', 'specialty']);

        $specialtyName = $appointment->specialty?->name ?? 'Đa khoa';
        $doctorName = $appointment->doctor?->full_name_with_title ?? 'Bác sĩ phụ trách';
        $roomNumber = $appointment->doctor?->room_display ?? ($appointment->doctor?->room_number ? (str_starts_with($appointment->doctor->room_number, 'Phòng') ? $appointment->doctor->room_number : 'Phòng ' . $appointment->doctor->room_number) : 'Phòng khám Tâm Anh');

        $title = rawurlencode("Lịch Khám Y Tế: {$specialtyName} - BS. {$doctorName}");
        $details = rawurlencode(
            "Mã lịch hẹn: {$appointment->appointment_code}\n" .
            "Bác sĩ phụ trách: {$doctorName}\n" .
            "Chuyên khoa: {$specialtyName}\n" .
            "Ca khám: {$appointment->shift_name}\n" .
            "Phòng khám: {$roomNumber}\n" .
            "Lưu ý: Quý khách vui lòng mang theo CMND/CCCD hoặc BHYT (nếu có) và có mặt trước giờ khám 15 phút."
        );
        $location = rawurlencode("Bệnh viện Đa khoa Tâm Anh, {$roomNumber}");

        // Format dates safely
        $date = $appointment->appointment_date instanceof \DateTimeInterface
            ? Carbon::parse($appointment->appointment_date)
            : Carbon::today();
        $dateStr = $date->format('Ymd');

        if ($appointment->shift === Appointment::SHIFT_MORNING) {
            $start = "{$dateStr}T073000";
            $end = "{$dateStr}T113000";
        } else {
            $start = "{$dateStr}T133000";
            $end = "{$dateStr}T170000";
        }

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$start}/{$end}&details={$details}&location={$location}";
    }

    /**
     * Alias for getGoogleCalendarUrl to maintain backward compatibility.
     */
    public function generateGoogleCalendarUrl(Appointment $appointment): string
    {
        return $this->getGoogleCalendarUrl($appointment);
    }

    /**
     * Generate iCalendar RFC 5545 (.ics) file contents.
     */
    public function generateIcsContent(Appointment $appointment): string
    {
        $appointment->loadMissing(['doctor.user', 'specialty', 'patientProfile']);

        $specialtyName = $appointment->specialty?->name ?? 'Đa khoa';
        $doctorName = $appointment->doctor?->full_name_with_title ?? 'Bác sĩ phụ trách';
        $roomNumber = $appointment->doctor?->room_display ?? ($appointment->doctor?->room_number ? (str_starts_with($appointment->doctor->room_number, 'Phòng') ? $appointment->doctor->room_number : 'Phòng ' . $appointment->doctor->room_number) : 'Phòng khám Tâm Anh');

        $date = $appointment->appointment_date instanceof \DateTimeInterface
            ? Carbon::parse($appointment->appointment_date)
            : Carbon::today();
        $dateStr = $date->format('Ymd');

        if ($appointment->shift === Appointment::SHIFT_MORNING) {
            $start = "{$dateStr}T073000";
            $end = "{$dateStr}T113000";
        } else {
            $start = "{$dateStr}T133000";
            $end = "{$dateStr}T170000";
        }

        $summary = "Lịch Khám Bệnh Viện Tâm Anh - " . $appointment->appointment_code;
        $description = "Lịch khám chuyên khoa " . $specialtyName . " với " . $doctorName . ". " . $roomNumber;
        $location = "Bệnh viện Đa khoa Tâm Anh - " . $roomNumber;
        $now = gmdate('Ymd\THis\Z');
        $uid = "APPOINTMENT-" . $appointment->appointment_code . "@tamanh.vn";

        return "BEGIN:VCALENDAR\r\n" .
            "VERSION:2.0\r\n" .
            "PRODID:-//Tam Anh Hospital//Hospital Management System//VI\r\n" .
            "CALSCALE:GREGORIAN\r\n" .
            "METHOD:PUBLISH\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:{$uid}\r\n" .
            "DTSTAMP:{$now}\r\n" .
            "DTSTART:{$start}\r\n" .
            "DTEND:{$end}\r\n" .
            "SUMMARY:{$summary}\r\n" .
            "DESCRIPTION:{$description}\r\n" .
            "LOCATION:{$location}\r\n" .
            "STATUS:CONFIRMED\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n";
    }
}
