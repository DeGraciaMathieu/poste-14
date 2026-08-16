<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CatalogueInterceptionsTest extends TestCase
{
    private function interceptions(): array
    {
        return (require dirname(__DIR__, 2).'/config/poste.php')['phrases'];
    }

    public function test_le_catalogue_propose_des_interceptions(): void
    {
        $this->assertNotEmpty($this->interceptions());
    }

    public function test_chaque_interception_est_entierement_chiffrable(): void
    {
        // Seules les lettres A-Z sont chiffrées : un accent ou une minuscule
        // passerait en clair dans la sortie et trahirait le message.
        foreach ($this->interceptions() as $interception) {
            $this->assertMatchesRegularExpression(
                '/^[A-Z ]+$/',
                $interception['texte'],
                "Message non chiffrable : {$interception['texte']}"
            );
        }
    }

    public function test_chaque_reglage_tient_sur_trois_a_six_rotors(): void
    {
        // Rotors de 26 positions : une clé hors de 0-25 serait injouable, et le
        // jeu ne gère que 3 à 6 rotors.
        foreach ($this->interceptions() as $interception) {
            $this->assertGreaterThanOrEqual(3, count($interception['cle']));
            $this->assertLessThanOrEqual(6, count($interception['cle']));

            foreach ($interception['cle'] as $position) {
                $this->assertGreaterThanOrEqual(0, $position);
                $this->assertLessThanOrEqual(25, $position);
            }
        }
    }

    public function test_la_difficulte_augmente_le_long_du_catalogue(): void
    {
        // Le nombre de rotors ne doit jamais redescendre : les interceptions sont
        // ordonnées de la plus facile à la plus dure.
        $rotors = array_map(fn ($i) => count($i['cle']), $this->interceptions());
        $croissant = $rotors;
        sort($croissant);

        $this->assertSame($croissant, $rotors, 'Le catalogue doit aller du plus facile au plus dur.');
    }

    public function test_le_mot_indice_apparait_vraiment_dans_le_message(): void
    {
        // L'indice et le surlignage promettent ce mot : il doit exister dans le clair.
        foreach ($this->interceptions() as $interception) {
            $this->assertStringContainsString($interception['motCle'], $interception['texte']);
        }
    }

    public function test_chaque_interception_porte_un_sens_historique(): void
    {
        // Les archives déclassifiées ont besoin d'un contexte pour chaque message.
        foreach ($this->interceptions() as $interception) {
            $this->assertArrayHasKey('sens', $interception);
            $this->assertNotEmpty($interception['sens']);
        }
    }

    public function test_le_sens_ne_divulgue_jamais_le_clair(): void
    {
        // Le contexte des archives ne doit jamais contenir le message en clair.
        foreach ($this->interceptions() as $interception) {
            $this->assertStringNotContainsStringIgnoringCase($interception['texte'], $interception['sens']);
        }
    }
}
