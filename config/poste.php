<?php

return [

    // Message intercepté à déchiffrer. Reste côté serveur : ni le clair ni la clé
    // ne doivent apparaître dans la page rendue.
    'plain' => env('POSTE_PLAIN', ''),

    // Réglage des trois rotors, ex. "7,19,4".
    'cle' => array_map('intval', array_filter(explode(',', (string) env('POSTE_CLE', '')), fn ($v) => $v !== '')),

];
