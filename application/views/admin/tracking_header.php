<h1>Tracking</h1>
<p class="trackingHeader">
    <?php
    if (!isset($selected)) {
        $selected = false;
    }
    $links = array();
    foreach (array("visitors"=>"Visitors","artists"=>"Artists") as $key=>$val) {
        $link = '<a href="/admin/tracking/'.$key.'"';
        if ($key == $selected) {
            $link .= ' class="selected"';
        }
        $link .= '>'.$val.'</a>';
        $links[] = $link;
    }
    echo join(" | ",$links);
    ?>
</p>
<p>
    <?php
    if (!isset($month)) {
        $month = date("Y-m");
    }
    ?>
    <input type="month" value="<?php echo $month; ?>" name="month" />
</p>
<script type="text/javascript">
    $("input[name='month']").on("change",function(){
        if (/[^a-z]month=([0-9\-]+)/i.test(location.search)) {
            location.href = location.origin+location.pathname+location.search.replace(/month=([0-9\-]+)/i,"month="+$(this).val());
        } else if (location.search.length > 1) {
            location.href = location.origin+location.pathname+location.search+"&month="+$(this).val();
        } else {
            location.href = location.origin+location.pathname+location.search+"?month="+$(this).val();
        }
    });
</script>