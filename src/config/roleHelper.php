<?php
// Helper de roles y redirecciones para evitar duplicaciones
function normalize_role($raw){
    $r = strtolower(trim($raw ?? ''));
    $map = [
        'admin' => ['admin','administrator','administrador'],
        'instructor' => ['instructor','entrenador','coach'],
        'nutriologo' => ['nutriologo','nutriólogo','nutricionista','nutriólogo'],
        'usuario' => ['usuario','user','cliente','miembro']
    ];
    foreach($map as $key => $aliases){
        foreach($aliases as $a){ if($r === $a) return $key; }
    }
    if(strpos($r,'admin') !== false) return 'admin';
    if(strpos($r,'instructor') !== false || strpos($r,'entrenador') !== false || strpos($r,'coach') !== false) return 'instructor';
    if(strpos($r,'nutri') !== false || strpos($r,'nutrió') !== false || strpos($r,'nutric') !== false) return 'nutriologo';
    return 'usuario';
}

function role_redirect_path($role){
    // Devolver rutas absolutas bajo el root del proyecto en Apache/XAMPP
    $base = '/fitandfuel/';
    $map = [
        'admin' => $base . 'src/views/admin/dashboard.php',
        'instructor' => $base . 'src/views/instructor/dashboard.php',
        'nutriologo' => $base . 'src/views/nutriologo/dashboard.php',
        'usuario' => $base . 'src/views/user/dashboard.php'
    ];
    return $map[$role] ?? ($base . 'src/views/user/dashboard.php');
}
