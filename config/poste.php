<?php

return [

    // Catalogue des interceptions à découvrir, ordonné du plus facile au plus dur.
    // La difficulté se déduit du nombre de rotors (la longueur de « cle ») : plus il
    // y a de rotors, plus le réglage est dur à trouver — et les aides s'estompent
    // (voir PosteController::aides). Chaque entrée : le clair, le réglage gagnant et
    // le mot surligné/donné en indice. Ces données restent côté serveur ; seul le
    // chiffré est envoyé au navigateur.
    'phrases' => [
        // 3 rotors — mot surligné + indice de rotor.
        ['texte' => 'LE CONVOI QUITTE LE PORT A LAUBE ESCORTE PAR DEUX DESTROYERS', 'cle' => [7, 19, 4], 'motCle' => 'CONVOI'],
        ['texte' => 'LA FLOTTE ENNEMIE APPAREILLE VERS LE NORD AVANT MINUIT', 'cle' => [3, 11, 20], 'motCle' => 'FLOTTE'],
        // 4 rotors — mot surligné, plus d'indice de rotor.
        ['texte' => 'TROIS SOUS MARINS PATROUILLENT AU LARGE DU CAP GRIS NEZ DEPUIS LAUBE', 'cle' => [14, 2, 9, 21], 'motCle' => 'MARINS'],
        ['texte' => 'LE GENERAL INSPECTE LES DEFENSES DE LA COTE DEMAIN MATIN A LA PREMIERE HEURE', 'cle' => [5, 22, 8, 1], 'motCle' => 'GENERAL'],
        // 5 rotors — aucune aide.
        ['texte' => 'MUNITIONS STOCKEES DANS LE HANGAR SEPT PRES DE LA GARE DE TRIAGE SOUS BONNE GARDE', 'cle' => [12, 1, 17, 6, 23], 'motCle' => 'HANGAR'],
        ['texte' => 'LES RENFORTS ARRIVENT PAR LE TRAIN DE HUIT HEURES QUATORZE VOIE NUMERO TROIS', 'cle' => [9, 15, 3, 20, 11], 'motCle' => 'RENFORTS'],
        // 6 rotors — aucune aide.
        ['texte' => 'ORDRE DE REPLI IMMEDIAT SUR LA POSITION DE SECOURS AVANT QUE LE PONT NE SAUTE A MINUIT', 'cle' => [21, 6, 13, 2, 18, 4], 'motCle' => 'REPLI'],
    ],

];
