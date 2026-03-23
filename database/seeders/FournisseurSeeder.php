<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fournisseur;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        $fournisseurs = [
            [
                'nom'            => 'Minoterie de Côte d\'Ivoire',
                'telephone'      => '27 20 21 22 23',
                'email'          => 'contact@minoterie-ci.com',
                'ville'          => 'Abidjan',
                'contact_nom'    => 'M. Konan',
                'type'           => 'matiere',
                'plafond_credit' => 500000,
                'notes'          => 'Principal fournisseur de farine. Livraison les lundis et jeudis.',
            ],
            [
                'nom'            => 'SIFCA Distribution',
                'telephone'      => '27 20 30 31 32',
                'email'          => 'ventes@sifca.ci',
                'ville'          => 'Abidjan',
                'contact_nom'    => 'Mme Diabaté',
                'type'           => 'matiere',
                'plafond_credit' => 300000,
                'notes'          => 'Fournisseur sucre, huile végétale et margarine.',
            ],
            [
                'nom'            => 'Lait & Co Abidjan',
                'telephone'      => '07 08 09 10 11',
                'email'          => null,
                'ville'          => 'Abidjan',
                'contact_nom'    => 'M. Touré',
                'type'           => 'matiere',
                'plafond_credit' => 0,
                'notes'          => 'Paiement comptant uniquement.',
            ],
            [
                'nom'            => 'Emballages Pro CI',
                'telephone'      => '05 06 07 08 09',
                'email'          => 'emballages@pro-ci.com',
                'ville'          => 'Yopougon',
                'contact_nom'    => null,
                'type'           => 'emballage',
                'plafond_credit' => 100000,
                'notes'          => 'Sachets, cartons d\'emballage.',
            ],
        ];

        foreach ($fournisseurs as $data) {
            Fournisseur::firstOrCreate(['nom' => $data['nom']], array_merge($data, ['created_by' => 1]));
        }
    }
}
