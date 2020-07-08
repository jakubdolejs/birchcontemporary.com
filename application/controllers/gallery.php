<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';
/**
 */

class Gallery extends Bc_controller {
    
    function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $hours = array();
        $last_times = NULL;
        $hours_microdata = array();
        $microdata_days = array(
            'Sun'=>'http://purl.org/goodrelations/v1#Sunday',
            'Mon'=>'http://purl.org/goodrelations/v1#Monday',
            'Tue'=>'http://purl.org/goodrelations/v1#Tuesday',
            'Wed'=>'http://purl.org/goodrelations/v1#Wednesday',
            'Thu'=>'http://purl.org/goodrelations/v1#Thursday',
            'Fri'=>'http://purl.org/goodrelations/v1#Friday',
            'Sat'=>'http://purl.org/goodrelations/v1#Saturday'
        );
        foreach (array(
                    array(
                        "day"=>"Wed",
                        "open_time"=>"10:00:00",
                        "close_time"=>"18:00:00"
                    ),
                     array(
                         "day"=>"Thu",
                         "open_time"=>"10:00:00",
                         "close_time"=>"18:00:00"
                     ),
                     array(
                         "day"=>"Fri",
                         "open_time"=>"10:00:00",
                         "close_time"=>"18:00:00"
                     ),
                     array(
                         "day"=>"Sat",
                         "open_time"=>"11:00:00",
                         "close_time"=>"17:00:00"
                     )
            ) as $times) {
            $key = '<meta itemprop="opens" content="'.date("H:i:s",strtotime($times["open_time"])).'" /><meta itemprop="closes" content="'.date("H:i:s",strtotime($times["close_time"])).'" />';
            if (!isset($hours_microdata[$key])) {
                $hours_microdata[$key] = array();
            }
            $hours_microdata[$key][] = '<link itemprop="dayOfWeek" href="'.$microdata_days[$times["day"]].'" />';
            if (!$last_times || $times["open_time"] != $last_times["open_time"] || $times["close_time"] != $last_times["close_time"]) {
                $hours[] = array("start"=>$times["day"],"open"=>strtotime($times["open_time"]),"close"=>strtotime($times["close_time"]));
            } else {
                $hours[count($hours)-1]["end"] = $times["day"];
            }
            $last_times = $times;
        }
        $has_minutes = FALSE;
        foreach ($hours as $k=>$h) {
            $open = new DateTime();
            $open->setTimestamp($h["open"]);
            $close = new DateTime();
            $close->setTimestamp($h["close"]);
            if (!$has_minutes && ($open->format("i") != "00" || $close->format("i") != "00")) {
                $has_minutes = TRUE;
                break;
            }
        }
        $time_format = "ga";
        if ($has_minutes) {
            $time_format = "H:i";
        }
        $opening_hours = array();
        foreach ($hours as $k=>$h) {
            $key = $h["start"];
            if (!empty($h["end"])) {
                $key .= "–".$h["end"];
            }
            $open = new DateTime();
            $open->setTimestamp($h["open"]);
            $close = new DateTime();
            $close->setTimestamp($h["close"]);
            $opening_hours[$key] = $open->format($time_format)."–".$close->format($time_format);
        }
        $gallery_info = array(
            "name"=>"Birch Contemporary ",
            "address"=>"129 Tecumseth Street",
            "city"=>"Toronto",
            "province"=>"ON",
            "postal_code"=>"M6J 2H2",
            "telephone"=>"+1 416 365 3003",
            "latitude"=>"43.6453393",
            "longitude"=>"-79.4059625"
        );
        $header_vars = $this->get_header_vars("/contact");
        $this->load->view("header",$header_vars);
        $gallery_staff = array();
        $this->load->view("contact",array("info"=>$gallery_info,"staff"=>$gallery_staff,"hours"=>$opening_hours,"hours_microdata"=>$hours_microdata));
        $this->load->view("footer");
    }
}