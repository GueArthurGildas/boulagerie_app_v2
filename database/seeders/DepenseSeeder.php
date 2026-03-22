<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategorieDepense;
use App\Models\Depense;

class DepenseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Loyer',           'couleur' => '#2C3E50'],
            ['nom' => 'Électricité',     'couleur' => '#F39C12'],
            ['nom' => 'Eau',             'couleur' => '#3498DB'],
            ['nom' => 'Carburant',       'couleur' => '#E67E22'],
            ['nom' => 'Salaires',        'couleur' => '#8E44AD'],
            ['nom' => 'Fournitures',     'couleur' => '#95A5A6'],
            ['nom' => 'Transport',       'couleur' => '#E74C3C'],
            ['nom' => 'Maintenance',     'couleur' => '#D35400'],
            ['nom' => 'Communication',   'couleur' => '#2980B9'],
            ['nom' => 'Taxes & impôts',  'couleur' => '#7F8C8D'],
            ['nom' => 'Divers',          'couleur' => '#BDC3C7'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['nom']] = CategorieDepense::firstOrCreate(
                ['nom' => $cat['nom']],
                ['couleur' => $cat['couleur']]
            );
        }

        // Quelques dépenses exemples
        $exemples = [
            ['libelle' => 'Loyer local boulangerie',     'cat' => 'Loyer',         'montant' => 150000, 'mode' => 'banque',       'recurrente' => true,  'freq' => 'mensuelle'],
            ['libelle' => 'Facture CIE électricité',     'cat' => 'Électricité',   'montant' => 45000,  'mode' => 'cash',         'recurrente' => true,  'freq' => 'mensuelle'],
            ['libelle' => 'Facture SODECI eau',          'cat' => 'Eau',           'montant' => 12000,  'mode' => 'orange_money', 'recurrente' => true,  'freq' => 'mensuelle'],
            ['libelle' => 'Carburant véhicule livraison','cat' => 'Carburant',     'montant' => 25000,  'mode' => 'cash',         'recurrente' => false, 'freq' => null],
            ['libelle' => 'Achat emballages cartons',    'cat' => 'Fournitures',   'montant' => 18500,  'mode' => 'cash',         'recurrente' => false, 'freq' => null],
            ['libelle' => 'Abonnement internet',         'cat' => 'Communication', 'montant' => 30000,  'mode' => 'wave',         'recurrente' => true,  'freq' => 'mensuelle'],
        ];

        foreach ($exemples as $ex) {
            Depense::create([
                'categorie_depense_id' => $catModels[$ex['cat']]->id,
                'libelle'              => $ex['libelle'],
                'montant'              => $ex['montant'],
                'mode_paiement'        => $ex['mode'],
                'date_depense'         => now()->subDays(rand(1, 20)),
                'statut'               => 'validee',
                'valide_par'           => 1,
                'valide_le'            => now(),
                'est_recurrente'       => $ex['recurrente'],
                'frequence_recurrence' => $ex['freq'],
                'created_by'           => 1,
            ]);
        }
    }
}
