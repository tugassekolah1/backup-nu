@props(['title' => 'Nahdlatul Ulama Banjaranyar, Cilongok - Berkhidmat untuk Umat'])

<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>{{ $title }}</title>

<!-- Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Baloo+2:wght@700;800&family=Inter:wght@300;400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{ $slot }}
