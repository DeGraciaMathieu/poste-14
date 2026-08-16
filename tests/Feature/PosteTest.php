<?php

namespace Tests\Feature;

use Tests\TestCase;

class PosteTest extends TestCase
{
    public function test_l_accueil_liste_toutes_les_interceptions(): void
    {
        $reponse = $this->get('/')->assertOk();

        foreach (array_keys(config('poste.phrases')) as $i) {
            $reponse->assertSee("/interception/{$i}", false);
        }
    }

    public function test_l_accueil_ne_divulgue_aucun_clair(): void
    {
        $reponse = $this->get('/');

        foreach (config('poste.phrases') as $interception) {
            $reponse->assertDontSee($interception['texte']);
        }
    }

    public function test_l_accueil_affiche_le_niveau_de_difficulte(): void
    {
        $reponse = $this->get('/')->assertOk();

        // Les niveaux (déduits du nombre de rotors) sont visibles.
        $reponse->assertSee('Facile')->assertSee('Expert');

        // Le nombre de rotors de chaque interception est indiqué.
        foreach (config('poste.phrases') as $interception) {
            $reponse->assertSee(count($interception['cle']).' rotors');
        }
    }

    public function test_l_accueil_previsualise_la_recompense_de_completion(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Déchiffrée')                    // tampon posé sur une interception résolue
            ->assertSee('Transmission complète')         // bannière de complétion totale
            ->assertSee('Réinitialiser la progression')  // contrôle de remise à zéro
            ->assertSee('Cryptanalyste');                // grade décerné au 100 %
    }

    public function test_l_origine_du_message_est_servie_a_la_resolution(): void
    {
        $origine = $this->getJson('/interception/0/origine')->assertOk()->json();
        $this->assertNotEmpty($origine['sens']);

        $this->getJson('/interception/999/origine')->assertNotFound();
    }

    public function test_le_puzzle_ne_devoile_pas_l_origine_dans_le_source(): void
    {
        // L'origine n'arrive qu'à la résolution, jamais dans le source de la page.
        $this->get('/interception/0')->assertOk()
            ->assertDontSee(config('poste.phrases.0.sens'));
    }

    public function test_le_puzzle_offre_un_retour_a_la_victoire(): void
    {
        $this->get('/interception/0')->assertOk()->assertSee('Retour aux interceptions');
    }

    public function test_chaque_interception_est_resoluble_sans_exposer_son_clair(): void
    {
        foreach (config('poste.phrases') as $i => $interception) {
            $reponse = $this->get("/interception/{$i}")->assertOk();

            // Le clair de l'interception ne doit jamais figurer dans la page.
            $reponse->assertDontSee($interception['texte']);

            // Avec le bon réglage des rotors, le chiffré donne un clair lisible
            // dont l'empreinte déclenche la victoire.
            [$cipher, $hash] = $this->cipherEtHash($reponse->getContent());
            $clair = $this->tourneLesRotors($cipher, $interception['cle']);

            $this->assertSame($interception['texte'], $clair, "L'interception {$i} n'est pas résoluble.");
            $this->assertSame($hash, hash('sha256', $clair));
        }
    }

    public function test_une_interception_inexistante_renvoie_404(): void
    {
        $this->get('/interception/999')->assertNotFound();
    }

    public function test_le_meme_puzzle_est_stable_entre_deux_visites(): void
    {
        $premier = $this->cipherEtHash($this->get('/interception/0')->getContent());
        $second = $this->cipherEtHash($this->get('/interception/0')->getContent());

        $this->assertSame($premier, $second);
    }

    public function test_un_niveau_facile_offre_les_aides(): void
    {
        $reponse = $this->get('/interception/0')->assertOk()->assertSee('Demander un indice');

        [$cipher, $hash] = $this->cipherEtHash($reponse->getContent());
        $indice = $this->getJson('/interception/0/indice')->assertOk()->json();

        $this->assertSame(0, $indice['index']);

        // L'indice cale le rotor I sur une position réellement gagnante : avec ce
        // réglage et le reste de la clé, le chiffré se résout.
        $reglage = config('poste.phrases.0.cle');
        $reglage[$indice['index']] = $indice['pos'];
        $this->assertSame($hash, hash('sha256', $this->tourneLesRotors($cipher, $reglage)));
    }

    public function test_un_niveau_difficile_n_offre_aucune_aide(): void
    {
        $difficile = count(config('poste.phrases')) - 1;
        $interception = config("poste.phrases.$difficile");

        $reponse = $this->get("/interception/{$difficile}")->assertOk();

        // Ni bouton d'indice, ni mot du clair livré au joueur.
        $reponse->assertDontSee('Demander un indice');
        $reponse->assertDontSee($interception['motCle']);

        // L'indice de rotor reste fermé même en appelant l'URL en direct.
        $this->get("/interception/{$difficile}/indice")->assertNotFound();
    }

    // ---- oracle indépendant : ce que fait un joueur, pas ce que fait le code ----

    private function cipherEtHash(string $page): array
    {
        preg_match('/const CIPHER = "([^"]*)"/', $page, $c);
        preg_match('/const PLAIN_HASH = "([a-f0-9]+)"/', $page, $h);

        return [$c[1], $h[1]];
    }

    private function tourneLesRotors(string $txt, array $cle): string
    {
        $i = 0;

        return preg_replace_callback('/[A-Z]/', function ($m) use ($cle, &$i) {
            $d = -$cle[$i % count($cle)];
            $i++;

            return chr(((ord($m[0]) - 65 + $d) % 26 + 26) % 26 + 65);
        }, $txt);
    }
}
