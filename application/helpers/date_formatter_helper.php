<?php
if (!function_exists("format_exhibition_dates")) {

    function format_exhibition_dates($start,$end) {
        $dates = "";
        $CI =& get_instance();
        if ($start->format("Y") == $end->format("Y")) {
            if ($start->format("n") == $end->format("n")) {
                $dates = '<time itemprop="startDate" datetime="'.$start->format("c").'">'.$start->format("F j").'</time>–<time itemprop="endDate" datetime="'.$end->format("c").'">'.$end->format("j, Y").'</time>';
            } else {
                $dates = '<time itemprop="startDate" datetime="'.$start->format("c").'">'.$start->format("F j").'</time>–<time itemprop="endDate" datetime="'.$end->format("c").'">'.$end->format("F j, Y").'</time>';
            }
        } else {
            $dates = '<time itemprop="startDate" datetime="'.$start->format("c").'">'.$start->format("F j, Y").'</time>–<time itemprop="endDate" datetime="'.$end->format("c").'">'.$end->format("F j, Y").'</time>';
        }
        return $dates;
    }
}

if (!function_exists("format_opening_reception_dates")) {

    function format_opening_reception_dates($start,$end) {
        $CI =& get_instance();
        $start_time_format = "g";
        if ($start->format("i") != "00") {
            $start_time_format .= ":i";
        }
        if ($start->format("a") != $end->format("a")) {
            $start_time_format .= "a";
        }
        $end_time_format = "g";
        if ($end->format("i") != "00") {
            $end_time_format .= ":i";
        }
        $end_time_format .= "a";
        $date_format = "l F j ";
        $reception = $start->format($date_format).' from <time itemprop="startDate" datetime="'.$start->format("c").'">'.$start->format($start_time_format).'</time> to <time itemprop="endDate" datetime="'.$end->format("c").'">'.$end->format($end_time_format).'</time>';
        return $reception;
    }
}