<h1>Contact</h1>
<?php
//$this->load->view("subscribe");
?>
<div class="contact" itemscope itemtype="http://schema.org/LocalBusiness">
    <meta itemprop="name" content="Birch Contemporary" />
    <div id="address">
        <div itemscope itemtype="http://schema.org/PostalAddress" itemprop="address">
            <div itemprop="streetAddress">129 Tecumseth Street</div>
            <div><span itemprop="addressLocality">Toronto</span>, <span itemprop="addressCountry" itemscope itemtype="http://schema.org/Country"><span itemprop="name">Canada</span></span></div>
            <div class="small-caps" itemprop="postalCode">M6J 2H2</div>
            <div>Tel. <a href="tel:+1 416 365 3003" itemprop="telephone">+1 416 365 3003</a></div>
            <?php
            foreach ($hours as $days=>$time) {
                echo '<div>'.$days.' '.$time.'</div>';
            }
            foreach ($hours_microdata as $hr=>$days) {
                echo '<span itemprop="openingHoursSpecification" itemscope itemtype="http://schema.org/OpeningHoursSpecification">'.$hr.''.join('',$days).'</span>';
            }
            ?>
        </div>
        <?php $this->load->view("subscribe"); ?>
    </div>
    <div id="streetview">
        <div></div>
    </div>
    <div id="map">
        <div></div>
    </div>
    <span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
        <meta itemprop="latitude" content="<?php echo $info["latitude"]; ?>" />
        <meta itemprop="longitude" content="<?php echo $info["longitude"]; ?>" />
    </span>
</div>
<script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
    //<![CDATA[
    $(document).on("ready",function(){
        var u = "info";
        var h = "birchcontemporary.com";
        var e = u+"@"+h;
        $("div[itemprop='postalCode']").after('<div><a href="mailto:'+e+'" itemprop="email">Email us</a></div>');
    });
    function initialize() {
        var gallery = new google.maps.LatLng(<?php echo $info["latitude"]; ?>, <?php echo $info["longitude"]; ?>);
        var panoramaOptions = {
            position: gallery,
            addressControl: false,
            zoomControl: false,
            panControl: false,
            pov: {
                heading: 90,
                pitch: 0
            },
            zoom: 1
        };
        var myPano = new google.maps.StreetViewPanorama(
            $('#streetview div').get(0),
            panoramaOptions);
        myPano.setVisible(true);
        var mapOptions = {
            zoom: 15,
            center: gallery,
            streetView: myPano,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map($('#map div').get(0),
            mapOptions);
    }
    google.maps.event.addDomListener(window, 'load', initialize);
    //]]>
</script>