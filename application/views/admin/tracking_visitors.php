
<?php
if (isset($days) && isset($month)) {
    $date = DateTime::createFromFormat("Y-m-d",$month."-01");
    if (empty($days)) {
        echo "<p>No activity in ".$date->format("F Y").'</p>';
    } else {
        $day_count = $date->format("t");
        echo '<table class="tracking"><thead><tr><td></td><td colspan="'.$day_count.'">Date</td></tr></thead><tbody><tr><td></td>';
        $max = 0;
        for ($i=1; $i<=$day_count; $i++) {
            if (isset($days[$i])) {
                if ($days[$i]["visitor_count"] > $max) {
                    $max = intval($days[$i]["visitor_count"]);
                }
            }
            echo '<td class="day">'.$i.'</td>';
        }
        echo '</tr><tr><td class="rowHeading"><div>Number of visitors</div></td>';
        for ($i=1; $i<=$day_count; $i++) {
            echo '<td class="trackingCount">';
            if (isset($days[$i])) {
                echo '<div class="bar" style="height:'.($days[$i]["visitor_count"]/$max*100).'%">&nbsp;</div>';
                echo '<div class="count">'.$days[$i]["visitor_count"].'</div>';
            } else {
                echo '<div class="count">0</div>';
            }
            echo '</td>';
        }
        echo '</tr></tbody></table>';
        if (!empty($visitors)) {
            echo '<h2>People who viewed works on the website in '.$date->format("F Y").'</h2><select name="artist"><option value="">Select</option>';
            foreach ($visitors as $visitor) {
                $name = $visitor["name"];
                echo '<option value="'.$visitor["email"].'" data-name="'.$name.'">'.htmlspecialchars($visitor["name"]).' (viewed '.$visitor["work_count"].' '.($visitor["work_count"] > 1 ? "works" : "work").' by '.$visitor["artist_count"].' '.($visitor["artist_count"] > 1 ? "artists" : "artist").')</option>';
            }
            echo '</select>';
        }
    }
}
?>
<script type="text/javascript">
    $("select[name='artist']").on("change",function(){
        if ($(this).val().length > 0) {
            var name = $(this).find("option").eq(this.options.selectedIndex).data("name");
            location.href = "/admin/tracking/visitor?visitor="+$(this).val()+"&name="+encodeURIComponent(name)+"&month=<?php echo $month; ?>";
        }
    });
</script>