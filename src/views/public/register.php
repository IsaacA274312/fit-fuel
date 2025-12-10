<?php
http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => false,
  'deprecated' => true,
  'message' => 'Endpoint duplicado. Usa /fitandfuel/src/public/register.php'
]);