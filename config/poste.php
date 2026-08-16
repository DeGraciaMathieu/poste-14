<?php

return [

    // Catalogue des interceptions à découvrir, ordonné du plus facile au plus dur.
    // La difficulté se déduit du nombre de rotors (la longueur de « cle ») : plus il
    // y a de rotors, plus le réglage est dur à trouver — et les aides s'estompent
    // (voir PosteController::aides). Chaque entrée : le clair, le réglage gagnant et
    // le mot surligné/donné en indice. Ces données restent côté serveur ; seul le
    // chiffré est envoyé au navigateur.
    // Le « sens » est le contexte historique révélé dans les archives à 100 % : il
    // ne cite jamais le clair (l'anti view-source reste intact).
    'phrases' => [
        // 3 rotors — mot surligné + indice de rotor.
        ['texte' => 'LES CAROTTES SONT CUITES', 'cle' => [7, 19, 4], 'motCle' => 'CAROTTES', 'sens' => "Formule devenue légendaire de Radio Londres, signalant qu'une opération clandestine était accomplie."],
        ['texte' => 'JEAN A DE LONGUES MOUSTACHES', 'cle' => [3, 11, 20], 'motCle' => 'MOUSTACHES', 'sens' => "Message personnel authentique de la BBC, déclenchant une action précise de la Résistance."],
        // 4 rotors — mot surligné, plus d'indice de rotor.
        ['texte' => 'ANDROMAQUE SE PARFUME A LA BERGAMOTE', 'cle' => [14, 2, 9, 21], 'motCle' => 'BERGAMOTE', 'sens' => "Message personnel de Radio Londres, à la poésie volontairement absurde pour tromper les écoutes ennemies."],
        ['texte' => 'ICI LONDRES LES FRANCAIS PARLENT AUX FRANCAIS', 'cle' => [5, 22, 8, 1], 'motCle' => 'LONDRES', 'sens' => "Indicatif d'ouverture des émissions françaises de la BBC, écoutées clandestinement sous l'Occupation."],
        // 5 rotors — aucune aide.
        ['texte' => 'LA FRANCE A PERDU UNE BATAILLE MAIS ELLE NA PAS PERDU LA GUERRE', 'cle' => [12, 1, 17, 6, 23], 'motCle' => 'BATAILLE', 'sens' => "Extrait de l'affiche « À tous les Français » placardée à Londres en août 1940."],
        ['texte' => 'QUOI QUIL ARRIVE LA FLAMME DE LA RESISTANCE NE DOIT PAS SETEINDRE ET NE SETEINDRA PAS', 'cle' => [9, 15, 3, 20, 11], 'motCle' => 'RESISTANCE', 'sens' => "Passage de l'Appel du 18 juin 1940, acte fondateur de la France libre."],
        // 6 rotors — aucune aide.
        ['texte' => 'LES SANGLOTS LONGS DES VIOLONS DE LAUTOMNE BLESSENT MON COEUR DUNE LANGUEUR MONOTONE', 'cle' => [21, 6, 13, 2, 18, 4], 'motCle' => 'LANGUEUR', 'sens' => "Les deux vers réunis : le second, diffusé le 5 juin 1944, annonçait le Débarquement sous quarante-huit heures."],
    ],

];
