<?php

// if (!function_exists('chi_square_inverse')) {
//     function CHIINV($probability, $degrees) {
//         $probability = (float)$probability; // Ensure $probability is a float
//         $degrees = floor((float)$degrees); // Ensure $degrees is a float and round down to the nearest integer

//         if (is_numeric($probability) && is_numeric($degrees)) {
//             $xLo = 100;
//             $xHi = 0;
//             $x = $xNew = 1;
//             $dx = 1;
//             $i = 0;
//             $PRECISION = 1.0e-12; // Define precision
//             $MAX_ITERATIONS = 100; // Define maximum iterations

//             while ((abs($dx) > $PRECISION) && ($i++ < $MAX_ITERATIONS)) {
//                 // Apply Newton-Raphson step
//                 $result = self::CHIDIST($x, $degrees);
//                 $error = $result - $probability;
//                 if ($error == 0.0) {
//                     $dx = 0;
//                 } elseif ($error < 0.0) {
//                     $xLo = $x;
//                 } else {
//                     $xHi = $x;
//                 }
//                 // Avoid division by zero
//                 if ($result != 0.0) {
//                     $dx = $error / $result;
//                     $xNew = $x - $dx;
//                 }
//                 // If the NR fails to converge (which for example may be the
//                 // case if the initial guess is too rough) we apply a bisection
//                 // step to determine a more narrow interval around the root.
//                 if (($xNew < $xLo) || ($xNew > $xHi) || ($result == 0.0)) {
//                     $xNew = ($xLo + $xHi) / 2;
//                     $dx = $xNew - $x;
//                 }
//                 $x = $xNew;
//             }
//             if ($i == $MAX_ITERATIONS) {
//                 return '#N/A'; // Return Excel's #N/A error
//             }
//             return round($x, 12); // Return the result rounded to 12 decimal places
//         }
//         return '#VALUE!'; // Return Excel's #VALUE! error
//     }
// }

function chi_square_inverse($p, $df) {
    $EPSILON = 0.000001;
    $MAX_ITERS = 100;
    $MIN = 0.000000001;
    $guess = max(0.0, $df - 0.5);
    $delta = 1.0;
    $x = 0.0;

    for ($i = 0; $i < $MAX_ITERS && abs($delta) > $EPSILON; $i++) {
        $x = $guess;
        $pdf_value = chi_square_pdf($x, $df);

        // Check if the PDF value is close to zero
        if (abs($pdf_value) < $EPSILON) {
            // Handle division by zero gracefully
            return 0.0; // For example, returning 0.0 here
        }

        // Check if denominator is zero before division
        if ($pdf_value == 0) {
            // Handle division by zero gracefully
            return 0.0; // For example, returning 0.0 here
        }

        // $delta = (chi_square_cdf($x, $df) - $p) / $pdf_value;
        $guess -= $delta;
        if ($guess <= $MIN) {
            return 0.0;
        }
    }

    return $guess;
}

// Chi-Square PDF function
function chi_square_pdf($x, $df) {
    if ($x <= 0 || $df < 1) {
        return 0.0;
    }
    return exp(($df / 2 - 1) * log($x) - $x / 2 - log(gamma_custom($df / 2))) / (pow(2, $df / 2) * gamma_custom($df / 2));
}

// Gamma function
function gamma_custom($x) {
    if ($x == 1) {
        return 1;
    } elseif ($x < 1) {
        return M_PI / (sin(M_PI * $x) * gamma_custom(1 - $x));
    } else {
        return ($x - 1) * gamma_custom($x - 1);
    }
}

?>
