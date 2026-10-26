<?php
/**
 * CHIM Injury Framework - HerikaServer hook (loaded from main.php before dispatch, after the NPC profile).
 *
 * - "info_injury" events from CHIMInjury.dll: the [injury ...] marker is removed and the event is
 *   stored for everyone nearby (CHIM would otherwise limit it to the names in the text).
 * - "instruction" requests with a [chim_injury ...] marker: rewritten into the configured spoken
 *   reaction (wound in combat, after the fight, healing). The decision was made through api/decide.php.
 * Everything else is left untouched.
 */

$chimInjuryType = $gameRequest[0] ?? '';
if ($chimInjuryType !== 'info_injury' && $chimInjuryType !== 'instruction') {
    return;
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'chim_injury.php';

if ($chimInjuryType === 'info_injury') {
    if (chimInjuryHandleEvent($gameRequest)) {
        terminate();
    }
    return;
}

$chimInjuryReaction = chimInjuryHandleReactionRequest($gameRequest);
if ($chimInjuryReaction === null) {
    return;
}
if (!$chimInjuryReaction['allow']) {
    terminate();
}
$gameRequest[3] = $chimInjuryReaction['data'];
