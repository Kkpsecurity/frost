<?php

use Illuminate\Support\Carbon;

$lesson_random_min = 600;  // 10min
$lesson_random_max = 1800; // 30min

$Start = Carbon::Parse( '2023-03-28 09:15:00 EDT' );
( $End = clone $Start )->addSeconds( $lesson_random_max );
( $Now = clone $Start )->addSeconds( $lesson_random_min );


?>
<!doctype html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto+Mono" />

<style>

body
{
    background:         white;
    color:              black;
    font-family:        arial, helvetica, sans-serif;
    font-size:          12px;
}

pre, .mono
{
    font-family:        'Roboto Mono', monospace;
}

th { padding: 4px 10px; }
td { padding: 2px 10px; }

.hit
{
    color:              red;
    font-size:          14px;
    font-weight:        bold;
}

</style>
</head>
<body>

<table border="1" cellspacing="0" cellpadding="4">
<tr>
  <td><?=$Start->isoFormat( 'ddd MM/DD HH:mm:ss' )?></td>
  <td colspan="3">Last Challenge</td>
</tr>
<tr>
  <th>Timestamp</th>
  <th>Delta</th>
  <th>Calc</th>
  <th>HIT</th>
</tr>

<?php

while ( $Now->lt( $End ) )
{

    $delta_sec = $Now->diffInSeconds( $Start ) - $lesson_random_min;
    $rand_max  = 100 - intval( ( $delta_sec / ( $lesson_random_max - $lesson_random_min ) ) * 100 );

    if ( $rand_max <= 1 )
    {
        $hit  = '<span class="hit">AUTO</span>';
    }
    else
    {
        $hit = ( 0 == rand( 0, $rand_max ) ) ? '<span class="hit">HIT</span>' : '';
    }


    print <<<ROW
<tr>
  <td>{$Now->isoFormat( 'ddd MM/DD HH:mm:ss' )}</td>
  <td align="center">{$delta_sec}</td>
  <td class="mono">if 0 == rand( 0, {$rand_max} )</td>
  <td align="center">{$hit}</td>
</tr>
ROW;

    $Now->addSeconds( 5 );

}

?>

</table>

</body>
</html>
<?php exit();
