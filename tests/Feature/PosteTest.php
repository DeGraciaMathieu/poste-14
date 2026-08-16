<?php

namespace Tests\Feature;

use Tests\TestCase;

class PosteTest extends TestCase
{
    public function test_l_accueil_liste_toutes_les_interceptions(): void
    {
        $reponse = $this->get('/')->assertOk();

        $total = count(config('poste.phrases'));
        for ($i = 0; $i < $total; $i++) {
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

    public function test_chaque_interception_est_resoluble_sans_exposer_son_clair(): void
    {
        foreach (array_keys(config('poste.phrases')) as $i) {
            $reponse = $this->get("/interception/{$i}")->assertOk();

            // Le clair de l'interception ne doit jamais figurer dans la page.
            $reponse->assertDontSee(config("poste.phrases.$i.texte"));

            // Le chiffré envoyé doit avoir une solution qui déclenche la victoire.
            [$cipher, $hash] = $this->cipherEtHash($reponse->getContent());
            $this->assertNotNull(
                $this->reglageQuiResout($cipher, $hash),
                "L'interception {$i} n'est pas résoluble."
            );
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

    public function test_l_indice_revele_une_position_de_rotor_reellement_gagnante(): void
    {
        [$cipher, $hash] = $this->cipherEtHash($this->get('/interception/0')->getContent());

        $indice = $this->getJson('/interception/0/indice')->assertOk()->json();

        $this->assertSame(0, $indice['index']);
        $this->assertGreaterThanOrEqual(0, $indice['pos']);
        $this->assertLessThanOrEqual(25, $indice['pos']);

        // En calant le rotor I sur l'indice, le reste doit rester résoluble.
        $this->assertNotNull(
            $this->reglageQuiResout($cipher, $hash, $indice['pos']),
            "L'indice ne mène pas à une solution valide."
        );
    }

    // ---- oracles indépendants : ce que fait un joueur, pas ce que fait le code ----

    private function cipherEtHash(string $page): array
    {
        preg_match('/const CIPHER = "([^"]*)"/', $page, $c);
        preg_match('/const PLAIN_HASH = "([a-f0-9]+)"/', $page, $h);

        return [$c[1], $h[1]];
    }

    private function reglageQuiResout(string $cipher, string $hash, ?int $rotorI = null): ?array
    {
        for ($a = $rotorI ?? 0; $a <= ($rotorI ?? 25); $a++) {
            for ($b = 0; $b < 26; $b++) {
                for ($c = 0; $c < 26; $c++) {
                    if (hash('sha256', $this->tourneLesRotors($cipher, [$a, $b, $c])) === $hash) {
                        return [$a, $b, $c];
                    }
                }
            }
        }

        return null;
    }

    private function tourneLesRotors(string $txt, array $cle): string
    {
        $i = 0;

        return preg_replace_callback('/[A-Z]/', function ($m) use ($cle, &$i) {
            $d = -$cle[$i % 3];
            $i++;

            return chr(((ord($m[0]) - 65 + $d) % 26 + 26) % 26 + 65);
        }, $txt);
    }
}
