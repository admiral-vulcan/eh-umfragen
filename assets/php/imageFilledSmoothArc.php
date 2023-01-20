<?php
function imageFilledSmoothArc ($img, $cx, $cy, $width, $height, $startDegree, $stopDegree, $colorId) {
    // Added by Tibor, 02/2010
    // Wrapper function for users of 'imageFilledArc()' which is a PHP function for GD2

    // 'imageFilledArc()':
    // * draws from $start to $stop clockwise
    // * uses a clockwise coordinate system
    // * uses degrees for the angles
    // * uses color identifier from imageColorAllocate()

    // 'imageSmoothArc':
    // * draws from $start to $stop counter-clockwise
    // * uses a counter-clockwise coordinate system
    // * uses radians for the angles
    // * uses an RGBA array for the color

    // So if you used:
    // imageFilledArc($img, $cx, $cy, $w, $h, 30, 120, $colorId)
    // then you should transform the start, stop and color values:
    // imageSmoothArc (&$img, $cx, $cy, $w, $h, $colorRGBA, -120 / 180.0 * M_PI, -30 / 180.0 * M_PI)
    // which produces exactly the same elliptical arc - but renders very nice high quality graphics (note the - sign and the radians).

    // This wrapper function takes care of the necessary transformations,
    // so you can easily migrate your code from:
    // imageFilledArc($img, $cx, $cy, $w, $h, 30, 120, $colorId)
    // to:
    // imageFilledSmoothArc($img, $cx, $cy, $w, $h, 30, 120, $colorId)

    // Parameters:
    // $cx - Center of ellipse, X-coord
    // $cy - Center of ellipse, Y-coord
    // $width - Width of ellipse ($w >= 2)
    // $height - Height of ellipse ($h >= 2 )
    // $startDegree - Starting angle of the arc in degrees, no limited range!
    // $stopDegree - Stopping angle of the arc in degrees, no limited range!
    // $colorId - Color identifier from imageColorAllocate()
    // $start _can_ be greater than $stop!

    // Transforming $startDegree and $stopDegree to $startRadian and $stopRadian
    // 'imageSmoothArc()' uses a counter-clockwise coordinate system (the coordinates should be negative)
    // 'imageSmoothArc()' draws counter-clockwise (therefore $start and $stop should be swapped)
    // 'imageSmoothArc()' uses radians for the angles ($radians = $degrees / 180.0 * M_PI)
    $startRadian = (0 - $stopDegree) / 180 * M_PI;
    $stopRadian = (0 - $startDegree) / 180 * M_PI;

    // Transforming $colorIdentifier to $colorRGBA
    // 'imageSmoothArc()' requires an array of four RGBA color values ([0]=>255,[1]=>255,[2]=>255,[3]=>0)
    // Thefirst three values are the RGB color, the fourth is the alpha blending factor.
    // 'imageColorsForIndex()' returns an associative array (["red"]=>255, ["green"]=> 255, ["blue"]=> 255, ["alpha"]=>0)
    $colorRGBA = array_values(imageColorsForIndex($img, $colorId));

    // Drawing the elliptic arc with imageSmoothArc()
    imageSmoothArc($img, $cx, $cy, $width, $height, $colorRGBA, $startRadian, $stopRadian);
}
?>