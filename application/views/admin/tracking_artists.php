
<?php
if (isset($days) && isset($month)) {
    $date = DateTime::createFromFormat("Y-m-d",$month."-01");
    if (empty($days)) {
        echo "<p>No activity in ".$date->format("F Y").'</p>';
    } else {
        $day_count = $date->format("t");
        echo '<table class="tracking"><thead><tr><td></td><td colspan="'.$day_count.'">Date</td></tr></thead><tbody><tr><td></td>';
        $max = 0;
        $visitors = array();
        for ($i=1; $i<=$day_count; $i++) {
            if (isset($days[$i])) {
                if ($days[$i]["artist_count"] > $max) {
                    $max = intval($days[$i]["artist_count"]);
                }
            }
            echo '<td class="day">'.$i.'</td>';
        }
        echo '</tr><tr><td class="rowHeading"><div>Number of artists viewed</div></td>';
        for ($i=1; $i<=$day_count; $i++) {
            echo '<td class="trackingCount">';
            if (isset($days[$i])) {
                echo '<div class="bar" style="height:'.($days[$i]["artist_count"]/$max*100).'%">&nbsp;</div>';
                echo '<div class="count">'.$days[$i]["artist_count"].'</div>';
            } else {
                echo '<div class="count">0</div>';
            }
            echo '</td>';
        }
        echo '</tr></tbody></table>';
        if (!empty($artists)) {
            echo '<h2>Artists whose work was viewed in '.$date->format("F Y").'</h2><select name="artist"><option value="">Select</option>';
            foreach ($artists as $artist) {
                echo '<option value="'.$artist["id"].'">'.htmlspecialchars($artist["name"]).' ('.$artist["work_count"].' '.($artist["work_count"] > 1 ? "works" : "work").' viewed by '.$artist["visitor_count"].' '.($artist["visitor_count"] > 1 ? "visitors" : "visitor").')</option>';
            }
            echo '</select>';
        }
    }
}
?>
<script type="text/javascript">
    $("select[name='artist']").on("change",function(){
        if ($(this).val().length > 0) {
            location.href = "/admin/tracking/artist/"+$(this).val()+"?month=<?php echo $month; ?>";
        }
    });
</script>