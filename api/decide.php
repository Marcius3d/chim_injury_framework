<?php
/**
 * GET ?kind=combat|after_fight|heal&npc=Name : may a follower speak this reaction now?
 * Plain text answer for CHIMInjury.dll: "allow" or "skip:<reason>|<readable reason>".
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap_inc.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

$kind = preg_replace('/[^a-z_]/', '', strtolower((string)($_GET['kind'] ?? '')));
$npc = mb_substr(trim((string)($_GET['npc'] ?? '')), 0, 80);
[$allowed, $reason] = chimInjuryDecideForGame($kind, $npc);
echo $allowed ? "allow\n" : 'skip:' . $reason . '|' . chimInjuryReasonText($reason) . "\n";
