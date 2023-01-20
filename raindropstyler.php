<html>
<body>

<?php
$min_size = 0.1;
$max_size = 2;
$min_left_ini = -50;
$max_left_ini = 150;
$min_left_end = -50;
$max_left_end = 150;
$min_delay = -100;
$max_delay = 0;
function generateRaindropRules($num_raindrops, $start_at) {
// Define min and max values for size, left-ini, left-end, and delay
    $min_size = 0.2;
    $max_size = 1;
    $min_left_ini = -100;
    $max_left_ini = 100;
    $min_left_end_lag = -10;
    $max_left_end_lag = 10;
    $min_delay = -100;
    $max_delay = 0;
    $min_animation_durationy = 800;
    $max_animation_durationy = 1400;


// Generate the raindrop animations
    for ($i = $start_at; $i <= $num_raindrops; $i++) {
        // Generate random size, left-ini, left-end, and delay values
        $size = round(rand($min_size * 100, $max_size * 100) / 100, 2);
        $left_ini = rand($min_left_ini, $max_left_ini);
        $left_end = $left_ini + rand($min_left_end_lag, $max_left_end_lag);
        $delay = rand($min_delay, $max_delay);
        $animation_duration = rand($min_animation_durationy, $max_animation_durationy);

        // Print the raindrop animation CSS
        echo ".raindrop:nth-child($i) {<br>";
        echo "  --size: {$size}vw;<br>";
        echo "  --left-ini: {$left_ini}vw;<br>";
        echo "  --left-end: {$left_end}vw;<br>";
        echo "  left: 76vw;<br>";
        echo "  animation: rainfall {$animation_duration}ms linear infinite;<br>";
        echo "  animation-delay: {$delay}s;<br>";
        echo "}<br><br>";
    }
}

generateRaindropRules(500, 66);

?>
</body>

<style>
.select-wrap {
  border: 1px solid;
}


</style>

<script>


</script>
</html>