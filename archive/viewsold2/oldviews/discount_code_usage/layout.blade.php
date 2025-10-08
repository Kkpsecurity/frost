<!doctype html>
<html lang="{{ App::getLocale() }}">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Discount Code Usage</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

<style>

body
{
    font-family:    arial, helvetica, sans-serif;
    font-size:      16px;
}

.grid_table
{
    display:        grid;
    gap:            10px;
}

.grid_table > *
{
    white-space:    nowrap;
}

.grid-span-2  { grid-column: span 2; }
.grid-span-3  { grid-column: span 3; }


#discount_code_info
{
    grid-template-columns:  repeat( 3, min-content );
    gap:                    5px 10px;
    border:                 2px solid #ccc;
    padding:                10px 20px;
}


#orders_table
{
    grid-template-columns: repeat( 6, min-content );
    font-size:             15px;
}

</style>
</head>
<body>

@yield('content')

</body>
</html>
