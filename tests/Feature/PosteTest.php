<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class PosteTest extends TestCase
{
    public function test_la_page_du_jour_se_charge(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Signal capté')
            ->assertSee('Demander un indice');
    }

    public function test_la_page_ne_divulgue_jamais_le_clair(): void
    {
        $reponse = $this->get('/');

        // Aucune interception du catalogue ne doit apparaître en clair dans la page.
        foreach (config('poste.phrases') as $interception) {
            $reponse->assertDontSee($interception['texte']);
        }
    }

    public function test_le_chiffre_du_jour_est_resoluble_et_sa_victoire_detectable(): void
    {
        [$cipher, $hash] = $this->cipherEtHash($this->get('/')->getContent());

        // Un joueur doit pouvoir trouver un réglage qui rend le message lisible,
        // et ce réglage doit déclencher la détection de victoire (le hash).
        $this->assertNotNull(
            $this->reglageQuiResout($cipher, $hash),
            'Aucun réglage de rotors ne résout le chiffré du jour.'
        );
    }

    public function test_le_meme_jour_sert_toujours_le_meme_puzzle(): void
    {
        $premier = $this->cipherEtHash($this->get('/')->getContent());
        $second = $this->cipherEtHash($this->get('/')->getContent());

        $this->assertSame($premier, $second);
    }

    public function test_chaque_jour_change_l_interception_et_le_cycle_se_repete(): void
    {
        $total = count(config('poste.phrases'));
        $depart = Carbon::parse(config('poste.depuis'));

        $cycle = [];
        for ($jour = 0; $jour < $total; $jour++) {
            $this->travelTo($depart->copy()->addDays($jour));
            $cycle[] = $this->cipherEtHash($this->get('/')->getContent())[0];
        }

        // Une interception différente chaque jour du cycle.
        $this->assertCount($total, array_unique($cycle));

        // Le lendemain du dernier jour, le cycle recommence.
        $this->travelTo($depart->copy()->addDays($total));
        $this->assertSame($cycle[0], $this->cipherEtHash($this->get('/')->getContent())[0]);
    }

    public function test_l_indice_revele_une_position_de_rotor_reellement_gagnante(): void
    {
        [$cipher, $hash] = $this->cipherEtHash($this->get('/')->getContent());

        $indice = $this->getJson('/indice')->assertOk()->json();

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
