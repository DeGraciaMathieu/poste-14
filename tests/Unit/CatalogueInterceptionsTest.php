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

    public function test_chaque_reglage_est_jouable_sur_trois_rotors(): void
    {
        // Trois rotors de 26 positions : une clé hors de 0-25 serait injouable.
        foreach ($this->interceptions() as $interception) {
            $this->assertCount(3, $interception['cle']);

            foreach ($interception['cle'] as $position) {
                $this->assertGreaterThanOrEqual(0, $position);
                $this->assertLessThanOrEqual(25, $position);
            }
        }
    }

    public function test_le_mot_indice_apparait_vraiment_dans_le_message(): void
    {
        // L'indice et le surlignage promettent ce mot : il doit exister dans le clair.
        foreach ($this->interceptions() as $interception) {
            $this->assertStringContainsString($interception['motCle'], $interception['texte']);
        }
    }
}
