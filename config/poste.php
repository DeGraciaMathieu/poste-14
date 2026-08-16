<?php

return [

    // Catalogue des interceptions à découvrir. Chaque entrée : le clair, le réglage
    // gagnant des rotors et le mot surligné/donné en indice. Ces données restent
    // côté serveur ; seul le chiffré est envoyé au navigateur.
    'phrases' => [
        ['texte' => 'LE CONVOI QUITTE LE PORT A LAUBE ESCORTE PAR DEUX DESTROYERS', 'cle' => [7, 19, 4], 'motCle' => 'CONVOI'],
        ['texte' => 'LA FLOTTE ENNEMIE APPAREILLE VERS LE NORD AVANT MINUIT', 'cle' => [3, 11, 20], 'motCle' => 'FLOTTE'],
        ['texte' => 'TROIS SOUS MARINS PATROUILLENT AU LARGE DU CAP GRIS NEZ', 'cle' => [14, 2, 9], 'motCle' => 'MARINS'],
        ['texte' => 'LE GENERAL INSPECTE LES DEFENSES DE LA COTE DEMAIN MATIN', 'cle' => [5, 22, 8], 'motCle' => 'GENERAL'],
        ['texte' => 'MUNITIONS STOCKEES DANS LE HANGAR SEPT PRES DE LA GARE', 'cle' => [12, 1, 17], 'motCle' => 'HANGAR'],
        ['texte' => 'LES RENFORTS ARRIVENT PAR LE TRAIN DE HUIT HEURES', 'cle' => [9, 15, 3], 'motCle' => 'RENFORTS'],
        ['texte' => 'ORDRE DE REPLI IMMEDIAT SUR LA POSITION DE SECOURS', 'cle' => [21, 6, 13], 'motCle' => 'REPLI'],
    ],

];
